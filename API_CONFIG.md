# Pavilion Theme API Configuration

The pavilion-theme now uses the pavilion-gemini API for all posts and categories data.

## Configuration

Edit `core.php` and update the API base URL:

```php
define('PAVILION_API_BASE_URL', 'http://localhost:8000/api');
```

Change this to match your pavilion-gemini API URL. For example:
- Local development: `http://localhost:8000/api`
- Production: `https://yourdomain.com/api`

## API Endpoints Used

The theme uses the following API endpoints:

1. **Articles List**: `GET /api/articles/?status=published&page_size=20&page=1`
2. **Single Article**: `GET /api/articles/{id}/`
3. **Categories Tree**: `GET /api/categories/tree/`

## Caching

API responses are cached for 5 minutes by default. You can adjust this in `core.php`:

```php
define('PAVILION_CACHE_DURATION', 300); // 5 minutes in seconds
```

To disable caching:

```php
define('PAVILION_CACHE_ENABLED', false);
```

## Requirements

- PHP 7.4+ with cURL extension enabled
- pavilion-gemini backend running and accessible
- Network access between theme and API server

## Testing

To test if the API connection is working, check the PHP error logs for any API errors.

You can also temporarily add debug output in `pavilion_api_request()` function in `core.php` to see API responses.

