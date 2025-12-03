<?php
/**
 * Test CricketData API
 */

$apiKey = '8deeab82-7ecd-460f-be0a-1e27bf59cb2c';

// Try different endpoint formats
$endpoints = [
    'v1_live' => 'https://api.cricketdata.org/v1/matches?apikey=' . $apiKey . '&status=live',
    'v1_all' => 'https://api.cricketdata.org/v1/matches?apikey=' . $apiKey,
    'v3_live' => 'https://api.cricketdata.org/v3/matches?apikey=' . $apiKey . '&status=live',
];

header('Content-Type: application/json');

foreach ($endpoints as $name => $url) {
    echo "\n=== Testing: $name ===\n";
    echo "URL: $url\n\n";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode\n";
    if ($error) {
        echo "Error: $error\n";
    }
    
    if ($response) {
        $data = json_decode($response, true);
        if ($data) {
            echo "Response structure:\n";
            print_r(array_keys($data));
            if (isset($data['data']) && is_array($data['data']) && count($data['data']) > 0) {
                echo "\nFirst match structure:\n";
                print_r($data['data'][0]);
            }
        } else {
            echo "Response (first 500 chars):\n";
            echo substr($response, 0, 500) . "\n";
        }
    }
    echo "\n" . str_repeat('-', 80) . "\n";
}

?>

