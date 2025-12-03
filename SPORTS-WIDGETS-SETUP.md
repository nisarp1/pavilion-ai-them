# Sports Widgets Setup Guide

## Overview
The right sidebar now includes three live widgets:
1. **Currency Exchange Rates** (already configured)
2. **Cricket Scores Widget**
3. **Football Scores Widget**

## Current Status
✅ Widgets are installed and displaying with mock data  
⚠️ To get **real live scores**, you need to configure API keys

## How to Get Free API Keys

### Cricket Scores API

**Option 1: Cricket Data API** (Recommended)
- Website: https://cricketdata.org/
- Free Tier: Available
- Features: Live scores, match details, player statistics
- Setup:
  1. Sign up at cricketdata.org
  2. Get your free API key
  3. Update `api-proxy.php` line with your key:
     ```php
     $apiKey = 'YOUR_CRICKET_DATA_API_KEY';
     ```

**Option 2: Sportmonks Cricket API**
- Website: https://www.sportmonks.com/
- Free Tier: Limited, then paid plans
- Setup similar to above

### Football Scores API

**Option 1: Football-Data.org** (Recommended)
- Website: https://www.football-data.org/
- Free Tier: Available (limited requests)
- Features: Live scores, fixtures, standings
- Setup:
  1. Sign up at football-data.org
  2. Get your free API key
  3. Update `api-proxy.php` with your key:
     ```php
     $apiKey = 'YOUR_FOOTBALL_DATA_API_KEY';
     ```

**Option 2: API-Football**
- Website: https://www.api-football.com/
- Free Tier: Limited
- Setup similar to above

## Enabling Real API Data

Once you have your API keys, follow these steps:

### Step 1: Update api-proxy.php

Open `/Applications/MAMP/htdocs/pavilion-theme/api-proxy.php` and:

1. Find the `getCricketData()` function (around line 30)
2. Uncomment the `TODO` section
3. Replace `YOUR_CRICKET_DATA_API_KEY` with your actual key:

```php
$apiKey = 'abc123xyz'; // Your actual API key

$ch = curl_init('https://api.cricketdata.org/v3/matches?apikey=' . $apiKey . '&status=live');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// ... rest of code
```

4. Do the same for `getFootballData()` function

### Step 2: Test the API

Open your browser and visit:
- Cricket: `http://localhost:8888/pavilion-theme/api-proxy.php?type=cricket`
- Football: `http://localhost:8888/pavilion-theme/api-proxy.php?type=football`

You should see JSON data with real match information.

### Step 3: Update Format Functions (if needed)

If the API returns data in a different format, you may need to:
1. Create `formatCricketData()` function in `api-proxy.php`
2. Create `formatFootballData()` function in `api-proxy.php`
3. These functions should convert API response to widget format

Example format function:

```php
function formatCricketData($apiData) {
    $formatted = [];
    foreach ($apiData as $match) {
        $formatted[] = [
            'league' => $match['series']['name'] ?? 'Cricket Match',
            'team1' => $match['team1']['name'] ?? 'Team 1',
            'team2' => $match['team2']['name'] ?? 'Team 2',
            'score1' => $match['team1']['score'] ?? '--',
            'score2' => $match['team2']['score'] ?? '--',
            'status' => $match['match_status'] ?? 'In Progress',
            'live' => $match['is_live'] ?? false
        ];
    }
    return $formatted;
}
```

## Widget Customization

### Styling
Styles are defined in `parts/shared/sports-widgets.php` starting around line 20.

### Refresh Rate
Current refresh rate: **5 minutes** (300000ms)

To change it, edit this line in `parts/shared/sports-widgets.php`:

```javascript
setInterval(function() {
    fetchCricketScores();
    fetchFootballScores();
}, 300000); // Change 300000 to your desired milliseconds
```

### Displayed Matches
Currently displays: **2 matches per widget**

To change:
1. Edit `getMockCricketData()` and `getMockFootballData()` in `sports-widgets.php`
2. Or modify the API response formatting

## Troubleshooting

### Widgets Show Mock Data
- **Cause**: API keys not configured or API is down
- **Solution**: Configure API keys in `api-proxy.php` (see Step 1 above)

### Widgets Show "Loading..."
- **Cause**: JavaScript error or fetch failed
- **Solution**: 
  1. Open browser console (F12)
  2. Check for JavaScript errors
  3. Verify `api-proxy.php` is accessible
  4. Check server logs

### CORS Errors
- **Cause**: Browser blocking API requests
- **Solution**: Already handled by using backend proxy (`api-proxy.php`)

### API Rate Limits
- **Cause**: Free tier API limits exceeded
- **Solution**: 
  1. Wait for rate limit to reset
  2. Upgrade to paid plan
  3. Add caching to reduce API calls

### Widgets Not Loading on Homepage
- **Cause**: Wrong file path or PHP error
- **Solution**:
  1. Verify `parts/shared/sports-widgets.php` exists
  2. Check `home.php` includes the file
  3. View page source to ensure HTML is rendered

## Files Modified
- `parts/shared/sports-widgets.php` - Widget HTML, CSS, JavaScript (NEW)
- `parts/shared/exchange-rates-widget.php` - Already existed
- `api-proxy.php` - Backend proxy for API calls (NEW)
- `home.php` - Added widget includes
- `core.php` - Added sidebar helper functions

## Support

### Free Cricket APIs
- https://cricketdata.org/
- https://www.sportmonks.com/
- https://www.roanuz.com/

### Free Football APIs
- https://www.football-data.org/
- https://www.api-football.com/
- https://apifootball.com/

## Current Mock Data

Cricket:
- T20 World Cup: India vs Australia
- IPL 2024: Mumbai Indians vs Chennai Super Kings

Football:
- Premier League: Man United vs Chelsea
- La Liga: Real Madrid vs Barcelona

These will be replaced with real data once API keys are configured.

