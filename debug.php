<?php
/**
 * Debug page - Enable error display and test API connection
 * Access: http://localhost:8888/pavilion-theme/debug.php
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<!DOCTYPE html><html><head><title>Pavilion Theme Debug</title><style>body{font-family:monospace;padding:20px;background:#f5f5f5;} .success{color:green;} .error{color:red;} .section{margin:20px 0;padding:15px;background:white;border-radius:5px;}</style></head><body>";
echo "<h1>Pavilion Theme Debug Information</h1>";

// Test core.php inclusion
echo "<div class='section'><h2>1. Core Functions Load Test</h2>";
try {
    require_once __DIR__ . '/core.php';
    echo "<span class='success'>✓ core.php loaded successfully</span><br>";
} catch (Exception $e) {
    echo "<span class='error'>✗ Error loading core.php: " . $e->getMessage() . "</span><br>";
    exit;
}

// Test API connection
echo "<h2>2. API Connection Test</h2>";
$articles = pavilion_get_articles(array('status' => 'published', 'page_size' => 3));
echo "Articles returned: " . count($articles) . "<br>";
if (count($articles) > 0) {
    echo "<span class='success'>✓ API connection working</span><br>";
    echo "First article title: " . htmlspecialchars($articles[0]['title'] ?? 'N/A') . "<br>";
} else {
    echo "<span class='error'>✗ No articles returned from API</span><br>";
}

// Test categories
echo "<h2>3. Categories Test</h2>";
$categories = pavilion_get_categories();
echo "Categories returned: " . count($categories) . "<br>";
if (count($categories) > 0) {
    echo "<span class='success'>✓ Categories loaded</span><br>";
} else {
    echo "<span class='error'>✗ No categories returned</span><br>";
}

// Test WP_Query
echo "<h2>4. WP_Query Test</h2>";
try {
    $test_query = new WP_Query(array('posts_per_page' => 1, 'status' => 'published'));
    echo "Posts found: " . $test_query->post_count . "<br>";
    if ($test_query->post_count > 0) {
        echo "<span class='success'>✓ WP_Query working</span><br>";
    } else {
        echo "<span class='error'>✗ WP_Query returned no posts</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>✗ WP_Query error: " . $e->getMessage() . "</span><br>";
}

// Test home.php includes
echo "<h2>5. Template Files Test</h2>";
$files_to_check = array(
    'parts/shared/html-header.php',
    'parts/shared/header.php'
);

foreach ($files_to_check as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "<span class='success'>✓ $file exists</span><br>";
    } else {
        echo "<span class='error'>✗ $file NOT FOUND</span><br>";
    }
}

echo "</div>";

echo "<div class='section'><h2>6. PHP Configuration</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "cURL enabled: " . (function_exists('curl_init') ? 'Yes' : 'No') . "<br>";
echo "Error reporting: " . error_reporting() . "<br>";
echo "Display errors: " . ini_get('display_errors') . "<br>";
echo "</div>";

echo "<div class='section'><h2>7. API Configuration</h2>";
echo "API Base URL: " . PAVILION_API_BASE_URL . "<br>";
echo "API Timeout: " . PAVILION_API_TIMEOUT . " seconds<br>";
echo "</div>";

echo "</body></html>";
?>

