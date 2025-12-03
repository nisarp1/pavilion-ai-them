<?php
/**
 * Test Category Filtering
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/core.php';

echo "<h1>Category Filtering Test</h1>";
echo "<hr>";

// Test 1: Get all published articles
echo "<h2>1. All Published Articles</h2>";
$all_articles = pavilion_get_articles(array('status' => 'published', 'page_size' => 5));
echo "Total articles: " . count($all_articles) . "<br>";
if (count($all_articles) > 0) {
    $first_article = $all_articles[0];
    echo "First article title: " . htmlspecialchars($first_article['title']) . "<br>";
    echo "First article categories: ";
    if (isset($first_article['categories']) && is_array($first_article['categories'])) {
        foreach ($first_article['categories'] as $cat) {
            $cat_slug = is_array($cat) ? $cat['slug'] : (isset($cat->slug) ? $cat->slug : 'N/A');
            $cat_id = is_array($cat) ? $cat['id'] : (isset($cat->id) ? $cat->id : 'N/A');
            echo $cat_slug . " (ID: $cat_id), ";
        }
    }
    echo "<br>";
}
echo "<hr>";

// Test 2: Get category by slug
echo "<h2>2. Category Lookup</h2>";
$test_categories = array('featured', 'cricket', 'football', 'ipl');
foreach ($test_categories as $slug) {
    $category = get_category_by_slug($slug);
    if ($category) {
        $cat_id = is_array($category) ? $category['id'] : (isset($category->id) ? $category->id : (isset($category->term_id) ? $category->term_id : null));
        $cat_name = is_array($category) ? $category['name'] : (isset($category->name) ? $category->name : 'N/A');
        echo "✓ Found '$slug': ID=$cat_id, Name=$cat_name<br>";
    } else {
        echo "✗ Category '$slug' not found<br>";
    }
}
echo "<hr>";

// Test 3: Filter by category using get_all_posts
echo "<h2>3. Filter by Category (Featured)</h2>";
$featured_posts = get_all_posts(array(
    'category_name' => 'featured',
    'posts_per_page' => 5,
    'status' => 'published'
));
echo "Featured posts found: " . count($featured_posts) . "<br>";
if (count($featured_posts) > 0) {
    foreach ($featured_posts as $post) {
        echo "- " . htmlspecialchars($post['title']) . " (ID: " . $post['id'] . ")<br>";
        echo "  Categories: " . implode(', ', array_map('strval', $post['categories'])) . "<br>";
    }
} else {
    echo "<strong style='color: red;'>No featured posts found!</strong><br>";
}
echo "<hr>";

// Test 4: Filter by Cricket
echo "<h2>4. Filter by Category (Cricket)</h2>";
$cricket_posts = get_all_posts(array(
    'category_name' => 'cricket',
    'posts_per_page' => 5,
    'status' => 'published'
));
echo "Cricket posts found: " . count($cricket_posts) . "<br>";
if (count($cricket_posts) > 0) {
    foreach (array_slice($cricket_posts, 0, 3) as $post) {
        echo "- " . htmlspecialchars($post['title']) . "<br>";
    }
} else {
    echo "<strong style='color: red;'>No cricket posts found!</strong><br>";
}
echo "<hr>";

// Test 5: WP_Query test
echo "<h2>5. WP_Query Test (Featured)</h2>";
$query = new WP_Query(array(
    'category_name' => 'featured',
    'posts_per_page' => 3,
    'status' => 'published'
));
echo "WP_Query found: " . $query->post_count . " posts<br>";
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        global $post;
        echo "- " . htmlspecialchars(get_the_title()) . "<br>";
    }
    wp_reset_postdata();
} else {
    echo "<strong style='color: red;'>WP_Query returned no posts!</strong><br>";
}

?>

