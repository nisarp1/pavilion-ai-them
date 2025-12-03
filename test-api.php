<?php
/**
 * Test API Connection
 * Access this file in browser to test API connectivity
 */

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/core.php';

echo "<h1>Pavilion API Connection Test</h1>";
echo "<hr>";

echo "<h2>1. Testing API Base URL</h2>";
echo "API Base URL: " . PAVILION_API_BASE_URL . "<br>";
echo "<hr>";

echo "<h2>2. Testing Articles Endpoint</h2>";
$articles = pavilion_get_articles(array('status' => 'published', 'page_size' => 5));
echo "Articles returned: " . count($articles) . "<br>";
if (count($articles) > 0) {
    echo "<pre>" . print_r($articles[0], true) . "</pre>";
} else {
    echo "<strong style='color: red;'>No articles returned!</strong><br>";
    echo "This could mean:<br>";
    echo "- API is not accessible<br>";
    echo "- No published articles exist<br>";
    echo "- API returned an error<br>";
}
echo "<hr>";

echo "<h2>3. Testing Categories Endpoint</h2>";
$categories = pavilion_get_categories();
echo "Categories returned: " . count($categories) . "<br>";
if (count($categories) > 0) {
    echo "<pre>" . print_r(array_slice($categories, 0, 3), true) . "</pre>";
} else {
    echo "<strong style='color: red;'>No categories returned!</strong><br>";
}
echo "<hr>";

echo "<h2>4. Direct API Test</h2>";
$test_url = PAVILION_API_BASE_URL . '/articles/?status=published&page_size=1';
echo "Testing URL: <a href='$test_url' target='_blank'>$test_url</a><br>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $test_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $http_code<br>";
if ($curl_error) {
    echo "<strong style='color: red;'>cURL Error: $curl_error</strong><br>";
} else {
    echo "Response (first 500 chars): <pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
}

echo "<hr>";
echo "<h2>5. PHP Configuration</h2>";
echo "cURL enabled: " . (function_exists('curl_init') ? 'Yes' : 'No') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";

?>

