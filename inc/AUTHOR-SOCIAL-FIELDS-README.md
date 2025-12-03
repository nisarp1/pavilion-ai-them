# Author Social Media Fields & Bio Feature

## Overview
This feature adds a complete author profile management system to WordPress, including custom profile picture upload, bio, social media links, and conditional display on single post pages.

## Features

### 1. Author Information Section
All author fields are now consolidated in the **"Author Information"** section when creating or editing users.

#### Available Fields:
- **Profile Picture** - Custom upload (not Gravatar)
  - Upload button with live preview
  - Circular display (150px in backend, 120px in frontend)
  - Remove button to clear picture
  - **REQUIRED** - Author box won't show on frontend without a picture
  
- **Author Biography** - Textarea for author bio
  - Custom field that replaces standard WordPress bio
  - Displayed on single post pages
  - **REQUIRED** - Must be filled to show author box
  
- **Author Title/Position** - Text field
  - E.g., "Senior Reporter", "News Editor", "Contributor"
  - Optional - displays below author name if set
  
- **Social Media URLs** (in Contact Info section):
  - Facebook URL
  - Twitter/X URL
  - Instagram URL
  - LinkedIn URL
  - YouTube URL
  - WhatsApp Number (with country code, e.g., +971501234567)

### 2. Custom Profile Picture System
- **Backend Only**: Profile pictures are uploaded and stored in WordPress
- **Media Library**: Uses WordPress media uploader
- **No Gravatar**: Completely independent from Gravatar system
- **Conditional Display**: Picture must be uploaded for author box to appear

## How to Use

### Creating a New Author:

#### Step 1: Add New User
1. Go to **Users → Add New** in WordPress Admin
2. Fill in the required user information (username, email, etc.)
3. Scroll down to **"Author Information"** section

#### Step 2: Upload Profile Picture (Required)
1. Click **"Upload Picture"** button
2. Choose an image from your computer or media library
3. Click **"Use This Picture"**
4. Preview will show immediately
5. **Important**: Without a picture, the author box won't display on posts

#### Step 3: Add Author Details
1. **Author Biography** - Write a bio (required for frontend display)
2. **Author Title/Position** - Add their job title (optional)
3. **Contact Info** - Scroll down and add social media URLs (optional)
   - Example: `https://facebook.com/username`
   - WhatsApp: Enter number with country code like `+971501234567`

#### Step 4: Create User
Click **"Add New User"** to save

### Editing Existing Author:

#### Step 1: Edit User
1. Go to **Users → All Users**
2. Click on the user you want to edit
3. Find the **"Author Information"** section

#### Step 2: Update Information
1. Upload/change profile picture
2. Update biography
3. Modify author title
4. Update social media links in Contact Info section

#### Step 3: Save
Click **"Update Profile"** to save changes

### Frontend Display Rules:

The author box will **ONLY** show on single post pages if:
1. ✅ Profile picture is uploaded (required)
2. ✅ Author biography is filled (required)
3. ✅ Author has written the post

If either condition is missing, the author box is automatically hidden.

## What's Displayed on Single Posts

When the author box is shown, it displays:
- **Author Avatar** - Circular profile picture
- **Author Name** - Linked to their author archive
- **Author Title** - If set (e.g., "Senior Reporter")
- **Author Bio** - From the biographical info field
- **Social Media Icons** - Only those with URLs filled in

## Social Media Icons Supported

The following platforms are supported with their respective icons:
- Facebook (Facebook icon)
- Twitter/X (X/Twitter icon)
- Instagram (Instagram icon)
- LinkedIn (LinkedIn icon)
- YouTube (YouTube icon)
- WhatsApp (WhatsApp icon)

## Technical Notes

### Files Modified/Created:
1. `/inc/author-social-fields.php` - Core functionality
2. `/functions.php` - Includes the author fields file
3. `/single-post.php` - Updated author display section
4. `/assets/css/style.css` - Author box styling

### Functions Available:
- `should_show_author_box($author_id)` - Check if author box should display
- `get_author_social_links($author_id)` - Get author's social media links

### Filters Used:
- `user_contactmethods` - Adds social media fields
- `show_user_profile` - Displays custom fields on profile
- `edit_user_profile` - Displays custom fields when editing users
- `personal_options_update` - Saves custom fields
- `edit_user_profile_update` - Saves custom fields

## Troubleshooting

### Author box not showing:
1. Check if "Show Author Box" is checked in user profile
2. Verify the user is not "admin" (unless explicitly enabled)
3. Ensure there's at least a biography filled in

### Social icons not appearing:
1. Make sure you've entered the FULL URL (including https://)
2. For WhatsApp, include country code with the number
3. Save the profile after making changes

### Avatar not showing:
1. Go to WordPress.com/Gravatar.com
2. Create an account with the same email as the WordPress user
3. Upload an image
4. Or use a WordPress plugin like "Simple Local Avatars"

## Future Enhancements

Possible additions:
- More social platforms (TikTok, Snapchat, etc.)
- Custom avatar upload (currently uses Gravatar)
- Author archive page customization
- Author widget for sidebar

