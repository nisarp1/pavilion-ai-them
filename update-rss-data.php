<?php
/**
 * Update data.json with real RSS feed data from pavilionend.in
 * This script fetches the RSS feed and populates the JSON file with real content
 */

// Load existing data.json
$json_file = __DIR__ . '/data.json';
$data = json_decode(file_get_contents($json_file), true);

// RSS feed URL
$rss_url = 'https://pavilionend.in/rss';

// Fetch and parse RSS
$rss_content = @file_get_contents($rss_url);

if ($rss_content === false) {
    echo "Error: Could not fetch RSS feed from $rss_url\n";
    echo "Using fallback method with curl...\n";
    
    // Try with curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $rss_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; RSS Reader)');
    $rss_content = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200 || $rss_content === false) {
        echo "Error: Still could not fetch RSS feed (HTTP $http_code)\n";
        echo "Please check your internet connection or RSS URL\n";
        exit(1);
    }
}

// Parse XML
libxml_use_internal_errors(true);
$xml = @simplexml_load_string($rss_content);

if ($xml === false) {
    echo "Error: Could not parse RSS XML\n";
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        echo "  " . $error->message . "\n";
    }
    exit(1);
}

// Clear posts array to populate with RSS data
$data['posts'] = array();
$post_id = 1;

// Category mapping from RSS to our categories
$category_map = array(
    'Football' => 27,
    'Cricket' => 28,
    'IPL' => 29,
    'ISL' => 30,
    'EPL' => 31,
    'Worldcup' => 32,
    'World Cup' => 32,
    'Uncategorized' => 28 // Default to Cricket
);

// Process RSS items
foreach ($xml->channel->item as $item) {
    $title = (string)$item->title;
    $link = (string)$item->link;
    $description = (string)$item->description;
    $pub_date = isset($item->pubDate) ? (string)$item->pubDate : date('Y-m-d H:i:s');
    $categories = array();
    
    // Extract categories from RSS item
    if (isset($item->category)) {
        foreach ($item->category as $cat) {
            $cat_name = (string)$cat;
            if (isset($category_map[$cat_name])) {
                $categories[] = $category_map[$cat_name];
            }
        }
    }
    
    // If no categories found, try to extract from content or set default
    if (empty($categories)) {
        // Try to determine category from title/content
        $title_lower = strtolower($title);
        if (strpos($title_lower, 'football') !== false || strpos($title_lower, 'soccer') !== false) {
            $categories[] = 27; // Football
        } elseif (strpos($title_lower, 'ipl') !== false) {
            $categories[] = 29; // IPL
        } elseif (strpos($title_lower, 'isl') !== false) {
            $categories[] = 30; // ISL
        } elseif (strpos($title_lower, 'epl') !== false || strpos($title_lower, 'premier league') !== false) {
            $categories[] = 31; // EPL
        } elseif (strpos($title_lower, 'world cup') !== false || strpos($title_lower, 'worldcup') !== false) {
            $categories[] = 32; // World Cup
        } else {
            // Default to Cricket
            $categories[] = 28;
        }
    }
    
    // Generate slug from link
    $slug = basename(parse_url($link, PHP_URL_PATH));
    $slug = str_replace('.html', '', $slug);
    $slug = trim($slug, '/');
    
    // If slug is empty, generate from title
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }
    
    // Parse description to get excerpt
    $excerpt = strip_tags($description);
    $excerpt = html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8');
    $excerpt = substr($excerpt, 0, 300) . '...';
    
    // Full content (description for now, can be enhanced with full content fetching)
    $content = '<p>' . nl2br(htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8')) . '</p>';
    
    // Try to extract image from description
    $image = '/assets/images/new/hero.jpg'; // Default image
    
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $description, $matches)) {
        $image = $matches[1];
        // Convert relative URL to absolute if needed
        if (strpos($image, 'http') !== 0) {
            $image = 'https://pavilionend.in' . $image;
        }
    }
    
    // Parse date
    $date = date('Y-m-d H:i:s', strtotime($pub_date));
    
    // Create post array
    $post = array(
        'id' => $post_id++,
        'title' => $title,
        'slug' => $slug,
        'excerpt' => $excerpt,
        'content' => $content,
        'date' => $date,
        'modified' => $date,
        'author' => 1,
        'categories' => array_unique($categories),
        'featured_image' => $image,
        'views' => rand(100, 5000),
        'shares' => rand(10, 200)
    );
    
    $data['posts'][] = $post;
}

// Save updated data.json
$json_output = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents($json_file, $json_output);

echo "Successfully updated data.json with RSS feed data!\n";
echo "Total posts added: " . count($data['posts']) . "\n";
echo "File saved to: $json_file\n";

// Display summary by category
$category_counts = array();
foreach ($data['posts'] as $post) {
    foreach ($post['categories'] as $cat_id) {
        if (!isset($category_counts[$cat_id])) {
            $category_counts[$cat_id] = 0;
        }
        $category_counts[$cat_id]++;
    }
}

echo "\nPosts by category:\n";
foreach ($data['categories'] as $category) {
    $count = isset($category_counts[$category['id']]) ? $category_counts[$category['id']] : 0;
    echo "  {$category['name']}: $count\n";
}

echo "\nDone!\n";

