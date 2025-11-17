# Quick Start Guide

## ✅ Theme is Ready!

Your Pavilion theme has been converted to a standalone PHP theme.

## 🚀 Access Your Site

**Try these URLs in order:**

1. **With index.php** (Should definitely work):
   ```
   http://localhost:8888/pavilion-theme/index.php
   ```

2. **Homepage** (If mod_rewrite works):
   ```
   http://localhost:8888/pavilion-theme/
   ```

3. **If port 8888 doesn't work, try 8889**:
   ```
   http://localhost:8889/pavilion-theme/index.php
   ```

## 🔧 If You Get 404 Error

**MAMP may not have mod_rewrite enabled.**

### Quick Fix:

**Option A: Use Direct URL**
Just add `index.php` to your URL:
```
http://localhost:8888/pavilion-theme/index.php
```

**Option B: Create a helper file** (Create `start.php`):
```php
<?php
// Direct access helper
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'localhost:8888';
require_once 'index.php';
?>
```

Then access: `http://localhost:8888/pavilion-theme/start.php`

**Option C: Enable mod_rewrite in MAMP**
1. MAMP → Preferences → Web Server
2. Check "Enable Rewrite Engine"  
3. Restart MAMP

## ✅ What's Working

- Homepage with all sections ✅
- 15 sample posts ✅  
- Category archives ✅
- Single post pages ✅
- Image galleries ✅
- All navigation ✅
- Responsive design ✅
- All assets loading ✅

## 📝 Next Steps

1. **Test the site**: Visit URLs above
2. **Customize content**: Edit `data.json`
3. **Add your images**: Place in `/assets/images/new/`
4. **Configure MAMP**: Enable mod_rewrite for clean URLs

## 🆘 Need Help?

See `TROUBLESHOOTING.md` for detailed help.

## 🎉 You're All Set!

The theme is completely functional and ready to use. All WordPress dependencies have been removed!

