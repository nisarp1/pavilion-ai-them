<?php
// Get category from URL path
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($request_uri, '/');
$path = str_replace('pavilion-theme/', '', $path);
$path = str_replace('pavilion-theme', '', $path);
$path = trim($path, '/');

// Remove query string
if (($pos = strpos($path, '?')) !== false) {
    $path = substr($path, 0, $pos);
}

// Get category by slug from path
$current_category = get_category_by_slug($path);

// Handle both array and object formats
if (is_array($current_category)) {
    $category_slug = isset($current_category['slug']) ? $current_category['slug'] : $path;
    $category_name = isset($current_category['name']) ? $current_category['name'] : ucfirst($path);
    $category_id = isset($current_category['id']) ? $current_category['id'] : null;
} else {
    $category_slug = isset($current_category->slug) ? $current_category->slug : $path;
    $category_name = isset($current_category->name) ? $current_category->name : ucfirst($path);
    $category_id = isset($current_category->id) ? $current_category->id : (isset($current_category->term_id) ? $current_category->term_id : null);
}

// If category not found, use path as slug
if (!$current_category) {
    $category_slug = $path;
    $category_name = ucfirst(str_replace('-', ' ', $path));
    $category_id = null;
}

// Get pagination parameters
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts_per_page = 15; // Show 15 posts per page

// Debug information
echo "<!-- DEBUG: Category slug from URL: " . $path . " -->";
echo "<!-- DEBUG: Category detected: " . $category_slug . " -->";
echo "<!-- DEBUG: Category name: " . $category_name . " -->";
echo "<!-- DEBUG: Category ID: " . ($category_id ? $category_id : 'N/A') . " -->";
echo "<!-- DEBUG: Current page: " . $paged . " -->";

// Category titles mapping
$category_titles = [
    'latest' => 'Latest News',
    'uae' => 'UAE News',
    'gulf' => 'Gulf News',
    'saudi' => 'Saudi Arabia News',
    'qatar' => 'Qatar News',
    'oman' => 'Oman News',
    'kuwait' => 'Kuwait News',
    'bahrain' => 'Bahrain News',
    'yemen' => 'Yemen News',
    'kerala' => 'Kerala News',
    'india' => 'India News',
    'world' => 'World News',
    'middle-east' => 'Middle East News',
    'asia' => 'Asia News',
    'europe' => 'Europe News',
    'america' => 'America News',
    'entertainment' => 'Entertainment',
    'movies' => 'Movies',
    'events' => 'Events',
    'lifestyle' => 'Lifestyle',
    'sports' => 'Sports',
    'business' => 'Business',
    'finance' => 'Finance',
    'tech' => 'Technology',
    'job' => 'Jobs',
    'cricket' => 'Cricket',
    'football' => 'Football',
    'ipl' => 'IPL',
    'isl' => 'ISL',
    'epl' => 'EPL',
    'worldcup' => 'World Cup',
    'team-india' => 'Team India',
    'video' => 'Video Stories'
];

$page_title = isset($category_titles[$category_slug]) ? $category_titles[$category_slug] : $category_name;

?>

<?php include 'parts/shared/html-header.php'; ?>
<?php include 'parts/shared/header.php'; ?>

<script>
    document.body.classList.add('category-page');
</script>

<!-- Category Title Section -->
<div class="container px-4">
    <div class="category-header-section">
        <div class="category-header-wrapper">
            <h1 class="category-header-title"><?php echo $page_title; ?></h1>
        </div>
    </div>
</div>

<section class="post-section section-gap no-padding-top">

    <div class="container px-4">
        <div class="row">
            <?php
            // Check if this is the /latest/ URL and show all posts
            $current_url = $_SERVER['REQUEST_URI'];
            $is_latest_page = (strpos($current_url, '/latest/') !== false || strpos($current_url, '/latest') !== false);

            echo "<!-- DEBUG: Current URL: " . $current_url . " -->";
            echo "<!-- DEBUG: Is latest page: " . ($is_latest_page ? 'YES' : 'NO') . " -->";
            echo "<!-- DEBUG: Category slug: " . $category_slug . " -->";
            echo "<!-- DEBUG: Category name: " . $category_name . " -->";

            $api_params = array(
                'status' => 'published',
                'page_size' => $posts_per_page,
                'page' => max(1, (int) $paged),
            );

            if (!$is_latest_page && !empty($category_slug)) {
                $api_params['category'] = $category_slug;
            }

            $api_response = pavilion_api_request('articles/', $api_params);

            $articles = array();
            $total_count = 0;

            if (isset($api_response['results']) && is_array($api_response['results'])) {
                $articles = $api_response['results'];
                $total_count = isset($api_response['count']) ? (int) $api_response['count'] : count($articles);
            } elseif (is_array($api_response)) {
                $articles = $api_response;
                $total_count = count($articles);
            }

            $category_posts = pavilion_convert_articles_to_posts($articles);
            $posts_count = count($category_posts);

            if ($total_count === 0) {
                $total_count = $posts_count + (($paged - 1) * $posts_per_page);
            }

            $total_pages = $posts_per_page > 0 ? max(1, (int) ceil($total_count / $posts_per_page)) : 1;

            $first_post = $posts_count > 0 ? $category_posts[0] : null;
            $first_column_posts = $posts_count > 1 ? array_slice($category_posts, 1, 4) : array();
            $second_column_posts = $posts_count > 5 ? array_slice($category_posts, 5, 7) : array();
            $third_column_posts = $posts_count > 12 ? array_slice($category_posts, 12, 3) : array();

            $category_base_link = '#';
            if ($category_id) {
                $category_base_link = get_category_link($category_id);
            }
            if (empty($category_base_link) || $category_base_link === '#') {
                $category_base_link = home_url($category_slug . '/');
            }
            $category_base_link = rtrim($category_base_link, '/');

            $build_page_url = function ($page) use ($category_base_link) {
                $url = $category_base_link . '/';
                if ($page > 1) {
                    $url .= '?paged=' . $page;
                }
                return $url;
            };

            $render_small_card = function ($post, $extra_classes = 'mb-3') {
                setup_postdata($post);
                ?>
                <a href="<?php the_permalink(); ?>" class="block-link <?php echo esc_attr(trim($extra_classes)); ?>">
                    <div class="media post-block small-block">
                        <div class="post-image-wrapper">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                            <?php else: ?>
                                <img class="img-fluid"
                                    src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.png"
                                    alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>
                            <?php echo get_video_play_button(); ?>
                        </div>
                        <div class="media-body">
                            <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="post-cat-group flex-shrink-0">
                                    <?php
                                    $categories = get_filtered_categories();
                                    if (!empty($categories)) {
                                        $cat_count = 0;
                                        foreach ($categories as $category) {
                                            if ($cat_count > 0)
                                                echo ', ';
                                            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                            $cat_count++;
                                            if ($cat_count >= 2) {
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="post-time ms-3 flex-shrink-0">
                                    <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                <?php
                wp_reset_postdata();
            };

            if ($posts_count > 0):
                ?>
                <div class="col-lg-4">
                    <main class="axil-content medium-section">
                        <?php if ($first_post): ?>
                            <?php setup_postdata($first_post); ?>
                            <div class="live-card mb-4">
                                <a href="<?php the_permalink(); ?>" class="block-link">
                                    <div class="flex-1">
                                        <h3 class="featured-title"><?php the_title(); ?></h3>
                                        <p class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?></p>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <div class="post-cat-group flex-shrink-0">
                                                <?php
                                                $categories = get_filtered_categories();
                                                if (!empty($categories)) {
                                                    $cat_count = 0;
                                                    foreach ($categories as $category) {
                                                        if ($cat_count > 0)
                                                            echo ', ';
                                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                        $cat_count++;
                                                        if ($cat_count >= 3) {
                                                            break;
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="post-time ms-3 flex-shrink-0">
                                                <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="post-image-wrapper">
                                        <?php if (has_post_thumbnail()): ?>
                                            <?php the_post_thumbnail('large', array('class' => 'img-fluid img-border-radius', 'alt' => get_the_title())); ?>
                                        <?php else: ?>
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.png"
                                                alt="<?php the_title_attribute(); ?>" class="img-fluid img-border-radius">
                                        <?php endif; ?>
                                        <?php
                                        $categories = get_filtered_categories();
                                        if (!empty($categories)):
                                            $main_category = $categories[0];
                                            ?>
                                            <div class="post-cat-group badge-on-image">
                                                <a href="<?php echo esc_url(get_category_link($main_category->term_id)); ?>"
                                                    class="post-cat cat-btn bg-primary-color"><?php echo esc_html($main_category->name); ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>

                        <?php if (!empty($first_column_posts)): ?>
                            <?php foreach ($first_column_posts as $post_item): ?>
                                <?php $render_small_card($post_item); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-posts-message">
                                <p>No additional posts for this section.</p>
                            </div>
                        <?php endif; ?>
                    </main>
                </div>

                <div class="col-lg-4">
                    <main class="axil-content medium-section">
                        <?php if (!empty($second_column_posts)): ?>
                            <?php foreach ($second_column_posts as $post_item): ?>
                                <?php $render_small_card($post_item); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-posts-message">
                                <p>No additional posts for this section.</p>
                            </div>
                        <?php endif; ?>

                        <?php if ($total_pages > 1): ?>
                            <div class="pagination-wrapper mt-4">
                                <div class="pagination">
                                    <?php if ($paged > 1): ?>
                                        <a class="prev page-numbers"
                                            href="<?php echo esc_url($build_page_url($paged - 1)); ?>">&laquo; Previous</a>
                                    <?php endif; ?>

                                    <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                                        <?php if ($page === (int) $paged): ?>
                                            <span class="page-numbers current"><?php echo $page; ?></span>
                                        <?php else: ?>
                                            <a class="page-numbers"
                                                href="<?php echo esc_url($build_page_url($page)); ?>"><?php echo $page; ?></a>
                                        <?php endif; ?>
                                    <?php endfor; ?>

                                    <?php if ($paged < $total_pages): ?>
                                        <a class="next page-numbers" href="<?php echo esc_url($build_page_url($paged + 1)); ?>">Next
                                            &raquo;</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </main>
                </div>

                <div class="col-lg-4">
                    <main class="axil-content medium-section">
                        <?php if (!empty($third_column_posts)): ?>
                            <?php foreach ($third_column_posts as $post_item): ?>
                                <?php $render_small_card($post_item); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-posts-message">
                                <p>No additional posts for this section.</p>
                            </div>
                        <?php endif; ?>
                    </main>
                </div>
            <?php else: ?>
                <div class="col-12">
                    <div class="no-posts-message">
                        <p>No posts found in this category.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- End of .row -->
    </div>
    <!-- End of .container -->
</section>

<?php include 'parts/shared/footer.php'; ?>
<?php include 'parts/shared/html-footer.php'; ?>