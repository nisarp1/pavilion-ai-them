<?php
/**
 * Latest Posts Page
 * Shows the most recent published articles across all categories.
 */

$paged = get_query_var('paged');
if (!$paged) {
    $paged = isset($_GET['paged']) ? (int) $_GET['paged'] : 1;
}
$paged = max(1, (int) $paged);
$posts_per_page = 15;
$page_title = 'Latest News';

// Fetch latest posts from API
$api_params = array(
    'status' => 'published',
    'page_size' => $posts_per_page,
    'page' => $paged,
);

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

$latest_posts = pavilion_convert_articles_to_posts($articles);
$posts_count = count($latest_posts);

if ($total_count === 0) {
    $total_count = $posts_count + (($paged - 1) * $posts_per_page);
}

$total_pages = $posts_per_page > 0 ? max(1, (int) ceil($total_count / $posts_per_page)) : 1;

$first_post = $posts_count > 0 ? $latest_posts[0] : null;
$first_column_posts = $posts_count > 1 ? array_slice($latest_posts, 1, 4) : array();
$second_column_posts = $posts_count > 5 ? array_slice($latest_posts, 5, 7) : array();
$third_column_posts = $posts_count > 12 ? array_slice($latest_posts, 12, 3) : array();

$latest_base_link = rtrim(home_url('/latest/'), '/');
$build_page_url = function ($page) use ($latest_base_link) {
    $url = $latest_base_link . '/';
    if ($page > 1) {
        $url .= '?paged=' . $page;
    }
    return $url;
};

$render_small_card = function ($post_item, $extra_classes = 'mb-3') {
    setup_postdata($post_item);
    ?>
    <a href="<?php the_permalink(); ?>" class="block-link <?php echo esc_attr(trim($extra_classes)); ?>">
        <div class="media post-block small-block">
            <div class="post-image-wrapper">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                <?php else: ?>
                    <img class="img-fluid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/pavilion.jpg"
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
                                if ($cat_count > 0) {
                                    echo ', ';
                                }
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
?>

<?php include 'parts/shared/html-header.php'; ?>
<?php include 'parts/shared/header.php'; ?>

<script>
    document.body.classList.add('category-page');
</script>

<div class="container px-4">
    <div class="category-header-section">
        <div class="category-header-wrapper">
            <h1 class="category-header-title"><?php echo esc_html($page_title); ?></h1>
        </div>
    </div>
</div>

<section class="post-section section-gap no-padding-top">
    <div class="container px-4">
        <div class="row">
            <?php if ($posts_count > 0): ?>
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
                                                        if ($cat_count > 0) {
                                                            echo ', ';
                                                        }
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
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/pavilion.jpg"
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
                                            <span class="page-numbers current"><?php echo (int) $page; ?></span>
                                        <?php else: ?>
                                            <a class="page-numbers"
                                                href="<?php echo esc_url($build_page_url($page)); ?>"><?php echo (int) $page; ?></a>
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
                        <!-- Advertisement removed -->

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
                        <p>No latest posts available right now.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'parts/shared/footer.php'; ?>
<?php include 'parts/shared/html-footer.php'; ?>