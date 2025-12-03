<?php
/**
 * Core utility functions for standalone PHP theme
 * Uses pavilion-gemini API for data access
 */

// Enable error reporting for debugging (disable in production)
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// API Configuration
define('PAVILION_API_BASE_URL', 'https://pavilion-ai-production.up.railway.app/api'); // Update this to your pavilion-gemini API URL
define('PAVILION_API_TIMEOUT', 10); // Timeout in seconds

// Cache configuration
define('PAVILION_CACHE_ENABLED', true);
define('PAVILION_CACHE_DURATION', 300); // 5 minutes

// API Cache storage
$pavilion_api_cache = array();

// Webstory cache to share between helper functions
$pavilion_webstory_cache = array();

// Make API request
function pavilion_api_request($endpoint, $params = array(), $method = 'GET')
{
    global $pavilion_api_cache;

    // Build cache key
    $cache_key = md5($endpoint . serialize($params) . $method);

    // Check cache
    if (PAVILION_CACHE_ENABLED && isset($pavilion_api_cache[$cache_key])) {
        $cached = $pavilion_api_cache[$cache_key];
        if (time() - $cached['time'] < PAVILION_CACHE_DURATION) {
            return $cached['data'];
        }
    }

    // Build URL
    $url = rtrim(PAVILION_API_BASE_URL, '/') . '/' . ltrim($endpoint, '/');

    if ($method === 'GET' && !empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, PAVILION_API_TIMEOUT);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log("Pavilion API Error: " . $error . " (URL: " . $url . ")");
        // Return empty array instead of null to prevent errors
        return array();
    }

    if ($http_code >= 200 && $http_code < 300) {
        $data = json_decode($response, true);

        // Handle JSON decode errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Pavilion API JSON Error: " . json_last_error_msg() . " (Response: " . substr($response, 0, 200) . ")");
            return array();
        }

        // Cache the response
        if (PAVILION_CACHE_ENABLED && $data !== null) {
            $pavilion_api_cache[$cache_key] = array(
                'data' => $data,
                'time' => time()
            );
        }

        return $data ? $data : array();
    }

    error_log("Pavilion API HTTP Error: " . $http_code . " (URL: " . $url . ")");
    // Return empty array instead of null to prevent errors
    return array();
}

// Fetch single media item from API (with simple caching)
function pavilion_get_media_item($media_id)
{
    static $media_cache = array();

    if (empty($media_id)) {
        return null;
    }

    $cache_key = (string) $media_id;

    if (array_key_exists($cache_key, $media_cache)) {
        return $media_cache[$cache_key];
    }

    // If media_id already looks like a full/relative URL, return it directly
    if (is_string($media_id) && (strpos($media_id, 'http') === 0 || strpos($media_id, '/') === 0)) {
        $media_cache[$cache_key] = array('url' => $media_id);
        return $media_cache[$cache_key];
    }

    // Attempt to fetch from media endpoint
    $response = pavilion_api_request('media/' . $media_id . '/');

    if (is_array($response) && !empty($response)) {
        $media_cache[$cache_key] = $response;
        return $response;
    }

    // Cache miss result to avoid refetching
    $media_cache[$cache_key] = null;
    return null;
}

function pavilion_get_media_url($media_id)
{
    $media_item = pavilion_get_media_item($media_id);

    if (!$media_item) {
        return '';
    }

    if (!empty($media_item['url'])) {
        return $media_item['url'];
    }

    if (!empty($media_item['file'])) {
        $file = $media_item['file'];
        if (strpos($file, 'http') === 0) {
            return $file;
        }
        $api_base = rtrim(PAVILION_API_BASE_URL, '/api');
        return $api_base . $file;
    }

    return '';
}

// Get all articles from API
function pavilion_get_articles($params = array())
{
    $query_params = array();

    // Map WordPress-style params to API params
    // Always default to published for public site
    $query_params['status'] = isset($params['status']) ? $params['status'] : 'published';

    // Check for category_name and convert to category slug for API
    if (isset($params['category_name'])) {
        $query_params['category'] = $params['category_name'];
        error_log("Pavilion API: Filtering by category_name: " . $params['category_name']);
    } elseif (isset($params['category'])) {
        $query_params['category'] = $params['category'];
        error_log("Pavilion API: Filtering by category: " . $params['category']);
    }

    if (isset($params['posts_per_page'])) {
        $query_params['page_size'] = $params['posts_per_page'];
    }

    if (isset($params['paged']) && $params['paged'] > 1) {
        $query_params['page'] = $params['paged'];
    }

    error_log("Pavilion API: Requesting articles with params: " . json_encode($query_params));
    $response = pavilion_api_request('articles/', $query_params);
    error_log("Pavilion API: Received " . (is_array($response) ? count($response) : 0) . " articles");

    // Handle API response
    if (is_array($response)) {
        if (isset($response['results'])) {
            return $response['results'];
        } elseif (isset($response['count']) && isset($response['results'])) {
            // Paginated response
            return $response['results'];
        } else {
            // Direct array of articles
            return $response;
        }
    }

    // Return empty array on error
    error_log("Pavilion API: No articles returned from API");
    return array();
}

// Get published web stories from API
function pavilion_get_webstories($params = array())
{
    $query_params = array();
    $endpoint = 'webstories/';

    // Support lightweight "latest" helper endpoint with built-in 24h filtering
    if (!empty($params['latest'])) {
        $endpoint = 'webstories/latest/';
    } else {
        if (!empty($params['published_after'])) {
            $query_params['published_after'] = $params['published_after'];
        }
        $query_params['status'] = isset($params['status']) ? $params['status'] : 'published';
    }

    if (!empty($params['limit'])) {
        $query_params['limit'] = (int) $params['limit'];
    } elseif (!empty($params['page_size'])) {
        $query_params['page_size'] = (int) $params['page_size'];
    }

    if (!empty($params['hours'])) {
        $query_params['hours'] = (int) $params['hours'];
    }

    if (!empty($params['include_slides'])) {
        $query_params['include_slides'] = $params['include_slides'] ? 'true' : 'false';
    }

    $response = pavilion_api_request($endpoint, $query_params);

    if (isset($response['results']) && is_array($response['results'])) {
        return $response['results'];
    }

    return is_array($response) ? $response : array();
}

function pavilion_convert_articles_to_posts($articles)
{
    $posts = array();

    if (!is_array($articles)) {
        return $posts;
    }

    foreach ($articles as $article) {
        if (!is_array($article) || !isset($article['id'])) {
            continue;
        }

        $post = array(
            'id' => $article['id'],
            'title' => $article['title'] ?? '',
            'slug' => $article['slug'] ?? '',
            'content' => $article['body'] ?? '',
            'excerpt' => $article['summary'] ?? '',
            'date' => isset($article['published_at']) ? $article['published_at'] : ($article['created_at'] ?? ''),
            'modified' => $article['updated_at'] ?? ($article['published_at'] ?? ''),
            'status' => $article['status'] ?? 'draft',
            'featured_image' => $article['featured_image_url'] ?? '',
            'categories' => array(),
            'author' => $article['author_name'] ?? 'admin',
            'views' => $article['views'] ?? 0,
            'shares' => $article['shares'] ?? 0,
        );

        if (isset($article['categories']) && is_array($article['categories'])) {
            foreach ($article['categories'] as $cat) {
                $cat_id = null;
                if (is_array($cat)) {
                    $cat_id = $cat['id'] ?? null;
                } elseif (is_object($cat)) {
                    $cat_id = $cat->id ?? null;
                }

                if ($cat_id) {
                    $post['categories'][] = $cat_id;
                }
            }
        }

        $posts[] = $post;
    }

    return $posts;
}

// Get single article by ID or slug
function pavilion_get_article($id_or_slug)
{
    if (is_numeric($id_or_slug)) {
        // Get by ID
        $response = pavilion_api_request("articles/{$id_or_slug}/");
    } else {
        // Get by slug - need to fetch all and filter
        $articles = pavilion_get_articles(array('status' => 'published'));
        foreach ($articles as $article) {
            if (isset($article['slug']) && $article['slug'] === $id_or_slug) {
                return $article;
            }
        }
        return null;
    }

    return $response;
}

// Get all categories from API
function pavilion_get_categories()
{
    $response = pavilion_api_request('categories/tree/');

    if ($response && is_array($response)) {
        // Flatten the tree structure for compatibility
        $flat_categories = array();
        foreach ($response as $parent) {
            $flat_categories[] = array(
                'id' => $parent['id'],
                'name' => $parent['name'],
                'slug' => $parent['slug'],
                'parent' => null,
                'description' => isset($parent['description']) ? $parent['description'] : ''
            );

            if (isset($parent['children']) && is_array($parent['children'])) {
                foreach ($parent['children'] as $child) {
                    $flat_categories[] = array(
                        'id' => $child['id'],
                        'name' => $child['name'],
                        'slug' => $child['slug'],
                        'parent' => $parent['id'],
                        'description' => isset($child['description']) ? $child['description'] : ''
                    );
                }
            }
        }
        return $flat_categories;
    }

    return array();
}

// Get category tree (for nested structures)
function pavilion_get_category_tree()
{
    static $category_tree_cache = null;

    // Cache the tree for the request
    if ($category_tree_cache === null) {
        $category_tree_cache = pavilion_api_request('categories/tree/') ?: array();
    }

    return $category_tree_cache;
}

// Legacy function for backward compatibility
function load_json_data()
{
    // Return empty array - we're using API now
    return array();
}

// Get site data
function get_site_data()
{
    $data = load_json_data();
    return isset($data['site']) ? $data['site'] : array();
}

// Get all posts
function get_all_posts($filters = array())
{
    $articles = pavilion_get_articles($filters);

    // Ensure we have an array
    if (!is_array($articles)) {
        return array();
    }

    // Convert API response to WordPress-compatible format
    $posts = pavilion_convert_articles_to_posts($articles);

    // Apply client-side filtering for category (in case API didn't filter or returned all posts)
    if (isset($filters['category_name'])) {
        $category_slug = $filters['category_name'];
        error_log("Applying client-side filter for category: " . $category_slug . " (Posts before filter: " . count($posts) . ")");

        $category = get_category_by_slug($category_slug);
        $category_id = null;

        if ($category) {
            $category_id = is_array($category) ? $category['id'] : (isset($category->id) ? $category->id : (isset($category->term_id) ? $category->term_id : null));
        }

        // Fallback to API categories if WordPress category lookup failed
        if (!$category_id) {
            $api_categories = pavilion_get_categories();
            foreach ($api_categories as $api_category) {
                if (!isset($api_category['slug']) || $api_category['slug'] !== $category_slug) {
                    continue;
                }
                $category = $api_category;
                $category_id = isset($api_category['id']) ? $api_category['id'] : null;
                break;
            }
        }

        if ($category_id) {
            error_log("Applying category filter using ID {$category_id} for slug {$category_slug}");

            // Filter posts by category ID
            $filtered_posts = array();
            foreach ($posts as $post) {
                $post_categories = isset($post['categories']) ? $post['categories'] : array();

                // Debug: log first post's categories
                if (count($filtered_posts) == 0 && count($posts) > 0) {
                    error_log("Sample post categories structure: " . json_encode($post_categories) . " (Looking for category ID: " . $category_id . ")");
                }

                // Check if post has this category ID
                // Handle different formats: ID directly, array with 'id', or object
                $has_category = false;
                foreach ($post_categories as $post_cat) {
                    $post_cat_id = null;

                    if (is_numeric($post_cat)) {
                        // It's just an ID number
                        $post_cat_id = $post_cat;
                    } elseif (is_array($post_cat)) {
                        // It's an array - could be {'id': X} or full category object
                        $post_cat_id = isset($post_cat['id']) ? $post_cat['id'] : null;
                    } elseif (is_object($post_cat)) {
                        // It's an object
                        $post_cat_id = isset($post_cat->id) ? $post_cat->id : (isset($post_cat->term_id) ? $post_cat->term_id : null);
                    }

                    // Match by ID
                    if ($post_cat_id && (int) $post_cat_id === (int) $category_id) {
                        $has_category = true;
                        break;
                    }
                }

                if ($has_category) {
                    $filtered_posts[] = $post;
                }
            }
            $posts = $filtered_posts;
            error_log("Category filter applied: " . $category_slug . " (ID: " . $category_id . ") - Found " . count($posts) . " posts after filtering");
        } else {
            // Category not found, return empty array
            error_log("Category not found by slug (WP + API): " . $category_slug);
            $posts = array();
        }
    }

    if (isset($filters['post__not_in'])) {
        $exclude_ids = $filters['post__not_in'];
        if (is_array($exclude_ids)) {
            $posts = array_filter($posts, function ($post) use ($exclude_ids) {
                // Handle both array and object formats
                $post_id = is_array($post) ? (isset($post['id']) ? $post['id'] : null) : (isset($post->id) ? $post->id : null);
                return $post_id && !in_array($post_id, $exclude_ids);
            });
        }
    }

    return array_values($posts);
}

// Get single post by ID or slug
function get_post($id_or_slug = null)
{
    if ($id_or_slug === null) {
        // Try to get from URL or global
        $id_or_slug = get_current_post_id();
    }

    if (!$id_or_slug) {
        return null;
    }

    $article = pavilion_get_article($id_or_slug);

    if (!$article) {
        return null;
    }

    // Convert to WordPress-compatible format
    $post = array(
        'id' => $article['id'],
        'title' => $article['title'],
        'slug' => $article['slug'],
        'content' => isset($article['body']) ? $article['body'] : '',
        'excerpt' => isset($article['summary']) ? $article['summary'] : '',
        'date' => isset($article['published_at']) ? $article['published_at'] : $article['created_at'],
        'modified' => $article['updated_at'],
        'status' => $article['status'],
        'featured_image' => isset($article['featured_image_url']) ? $article['featured_image_url'] : '',
        'categories' => array(),
        'author' => isset($article['author_name']) ? $article['author_name'] : 'admin',
        'views' => 0,
        'shares' => 0
    );

    // Map categories - handle both array and object formats from API
    if (isset($article['categories']) && is_array($article['categories'])) {
        foreach ($article['categories'] as $cat) {
            // Handle both array and object formats
            $cat_id = is_array($cat) ? (isset($cat['id']) ? $cat['id'] : null) : (isset($cat->id) ? $cat->id : null);
            if ($cat_id) {
                $post['categories'][] = $cat_id;
            }
        }
    }

    return $post;
}

// Get current post ID from URL
function get_current_post_id()
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = trim($path, '/');

    if (empty($path) || $path === 'index.php') {
        return null;
    }

    // Extract slug from path
    $slug = basename($path);

    // Try to get article by slug
    $article = pavilion_get_article($slug);
    if ($article && isset($article['id'])) {
        return $article['id'];
    }

    return null;
}

// Get current post
function get_current_post()
{
    $id = get_current_post_id();
    if ($id) {
        return get_post($id);
    }
    return null;
}

// Get category by slug
function get_category_by_slug($slug)
{
    $categories = pavilion_get_categories();

    foreach ($categories as $category) {
        // Handle both array and object formats
        $cat_slug = is_array($category) ? (isset($category['slug']) ? $category['slug'] : null) : (isset($category->slug) ? $category->slug : null);

        if ($cat_slug === $slug) {
            // Convert to object and add term_id for backward compatibility
            if (is_array($category)) {
                $cat_obj = (object) $category;
                if (!isset($cat_obj->term_id)) {
                    $cat_obj->term_id = isset($category['id']) ? $category['id'] : null;
                }
                if (!isset($cat_obj->id)) {
                    $cat_obj->id = isset($category['id']) ? $category['id'] : null;
                }
            } else {
                $cat_obj = $category;
                if (!isset($cat_obj->term_id) && isset($cat_obj->id)) {
                    $cat_obj->term_id = $cat_obj->id;
                }
            }
            return $cat_obj;
        }
    }

    error_log("Category not found by slug: " . $slug);
    return null;
}

// Get category link
function get_category_link($category_id)
{
    // Get theme base path (includes /pavilion-theme/)
    $base_path = rtrim(get_theme_base_path(), '/');

    $categories = pavilion_get_categories();

    foreach ($categories as $category) {
        // Handle both array and object formats
        $cat_id = is_array($category) ? (isset($category['id']) ? $category['id'] : null) : (isset($category->id) ? $category->id : null);
        if ($cat_id == $category_id) {
            $cat_slug = is_array($category) ? (isset($category['slug']) ? $category['slug'] : '') : (isset($category->slug) ? $category->slug : '');
            if ($cat_slug) {
                return $base_path . '/' . $cat_slug . '/';
            }
        }
    }

    return '#';
}

// Get safe category link
function get_safe_category_link($slug)
{
    $category = get_category_by_slug($slug);
    if ($category) {
        // Handle both array and object formats
        $category_id = is_array($category) ? (isset($category['id']) ? $category['id'] : null) : (isset($category->id) ? $category->id : (isset($category->term_id) ? $category->term_id : null));
        if ($category_id) {
            return get_category_link($category_id);
        }
    }
    return '#';
}

// Get query variable from URL (WordPress compatibility)
function get_query_var($var, $default = '')
{
    // Check $_GET first
    if (isset($_GET[$var])) {
        return htmlspecialchars(strip_tags(trim($_GET[$var])), ENT_QUOTES, 'UTF-8');
    }

    // Check $_REQUEST as fallback
    if (isset($_REQUEST[$var])) {
        return htmlspecialchars(strip_tags(trim($_REQUEST[$var])), ENT_QUOTES, 'UTF-8');
    }

    // Return default if not found
    return $default;
}

// Get filtered categories for a post
function get_filtered_categories($post_id = null)
{
    global $post;

    if ($post_id === null) {
        // Try to use global post first
        $current_post = null;
        if (is_array($post)) {
            $current_post = $post;
        } elseif (is_object($post)) {
            $current_post = (array) $post;
        }

        if (!$current_post) {
            // Fallback to current post from URL
            $post = get_current_post();
            $current_post = $post;
        }
        $post = $current_post;
    } else {
        $post = get_post($post_id);
    }

    if (!$post || !isset($post['categories'])) {
        return array();
    }

    $all_categories = pavilion_get_categories();

    $post_categories = array();
    foreach ($all_categories as $category) {
        // Handle both array and object formats
        $cat_id = is_array($category) ? (isset($category['id']) ? $category['id'] : null) : (isset($category->id) ? $category->id : null);
        if ($cat_id && in_array($cat_id, $post['categories'])) {
            // Convert to object for compatibility with WordPress-style code
            if (is_array($category)) {
                $cat_obj = (object) $category;
            } else {
                $cat_obj = $category;
            }
            // Add term_id for backward compatibility (WordPress uses term_id)
            if (!isset($cat_obj->term_id)) {
                $cat_obj->term_id = $cat_id;
            }
            $post_categories[] = $cat_obj;
        }
    }

    return $post_categories;
}

// Get post categories (formatted)
function get_post_categories($post_id = null)
{
    return get_filtered_categories($post_id);
}

// Get post title
function get_the_title($post_id = null)
{
    global $post;

    if ($post_id === null) {
        // Try to use global post first
        if (is_array($post) && isset($post['title'])) {
            return $post['title'];
        } elseif (is_object($post) && isset($post->title)) {
            return $post->title;
        }
        // Fallback to current post
        $post = get_current_post();
    } else {
        $post = get_post($post_id);
    }

    return $post ? $post['title'] : '';
}

// Get post excerpt
function get_the_excerpt($post_id = null)
{
    global $post;

    if ($post_id === null) {
        // Try to use global post first
        if (is_array($post) && isset($post['excerpt'])) {
            return $post['excerpt'];
        } elseif (is_object($post) && isset($post->excerpt)) {
            return $post->excerpt;
        }
        // Fallback to current post
        $post = get_current_post();
    } else {
        $post = get_post($post_id);
    }

    return $post ? $post['excerpt'] : '';
}

// Get post content
function get_the_content($post_id = null)
{
    global $post;

    if ($post_id === null) {
        // Try to use global post first
        if (is_array($post) && isset($post['content'])) {
            return $post['content'];
        } elseif (is_object($post) && isset($post->content)) {
            return $post->content;
        }
        // Fallback to current post
        $post = get_current_post();
    } else {
        $post = get_post($post_id);
    }

    return $post ? $post['content'] : '';
}

// Get post permalink
function get_permalink($post_id = null)
{
    global $post;

    // Get theme base path (includes /pavilion-theme/)
    $base_path = rtrim(get_theme_base_path(), '/');

    if ($post_id === null) {
        // Try to use global post first
        if (is_array($post) && isset($post['slug'])) {
            return $base_path . '/' . $post['slug'] . '/';
        } elseif (is_object($post) && isset($post->slug)) {
            return $base_path . '/' . $post->slug . '/';
        }
        // Fallback to current post
        $post = get_current_post();
    } else {
        $post = get_post($post_id);
    }

    if (!$post) {
        return '#';
    }

    $slug = is_array($post) ? (isset($post['slug']) ? $post['slug'] : '') : (isset($post->slug) ? $post->slug : '');
    if (!$slug) {
        return '#';
    }

    return $base_path . '/' . $slug . '/';
}

// Get post date
function get_the_date($format = 'F j, Y', $post_id = null)
{
    global $post;

    if ($post_id === null) {
        // Try to use global post first
        if (is_array($post) && isset($post['date'])) {
            $timestamp = strtotime($post['date']);
            return date($format, $timestamp);
        } elseif (is_object($post) && isset($post->date)) {
            $timestamp = strtotime($post->date);
            return date($format, $timestamp);
        }
        // Fallback to current post
        $post = get_current_post();
    } else {
        $post = get_post($post_id);
    }

    if (!$post) {
        return '';
    }

    $timestamp = strtotime($post['date']);
    return date($format, $timestamp);
}

// Get modified date
function get_the_modified_date($format = 'c', $post_id = null)
{
    if ($post_id === null) {
        $post = get_current_post();
    } else {
        $post = get_post($post_id);
    }

    if (!$post || !isset($post['modified'])) {
        return '';
    }

    $timestamp = strtotime($post['modified']);
    return date($format, $timestamp);
}

// Check if post has thumbnail
function has_post_thumbnail($post_id = null)
{
    global $post;

    if ($post_id === null) {
        // Try to use global post first
        if (is_array($post) && isset($post['featured_image'])) {
            return !empty($post['featured_image']);
        } elseif (is_object($post) && isset($post->featured_image)) {
            return !empty($post->featured_image);
        }
        // Fallback to current post
        $post = get_current_post();
    } else {
        $post = get_post($post_id);
    }

    return $post && !empty($post['featured_image']);
}

// Get post thumbnail URL
function get_the_post_thumbnail_url($post_id = null, $size = 'large')
{
    global $post;

    if ($post_id === null) {
        // Try to use global post first
        $current_post = null;
        if (is_array($post) && isset($post['featured_image'])) {
            $current_post = $post;
        } elseif (is_object($post) && isset($post->featured_image)) {
            $current_post = (array) $post;
        }

        if ($current_post) {
            $image_url = $current_post['featured_image'];
        } else {
            // Fallback to current post
            $post = get_current_post();
            $image_url = $post && isset($post['featured_image']) ? $post['featured_image'] : '';
        }
    } else {
        $post = get_post($post_id);
        $image_url = $post && isset($post['featured_image']) ? $post['featured_image'] : '';
    }

    if (empty($image_url)) {
        return get_stylesheet_directory_uri() . 'assets/images/pavilion.jpg';
    }

    // If it's already a full URL, return as is
    if (strpos($image_url, 'http') === 0) {
        return $image_url;
    }

    // If it starts with /media/, it's from the API, prepend API base URL
    if (strpos($image_url, '/media/') === 0) {
        $api_base = rtrim(PAVILION_API_BASE_URL, '/api');
        return $api_base . $image_url;
    }

    // Otherwise, treat as relative path
    return get_stylesheet_directory_uri() . ltrim($image_url, '/');
}

// Get post thumbnail HTML
function get_the_post_thumbnail($post_id = null, $size = 'large', $attr = array())
{
    $url = get_the_post_thumbnail_url($post_id, $size);
    $title = get_the_title($post_id);
    $alt = isset($attr['alt']) ? $attr['alt'] : $title;
    $class = isset($attr['class']) ? $attr['class'] : '';

    return '<img src="' . esc_url($url) . '" alt="' . esc_attr($alt) . '" class="' . esc_attr($class) . '">';
}

// Output post thumbnail
function the_post_thumbnail($size = 'large', $attr = array())
{
    echo get_the_post_thumbnail(null, $size, $attr);
}

// Get post views
function get_post_views($post_id)
{
    $post = get_post($post_id);
    return $post && isset($post['views']) ? $post['views'] : 0;
}

// Set post views (placeholder)
function set_post_views($post_id)
{
    // In a real implementation, this would update the JSON file
    // For now, just return
    return true;
}

// Get share count
function get_share_count($post_id = null)
{
    if ($post_id === null) {
        $post = get_current_post();
    } else {
        $post = get_post($post_id);
    }

    return $post && isset($post['shares']) ? $post['shares'] : 0;
}

// Time ago function
function meks_time_ago($post_id = null)
{
    global $post;

    // Get timestamp from various sources
    if ($post_id === null) {
        // Try to use global post if set up
        if (is_array($post) && isset($post['date'])) {
            $timestamp = strtotime($post['date']);
        } elseif (is_object($post) && isset($post->post_date)) {
            $timestamp = strtotime($post->post_date);
        } else {
            // Try to get current post
            $current_post = get_current_post();
            $timestamp = $current_post ? strtotime($current_post['date']) : time();
        }
    } elseif (is_numeric($post_id)) {
        // Post ID provided
        $post = get_post($post_id);
        $timestamp = $post && isset($post['date']) ? strtotime($post['date']) : time();
    } elseif (is_string($post_id)) {
        // Date string provided
        $timestamp = strtotime($post_id);
    } else {
        $timestamp = time();
    }

    $diff = time() - $timestamp;

    if ($diff < 60) {
        return $diff . ' sec ago';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' min ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

// WordPress trim words
function wp_trim_words($text, $num_words = 55, $more = null)
{
    if (null === $more) {
        $more = '&hellip;';
    }

    $text = strip_tags($text);
    $words = explode(' ', $text);

    if (count($words) > $num_words) {
        $words = array_slice($words, 0, $num_words);
        $text = implode(' ', $words) . $more;
    }

    return $text;
}

// Home URL
function home_url($path = '')
{
    // Get theme base path (includes /pavilion-theme/)
    $base_path = rtrim(get_theme_base_path(), '/');

    if (empty($path)) {
        return $base_path . '/';
    }

    return $base_path . '/' . ltrim($path, '/');
}

// Get theme base path (detects if theme is in subdirectory)
function get_theme_base_path()
{
    static $theme_path = null;

    if ($theme_path === null) {
        // Calculate relative path from document root to current script directory
        // This script (core.php) is in the root of the theme
        $script_dir = str_replace('\\', '/', __DIR__);
        $doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);

        // Remove document root from script directory to get relative path
        $relative_path = str_replace($doc_root, '', $script_dir);

        // Ensure it starts with / and doesn't end with /
        $theme_path = '/' . trim($relative_path, '/');

        // If we are at root, theme_path should be empty string (for concatenation)
        if ($theme_path === '/') {
            $theme_path = '';
        }
    }

    return $theme_path;
}

// Get full URL (with domain) from a relative path
function get_full_url($path = '')
{
    // Get protocol (http or https)
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';

    // Get host
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost:8888';

    // If path is empty, use current request URI
    if (empty($path)) {
        $path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    }

    // Ensure path starts with /
    if (!empty($path) && $path[0] !== '/') {
        $path = '/' . $path;
    }

    return $protocol . '://' . $host . $path;
}

// Get stylesheet directory URI
function get_stylesheet_directory_uri()
{
    $theme_path = get_theme_base_path();
    return $theme_path . '/';
}

// Get template directory
function get_template_directory()
{
    return __DIR__;
}

// Blog info
function get_bloginfo($show = '')
{
    $site_data = get_site_data();

    switch ($show) {
        case 'name':
            return isset($site_data['name']) ? $site_data['name'] : 'Pavilion End';
        case 'description':
            return isset($site_data['description']) ? $site_data['description'] : '';
        default:
            return '';
    }
}

function bloginfo($show = '')
{
    echo esc_html(get_bloginfo($show));
}

// Escaping functions
function esc_url($url)
{
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}

function esc_attr($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_html($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_url_raw($url)
{
    return $url;
}

// KSES functions
function wp_kses_post($data)
{
    return strip_tags($data, '<p><a><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6><br><img><blockquote><pre><code>');
}

// Title attribute
function get_the_title_attribute($post_id = null)
{
    return esc_attr(get_the_title($post_id));
}

function the_title_attribute($post_id = null)
{
    echo get_the_title_attribute($post_id);
}

// Search query
function get_search_query()
{
    return isset($_GET['s']) ? $_GET['s'] : '';
}

// Check if single post
function is_single()
{
    $post = get_current_post();
    return $post !== null;
}

// Check if page
function is_page()
{
    // For now, treat single posts as pages
    return is_single();
}

// Check if home/front page
function is_home()
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = trim($path, '/');
    return empty($path) || $path === 'index.php' || $path === 'home.php';
}

function is_front_page()
{
    return is_home();
}

// Current time
function current_time($type = 'mysql')
{
    return date('Y-m-d H:i:s');
}

// Get author
function get_the_author($post_id = null)
{
    if ($post_id === null) {
        $post = get_current_post();
    } else {
        $post = get_post($post_id);
    }

    if (!$post) {
        return '';
    }

    // Author name is already in the post data from API
    return isset($post['author']) ? $post['author'] : 'admin';
}

function the_author($post_id = null)
{
    echo esc_html(get_the_author($post_id));
}

// Get author meta
function get_the_author_meta($field = '', $user_id = null)
{
    if ($user_id === null) {
        $post = get_current_post();
        if ($post) {
            $user_id = $post['author'];
        } else {
            return '';
        }
    }

    $data = load_json_data();
    $authors = isset($data['authors']) ? $data['authors'] : array();

    foreach ($authors as $author) {
        if ($author['id'] == $user_id) {
            switch ($field) {
                case 'display_name':
                case 'name':
                    return $author['display_name'];
                case 'description':
                case 'bio':
                    return isset($author['bio']) ? $author['bio'] : '';
                case 'ID':
                    return $author['id'];
                default:
                    return isset($author[$field]) ? $author[$field] : '';
            }
        }
    }

    return '';
}

// Author posts URL
function get_author_posts_url($author_id)
{
    return '/author/' . $author_id . '/';
}

// Get recommended posts
function get_recommended_posts($limit = 6, $exclude = array())
{
    $posts = get_all_posts(array(
        'posts_per_page' => $limit + count($exclude),
        'post__not_in' => $exclude
    ));

    // Shuffle for variety
    shuffle($posts);

    return array_slice($posts, 0, $limit);
}

// Get video play button HTML
function get_video_play_button($post_id = null)
{
    $categories = get_filtered_categories($post_id);

    if (!empty($categories)) {
        foreach ($categories as $category) {
            $slug = is_object($category) ? ($category->slug ?? '') : (isset($category['slug']) ? $category['slug'] : '');

            if ($slug === 'video') {
                return '<div class="video-popup video-play-btn"></div>';
            }
        }
    }

    return '';
}

// Should show author box
function should_show_author_box($post_id = null, $fallback_id = null)
{
    $post = $post_id ? get_post($post_id) : get_current_post();
    return $post !== null;
}

// Get author social links
function get_author_social_links($author_id)
{
    $data = load_json_data();
    $authors = isset($data['authors']) ? $data['authors'] : array();

    foreach ($authors as $author) {
        if ($author['id'] == $author_id && isset($author['social'])) {
            $social = array();
            if (isset($author['social']['twitter'])) {
                $social['twitter'] = array('url' => $author['social']['twitter'], 'icon' => 'fab fa-twitter', 'label' => 'Twitter');
            }
            if (isset($author['social']['facebook'])) {
                $social['facebook'] = array('url' => $author['social']['facebook'], 'icon' => 'fab fa-facebook-f', 'label' => 'Facebook');
            }
            if (isset($author['social']['instagram'])) {
                $social['instagram'] = array('url' => $author['social']['instagram'], 'icon' => 'fab fa-instagram', 'label' => 'Instagram');
            }
            if (isset($author['social']['linkedin'])) {
                $social['linkedin'] = array('url' => $author['social']['linkedin'], 'icon' => 'fab fa-linkedin', 'label' => 'LinkedIn');
            }
            return $social;
        }
    }

    return array();
}

// Get author profile picture
function get_author_profile_picture($author_id, $size = 'medium')
{
    $data = load_json_data();
    $authors = isset($data['authors']) ? $data['authors'] : array();

    foreach ($authors as $author) {
        if ($author['id'] == $author_id && isset($author['avatar'])) {
            return get_stylesheet_directory_uri() . $author['avatar'];
        }
    }

    return '';
}

// Get user meta (placeholder)
function get_user_meta($user_id, $key, $single = true)
{
    if ($key === 'author_title') {
        $data = load_json_data();
        $authors = isset($data['authors']) ? $data['authors'] : array();
        foreach ($authors as $author) {
            if ($author['id'] == $user_id && isset($author['title'])) {
                return $author['title'];
            }
        }
    }
    return '';
}

// Get gallery posts with flexible filtering
function get_gallery_posts($limit = 3, $options = array())
{
    global $pavilion_webstory_cache;

    $limit = (int) $limit;
    $defaults = array(
        'latest' => true,
        'hours' => 24,
        'include_slides' => true,
        'status' => 'published',
        'published_after' => null,
        'limit' => $limit > 0 ? $limit : 3,
    );

    $options = is_array($options) ? array_merge($defaults, $options) : $defaults;

    $request_params = array(
        'limit' => (int) ($options['limit'] ?? $limit ?? 3),
        'include_slides' => !empty($options['include_slides']),
    );

    $latest = array_key_exists('latest', $options) ? (bool) $options['latest'] : true;

    if ($latest) {
        $request_params['latest'] = true;
        if (array_key_exists('hours', $options)) {
            if ($options['hours'] !== null) {
                $request_params['hours'] = (int) $options['hours'];
            }
        } else {
            $request_params['hours'] = 24;
        }
    } else {
        if (!empty($options['published_after'])) {
            $request_params['published_after'] = $options['published_after'];
        }
        $request_params['status'] = !empty($options['status']) ? $options['status'] : 'published';
    }

    $stories = pavilion_get_webstories($request_params);

    if (!is_array($stories) || empty($stories)) {
        return array();
    }

    $transformed = array();
    foreach ($stories as $story) {
        $normalized = pavilion_transform_webstory($story);
        if (empty($normalized)) {
            continue;
        }

        $transformed[] = $normalized;
        $story_id = isset($normalized['id']) ? $normalized['id'] : null;
        if ($story_id) {
            $pavilion_webstory_cache[$story_id] = $normalized;
        }
    }

    return array_slice($transformed, 0, $limit);
}

// Get gallery images
function get_gallery_images($post_id = null)
{
    global $pavilion_webstory_cache;

    if ($post_id === null) {
        $post = get_current_post();
        $post_id = isset($post['id']) ? $post['id'] : null;
    }

    if (!$post_id) {
        return array();
    }

    if (isset($pavilion_webstory_cache[$post_id])) {
        return isset($pavilion_webstory_cache[$post_id]['images'])
            ? $pavilion_webstory_cache[$post_id]['images']
            : array();
    }

    // Fetch single story if not cached
    $story = pavilion_api_request('webstories/' . $post_id . '/');
    if (empty($story) || !is_array($story)) {
        return array();
    }

    $normalized = pavilion_transform_webstory($story);
    if (!empty($normalized)) {
        $pavilion_webstory_cache[$post_id] = $normalized;
        return isset($normalized['images']) ? $normalized['images'] : array();
    }

    return array();
}

function pavilion_transform_webstory($story)
{
    if (!is_array($story) || empty($story['id'])) {
        return array();
    }

    $cover = $story['cover_image_url'] ?? '';
    if (empty($cover) && !empty($story['cover_image'])) {
        $cover = $story['cover_image'];
    } elseif (empty($cover) && !empty($story['cover_external_url'])) {
        $cover = $story['cover_external_url'];
    }

    $images = array();
    if (!empty($story['slides']) && is_array($story['slides'])) {
        foreach ($story['slides'] as $slide) {
            $image_url = $slide['image_url'] ?? '';
            if (empty($image_url) && !empty($slide['external_image_url'])) {
                $image_url = $slide['external_image_url'];
            }
            if (empty($image_url) && !empty($slide['media_id'])) {
                $image_url = pavilion_get_media_url($slide['media_id']);
            }

            if (empty($image_url)) {
                continue;
            }

            $images[] = array(
                'url' => $image_url,
                'image_url' => $image_url,
                'image_id' => $slide['media_id'] ?? null,
                'caption' => $slide['caption'] ?? '',
            );
        }
    }

    return array(
        'id' => $story['id'],
        'title' => $story['title'] ?? '',
        'summary' => $story['summary'] ?? '',
        'status' => $story['status'] ?? 'draft',
        'featured_image' => $cover,
        'featured_image_url' => $cover,
        'published_at' => $story['published_at'] ?? '',
        'created_at' => $story['created_at'] ?? '',
        'images' => $images,
    );
}

// WP Query class (simplified)
class WP_Query
{
    public $posts = array();
    public $post_count = 0;
    public $current_post = -1;
    public $found_posts = 0;

    public function __construct($args = array())
    {
        // Map WordPress query args to our filter format
        $filters = array();

        // Default to published status for public site
        $filters['status'] = isset($args['status']) ? $args['status'] : (isset($args['post_status']) ? $args['post_status'] : 'published');

        if (isset($args['posts_per_page'])) {
            $filters['posts_per_page'] = $args['posts_per_page'];
        }

        if (isset($args['paged'])) {
            $filters['paged'] = $args['paged'];
        }

        if (isset($args['category_name'])) {
            $filters['category_name'] = $args['category_name'];
        }

        if (isset($args['cat'])) {
            // Category ID - need to convert to slug or filter
            $category_id = $args['cat'];
            $categories = pavilion_get_categories();
            foreach ($categories as $cat) {
                $cat_id = is_array($cat) ? $cat['id'] : (isset($cat->id) ? $cat->id : null);
                if ($cat_id == $category_id) {
                    $cat_slug = is_array($cat) ? $cat['slug'] : (isset($cat->slug) ? $cat->slug : null);
                    if ($cat_slug) {
                        $filters['category_name'] = $cat_slug;
                    }
                    break;
                }
            }
        }

        if (isset($args['post__not_in'])) {
            $filters['post__not_in'] = $args['post__not_in'];
        }

        // Get all posts first with error handling
        try {
            $this->posts = get_all_posts($filters);

            // Ensure we have an array
            if (!is_array($this->posts)) {
                $this->posts = array();
            }

            // Handle offset (skip first N posts)
            if (isset($args['offset']) && $args['offset'] > 0 && is_numeric($args['offset'])) {
                $this->posts = array_slice($this->posts, (int) $args['offset']);
            }

            // Apply posts_per_page limit after offset
            if (isset($args['posts_per_page']) && is_numeric($args['posts_per_page']) && count($this->posts) > (int) $args['posts_per_page']) {
                $this->posts = array_slice($this->posts, 0, (int) $args['posts_per_page']);
            }

            $this->post_count = count($this->posts);
            $this->found_posts = $this->post_count; // For pagination compatibility
            $this->current_post = -1;
        } catch (Exception $e) {
            error_log("WP_Query error: " . $e->getMessage());
            $this->posts = array();
            $this->post_count = 0;
            $this->found_posts = 0;
            $this->current_post = -1;
        }
    }

    public function have_posts()
    {
        return ($this->current_post + 1) < $this->post_count;
    }

    public function the_post()
    {
        global $post;
        $this->current_post++;
        if (isset($this->posts[$this->current_post])) {
            setup_postdata($this->posts[$this->current_post]);
            return true;
        }
        return false;
    }

    public function reset_postdata()
    {
        global $post;
        $this->current_post = -1;
        wp_reset_postdata();
    }
}

// Global post variable
$GLOBALS['post'] = null;

function setup_postdata($post_obj)
{
    global $post;
    $post = is_array($post_obj) ? $post_obj : (array) $post_obj;
}

function wp_reset_postdata()
{
    global $post;
    $post = null;
}

// Get the ID
function get_the_ID()
{
    global $post;
    if (is_array($post) && isset($post['id'])) {
        return $post['id'];
    }
    $current_post = get_current_post();
    return $current_post ? $current_post['id'] : 0;
}

// Template functions
function the_title($before = '', $after = '', $echo = true)
{
    $title = get_the_title();
    if ($echo) {
        echo $before . esc_html($title) . $after;
    } else {
        return $before . $title . $after;
    }
}

function the_permalink()
{
    echo esc_url(get_permalink());
}

function the_content($more_link_text = null, $strip_teaser = false)
{
    $content = get_the_content();
    echo wp_kses_post($content);
}

function the_excerpt()
{
    echo esc_html(get_the_excerpt());
}

function the_date($d = '', $before = '', $after = '', $echo = true)
{
    $date = get_the_date($d);
    if ($echo) {
        echo $before . $date . $after;
    } else {
        return $before . $date . $after;
    }
}

// Include template parts
function get_template_part($slug, $name = null)
{
    $templates = array();
    $name = (string) $name;
    if ('' !== $name) {
        $templates[] = "{$slug}-{$name}.php";
    }
    $templates[] = "{$slug}.php";

    $located = locate_template($templates);
    if ($located) {
        include $located;
    }
}

function locate_template($template_names)
{
    $located = '';
    foreach ($template_names as $template_name) {
        if (file_exists($template_name)) {
            $located = $template_name;
            break;
        }
    }
    return $located;
}

// Starkers utilities (placeholder)
class Starkers_Utilities
{
    public static function get_template_parts($parts)
    {
        foreach ($parts as $part) {
            $file = 'parts/shared/' . $part . '.php';
            if (file_exists($file)) {
                include $file;
            }
        }
    }
}

// Post meta (placeholder)
function get_post_meta($post_id, $key, $single = true)
{
    // For gallery images
    if ($key === '_gallery_images') {
        $gallery = null;
        $data = load_json_data();
        $galleries = isset($data['galleries']) ? $data['galleries'] : array();
        foreach ($galleries as $g) {
            if ($g['id'] == $post_id) {
                $gallery = $g;
                break;
            }
        }

        if ($gallery && isset($gallery['images'])) {
            $images = array();
            foreach ($gallery['images'] as $img) {
                $images[] = array(
                    'image_id' => md5($img['url']),
                    'caption' => isset($img['caption']) ? $img['caption'] : ''
                );
            }
            return $images;
        }
    }
    return '';
}

// Attachment functions (placeholder)
function wp_get_attachment_image_url($attachment_id, $size = 'thumbnail')
{
    // This is a placeholder - in real implementation, would map to actual image URLs
    return get_stylesheet_directory_uri() . 'assets/images/pavilion.jpg';
}

// Get attachment image source array (URL, width, height)
function wp_get_attachment_image_src($attachment_id, $size = 'thumbnail')
{
    // If attachment_id is 0 or empty, return false
    if (empty($attachment_id)) {
        return false;
    }

    // Try to get the image URL from the current post's featured_image
    // The attachment_id from get_post_thumbnail_id() is typically a hash of the image URL
    global $post;
    $image_url = '';

    if ($post && isset($post['featured_image'])) {
        // Verify the attachment_id matches (it's a hash of the featured_image URL)
        $expected_hash = md5($post['featured_image']);
        if ($attachment_id == $expected_hash || $attachment_id == $post['featured_image']) {
            $image_url = $post['featured_image'];
        }
    }

    // If we found an image URL, process it
    if (!empty($image_url)) {
        // If it's already a full URL, use it
        if (strpos($image_url, 'http') === 0) {
            $url = $image_url;
        } elseif (strpos($image_url, '/media/') === 0) {
            // If it starts with /media/, it's from the API, prepend API base URL
            $api_base = rtrim(PAVILION_API_BASE_URL, '/api');
            $url = $api_base . $image_url;
        } else {
            // Fallback to default image
            $url = get_stylesheet_directory_uri() . 'assets/images/pavilion.jpg';
        }
    } else {
        // Fallback to default image
        $url = get_stylesheet_directory_uri() . 'assets/images/pavilion.jpg';
    }

    // Return array with URL, width, height (WordPress format)
    // Default dimensions based on size
    $dimensions = array(
        'thumbnail' => array(150, 150),
        'medium' => array(300, 300),
        'large' => array(1024, 1024),
        'full' => array(1920, 1080)
    );

    $width = isset($dimensions[$size]) ? $dimensions[$size][0] : 300;
    $height = isset($dimensions[$size]) ? $dimensions[$size][1] : 300;

    return array($url, $width, $height);
}

function get_post_thumbnail_id($post_id = null)
{
    $post = $post_id ? get_post($post_id) : get_current_post();
    if ($post && isset($post['featured_image'])) {
        return md5($post['featured_image']);
    }
    return 0;
}

// Sidebar functions
function get_recent_posts_sidebar($limit = 5, $exclude_ids = array())
{
    $args = array('posts_per_page' => $limit);
    if (!empty($exclude_ids)) {
        $args['post__not_in'] = $exclude_ids;
    }

    // Exclude current post if on single page
    if (is_single()) {
        $current_post_id = get_the_ID();
        if (!isset($args['post__not_in'])) {
            $args['post__not_in'] = array();
        }
        $args['post__not_in'][] = $current_post_id;
    }

    return get_all_posts($args);
}

function get_popular_posts_sidebar($limit = 5, $exclude_ids = array(), $days_back = 30)
{
    $all_posts = get_all_posts();

    // Exclude current post
    if (is_single()) {
        $exclude_ids[] = get_the_ID();
    }

    if (!empty($exclude_ids)) {
        $all_posts = array_filter($all_posts, function ($post) use ($exclude_ids) {
            return !in_array($post['id'], $exclude_ids);
        });
    }

    // Sort by views
    usort($all_posts, function ($a, $b) {
        $views_a = isset($a['views']) ? $a['views'] : 0;
        $views_b = isset($b['views']) ? $b['views'] : 0;
        return $views_b - $views_a;
    });

    return array_slice($all_posts, 0, $limit);
}

function get_trending_posts_sidebar($limit = 5, $exclude_ids = array())
{
    $all_posts = get_all_posts();

    // Exclude current post
    if (is_single()) {
        $exclude_ids[] = get_the_ID();
    }

    if (!empty($exclude_ids)) {
        $all_posts = array_filter($all_posts, function ($post) use ($exclude_ids) {
            return !in_array($post['id'], $exclude_ids);
        });
    }

    // Sort by engagement (views + shares)
    usort($all_posts, function ($a, $b) {
        $engagement_a = (isset($a['views']) ? $a['views'] : 0) + (isset($a['shares']) ? $a['shares'] : 0);
        $engagement_b = (isset($b['views']) ? $b['views'] : 0) + (isset($b['shares']) ? $b['shares'] : 0);
        return $engagement_b - $engagement_a;
    });

    return array_slice($all_posts, 0, $limit);
}

function format_post_for_sidebar($post)
{
    // Handle both array and object format
    if (is_array($post)) {
        $post_id = isset($post['id']) ? $post['id'] : 0;
        $post_title = isset($post['title']) ? $post['title'] : '';
        $post_date = isset($post['date']) ? $post['date'] : '';
        $views = isset($post['views']) ? $post['views'] : 0;
        $shares = isset($post['shares']) ? $post['shares'] : 0;
    } else {
        $post_id = isset($post->ID) ? $post->ID : (isset($post['id']) ? $post['id'] : 0);
        $post_title = isset($post->post_title) ? $post->post_title : (isset($post['title']) ? $post['title'] : '');
        $post_date = isset($post->post_date) ? $post->post_date : (isset($post['date']) ? $post['date'] : '');
        $views = isset($post->views) ? $post->views : (isset($post['views']) ? $post['views'] : 0);
        $shares = isset($post->shares) ? $post->shares : (isset($post['shares']) ? $post['shares'] : 0);
    }

    $categories = get_filtered_categories($post_id);
    $category_links = array();

    if (!empty($categories)) {
        foreach ($categories as $category) {
            $cat_name = is_array($category) ? $category['name'] : $category->name;
            // Use term_id if available, otherwise fall back to id
            $cat_id = is_array($category) ? $category['id'] : (isset($category->term_id) ? $category->term_id : $category->id);
            $category_links[] = '<a href="' . esc_url(get_category_link($cat_id)) . '" class="post-cat color-blue-three">' . esc_html($cat_name) . '</a>';
        }
    }

    $image_url = get_the_post_thumbnail_url($post_id, 'medium');

    return array(
        'id' => $post_id,
        'title' => $post_title,
        'url' => get_permalink($post_id),
        'image_url' => $image_url,
        'categories' => $category_links,
        'time_ago' => meks_time_ago($post_date), // Pass date directly
        'view_count' => $views,
        'comment_count' => 0,
        'share_count' => $shares
    );
}

// Is category check
function is_category()
{
    return false; // Not implemented yet
}

// Get comments number
function get_comments_number($post_id = null)
{
    // Placeholder - return 0 for now
    return 0;
}

function wp_count_comments($post_id = null)
{
    // Return empty object with approved = 0
    return (object) array('approved' => 0);
}

// Core functions loaded

