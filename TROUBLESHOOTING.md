# Troubleshooting Guide

## "404 Page Not Found" Error

If you're seeing a 404 error at `http://localhost:8888/pavilion-theme/`, try these solutions:

### Solution 1: Access index.php Directly

Try accessing the site with index.php in the URL:
```
http://localhost:8888/pavilion-theme/index.php
```

This bypasses .htaccess rules and should work immediately.

### Solution 2: Enable mod_rewrite in MAMP

1. Open MAMP preferences
2. Go to Web Server tab
3. Make sure "Enable Rewrite Engine" is checked
4. Restart MAMP servers

### Solution 3: Check MAMP Apache Configuration

1. Open `/Applications/MAMP/bin/apache2/conf/httpd.conf`
2. Find: `LoadModule rewrite_module modules/mod_rewrite.so`
3. Make sure it's NOT commented out (no # at the start)
4. Restart MAMP

### Solution 4: Alternative Router

If .htaccess still doesn't work, you can use a simple entry point by creating `router.php`:

```php
<?php
// Simple entry point for testing
$_SERVER['REQUEST_URI'] = '/';
require_once __DIR__ . '/index.php';
```

Then access: `http://localhost:8888/pavilion-theme/router.php`

### Solution 5: Check File Permissions

Make sure files are readable:
```bash
chmod 644 /Applications/MAMP/htdocs/pavilion-theme/*.php
chmod 644 /Applications/MAMP/htdocs/pavilion-theme/.htaccess
```

## Common Issues

**No Images Showing:**
- Check browser console for 404 errors on image paths
- Verify images exist in `/assets/images/new/`
- Make sure paths start with `/assets/` not relative paths

**No Styling:**
- Open browser DevTools and check Network tab
- Look for failed CSS/JS loads
- Verify all assets exist in `/assets/css/` and `/assets/js/`

**White Screen:**
- Enable PHP errors: `ini_set('display_errors', 1);` at top of index.php
- Check MAMP error logs
- Verify PHP version is 7.4+

**Data Not Loading:**
- Verify `data.json` exists and is readable
- Check JSON syntax: `php -l data.json`
- Try accessing: `http://localhost:8888/pavilion-theme/data.json`

## Debugging Commands

Test PHP syntax:
```bash
cd /Applications/MAMP/htdocs/pavilion-theme
php -l core.php
php -l index.php
php -l home.php
```

Test data loading:
```bash
php -r "require 'core.php'; var_dump(load_json_data());"
```

## Still Having Issues?

1. Check MAMP is running on port 8888
2. Try a different port (8889)
3. Check Apache error logs in MAMP
4. Verify all files were copied correctly
5. Try accessing from another browser

## Quick Test

Run this in terminal to test if everything works:
```bash
cd /Applications/MAMP/htdocs/pavilion-theme
php -r "require 'core.php'; echo 'Data loaded: ' . (load_json_data() ? 'YES' : 'NO') . PHP_EOL; echo 'Site name: ' . get_bloginfo('name') . PHP_EOL;"
```

If this works, the issue is with MAMP/Apache configuration.

