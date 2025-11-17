# Author Display Control - Complete Guide

## Overview
You now have complete control over author display on single posts, both at the **post level** and **author profile level**.

---

## ✅ What's New

### 1. **Per-Post Author Control** (NEW!)
Each post now has an "Author Display Settings" metabox where you can:
- ✅ Show/Hide author box for that specific post
- ✅ See author preview (picture, name, title)
- ✅ Get warnings if author data is missing
- ✅ Quick link to edit author profile

### 2. **Author Profile Management**
When creating/editing users, you can set:
- ✅ Profile Picture (custom upload)
- ✅ Author Biography
- ✅ Author Title/Position
- ✅ Social Media Links

### 3. **Posts List Column** (NEW!)
The posts list now shows "Author Box" status:
- 🟢 **Visible** - Has picture & bio, will show
- 🟡 **Partial** - Has either picture or bio
- 🔴 **Hidden** - Manually hidden via checkbox
- 🔴 **No Data** - No picture or bio set

---

## 📝 How to Control Author Display

### Method 1: Per-Post Control (Recommended)

#### When Creating/Editing a Post:

1. **Look at the Right Sidebar**
   - Find "Author Display Settings" metabox

2. **Check Author Preview**
   - See current author's picture, name, title
   - Check for any warnings about missing data

3. **Hide Author Box (Optional)**
   - Check "Hide author box on this post" to hide
   - Uncheck to show (if author has data)

4. **Fix Author Issues**
   - Click "Edit Author Profile" link
   - Upload picture or add bio if needed

5. **Publish/Update**
   - Author box will show/hide based on your settings

#### Quick Reference:
```
✅ Author box SHOWS if:
- Checkbox is UNCHECKED (not hidden)
- Author has profile picture OR bio

❌ Author box HIDDEN if:
- Checkbox is CHECKED (manually hidden)
- Author has NO picture AND NO bio
```

### Method 2: Author Profile Setup

#### Setting Up Author Information:

1. **Go to Users**
   - Navigate to: Users → All Users (or Add New)
   - Click on a user to edit

2. **Find "Author Information" Section**
   - This appears when editing any user

3. **Upload Profile Picture** (Recommended)
   - Click "Upload Picture"
   - Select image from media library or upload
   - See live preview
   - Click "Change Picture" to update
   - Click "Remove Picture" to delete

4. **Add Biography** (Recommended)
   - Write author bio in the textarea
   - This displays on posts

5. **Add Author Title** (Optional)
   - E.g., "Senior Reporter", "News Editor"
   - Displays below author name

6. **Add Social Links** (Optional)
   - Scroll to "Contact Info" section
   - Add URLs for:
     - Facebook
     - Twitter/X
     - Instagram
     - LinkedIn
     - YouTube
     - WhatsApp (with country code: +971...)

7. **Save Changes**
   - Click "Update Profile" or "Add New User"

---

## 🎯 Common Scenarios

### Scenario 1: Author Box Not Showing
**Problem**: Changed author but box doesn't appear

**Solution**:
1. Edit the post
2. Check "Author Display Settings" metabox
3. Look for warnings:
   - ❌ "Hide author box" is checked → Uncheck it
   - ❌ "No profile picture" → Click "Edit Author Profile" → Upload picture
   - ❌ "No biography" → Click "Edit Author Profile" → Add bio
4. Update post

### Scenario 2: Hide Author on Specific Posts
**Problem**: Want to hide author on certain posts only

**Solution**:
1. Edit the post
2. Find "Author Display Settings" metabox
3. Check "Hide author box on this post"
4. Update post
5. Author info won't show on that post

### Scenario 3: Change Post Author
**Problem**: Changed author but old author still shows

**Solution**:
1. Make sure you saved the post after changing author
2. Check new author has profile picture or bio
3. Verify "Hide author box" is not checked
4. If still not working:
   - Edit new author's profile
   - Add picture and bio
   - Save profile
   - Refresh post

### Scenario 4: Bulk Author Setup
**Problem**: Need to set up multiple authors

**Solution**:
1. Go to Users → All Users
2. Click each user
3. For each user:
   - Upload profile picture
   - Add biography
   - Add title (optional)
   - Add social links (optional)
   - Click "Update Profile"
4. Posts will automatically show author boxes

---

## 🔍 Checking Author Status

### In Posts List:
- Look at "Author Box" column
- 🟢 **Visible** = Ready to display
- 🟡 **Partial** = Missing some data
- 🔴 **Hidden** = Won't show
- 🔴 **No Data** = Author needs setup

### In Post Editor:
- Check "Author Display Settings" metabox
- See author preview
- Read warning messages
- Use "Edit Author Profile" link

### In Author Profile:
- Check if picture is uploaded (required)
- Check if bio is filled (required)
- Verify title and social links (optional)

---

## 📊 Display Logic

### Author Box Shows When:
1. ✅ Post checkbox is NOT checked (not manually hidden)
2. ✅ Author has profile picture **OR** biography
3. ✅ At least one piece of information exists

### Author Box Hidden When:
1. ❌ "Hide author box" checkbox is checked
2. ❌ Author has NO picture AND NO bio
3. ❌ Invalid author ID

### What Displays:
- **Always**: Author name (linked to archive)
- **If Set**: Profile picture (circular, 120px)
- **If Set**: Author title/position
- **If Set**: Author biography
- **If Set**: Social media icons

---

## 🛠️ Admin Features

### For Posts:
- ✅ Per-post show/hide control
- ✅ Author preview in metabox
- ✅ Warning indicators
- ✅ Quick edit author profile link
- ✅ Status column in posts list

### For Authors:
- ✅ Custom picture upload (no Gravatar needed)
- ✅ Biography textarea
- ✅ Title/position field
- ✅ Social media links
- ✅ Live preview in backend
- ✅ All in one place

---

## 💡 Best Practices

### For Authors:
1. ✅ Always upload a profile picture (professional photo)
2. ✅ Write a brief, engaging bio (2-3 sentences)
3. ✅ Add job title for credibility
4. ✅ Link social media profiles (optional)
5. ✅ Keep information updated

### For Posts:
1. ✅ Assign correct author when creating
2. ✅ Check author display settings before publishing
3. ✅ Use hide option for guest posts or announcements
4. ✅ Review author preview in metabox
5. ✅ Update author info if warnings appear

### For Site Admins:
1. ✅ Set up all authors before launch
2. ✅ Create author profile guidelines
3. ✅ Regularly audit author information
4. ✅ Use posts list column to check status
5. ✅ Train content editors on these features

---

## 🐛 Troubleshooting

### Author Box Still Not Showing:
1. Clear browser cache
2. Clear WordPress cache (if using cache plugin)
3. Check post is published (not draft)
4. Verify author exists and is active
5. Check theme files are updated

### Picture Not Uploading:
1. Check file size (max 2MB recommended)
2. Use JPG or PNG format
3. Try different image
4. Check media upload permissions
5. Contact site admin if persists

### Changes Not Saving:
1. Check you clicked "Update" button
2. Verify you have edit permissions
3. Look for error messages
4. Try again in different browser
5. Contact administrator

---

## 📞 Support

If you encounter issues:
1. Check this guide first
2. Review warning messages in metabox
3. Verify author profile is complete
4. Clear caches and try again
5. Contact your WordPress administrator

---

## 🎉 Quick Start Checklist

**For New Authors:**
- [ ] Upload profile picture
- [ ] Write biography
- [ ] Add job title
- [ ] Add social links (optional)
- [ ] Save profile

**For New Posts:**
- [ ] Assign correct author
- [ ] Check "Author Display Settings"
- [ ] Verify author has picture/bio
- [ ] Decide show/hide
- [ ] Publish post

**For Existing Posts:**
- [ ] Edit post
- [ ] Check author display status
- [ ] Update if needed
- [ ] Save changes

---

**Last Updated**: Current Version
**Feature**: Complete Author Display Control System

