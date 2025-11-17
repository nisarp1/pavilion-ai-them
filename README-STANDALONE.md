# Pavilion Theme - Standalone PHP Version

This is a standalone PHP version of the Pavilion theme, converted from WordPress to work independently without WordPress dependencies.

## Features

- **No WordPress Required**: All WordPress functions have been replaced with custom PHP functions
- **JSON Data Storage**: All posts, categories, authors, and metadata are stored in `data.json`
- **Simple Routing**: Clean URL routing handled by `index.php`
- **Full Theme Functionality**: All original theme features preserved

## File Structure

- `core.php` - Core utility functions (replaces WordPress functions)
- `data.json` - Sample data (posts, categories, authors, galleries)
- `index.php` - Main router for clean URLs
- `home.php` - Homepage template
- `single-post.php` - Single post template
- `parts/shared/` - Shared template parts (header, footer, etc.)
- `assets/` - CSS, JS, images, fonts

## Setup

1. Ensure PHP 7.4+ is installed
2. Place theme files in your web server directory
3. Make sure `.htaccess` is properly configured (or configure URL rewriting for your server)
4. Access the site via your web browser

## Adding Content

Edit `data.json` to add:
- Posts (in the `posts` array)
- Categories (in the `categories` array)
- Authors (in the `authors` array)
- Galleries (in the `galleries` array)

### Post Format

```json
{
  "id": 1,
  "title": "Post Title",
  "slug": "post-slug",
  "excerpt": "Short description...",
  "content": "<p>Full content here...</p>",
  "date": "2025-01-20 10:30:00",
  "modified": "2025-01-20 10:30:00",
  "author": 1,
  "categories": [1, 2],
  "featured_image": "/assets/images/new/hero.jpg",
  "views": 1523,
  "shares": 89
}
```

## URL Structure

- `/` - Homepage
- `/post-slug/` - Single post (based on slug in data.json)
- `/category-slug/` - Category archive
- `/latest/` - Latest posts page
- `/contact/` - Contact page

## Important Notes

1. **Image Paths**: All image paths in `data.json` should start with `/` (e.g., `/assets/images/new/hero.jpg`)
2. **Category Links**: Categories are linked by slug (e.g., `/uae/`, `/gulf/`)
3. **Data Updates**: After modifying `data.json`, refresh your browser to see changes

## Removed WordPress Features

- WordPress database connections
- WordPress hooks and filters
- WordPress admin panel
- WordPress plugins
- WordPress user management

All WordPress functions have been replaced with equivalent standalone PHP functions that read from `data.json`.

## Development

To add new features:
1. Modify `core.php` to add new utility functions
2. Update templates to use new functions
3. Update `data.json` structure if needed

## Support

This is a standalone conversion. For original WordPress functionality, refer to the original WordPress theme files.

