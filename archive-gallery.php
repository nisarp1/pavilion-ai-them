<?php
// Get pagination parameters
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts_per_page = 15; // Show 15 posts per page

// Query gallery posts
$gallery_posts = new WP_Query(array(
    'post_type' => 'gallery',
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => false,
    'update_post_meta_cache' => true,
    'update_post_term_cache' => true,
    'ignore_sticky_posts' => true
));
?>

<?php include 'parts/shared/html-header.php'; ?>
<?php include 'parts/shared/header.php'; ?>

<script>
document.body.classList.add('gallery-archive-page');
</script>

<!-- Gallery Title Section -->
<div class="container px-4">
    <div class="category-header-section">
        <div class="category-header-wrapper">
            <h1 class="category-header-title">Gallery Archives</h1>
        </div>
    </div>
</div>

<section class="post-section section-gap no-padding-top">

    <div class="container px-4">
        <div class="row">
            <?php
            if ($gallery_posts->have_posts()) :
                // Get all posts into an array for better control
                $all_posts = array();
                while ($gallery_posts->have_posts()) : $gallery_posts->the_post();
                    $all_posts[] = get_the_ID(); // Store post ID instead of post object
                endwhile;
                wp_reset_postdata();
                
                // Display first post as big featured card
                if (!empty($all_posts)) :
                    $first_post_id = $all_posts[0];
                    $first_post = get_post($first_post_id);
                    setup_postdata($first_post);
            ?>
            <!-- First Column - Big Card + 4 Small Cards - col-4 -->
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
                                        <span class="post-cat cat-btn bg-primary-color">GALLERY</span>
                                    </div>
                                    <div class="post-time ms-3 flex-shrink-0">
                                        <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="post-image-wrapper">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', array('class' => 'img-fluid img-border-radius', 'alt' => get_the_title())); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>" class="img-fluid img-border-radius">
                                <?php endif; ?>
                                <div class="post-cat-group badge-on-image">
                                    <span class="post-cat cat-btn bg-primary-color">GALLERY</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- End Big Featured Card -->
                    
                    <!-- 4 Small Cards Below Big Card -->
                    <?php if (count($all_posts) > 1) : ?>
                    <?php
                        // Get next 4 posts for first column
                        $first_column_posts = array_slice($all_posts, 1, 4);
                        foreach ($first_column_posts as $post_id) :
                            $post = get_post($post_id);
                            setup_postdata($post);
                        ?>
                        <!-- Small Card -->
                        <a href="<?php the_permalink(); ?>" class="block-link mb-3">
                        <div class="media post-block small-block">
                            <div class="post-image-wrapper">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                                <?php else : ?>
                                    <img class="img-fluid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="media-body">
                                <a href="<?php the_permalink(); ?>" class="block-link">
                                    <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                </a>
                                <div class="d-flex align-items-center flex-nowrap">
                                    <div class="post-cat-group flex-shrink-0">
                                        <span class="post-cat color-blue-three">GALLERY</span>
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
                    <?php if (count($all_posts) > 5) : ?>
                    <?php
                        // Get remaining posts for second column (posts 6-12)
                        $second_column_posts = array_slice($all_posts, 5, 7);
                        foreach ($second_column_posts as $post_id) :
                            $post = get_post($post_id);
                            setup_postdata($post);
                        ?>
                        <!-- Small Card -->
                        <a href="<?php the_permalink(); ?>" class="block-link mb-3">
                        <div class="media post-block small-block">
                            <div class="post-image-wrapper">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                                <?php else : ?>
                                    <img class="img-fluid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="media-body">
                                <a href="<?php the_permalink(); ?>" class="block-link">
                                    <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                </a>
                                <div class="d-flex align-items-center flex-nowrap">
                                    <div class="post-cat-group flex-shrink-0">
                                        <span class="post-cat color-blue-three">GALLERY</span>
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
                    <?php else : ?>
                        <div class="no-posts-message">
                            <p>No additional galleries for this column.</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Pagination for Second Column -->
                    <?php if ($gallery_posts->max_num_pages > 1) : ?>
                    <div class="pagination-wrapper mt-4">
                        <?php
                        echo paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'current' => $paged,
                            'total' => $gallery_posts->max_num_pages,
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
                    <?php if (count($all_posts) > 12) : ?>
                    <?php
                        // Get remaining posts for third column (posts 13-15)
                        $third_column_posts = array_slice($all_posts, 12, 3);
                        foreach ($third_column_posts as $post_id) :
                            $post = get_post($post_id);
                            setup_postdata($post);
                        ?>
                        <!-- Small Card -->
                        <a href="<?php the_permalink(); ?>" class="block-link mb-3">
                            <div class="media post-block small-block">
                                <div class="post-image-wrapper">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                                    <?php else : ?>
                                        <img class="img-fluid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="media-body">
                                    <a href="<?php the_permalink(); ?>" class="block-link">
                                        <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                    </a>
                                    <div class="d-flex align-items-center flex-nowrap">
                                        <div class="post-cat-group flex-shrink-0">
                                            <span class="post-cat color-blue-three">GALLERY</span>
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
                    <?php else : ?>
                        <div class="no-posts-message">
                            <p>No additional galleries for this column.</p>
                        </div>
                    <?php endif; ?>
                </main>
            </div>
            <!-- End Third Column -->
            
            <?php
                endif; // End of if (!empty($all_posts))
            else :
            ?>
            <div class="col-lg-8">
                <div class="no-posts-message">
                    <p>No galleries found.</p>
                </div>
            </div>
            <?php 
            endif; // End of if ($gallery_posts->have_posts())
            ?>
        </div>
        <!-- End of .row -->
    </div>
    <!-- End of .container -->
</section>

<?php include 'parts/shared/footer.php'; ?>
<?php include 'parts/shared/html-footer.php'; ?> 