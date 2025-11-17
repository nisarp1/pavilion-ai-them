<?php
/**
 * Update homepage category mappings
 * - Latest News → latest category
 * - Top News → featured category
 * - Gulf News → cricket category
 * - UAE Updates → football category
 * - Video Stories → video category
 * - Entertainment → team-india category
 * - Sports → isl category
 */

// Load data.json
$json_file = __DIR__ . '/data.json';
$data = json_decode(file_get_contents($json_file), true);

if (!$data) {
    die("Error: Could not load data.json\n");
}

echo "Updating homepage category mappings...\n";

// Update each post to add additional categories based on needs
// Posts will show in multiple sections if needed for testing
foreach ($data['posts'] as &$post) {
    $post_id = $post['id'];
    
    // Keep existing categories
    $categories = isset($post['categories']) ? $post['categories'] : array(28); // Default to Cricket
    
    // Add categories based on post ID for distribution
    if ($post_id <= 25) {
        // First 25 posts: Add to featured (Top News)
        if (!in_array(1, $categories)) {
            $categories[] = 1; // featured
        }
        if (!in_array(2, $categories)) {
            $categories[] = 2; // latest
        }
    } elseif ($post_id <= 30) {
        // Posts 26-30: Add to Football and Video
        if (!in_array(27, $categories)) {
            $categories[] = 27; // football
        }
        if (!in_array(33, $categories)) {
            $categories[] = 33; // video
        }
    } elseif ($post_id <= 35) {
        // Posts 31-35: Add to IPL and Team India
        if (!in_array(29, $categories)) {
            $categories[] = 29; // ipl
        }
        if (!in_array(34, $categories)) {
            $categories[] = 34; // team-india
        }
    } elseif ($post_id <= 40) {
        // Posts 36-40: Add to ISL
        if (!in_array(30, $categories)) {
            $categories[] = 30; // isl
        }
    } elseif ($post_id <= 45) {
        // Posts 41-45: Add to EPL and World Cup
        if (!in_array(31, $categories)) {
            $categories[] = 31; // epl
        }
        if (!in_array(32, $categories)) {
            $categories[] = 32; // worldcup
        }
    }
    
    $post['categories'] = array_unique($categories);
}

// Save updated data
$json_output = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents($json_file, $json_output);

echo "✅ Updated data.json with category mappings!\n";
echo "Total posts: " . count($data['posts']) . "\n";

// Show category distribution
echo "\nCategory distribution:\n";
foreach ($data['categories'] as $category) {
    $count = 0;
    foreach ($data['posts'] as $post) {
        if (in_array($category['id'], $post['categories'])) {
            $count++;
        }
    }
    echo "  {$category['name']}: {$count} posts\n";
}

echo "\nDone!\n";

