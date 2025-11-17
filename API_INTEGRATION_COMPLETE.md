# Pavilion Theme API Integration Complete

The pavilion-theme has been successfully integrated with the pavilion-gemini API backend.

## What Was Changed

### 1. Core Functions (`core.php`)
- **Replaced JSON-based data loading** with API calls to pavilion-gemini
- **Added API client functions**:
  - `pavilion_api_request()` - Makes HTTP requests to the API
  - `pavilion_get_articles()` - Fetches articles from API
  - `pavilion_get_article()` - Fetches single article by ID or slug
  - `pavilion_get_categories()` - Fetches categories from API
  - `pavilion_get_category_tree()` - Fetches category tree structure

- **Updated all WordPress-compatible functions** to use API:
  - `get_all_posts()` - Now uses API
  - `get_post()` - Now uses API
  - `get_category_by_slug()` - Now uses API
  - `get_category_link()` - Now uses API
  - `get_filtered_categories()` - Now uses API
  - `get_the_post_thumbnail_url()` - Handles API image URLs
  - `WP_Query` class - Updated to work with API

### 2. Backend API (`pavilion-gemini/backend/cms/views.py`)
- **Made read endpoints publicly accessible**:
  - `ArticleViewSet.list()` and `retrieve()` - Public read access
  - `CategoryViewSet.list()`, `retrieve()`, `tree()`, `children()` - Public read access
  - Write operations (create, update, delete) still require authentication

### 3. Features
- **API Response Caching**: 5-minute cache for API responses to improve performance
- **Error Handling**: Graceful fallbacks when API is unavailable
- **Image URL Handling**: Properly handles full URLs from API media endpoints
- **WordPress Compatibility**: All existing WordPress-style functions work the same way

## Configuration

### Update API URL

Edit `core.php` line 8:

```php
define('PAVILION_API_BASE_URL', 'http://localhost:8000/api');
```

Change to your actual pavilion-gemini API URL:
- Local: `http://localhost:8000/api`
- Production: `https://yourdomain.com/api`

### Cache Settings

You can adjust caching in `core.php`:

```php
define('PAVILION_CACHE_ENABLED', true);  // Enable/disable caching
define('PAVILION_CACHE_DURATION', 300);  // Cache duration in seconds (5 minutes)
```

## API Endpoints Used

The theme uses these endpoints:

1. **GET /api/articles/** - List published articles
   - Query params: `status`, `category`, `page_size`, `page`
   
2. **GET /api/articles/{id}/** - Get single article by ID

3. **GET /api/categories/tree/** - Get category tree structure

## Requirements

- PHP 7.4+ with cURL extension
- pavilion-gemini backend running and accessible
- Network connectivity between theme and API server

## Testing

1. Ensure pavilion-gemini backend is running
2. Update `PAVILION_API_BASE_URL` in `core.php`
3. Check PHP error logs for any API connection issues
4. Visit the theme homepage to see articles from API

## Notes

- The theme now fetches **only published articles** by default for public display
- Category filtering works the same as before
- All existing template files should work without changes
- Image URLs from API are automatically handled

## Troubleshooting

If you see no articles:

1. Check that pavilion-gemini backend is running
2. Verify API URL is correct in `core.php`
3. Check PHP error logs for API errors
4. Verify articles exist and are published in pavilion-gemini
5. Test API endpoint directly: `http://your-api-url/api/articles/?status=published`

