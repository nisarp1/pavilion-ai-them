<?php
/**
 * Create data.json with RSS feed data from pavilionend.in
 */

// RSS feed URL
$rss_url = 'https://pavilionend.in/rss';

// Fetch RSS
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $rss_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$rss_content = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200 || $rss_content === false) {
    die("Error: Could not fetch RSS feed (HTTP $http_code)\n");
}

// Parse XML
libxml_use_internal_errors(true);
$xml = simplexml_load_string($rss_content);

if ($xml === false) {
    die("Error: Could not parse RSS XML\n");
}

// Create data structure
$data = array(
    'site' => array(
        'name' => 'Pavilion End',
        'description' => 'Latest Cricket, Football, ISL, IPL, EPL and World Cup News',
        'url' => '/',
        'logo' => '/assets/images/new/logo.svg'
    ),
    'categories' => array(
        array('id' => 27, 'slug' => 'football', 'name' => 'Football'),
        array('id' => 28, 'slug' => 'cricket', 'name' => 'Cricket'),
        array('id' => 29, 'slug' => 'ipl', 'name' => 'IPL'),
        array('id' => 30, 'slug' => 'isl', 'name' => 'ISL'),
        array('id' => 31, 'slug' => 'epl', 'name' => 'EPL'),
        array('id' => 32, 'slug' => 'worldcup', 'name' => 'World Cup'),
        array('id' => 1, 'slug' => 'featured', 'name' => 'Top News', 'description' => 'Featured stories'),
        array('id' => 2, 'slug' => 'latest', 'name' => 'Latest', 'description' => 'Latest news')
    ),
    'authors' => array(
        array(
            'id' => 1,
            'name' => 'Pavilion Editorial Team',
            'display_name' => 'Pavilion Editorial Team',
            'slug' => 'pavilion-editorial',
            'bio' => 'The editorial team at Pavilion End brings you the latest sports news and updates.',
            'title' => 'Editor',
            'avatar' => '/assets/images/pavilion.jpg',
            'social' => array(
                'twitter' => 'https://twitter.com',
                'facebook' => 'https://facebook.com',
                'instagram' => 'https://instagram.com'
            )
        )
    ),
    'posts' => array()
);

// Category mapping
$category_map = array(
    'Football' => 27,
    'Cricket' => 28,
    'IPL' => 29,
    'ISL' => 30,
    'EPL' => 31,
    'Worldcup' => 32,
    'World Cup' => 32
);

// Process RSS items
$post_id = 1;
foreach ($xml->channel->item as $item) {
    $title = (string) $item->title;
    $link = (string) $item->link;
    $description = (string) $item->description;
    $pub_date = isset($item->pubDate) ? (string) $item->pubDate : date('Y-m-d H:i:s');
    $categories = array();

    // Extract categories
    if (isset($item->category)) {
        foreach ($item->category as $cat) {
            $cat_name = (string) $cat;
            if (isset($category_map[$cat_name])) {
                $categories[] = $category_map[$cat_name];
            }
        }
    }

    // Smart categorization if no categories
    if (empty($categories)) {
        $title_lower = strtolower($title);
        if (strpos($title_lower, 'football') !== false || strpos($description, 'football') !== false) {
            $categories[] = 27;
        } elseif (strpos($title_lower, 'ipl') !== false || strpos($description, 'ipl') !== false) {
            $categories[] = 29;
        } elseif (strpos($title_lower, 'isl') !== false || strpos($description, 'isl') !== false) {
            $categories[] = 30;
        } elseif (strpos($title_lower, 'epl') !== false || strpos($title_lower, 'premier') !== false) {
            $categories[] = 31;
        } elseif (strpos($title_lower, 'world cup') !== false || strpos($title_lower, 'worldcup') !== false) {
            $categories[] = 32;
        } else {
            $categories[] = 28; // Default to Cricket
        }
    }

    // Generate slug
    $slug = basename(parse_url($link, PHP_URL_PATH));
    $slug = str_replace(array('.html', '.htm'), '', $slug);
    $slug = trim($slug, '/');
    if (empty($slug)) {
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($title));
    }

    // Parse description
    $full_description = $description;
    $excerpt = strip_tags($description);
    $excerpt = html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8');
    $excerpt_short = mb_substr($excerpt, 0, 200, 'UTF-8') . '...';

    // Extract image
    $image = '/assets/images/pavilion.jpg';
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $description, $matches)) {
        $image = $matches[1];
    }

    // Parse date
    $date = date('Y-m-d H:i:s', strtotime($pub_date));

    // Create post
    $post = array(
        'id' => $post_id++,
        'title' => $title,
        'slug' => $slug,
        'excerpt' => $excerpt_short,
        'content' => '<p>' . nl2br(htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8')) . '</p>',
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

// Clean data for JSON encoding
function clean_for_json($data)
{
    if (is_array($data)) {
        return array_map('clean_for_json', $data);
    } elseif (is_string($data)) {
        // Remove invalid UTF-8 characters
        $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        return $data;
    }
    return $data;
}

$data = clean_for_json($data);

// Save data.json
$json_output = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($json_output === false) {
    echo "ERROR: json_encode failed: " . json_last_error_msg() . "\n";
    exit(1);
}

$file_path = __DIR__ . '/data.json';
$result = file_put_contents($file_path, $json_output);

if ($result === false) {
    echo "ERROR: Failed to write data.json\n";
    exit(1);
}

echo "Successfully created data.json!\n";
echo "Total posts: " . count($data['posts']) . "\n";
echo "Wrote " . $result . " bytes\n";

// Category summary
$cat_counts = array();
foreach ($data['posts'] as $post) {
    foreach ($post['categories'] as $cat_id) {
        $cat_counts[$cat_id] = isset($cat_counts[$cat_id]) ? $cat_counts[$cat_id] + 1 : 1;
    }
}

echo "\nPosts by category:\n";
foreach ($data['categories'] as $category) {
    $count = isset($cat_counts[$category['id']]) ? $cat_counts[$category['id']] : 0;
    echo "  {$category['name']}: $count\n";
}

echo "\nDone!\n";

