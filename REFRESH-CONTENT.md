# Refresh RSS Content Guide

## Quick Refresh

To update your site with the latest articles from PavilionEnd.in:

```bash
cd /Applications/MAMP/htdocs/pavilion-theme
php fetch-rss-data.php
```

This will fetch the latest 50 posts and update `data.json`.

## Automated Refresh (Recommended)

Set up a daily cron job to automatically refresh content:

```bash
# Add to crontab (run daily at 2 AM)
0 2 * * * cd /Applications/MAMP/htdocs/pavilion-theme && php fetch-rss-data.php >> /var/log/rss-refresh.log 2>&1
```

## Manual Refresh

You can also manually refresh by:
1. Running `php fetch-rss-data.php` from terminal
2. Or create a simple admin interface
3. Or set up a webhook from PavilionEnd.in

---

_Note: If you deleted fetch-rss-data.php, you can recreate it from the backup in your git history._

