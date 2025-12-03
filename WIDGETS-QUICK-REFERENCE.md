# Widgets Quick Reference

## Homepage Right Sidebar Widgets

The homepage now features the following right sidebar widgets (in order from top to bottom):

### 1. Currency Exchange Rates Widget
**File:** `parts/shared/exchange-rates-widget.php`  
**Status:** ✅ Fully configured  
**Features:**
- Gold rates (24K and 22K)
- Currency conversions (AED/INR, USD/INR, USD/AED)
- Live updates with timestamps

### 2. Cricket Scores Widget
**File:** `parts/shared/sports-widgets.php`  
**Status:** ⚠️ Shows mock data (needs API key)  
**Features:**
- Live cricket scores
- Match details (teams, scores, status)
- League information
- Auto-refresh every 5 minutes

**To enable real data:**
1. Get free API key from https://cricketdata.org/
2. Update `api-proxy.php` with your key
3. See `SPORTS-WIDGETS-SETUP.md` for details

### 3. Football Scores Widget
**File:** `parts/shared/sports-widgets.php`  
**Status:** ⚠️ Shows mock data (needs API key)  
**Features:**
- Live football matches
- Match scores and status
- League information
- Auto-refresh every 5 minutes

**To enable real data:**
1. Get free API key from https://www.football-data.org/
2. Update `api-proxy.php` with your key
3. See `SPORTS-WIDGETS-SETUP.md` for details

### 4. Advertisement Section
**File:** Directly in `home.php`  
**Status:** ✅ Configured  
**Features:**
- Custom advertisement image
- Responsive design

## Widget Styling

All widgets use the following CSS classes:
- `bg-grey-light-three` - Background color
- `m-b-xs-20` / `m-b-xs-40` - Bottom margin
- `section-title` - Widget title styling
- `axil-title` - Title text styling

## Configuration Files

| File | Purpose |
|------|---------|
| `parts/shared/sports-widgets.php` | Widget HTML, CSS, and JavaScript |
| `api-proxy.php` | Backend proxy for API calls |
| `parts/shared/exchange-rates-widget.php` | Currency widget |
| `home.php` | Widget includes and layout |
| `core.php` | Sidebar helper functions |

## Testing Widgets

### Test Currency Widget
Visit: `http://localhost:8888/pavilion-theme/`

### Test Cricket API Proxy
Visit: `http://localhost:8888/pavilion-theme/api-proxy.php?type=cricket`

### Test Football API Proxy
Visit: `http://localhost:8888/pavilion-theme/api-proxy.php?type=football`

### Expected Response Format

**Success:**
```json
{
    "success": true,
    "data": [
        {
            "league": "T20 World Cup",
            "team1": "India",
            "team2": "Australia",
            "score1": "145/6",
            "score2": "148/3",
            "status": "Australia won by 7 wickets",
            "live": false
        }
    ],
    "timestamp": 1234567890
}
```

**Error:**
```json
{
    "success": false,
    "error": "Invalid type parameter"
}
```

## Mock Data

While API keys are not configured, widgets display mock data:

**Cricket:**
- T20 World Cup: India vs Australia
- IPL 2024: Mumbai Indians vs Chennai Super Kings

**Football:**
- Premier League: Man United vs Chelsea
- La Liga: Real Madrid vs Barcelona

## Customization

### Change Refresh Rate
Edit `parts/shared/sports-widgets.php` line ~376:
```javascript
setInterval(function() {
    fetchCricketScores();
    fetchFootballScores();
}, 300000); // Change to your desired milliseconds
```

### Change Number of Matches
Edit the mock data arrays or modify the API response filtering in `api-proxy.php`

### Update Widget Title
Edit `parts/shared/sports-widgets.php`:
```html
<h2 class="axil-title"><i class="fas fa-baseball-ball"></i> Cricket Scores</h2>
```

### Change Background Color
Edit `parts/shared/sports-widgets.php`:
```html
<div class="sports-widget bg-grey-light-three m-b-xs-20">
```

## Browser Console Debugging

Open browser console (F12) and look for:
- `Cricket fetch error:` - Cricket API issue
- `Football fetch error:` - Football API issue

## Next Steps

1. ✅ Widgets are installed and displaying mock data
2. ⚠️ Configure API keys for live data (see `SPORTS-WIDGETS-SETUP.md`)
3. ⚠️ Test with real APIs
4. ⚠️ Customize styling if needed
5. ⚠️ Set up caching if using paid API tiers

## Support Files

- `SPORTS-WIDGETS-SETUP.md` - Detailed setup guide
- `WIDGETS-QUICK-REFERENCE.md` - This file
- `README-STANDALONE.md` - General theme documentation
- `SETUP.md` - Theme setup instructions

