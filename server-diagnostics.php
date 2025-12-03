<?php
// Load core
require_once 'core.php';

echo "<h1>Server Diagnostics</h1>";

// 1. Path Detection
echo "<h2>1. Path Detection</h2>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Dir: " . __DIR__ . "<br>";
echo "Calculated Theme Base Path: '" . get_theme_base_path() . "'<br>";
echo "Stylesheet Directory URI: '" . get_stylesheet_directory_uri() . "'<br>";
echo "Home URL: '" . home_url() . "'<br>";

// 2. File Existence
echo "<h2>2. File Existence</h2>";
$files = [
    'assets/css/style.css',
    'assets/css/vendor/bootstrap.min.css',
    'assets/js/main.js'
];
foreach ($files as $file) {
    echo "Checking $file: " . (file_exists(__DIR__ . '/' . $file) ? "✅ Found" : "❌ MISSING") . "<br>";
}

// 3. API Connectivity
echo "<h2>3. API Connectivity</h2>";
$api_url = PAVILION_API_BASE_URL . '/articles/';
echo "Testing API URL: $api_url<br>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
// Disable SSL verification for testing if needed
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: $error<br>";
} else {
    echo "HTTP Status: $http_code<br>";
    if ($http_code >= 200 && $http_code < 300) {
        $data = json_decode($response, true);
        $count = is_array($data) ? (isset($data['count']) ? $data['count'] : count($data)) : 0;
        echo "✅ API Connection Successful. Found $count articles.<br>";
    } else {
        echo "❌ API Error. HTTP Code: $http_code<br>";
        echo "Response: " . substr($response, 0, 200) . "...<br>";
    }
}
?>