<?php
// Get pagination parameters
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts_per_page = 15; // Show 15 posts per page

// Debug information
echo "<!-- DEBUG: Archive page detected -->";
echo "<!-- DEBUG: Current page: " . $paged . " -->";

$page_title = 'Latest News';

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
            // Get recent posts from all categories
            $archive_posts = new WP_Query(array(
                'posts_per_page' => 15,
                'paged' => $paged,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC'
            ));

            if ($archive_posts->have_posts()):
                // Get all posts into an array for better control
                $all_posts = array();
                while ($archive_posts->have_posts()):
                    $archive_posts->the_post();
                    $all_posts[] = get_post();
                endwhile;
                wp_reset_postdata();

                // Display first post as big featured card
                if (!empty($all_posts)):
                    $first_post = $all_posts[0];
                    setup_postdata($first_post);
                    ?>
                    <!-- First Column - Big Card + 5 Small Cards - col-4 -->
                    <div class="col-lg-4">
                        <main class="axil-content medium-section">
                            <!-- Big Featured Card -->
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
                                                        if ($cat_count >= 3)
                                                            break; // Limit to 3 categories
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
                            <!-- End Big Featured Card -->

                            <!-- 4 Small Cards Below Big Card -->
                            <?php if (count($all_posts) > 1): ?>
                                <?php
                                // Get next 4 posts for first column
                                $first_column_posts = array_slice($all_posts, 1, 4);
                                foreach ($first_column_posts as $post):
                                    setup_postdata($post);
                                    ?>
                                    <!-- Small Card -->
                                    <a href="<?php the_permalink(); ?>" class="block-link mb-3">
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
                                                <a href="<?php the_permalink(); ?>" class="block-link">
                                                    <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                                </a>
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
                                                                if ($cat_count >= 2)
                                                                    break; // Limit to 2 categories for smaller blocks
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
                                    <!-- End Small Card -->
                                    <?php
                                endforeach;
                                wp_reset_postdata();
                                ?>
                            <?php endif; ?>
                        </main>
                    </div>
                    <!-- End First Column -->

                    <!-- Second Column - 7 Small Cards + Pagination - col-4 -->
                    <div class="col-lg-4">
                        <main class="axil-content medium-section">
                            <?php
                            echo "<!-- DEBUG: Total posts: " . count($all_posts) . " -->";
                            if (count($all_posts) > 5): ?>
                                <?php
                                // Get remaining posts for second column (posts 6-12)
                                $second_column_posts = array_slice($all_posts, 5, 7);
                                echo "<!-- DEBUG: Second column posts: " . count($second_column_posts) . " -->";
                                foreach ($second_column_posts as $post):
                                    setup_postdata($post);
                                    ?>
                                    <!-- Small Card -->
                                    <a href="<?php the_permalink(); ?>" class="block-link mb-3">
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
                                                <a href="<?php the_permalink(); ?>" class="block-link">
                                                    <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                                </a>
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
                                                                if ($cat_count >= 2)
                                                                    break; // Limit to 2 categories for smaller blocks
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
                                    <!-- End Small Card -->
                                    <?php
                                endforeach;
                                wp_reset_postdata();
                                ?>
                            <?php else: ?>
                                <div class="no-posts-message">
                                    <p>No additional posts for this column.</p>
                                </div>
                            <?php endif; ?>

                            <!-- Pagination for Second Column -->
                            <?php if ($archive_posts->max_num_pages > 1): ?>
                                <div class="pagination-wrapper mt-4">
                                    <?php
                                    echo paginate_links(array(
                                        'base' => add_query_arg('paged', '%#%'),
                                        'format' => '',
                                        'current' => $paged,
                                        'total' => $archive_posts->max_num_pages,
                                        'prev_text' => '&laquo; Previous',
                                        'next_text' => 'Next &raquo;'
                                    ));
                                    ?>
                                </div>
                            <?php endif; ?>
                        </main>
                    </div>
                    <!-- End Second Column -->

                    <!-- Third Column - 3 Small Cards - col-4 -->
                    <div class="col-lg-4">
                        <main class="axil-content medium-section">
                            <!-- 3 Small Cards -->
                            <?php if (count($all_posts) > 12): ?>
                                <?php
                                // Get remaining posts for third column (posts 13-15)
                                $third_column_posts = array_slice($all_posts, 12, 3);
                                foreach ($third_column_posts as $post):
                                    setup_postdata($post);
                                    ?>
                                    <!-- Small Card -->
                                    <a href="<?php the_permalink(); ?>" class="block-link mb-3">
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
                                                <a href="<?php the_permalink(); ?>" class="block-link">
                                                    <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                                </a>
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
                                                                if ($cat_count >= 2)
                                                                    break; // Limit to 2 categories for smaller blocks
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
                                    <!-- End Small Card -->
                                    <?php
                                endforeach;
                                wp_reset_postdata();
                                ?>
                            <?php else: ?>
                                <div class="no-posts-message">
                                    <p>No additional posts for this column.</p>
                                </div>
                            <?php endif; ?>
                        </main>
                    </div>
                    <!-- End Third Column -->

                    <?php
                endif; // End of if (!empty($all_posts))
            else:
                ?>
                <div class="col-lg-8">
                    <div class="no-posts-message">
                        <p>No posts found.</p>
                    </div>
                </div>
                <?php
            endif; // End of if ($archive_posts->have_posts())
            ?>
        </div>
        <!-- End of .row -->
    </div>
    <!-- End of .container -->
</section>

<?php include 'parts/shared/footer.php'; ?>
<?php include 'parts/shared/html-footer.php'; ?>