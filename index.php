<?php
/**
 * Main router for standalone PHP theme
 */

// Load core functions
require_once __DIR__ . '/core.php';

// Get the request path
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($request_uri, '/');

// Remove query string
if (($pos = strpos($path, '?')) !== false) {
    $path = substr($path, 0, $pos);
}

// Remove 'pavilion-theme' prefix if present
$path = str_replace('pavilion-theme/', '', $path);
$path = str_replace('pavilion-theme', '', $path);
$path = trim($path, '/');

// Route handling
if (empty($path) || $path === 'index.php') {
    // Homepage
    if (file_exists(__DIR__ . '/home.php')) {
        include __DIR__ . '/home.php';
    } else {
        // Fallback
        echo "Welcome to " . get_bloginfo('name');
    }
} else {
    // Check if it's a post slug
    $post = get_post($path);
    
    if ($post) {
        // Single post
        if (file_exists(__DIR__ . '/single-post.php')) {
            include __DIR__ . '/single-post.php';
        } else {
            // Fallback single post display
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title><?php echo esc_html(get_the_title()); ?> - <?php bloginfo('name'); ?></title>
            </head>
            <body>
                <h1><?php the_title(); ?></h1>
                <div><?php the_content(); ?></div>
            </body>
            </html>
            <?php
        }
    } else {
        // Check if it's a category
        $category = get_category_by_slug($path);
        
        if ($category) {
            // Category archive
            if (file_exists(__DIR__ . '/category.php')) {
                include __DIR__ . '/category.php';
            } else {
                // Fallback category display
                // Handle both array and object formats
                $cat_name = is_array($category) ? (isset($category['name']) ? $category['name'] : '') : (isset($category->name) ? $category->name : '');
                $cat_slug = is_array($category) ? (isset($category['slug']) ? $category['slug'] : '') : (isset($category->slug) ? $category->slug : '');
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <title><?php echo esc_html($cat_name); ?> - <?php bloginfo('name'); ?></title>
                </head>
                <body>
                    <h1><?php echo esc_html($cat_name); ?></h1>
                    <?php
                    $posts = get_all_posts(array('category_name' => $cat_slug, 'posts_per_page' => 10));
                    foreach ($posts as $post) {
                        echo '<h2><a href="' . get_permalink($post['id']) . '">' . esc_html($post['title']) . '</a></h2>';
                    }
                    ?>
                </body>
                </html>
                <?php
            }
        } else {
            // Check for special pages
            if ($path === 'contact' || $path === 'contact/') {
                if (file_exists(__DIR__ . '/contact.php')) {
                    include __DIR__ . '/contact.php';
                } else {
                    echo "Contact page";
                }
            } elseif ($path === 'about-us' || $path === 'about-us/') {
                if (file_exists(__DIR__ . '/about-us.php')) {
                    include __DIR__ . '/about-us.php';
                } else {
                    echo "About Us page";
                }
            } elseif ($path === 'latest' || $path === 'latest/') {
                if (file_exists(__DIR__ . '/page-latest.php')) {
                    include __DIR__ . '/page-latest.php';
                } elseif (file_exists(__DIR__ . '/archive.php')) {
                    include __DIR__ . '/archive.php';
                } else {
                    // Show latest posts
                    ?>
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Latest News - <?php bloginfo('name'); ?></title>
                    </head>
                    <body>
                        <h1>Latest News</h1>
                        <?php
                        $posts = get_all_posts(array('posts_per_page' => 10));
                        foreach ($posts as $post) {
                            echo '<h2><a href="' . get_permalink($post['id']) . '">' . esc_html($post['title']) . '</a></h2>';
                        }
                        ?>
                    </body>
                    </html>
                    <?php
                }
            } else {
                // 404
                if (file_exists(__DIR__ . '/404.php')) {
                    include __DIR__ . '/404.php';
                } else {
                    header("HTTP/1.0 404 Not Found");
                    echo "404 - Page Not Found";
                }
            }
        }
    }
}
?>

