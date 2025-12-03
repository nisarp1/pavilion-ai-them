<?php
/**
 * Test Category API Filtering
 * Access this file directly to debug category filtering
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/core.php';

$test_category = isset($_GET['category']) ? $_GET['category'] : 'cricket';

echo "<h1>Category API Test: " . htmlspecialchars($test_category) . "</h1>";
echo "<hr>";

// Test 1: Get category by slug
echo "<h2>1. Category Lookup</h2>";
$category = get_category_by_slug($test_category);
if ($category) {
    $cat_id = is_array($category) ? $category['id'] : (isset($category->id) ? $category->id : (isset($category->term_id) ? $category->term_id : null));
    $cat_name = is_array($category) ? $category['name'] : (isset($category->name) ? $category->name : 'N/A');
    echo "✓ Found category: ID=$cat_id, Name=$cat_name<br>";
} else {
    echo "✗ Category '$test_category' not found<br>";
}
echo "<hr>";

// Test 2: Get all categories
echo "<h2>2. All Categories</h2>";
$all_categories = pavilion_get_categories();
echo "Total categories: " . count($all_categories) . "<br>";
foreach ($all_categories as $cat) {
    $cat_slug = is_array($cat) ? (isset($cat['slug']) ? $cat['slug'] : 'N/A') : (isset($cat->slug) ? $cat->slug : 'N/A');
    $cat_id = is_array($cat) ? (isset($cat['id']) ? $cat['id'] : 'N/A') : (isset($cat->id) ? $cat->id : 'N/A');
    echo "- $cat_slug (ID: $cat_id)<br>";
}
echo "<hr>";

// Test 3: Get articles with category filter via API
echo "<h2>3. API Request with Category Filter</h2>";
$api_params = array(
    'status' => 'published',
    'category' => $test_category,
    'page_size' => 5
);
echo "API Params: " . json_encode($api_params) . "<br>";
$articles = pavilion_get_articles($api_params);
echo "Articles returned from API: " . count($articles) . "<br>";
if (count($articles) > 0) {
    echo "<h3>First Article:</h3>";
    $first = $articles[0];
    echo "Title: " . htmlspecialchars($first['title']) . "<br>";
    echo "Categories: ";
    if (isset($first['categories']) && is_array($first['categories'])) {
        foreach ($first['categories'] as $cat) {
            $cat_id = is_array($cat) ? (isset($cat['id']) ? $cat['id'] : null) : (isset($cat->id) ? $cat->id : null);
            $cat_slug = is_array($cat) ? (isset($cat['slug']) ? $cat['slug'] : null) : (isset($cat->slug) ? $cat->slug : null);
            echo "$cat_slug (ID: $cat_id), ";
        }
    }
    echo "<br>";
}
echo "<hr>";

// Test 4: Get posts using get_all_posts
echo "<h2>4. get_all_posts with category_name</h2>";
$posts = get_all_posts(array(
    'category_name' => $test_category,
    'status' => 'published',
    'posts_per_page' => 5
));
echo "Posts returned: " . count($posts) . "<br>";
if (count($posts) > 0) {
    echo "<h3>First Post:</h3>";
    $first = $posts[0];
    echo "Title: " . htmlspecialchars($first['title']) . "<br>";
    echo "Categories: " . json_encode($first['categories']) . "<br>";
}
echo "<hr>";

// Test 5: WP_Query test
echo "<h2>5. WP_Query Test</h2>";
$query = new WP_Query(array(
    'category_name' => $test_category,
    'posts_per_page' => 5,
    'post_status' => 'publish'
));
echo "Query found posts: " . $query->found_posts . "<br>";
echo "Query post count: " . $query->post_count . "<br>";
if ($query->have_posts()) {
    echo "<h3>Posts:</h3>";
    while ($query->have_posts()) {
        $query->the_post();
        echo "- " . get_the_title() . " (ID: " . get_the_ID() . ")<br>";
    }
    wp_reset_postdata();
} else {
    echo "No posts found!<br>";
}
echo "<hr>";

// Test 6: Check error logs
echo "<h2>6. Check PHP Error Log</h2>";
echo "Check your PHP error log for detailed debugging information.<br>";
echo "Error log location: " . ini_get('error_log') . "<br>";

