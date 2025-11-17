<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "PHP is working!<br>";
echo "Testing core.php include...<br>";

try {
    require_once __DIR__ . '/core.php';
    echo "✓ core.php loaded successfully<br>";
    
    // Test basic functions
    if (function_exists('home_url')) {
        echo "✓ home_url() function exists<br>";
        echo "home_url() returns: " . home_url() . "<br>";
    } else {
        echo "✗ home_url() function not found<br>";
    }
    
    if (function_exists('pavilion_api_request')) {
        echo "✓ pavilion_api_request() function exists<br>";
    } else {
        echo "✗ pavilion_api_request() function not found<br>";
    }
    
    // Test API connection
    echo "<br>Testing API connection...<br>";
    $test_url = 'http://localhost:8000/api/articles/?status=published&page_size=1';
    echo "Testing: $test_url<br>";
    
    $ch = curl_init($test_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "✗ cURL Error: $error<br>";
    } else {
        echo "✓ HTTP Code: $http_code<br>";
        if ($http_code == 200) {
            echo "✓ API is accessible<br>";
            $data = json_decode($response, true);
            if ($data) {
                echo "✓ API returned valid JSON<br>";
            } else {
                echo "✗ API returned invalid JSON<br>";
            }
        } else {
            echo "✗ API returned error code: $http_code<br>";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "✗ Fatal Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

?>

