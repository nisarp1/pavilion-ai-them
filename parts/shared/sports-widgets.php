<!-- Cricket Scores Widget -->
<div class="sports-widget bg-grey-light-three m-b-xs-20">
    <div class="section-title m-b-xs-10">
        <a href="#" class="d-block">
            <h2 class="axil-title"><i class="fas fa-baseball-ball"></i> Cricket Scores</h2>
        </a>
        <div class="last-updated">Live <i class="fas fa-circle pulsing-dot"></i></div>
    </div>
    <div class="sports-content" id="cricket-scores">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i> Loading scores...
        </div>
    </div>
    <div class="widget-footer">
        <small class="text-muted">Powered by <a href="https://cricketdata.org" target="_blank" rel="noopener" style="color: #666; text-decoration: none;">CricketData</a></small>
    </div>
</div>
<!-- End of Cricket Scores Widget -->

<!-- Football Scores Widget -->
<div class="sports-widget bg-grey-light-three m-b-xs-40">
    <div class="section-title m-b-xs-10">
        <a href="#" class="d-block">
            <h2 class="axil-title"><i class="fas fa-futbol"></i> Football Matches</h2>
        </a>
        <div class="last-updated">Live <i class="fas fa-circle pulsing-dot"></i></div>
    </div>
    <div class="sports-content" id="football-scores">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i> Loading matches...
        </div>
    </div>
    <div class="widget-footer">
        <small class="text-muted">Powered by API-Sports</small>
    </div>
</div>
<!-- End of Football Scores Widget -->

<style>
.sports-widget {
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.sports-widget .section-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e0e0e0;
}

.sports-widget .axil-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    color: #333;
}

.sports-widget .axil-title i {
    margin-right: 8px;
    color: #007bff;
}

.sports-widget .last-updated {
    font-size: 12px;
    color: #666;
    display: flex;
    align-items: center;
}

.pulsing-dot {
    font-size: 8px;
    margin-left: 5px;
    color: #28a745;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.loading-spinner {
    text-align: center;
    padding: 20px;
    color: #666;
}

.loading-spinner i {
    margin-right: 5px;
}

.match-item, .cricket-match-item {
    padding: 12px;
    margin-bottom: 10px;
    background: white;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.match-header {
    font-size: 11px;
    color: #999;
    text-transform: uppercase;
    margin-bottom: 8px;
    font-weight: 600;
}

.team-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
    padding: 5px 0;
}

.team-name {
    font-weight: 500;
    font-size: 14px;
    flex: 1;
}

.team-score {
    font-weight: bold;
    font-size: 16px;
    color: #333;
    min-width: 40px;
    text-align: right;
}

.match-status {
    font-size: 11px;
    color: #666;
    margin-top: 8px;
    text-align: center;
    padding: 5px;
    background: #f8f9fa;
    border-radius: 3px;
}

.match-status.live {
    background: #fff3cd;
    color: #856404;
}

.team-india-match {
    border-left-color: #FF9933 !important;
    background: #fff8f0 !important;
}

.team-india-badge {
    display: inline-block;
    background: #FF9933;
    color: white;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: bold;
    margin-left: 8px;
}

.no-matches {
    padding: 20px;
    text-align: center;
    color: #999;
    font-size: 14px;
}
</style>

<script>
(function() {
    // CricketData API Configuration
    const CRICKETDATA_API_KEY = '8deeab82-7ecd-460f-be0a-1e27bf59cb2c';
    const CRICKETDATA_BASE_URL = 'https://api.cricketdata.org/v1';
    const TEAM_INDIA_PRIORITY = true; // Prioritize Team India matches
    
    // Get Cricket Scores from CricketData API
    function fetchCricketScores() {
        const container = document.getElementById('cricket-scores');
        
        // Using our backend proxy with CricketData API
        fetch('api-proxy.php?type=cricket&api_key=' + CRICKETDATA_API_KEY)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    // Sort matches to prioritize Team India
                    const sortedMatches = prioritizeTeamIndiaMatches(data.data);
                    displayCricketScores(sortedMatches);
                } else {
                    throw new Error('No data');
                }
            })
            .catch(error => {
                console.error('Cricket fetch error:', error);
                // Fallback to mock data
                const mockData = getMockCricketData();
                displayCricketScores(mockData);
            });
    }
    
    // Prioritize Team India matches
    function prioritizeTeamIndiaMatches(matches) {
        if (!TEAM_INDIA_PRIORITY || !matches || matches.length === 0) {
            return matches;
        }
        
        const teamIndiaMatches = [];
        const otherMatches = [];
        
        matches.forEach(match => {
            const team1 = (match.team1 || '').toLowerCase();
            const team2 = (match.team2 || '').toLowerCase();
            const isTeamIndia = team1.includes('india') || team2.includes('india') || 
                              team1.includes('indian') || team2.includes('indian');
            
            if (isTeamIndia) {
                teamIndiaMatches.push(match);
            } else {
                otherMatches.push(match);
            }
        });
        
        // Return Team India matches first, then others
        return [...teamIndiaMatches, ...otherMatches];
    }
    
    function getMockCricketData() {
        return [
            {
                league: 'T20 World Cup',
                team1: 'India',
                team2: 'Australia',
                score1: '145/6',
                score2: '148/3',
                status: 'Australia won by 7 wickets',
                live: false
            },
            {
                league: 'IPL 2024',
                team1: 'Mumbai Indians',
                team2: 'Chennai Super Kings',
                score1: '0/0',
                score2: '0/0',
                status: 'Live updates available',
                live: false
            }
        ];
    }
    
    function displayCricketScores(matches) {
        const container = document.getElementById('cricket-scores');
        
        if (!matches || matches.length === 0) {
            container.innerHTML = '<div class="no-matches">No matches scheduled right now.</div>';
            return;
        }
        
        let html = '';
        matches.forEach(match => {
            // Highlight Team India matches
            const isTeamIndia = (match.team1 && (match.team1.toLowerCase().includes('india') || match.team1.toLowerCase().includes('indian'))) ||
                               (match.team2 && (match.team2.toLowerCase().includes('india') || match.team2.toLowerCase().includes('indian')));
            const highlightClass = isTeamIndia ? 'team-india-match' : '';
            
            html += `
                <div class="cricket-match-item ${highlightClass}">
                    <div class="match-header">
                        ${escapeHtml(match.league || 'Cricket Match')}
                        ${isTeamIndia ? '<span class="team-india-badge">🇮🇳 INDIA</span>' : ''}
                    </div>
                    <div class="team-row">
                        <span class="team-name">${escapeHtml(match.team1 || 'TBD')}</span>
                        <span class="team-score">${escapeHtml(match.score1 || '0/0')}</span>
                    </div>
                    <div class="team-row">
                        <span class="team-name">${escapeHtml(match.team2 || 'TBD')}</span>
                        <span class="team-score">${escapeHtml(match.score2 || '0/0')}</span>
                    </div>
                    <div class="match-status ${match.live ? 'live' : ''}">${escapeHtml(match.status || 'Scheduled')}</div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Get Football Scores
    function fetchFootballScores() {
        const container = document.getElementById('football-scores');
        
        // Using our backend proxy
        fetch('api-proxy.php?type=football')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    displayFootballScores(data.data);
                } else {
                    throw new Error('No data');
                }
            })
            .catch(error => {
                console.error('Football fetch error:', error);
                // Fallback to mock data
                const mockData = getMockFootballData();
                displayFootballScores(mockData);
            });
    }
    
    function getMockFootballData() {
        return [
            {
                league: 'Premier League',
                team1: 'Man United',
                team2: 'Chelsea',
                score1: '2',
                score2: '1',
                status: 'Full Time',
                live: false
            },
            {
                league: 'La Liga',
                team1: 'Real Madrid',
                team2: 'Barcelona',
                score1: '1',
                score2: '1',
                status: 'Live - 67\'',
                live: true
            }
        ];
    }
    
    function displayFootballScores(matches) {
        const container = document.getElementById('football-scores');
        
        if (!matches || matches.length === 0) {
            container.innerHTML = '<div class="no-matches">No matches scheduled right now.</div>';
            return;
        }
        
        let html = '';
        matches.forEach(match => {
            html += `
                <div class="match-item">
                    <div class="match-header">${escapeHtml(match.league)}</div>
                    <div class="team-row">
                        <span class="team-name">${escapeHtml(match.team1)}</span>
                        <span class="team-score">${escapeHtml(match.score1)}</span>
                    </div>
                    <div class="team-row">
                        <span class="team-name">${escapeHtml(match.team2)}</span>
                        <span class="team-score">${escapeHtml(match.score2)}</span>
                    </div>
                    <div class="match-status ${match.live ? 'live' : ''}">${escapeHtml(match.status)}</div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            fetchCricketScores();
            fetchFootballScores();
        });
    } else {
        fetchCricketScores();
        fetchFootballScores();
    }
    
    // Refresh cricket scores more frequently (every 2 minutes for live updates)
    setInterval(function() {
        fetchCricketScores();
    }, 120000); // 2 minutes
    
    // Refresh football scores every 5 minutes
    setInterval(function() {
        fetchFootballScores();
    }, 300000);
})();
</script>

