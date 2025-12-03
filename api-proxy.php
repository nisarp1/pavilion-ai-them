<?php
/**
 * API Proxy for Sports Widgets
 * This file acts as a backend proxy to fetch sports data from APIs
 * 
 * Usage:
 * - GET /api-proxy.php?type=cricket
 * - GET /api-proxy.php?type=football
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$type = isset($_GET['type']) ? $_GET['type'] : '';

// Configuration
// You can get free API keys from:
// Cricket: https://cricketdata.org/
// Football: https://www.football-data.org/

function getCricketData() {
    $apiKey = isset($_GET['api_key']) ? $_GET['api_key'] : '8deeab82-7ecd-460f-be0a-1e27bf59cb2c';
    
    // Try to get live matches first
    $liveMatches = fetchCricketDataAPI($apiKey, 'live');
    
    // If no live matches, get recent/fixtures
    if (empty($liveMatches)) {
        $liveMatches = fetchCricketDataAPI($apiKey, 'recent');
    }
    
    // If still no matches, get upcoming fixtures
    if (empty($liveMatches)) {
        $liveMatches = fetchCricketDataAPI($apiKey, 'fixtures');
    }
    
    // Format and prioritize Team India matches
    $formattedMatches = formatCricketData($liveMatches);
    
    // Prioritize Team India matches
    $teamIndiaMatches = [];
    $otherMatches = [];
    
    foreach ($formattedMatches as $match) {
        $team1 = isset($match['team1']) ? strtolower($match['team1']) : '';
        $team2 = isset($match['team2']) ? strtolower($match['team2']) : '';
        
        $isTeamIndia = (strpos($team1, 'india') !== false || strpos($team2, 'india') !== false ||
                        strpos($team1, 'indian') !== false || strpos($team2, 'indian') !== false);
        
        if ($isTeamIndia) {
            $teamIndiaMatches[] = $match;
        } else {
            $otherMatches[] = $match;
        }
    }
    
    // Return Team India matches first, then others (limit to 5 total)
    $allMatches = array_merge($teamIndiaMatches, $otherMatches);
    return array_slice($allMatches, 0, 5);
}

function fetchCricketDataAPI($apiKey, $status = 'live') {
    // Try multiple endpoint formats for CricketData API
    $endpoints = [];
    
    if ($status === 'live') {
        $endpoints = [
            'https://api.cricketdata.org/v1/matches?apikey=' . $apiKey . '&status=live',
            'https://api.cricketdata.org/v1/matches?apikey=' . $apiKey . '&matchType=live',
            'https://api.cricketdata.org/v3/matches?apikey=' . $apiKey . '&status=live',
            'https://api.cricketdata.org/v1/matches?apikey=' . $apiKey // Get all and filter
        ];
    } elseif ($status === 'recent') {
        $endpoints = [
            'https://api.cricketdata.org/v1/matches?apikey=' . $apiKey . '&status=completed&limit=10',
            'https://api.cricketdata.org/v1/matches?apikey=' . $apiKey . '&status=finished&limit=10',
            'https://api.cricketdata.org/v3/matches?apikey=' . $apiKey . '&status=completed&limit=10'
        ];
    } elseif ($status === 'all') {
        // Get all matches (for filtering)
        $endpoints = [
            'https://api.cricketdata.org/v1/matches?apikey=' . $apiKey,
            'https://api.cricketdata.org/v1/matches?apikey=' . $apiKey . '&limit=50',
            'https://api.cricketdata.org/v3/matches?apikey=' . $apiKey
        ];
    } else {
        $endpoints = [
            'https://api.cricketdata.org/v1/matches?apikey=' . $apiKey . '&status=fixture&limit=10',
            'https://api.cricketdata.org/v1/matches?apikey=' . $apiKey . '&status=upcoming&limit=10',
            'https://api.cricketdata.org/v3/matches?apikey=' . $apiKey . '&status=fixture&limit=10'
        ];
    }
    
    // Try each endpoint until one works
    foreach ($endpoints as $url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: Mozilla/5.0'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("CricketData API cURL Error: " . $error . " (URL: " . $url . ")");
            continue; // Try next endpoint
        }
        
        if ($httpCode !== 200) {
            error_log("CricketData API HTTP Error: " . $httpCode . " (URL: " . $url . ")");
            if ($httpCode === 401 || $httpCode === 403) {
                // Authentication error, don't try other endpoints
                break;
            }
            continue; // Try next endpoint
        }
        
        if (!$response) {
            continue;
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("CricketData JSON Error: " . json_last_error_msg() . " (URL: " . $url . ")");
            continue;
        }
        
        // Handle different response formats
        $matches = [];
        if (isset($data['data']) && is_array($data['data'])) {
            $matches = $data['data'];
        } elseif (isset($data['matches']) && is_array($data['matches'])) {
            $matches = $data['matches'];
        } elseif (isset($data['results']) && is_array($data['results'])) {
            $matches = $data['results'];
        } elseif (is_array($data) && isset($data[0])) {
            $matches = $data;
        }
        
        // Filter by status if needed (for endpoints that return all matches)
        if ($status === 'live' && !empty($matches)) {
            $matches = array_filter($matches, function($match) {
                $matchStatus = '';
                if (isset($match['status'])) {
                    $matchStatus = strtolower($match['status']);
                } elseif (isset($match['matchStatus'])) {
                    $matchStatus = strtolower($match['matchStatus']);
                } elseif (isset($match['state'])) {
                    $matchStatus = strtolower($match['state']);
                }
                return (strpos($matchStatus, 'live') !== false || 
                       strpos($matchStatus, 'in progress') !== false ||
                       strpos($matchStatus, 'ongoing') !== false);
            });
            $matches = array_values($matches); // Re-index array
        }
        
        if (!empty($matches)) {
            return $matches;
        }
    }
    
    return [];
}

function formatCricketData($matches) {
    if (empty($matches) || !is_array($matches)) {
        return [];
    }
    
    $formatted = [];
    
    foreach ($matches as $match) {
        // Extract team names
        $team1 = '';
        $team2 = '';
        $score1 = '0/0';
        $score2 = '0/0';
        $status = 'Match scheduled';
        $live = false;
        $league = 'Cricket Match';
        
        // Handle different API response formats for teams
        if (isset($match['team1']) && isset($match['team2'])) {
            $team1 = is_array($match['team1']) ? (isset($match['team1']['name']) ? $match['team1']['name'] : (isset($match['team1']['shortName']) ? $match['team1']['shortName'] : '')) : $match['team1'];
            $team2 = is_array($match['team2']) ? (isset($match['team2']['name']) ? $match['team2']['name'] : (isset($match['team2']['shortName']) ? $match['team2']['shortName'] : '')) : $match['team2'];
        } elseif (isset($match['homeTeam']) && isset($match['awayTeam'])) {
            $team1 = is_array($match['homeTeam']) ? (isset($match['homeTeam']['name']) ? $match['homeTeam']['name'] : (isset($match['homeTeam']['shortName']) ? $match['homeTeam']['shortName'] : '')) : $match['homeTeam'];
            $team2 = is_array($match['awayTeam']) ? (isset($match['awayTeam']['name']) ? $match['awayTeam']['name'] : (isset($match['awayTeam']['shortName']) ? $match['awayTeam']['shortName'] : '')) : $match['awayTeam'];
        } elseif (isset($match['teams']) && is_array($match['teams'])) {
            $team1 = isset($match['teams'][0]) ? (is_array($match['teams'][0]) ? (isset($match['teams'][0]['name']) ? $match['teams'][0]['name'] : (isset($match['teams'][0]['shortName']) ? $match['teams'][0]['shortName'] : '')) : $match['teams'][0]) : '';
            $team2 = isset($match['teams'][1]) ? (is_array($match['teams'][1]) ? (isset($match['teams'][1]['name']) ? $match['teams'][1]['name'] : (isset($match['teams'][1]['shortName']) ? $match['teams'][1]['shortName'] : '')) : $match['teams'][1]) : '';
        }
        
        // Extract scores - handle various formats
        if (isset($match['score'])) {
            if (is_array($match['score'])) {
                $score1 = isset($match['score'][0]) ? (is_array($match['score'][0]) ? (isset($match['score'][0]['runs']) ? $match['score'][0]['runs'] . '/' . (isset($match['score'][0]['wickets']) ? $match['score'][0]['wickets'] : '0') : '') : $match['score'][0]) : '0/0';
                $score2 = isset($match['score'][1]) ? (is_array($match['score'][1]) ? (isset($match['score'][1]['runs']) ? $match['score'][1]['runs'] . '/' . (isset($match['score'][1]['wickets']) ? $match['score'][1]['wickets'] : '0') : '') : $match['score'][1]) : '0/0';
            } else {
                $score1 = $match['score'];
            }
        } elseif (isset($match['homeScore']) && isset($match['awayScore'])) {
            $score1 = is_array($match['homeScore']) ? (isset($match['homeScore']['runs']) ? $match['homeScore']['runs'] . '/' . (isset($match['homeScore']['wickets']) ? $match['homeScore']['wickets'] : '0') : '') : $match['homeScore'];
            $score2 = is_array($match['awayScore']) ? (isset($match['awayScore']['runs']) ? $match['awayScore']['runs'] . '/' . (isset($match['awayScore']['wickets']) ? $match['awayScore']['wickets'] : '0') : '') : $match['awayScore'];
        } elseif (isset($match['scores']) && is_array($match['scores'])) {
            // Handle scores array format
            if (isset($match['scores'][0])) {
                $score1 = is_array($match['scores'][0]) ? (isset($match['scores'][0]['runs']) ? $match['scores'][0]['runs'] . '/' . (isset($match['scores'][0]['wickets']) ? $match['scores'][0]['wickets'] : '0') : '') : $match['scores'][0];
            }
            if (isset($match['scores'][1])) {
                $score2 = is_array($match['scores'][1]) ? (isset($match['scores'][1]['runs']) ? $match['scores'][1]['runs'] . '/' . (isset($match['scores'][1]['wickets']) ? $match['scores'][1]['wickets'] : '0') : '') : $match['scores'][1];
            }
        }
        
        // Extract status
        if (isset($match['status'])) {
            $status = $match['status'];
            $live = (strpos(strtolower($status), 'live') !== false || 
                    strpos(strtolower($status), 'in progress') !== false ||
                    strpos(strtolower($status), 'ongoing') !== false ||
                    strpos(strtolower($status), 'started') !== false);
        } elseif (isset($match['matchStatus'])) {
            $status = $match['matchStatus'];
            $live = (strpos(strtolower($status), 'live') !== false);
        } elseif (isset($match['state'])) {
            $status = $match['state'];
            $live = (strpos(strtolower($status), 'live') !== false || strpos(strtolower($status), 'in progress') !== false);
        }
        
        // Extract league/tournament/series
        if (isset($match['series'])) {
            $league = is_array($match['series']) ? (isset($match['series']['name']) ? $match['series']['name'] : 'Cricket') : $match['series'];
        } elseif (isset($match['tournament'])) {
            $league = is_array($match['tournament']) ? (isset($match['tournament']['name']) ? $match['tournament']['name'] : 'Cricket') : $match['tournament'];
        } elseif (isset($match['league'])) {
            $league = is_array($match['league']) ? (isset($match['league']['name']) ? $match['league']['name'] : 'Cricket') : $match['league'];
        } elseif (isset($match['competition'])) {
            $league = is_array($match['competition']) ? (isset($match['competition']['name']) ? $match['competition']['name'] : 'Cricket') : $match['competition'];
        }
        
        // Only add if we have both teams
        if ($team1 && $team2) {
            $formatted[] = [
                'league' => $league,
                'team1' => $team1,
                'team2' => $team2,
                'score1' => $score1 ? $score1 : '0/0',
                'score2' => $score2 ? $score2 : '0/0',
                'status' => $status,
                'live' => $live
            ];
        }
    }
    
    return $formatted;
}

function getFootballData() {
    // For now, return mock data
    // TODO: Replace with actual API call when you have a key
    
    $mockData = [
        [
            'league' => 'Premier League',
            'team1' => 'Man United',
            'team2' => 'Chelsea',
            'score1' => '2',
            'score2' => '1',
            'status' => 'Full Time',
            'live' => false
        ],
        [
            'league' => 'La Liga',
            'team1' => 'Real Madrid',
            'team2' => 'Barcelona',
            'score1' => '1',
            'score2' => '1',
            'status' => 'Live - 67\'',
            'live' => true
        ]
    ];
    
    return $mockData;
    
    /* TODO: Uncomment and configure when you have API keys
    $apiKey = 'YOUR_FOOTBALL_DATA_API_KEY';
    
    $ch = curl_init('https://api.football-data.org/v4/matches');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Auth-Token: ' . $apiKey
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (isset($data['matches']) && !empty($data['matches'])) {
        return formatFootballData($data['matches']);
    }
    
    return $mockData; // Fallback
    */
}

// Main execution
try {
    switch ($type) {
        case 'cricket':
            $cricketData = getCricketData();
            // If no data, try to get any matches (including non-live) and filter for India
            if (empty($cricketData)) {
                $apiKey = isset($_GET['api_key']) ? $_GET['api_key'] : '8deeab82-7ecd-460f-be0a-1e27bf59cb2c';
                $allMatches = fetchCricketDataAPI($apiKey, 'all');
                if (!empty($allMatches)) {
                    // Filter for India matches specifically
                    $indiaMatches = array_filter($allMatches, function($match) {
                        $team1 = '';
                        $team2 = '';
                        if (isset($match['team1'])) {
                            $team1 = is_array($match['team1']) ? (isset($match['team1']['name']) ? strtolower($match['team1']['name']) : '') : strtolower($match['team1']);
                        }
                        if (isset($match['team2'])) {
                            $team2 = is_array($match['team2']) ? (isset($match['team2']['name']) ? strtolower($match['team2']['name']) : '') : strtolower($match['team2']);
                        }
                        return (strpos($team1, 'india') !== false || strpos($team2, 'india') !== false ||
                               strpos($team1, 'indian') !== false || strpos($team2, 'indian') !== false);
                    });
                    if (!empty($indiaMatches)) {
                        $cricketData = formatCricketData(array_values($indiaMatches));
                    }
                }
            }
            
            echo json_encode([
                'success' => !empty($cricketData),
                'data' => $cricketData,
                'timestamp' => time(),
                'count' => count($cricketData)
            ]);
            break;
            
        case 'football':
            echo json_encode([
                'success' => true,
                'data' => getFootballData(),
                'timestamp' => time()
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'error' => 'Invalid type parameter. Use type=cricket or type=football'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

