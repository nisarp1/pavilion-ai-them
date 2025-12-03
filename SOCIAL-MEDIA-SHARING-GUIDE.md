# Social Media Sharing Image Guide for Byline Gulf

## 🎯 Issue Fixed

When sharing your website on WhatsApp, Facebook, Twitter, and other social platforms, the correct images and favicon will now display properly.

---

## 📐 Image Dimensions & Requirements

### 1. Social Media Sharing Image (Open Graph Image)

**File Name:** `byline-gulf-social-share.jpg`  
**Location:** `/wp-content/themes/byline/assets/images/new/`

#### Recommended Dimensions:
- **Primary:** 1200 x 630 pixels (Aspect Ratio 1.91:1)
- **Minimum:** 600 x 315 pixels
- **Maximum:** 8 MB file size

#### Platform-Specific Recommendations:
- **Facebook/WhatsApp:** 1200 x 630px (perfect)
- **Twitter:** 1200 x 675px (16:9) or 1200 x 628px (both work)
- **LinkedIn:** 1200 x 627px
- **Instagram:** 1080 x 1080px (square) or 1200 x 630px

**Best Universal Size:** **1200 x 630 pixels** ✅

#### Image Specifications:
- **Format:** JPG or PNG (JPG recommended for smaller file size)
- **Quality:** 85-90% (good balance between quality and size)
- **Color Mode:** RGB
- **Resolution:** 72 DPI (for web)
- **File Size:** Under 1 MB (recommended), max 8 MB

---

### 2. Favicon

**File Names:** 
- `favicon.png` (already exists ✓)
- `favicon.ico` (already exists ✓)

**Location:** 
- PNG: `/wp-content/themes/byline/assets/images/new/favicon.png`
- ICO: `/favicon.ico` (root directory)

#### Favicon Dimensions:
- **Standard:** 16x16, 32x32, 48x48 pixels (ICO can contain multiple sizes)
- **PNG:** 192x192 pixels (for high-DPI displays)
- **Apple Touch Icon:** 180x180 pixels
- **Format:** ICO, PNG
- **Transparent Background:** Recommended

---

## 🎨 How to Create the Social Media Sharing Image

### Option 1: Use Canva (Easiest)

1. **Go to Canva.com**
2. **Click "Custom Size"**
3. **Enter dimensions:** 1200 x 630 pixels
4. **Design your image with:**
   - Byline Gulf logo (centered or top)
   - Tagline: "News Beyond the Lines"
   - Background: Brand colors or gradient
   - Optional: Map of Gulf region or Dubai skyline
   - Text: "Stay updated with latest news from the Middle East"

5. **Download as JPG** (high quality)
6. **Save as:** `byline-gulf-social-share.jpg`

### Option 2: Use Photoshop/GIMP

1. **Create new document:** 1200 x 630px, 72 DPI, RGB
2. **Add elements:**
   - Background color or image
   - Logo (centered)
   - Text overlay with tagline
   - Keep important content in "safe zone" (center 80%)
3. **Export as JPG:** Quality 85-90%
4. **Save as:** `byline-gulf-social-share.jpg`

### Option 3: Online Tools

**Recommended Tools:**
- **Creatopy** - https://www.creatopy.com/social-media-templates/
- **Placeit** - https://placeit.net/c/templates/social-media
- **Snappa** - https://snappa.com/
- **Adobe Express** - https://www.adobe.com/express/

---

## 📤 How to Upload the Image

### Method 1: FTP/File Manager (Recommended)

1. **Connect to your server** via FTP or File Manager
2. **Navigate to:** `/wp-content/themes/byline/assets/images/new/`
3. **Upload file:** `byline-gulf-social-share.jpg`
4. **Set permissions:** 644 (rw-r--r--)
5. **Done!** ✓

### Method 2: WordPress Media Library (Alternative)

If you prefer to use WordPress media library:

1. Upload to WordPress Media Library
2. Get the full URL of the uploaded image
3. Edit the file: `/wp-content/themes/byline/parts/shared/html-header.php`
4. Change line 21 to use your media library URL:
   ```php
   $og_image = 'https://bylinegulf.com/wp-content/uploads/2025/10/your-image-name.jpg';
   ```

---

## 🖼️ Design Guidelines for Social Media Image

### Safe Zone:
Keep important content (logo, text) within the center **1200 x 600px** area.  
Some platforms may crop edges.

### Text Guidelines:
- **Maximum text:** 20% of image area (Facebook guideline)
- **Font size:** Minimum 40px for readability
- **Colors:** High contrast (dark text on light bg or vice versa)
- **Logo size:** 200-400px wide

### Content Guidelines:
✅ **Include:**
- Byline Gulf logo
- Site name or tagline
- Brand colors
- Professional imagery

❌ **Avoid:**
- Too much text
- Low-resolution images
- Busy backgrounds that compete with text
- Important content at edges

---

## 🔍 Current Setup

### What's Already Working:

1. ✅ **Favicon** - Exists and properly linked
   - Location: `/assets/images/new/favicon.png`
   - Location: `/favicon.ico` (root)

2. ✅ **Open Graph Meta Tags** - All properly configured
   - Title, description, URL
   - Image dimensions (1200x630)
   - Twitter Card support
   - WhatsApp preview support

3. ✅ **Article Images** - Featured images automatically used
   - When you set a featured image on a post, it's used for social sharing
   - Falls back to default image if no featured image

### What You Need to Do:

1. ⚠️ **Upload Custom Social Share Image**
   - Create: `byline-gulf-social-share.jpg` (1200x630px)
   - Upload to: `/wp-content/themes/byline/assets/images/new/`
   - Currently using: `Logo.png` as fallback

---

## 🧪 How to Test Social Media Sharing

### Facebook Debugger:
1. Go to: https://developers.facebook.com/tools/debug/
2. Enter URL: https://bylinegulf.com/
3. Click "Debug"
4. See preview
5. If needed, click "Scrape Again" to refresh

### Twitter Card Validator:
1. Go to: https://cards-dev.twitter.com/validator
2. Enter URL: https://bylinegulf.com/
3. See preview

### LinkedIn Post Inspector:
1. Go to: https://www.linkedin.com/post-inspector/
2. Enter URL: https://bylinegulf.com/
3. See preview

### WhatsApp:
1. Send the URL to yourself on WhatsApp
2. Wait for preview to load
3. Check image, title, description

### Manual Testing:
1. Clear Facebook/WhatsApp cache (use debugger tools above)
2. Share the link in a private chat
3. Verify correct image appears

---

## 📋 Quick Checklist

Before uploading:
- [ ] Image is 1200 x 630 pixels
- [ ] File format is JPG or PNG
- [ ] File size is under 1 MB
- [ ] Image quality is 85-90%
- [ ] Logo is visible and centered
- [ ] Text is readable
- [ ] Colors match brand
- [ ] No copyright issues

After uploading:
- [ ] File uploaded to `/wp-content/themes/byline/assets/images/new/`
- [ ] File named exactly: `byline-gulf-social-share.jpg`
- [ ] File permissions are 644
- [ ] Clear browser cache
- [ ] Test with Facebook Debugger
- [ ] Test on WhatsApp
- [ ] Share a test post

---

## 🎨 Temporary Solution (Until You Upload Custom Image)

Currently, the system is using `Logo.png` as a fallback. This will work, but:
- May not display perfectly on all platforms
- Not optimized for social media dimensions
- Better to upload a custom 1200x630px image

---

## 🔧 Technical Details

### Files Modified:
- `/wp-content/themes/byline/parts/shared/html-header.php`
  - Lines 20-25: Default social share image path
  - Lines 36-49: Article featured image logic

### How It Works:

1. **Homepage/General Pages:**
   - Uses: `byline-gulf-social-share.jpg`
   - Fallback: `Logo.png`

2. **Single Posts/Articles:**
   - Uses: Featured image (if set)
   - Fallback: `byline-gulf-social-share.jpg`
   - Last fallback: `Logo.png`

3. **Meta Tags Generated:**
   - `og:image` - Main image URL
   - `og:image:width` - 1200
   - `og:image:height` - 630
   - `twitter:image` - Same image for Twitter
   - `og:image:type` - image/jpeg

---

## 📞 Need Help?

If you need assistance:
1. Check this guide first
2. Test with Facebook Debugger
3. Clear social media cache
4. Wait 24 hours for cache to expire naturally
5. Contact your WordPress administrator

---

## 💡 Pro Tips

1. **Always set featured images** on posts for best sharing experience
2. **Use high-quality images** (min 1200px wide)
3. **Keep text overlay minimal** on images
4. **Test before publishing** using Facebook Debugger
5. **Update og:image** when redesigning site
6. **Check mobile preview** - most shares happen on mobile
7. **Use branded images** to increase recognition

---

**Last Updated:** October 2025  
**Version:** 1.0  
**For:** Byline Gulf FZE  
**Website:** https://bylinegulf.com/


