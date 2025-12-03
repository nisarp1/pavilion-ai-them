<?php
// Minimal test to see if page loads
echo "PHP is working!<br>";
echo "Now testing core.php...<br>";

try {
    require_once __DIR__ . '/core.php';
    echo "✓ core.php loaded<br>";
    
    if (function_exists('home_url')) {
        echo "✓ home_url() exists<br>";
    }
    
    echo "Testing API call...<br>";
    $articles = pavilion_get_articles(array('status' => 'published', 'page_size' => 1));
    echo "✓ API call returned: " . (is_array($articles) ? count($articles) . " articles" : "error") . "<br>";
    
} catch (Throwable $e) {
    echo "✗ Fatal Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br>Page should be visible now!";
?>

