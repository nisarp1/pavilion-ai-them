<?php include 'parts/shared/html-header.php'; ?>
<?php include 'parts/shared/header.php'; ?>

<?php while (have_posts()) : the_post(); ?>

<!-- Banner starts -->
<section class="banner section-gap">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="post-title-wrapper">
                    <div class="d-flex align-items-center flex-nowrap">
                        <div class="post-cat-group flex-shrink-0">
                            <?php
                            $categories = get_filtered_categories();
                            if (!empty($categories)) {
                                $cat_count = 0;
                                foreach ($categories as $category) {
                                    if ($cat_count > 0) echo ', ';
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                    $cat_count++;
                                    if ($cat_count >= 3) break; // Limit to 3 categories
                                }
                            }
                            ?>
                        </div>
                        <div class="post-time ms-3 flex-shrink-0">
                            <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                        </div>
                    </div>

                    <h2 class="axil-post-title hover-line"><?php the_title(); ?></h2>
                    <div class="post-metas banner-post-metas">
                        <ul class="list-inline">
                            <li><i class="dot">.</i><?php echo get_the_date(); ?></li>
                            <li><a href="#"><i class="feather icon-activity"></i><?php echo get_post_views(get_the_ID()); ?> Views</a></li>
                            <li><a href="#"><i class="feather icon-share-2"></i><?php echo get_share_count(); ?> Shares</a></li>
                        </ul>
                    </div>
                    <!-- End of .post-metas -->

                </div>
                <!-- End of .post-title-wrapper -->
            </div>
            <!-- End of .col-lg-6 -->

            <div class="col-lg-6">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                <?php else : ?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>" class="img-fluid">
                <?php endif; ?>
            </div>
        </div>
        <!-- End of .row -->
    </div>
    <!-- End of .container -->
</section>
<!-- End of .banner -->

<!-- post-single-wrapper starts -->
<div class="post-single-wrapper section-gap">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <main class="site-main">
                    <article class="post-details">
                        <div class="single-blog-wrapper">
                            <div class="post-details__social-share mt-2 sticky-social-share">
                                <ul class="social-share social-share__with-bg social-share__vertical">
                                    <li>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="fab fa-x-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' - ' . get_permalink()); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="copyToClipboard(); return false;" title="Copy Link">
                                            <i class="fas fa-link"></i>
                                        </a>
                                    </li>
                                </ul>
                                <!-- End of .social-share__no-bg -->
                            </div>
                            <!-- End of .social-share -->


                            <?php the_content(); ?>
                            

                        </div>
                        <!-- End of .single-blog-wrapper -->
                    </article>
                    <!-- End of .post-details -->





                    <!-- Post Navigation -->
                    <?php
                    // Get all published posts ordered by date
                    $all_posts = get_posts(array(
                        'post_type' => 'post',
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                    
                    $current_post_id = get_the_ID();
                    $current_post_index = -1;
                    
                    // Find current post index
                    foreach ($all_posts as $index => $post) {
                        if ($post->ID == $current_post_id) {
                            $current_post_index = $index;
                            break;
                        }
                    }
                    
                    // Get previous and next posts manually
                    $prev_post = null;
                    $next_post = null;
                    
                    if ($current_post_index !== -1) {
                        // Previous post (newer date)
                        if ($current_post_index < count($all_posts) - 1) {
                            $prev_post = $all_posts[$current_post_index + 1];
                        }
                        
                        // Next post (older date)
                        if ($current_post_index > 0) {
                            $next_post = $all_posts[$current_post_index - 1];
                        }
                    }
                    

                    
                    ?>
                    <div class="post-navigation m-b-xs-60">
                        <div class="row">
                            <div class="col-lg-6">
                                <?php
                                if (!empty($prev_post)) :
                                    $prev_thumbnail_id = get_post_thumbnail_id($prev_post->ID);
                                    $prev_image_url = '';
                                    if ($prev_thumbnail_id) {
                                        $prev_image_url = wp_get_attachment_image_url($prev_thumbnail_id, 'medium');
                                    } else {
                                        $prev_image_url = get_stylesheet_directory_uri() . '/assets/images/new/hero.jpg';
                                    }
                                ?>
                                <div class="nav-previous">
                                    <a href="<?php echo get_permalink($prev_post->ID); ?>" class="d-flex align-items-center">
                                        <div class="nav-image-wrapper">
                                            <img src="<?php echo esc_url($prev_image_url); ?>" alt="<?php echo esc_attr($prev_post->post_title); ?>" class="img-fluid">
                                        </div>
                                        <div class="nav-content">
                                            <span class="nav-label">Previous Post</span>
                                            <h4 class="nav-title"><?php echo esc_html($prev_post->post_title); ?></h4>
                                        </div>
                                    </a>
                                </div>
                                <?php else : ?>
                                <div class="nav-previous nav-disabled">
                                    <div class="d-flex align-items-center">
                                        <div class="nav-image-wrapper">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="No previous post" class="img-fluid">
                                        </div>
                                        <div class="nav-content">
                                            <span class="nav-label">Previous Post</span>
                                            <h4 class="nav-title">No previous post</h4>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-lg-6">
                                <?php
                                if (!empty($next_post)) :
                                    $next_thumbnail_id = get_post_thumbnail_id($next_post->ID);
                                    $next_image_url = '';
                                    if ($next_thumbnail_id) {
                                        $next_image_url = wp_get_attachment_image_url($next_thumbnail_id, 'medium');
                                    } else {
                                        $next_image_url = get_stylesheet_directory_uri() . '/assets/images/new/hero.jpg';
                                    }
                                ?>
                                <div class="nav-next">
                                    <a href="<?php echo get_permalink($next_post->ID); ?>" class="d-flex align-items-center">
                                        <div class="nav-content">
                                            <span class="nav-label">Next Post</span>
                                            <h4 class="nav-title"><?php echo esc_html($next_post->post_title); ?></h4>
                                        </div>
                                        <div class="nav-image-wrapper">
                                            <img src="<?php echo esc_url($next_image_url); ?>" alt="<?php echo esc_attr($next_post->post_title); ?>" class="img-fluid">
                                        </div>
                                    </a>
                                </div>
                                <?php else : ?>
                                <div class="nav-next nav-disabled">
                                    <div class="d-flex align-items-center">
                                        <div class="nav-content">
                                            <span class="nav-label">Next Post</span>
                                            <h4 class="nav-title">No next post</h4>
                                        </div>
                                        <div class="nav-image-wrapper">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="No next post" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- End of Post Navigation -->

                    <!-- Alternative: Show Recent Posts if no navigation -->
                    <?php if (count($all_posts) <= 1) : ?>
                    <div class="post-navigation m-b-xs-60">
                        <div class="section-title m-b-xs-30">
                            <h4 class="axil-title">Recent Posts</h4>
                        </div>
                        <div class="row">
                            <?php
                            $recent_posts = get_posts(array(
                                'post_type' => 'post',
                                'post_status' => 'publish',
                                'posts_per_page' => 4,
                                'orderby' => 'date',
                                'order' => 'DESC',
                                'post__not_in' => array($current_post_id)
                            ));
                            
                            foreach ($recent_posts as $post) :
                                $thumbnail_id = get_post_thumbnail_id($post->ID);
                                $image_url = '';
                                if ($thumbnail_id) {
                                    $image_url = wp_get_attachment_image_url($thumbnail_id, 'medium');
                                } else {
                                    $image_url = get_stylesheet_directory_uri() . '/assets/images/new/hero.jpg';
                                }
                            ?>
                            <div class="col-lg-6 col-md-6 m-b-xs-20">
                                <div class="nav-previous">
                                    <a href="<?php echo get_permalink($post->ID); ?>" class="d-flex align-items-center">
                                        <div class="nav-image-wrapper">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($post->post_title); ?>" class="img-fluid">
                                        </div>
                                        <div class="nav-content">
                                            <span class="nav-label">Recent Post</span>
                                            <h4 class="nav-title"><?php echo esc_html($post->post_title); ?></h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="about-author m-b-xs-60">
                        <div class="media">
                            <div class="media-body">
                                <div class="media-body-title">
                                    <h3><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a></h3>
                                </div>
                                <!-- End of .media-body-title -->

                                <div class="media-body-content">
                                    <p>At 29 years old, my favorite compliment is being told that I look
                                        like my
                                        mom.
                                        Seeing myself in her image, like this daughter up top, makes me so
                                        proud
                                        of
                                        how
                                        far I’ve come, and so thankful for where I come from.</p>
                                    <ul class="social-share social-share__with-bg">
                                        <li>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>"
                                                target="_blank"
                                                rel="noopener noreferrer">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>"
                                                target="_blank"
                                                onclick="window.open(this.href, 'twitter-share', 'width=580,height=296'); return false;">
                                                <i class="fab fa-x-twitter"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' - ' . get_permalink()); ?>"
                                                target="_blank"
                                                onclick="window.open(this.href, 'whatsapp-share', 'width=580,height=296'); return false;">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" onclick="copyToClipboard(); return false;" title="Copy Link">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        </li>
                                    </ul>
                                    <!-- End of .social-share__no-bg -->
                                </div>
                                <!-- End of .media-body-content -->
                            </div>
                        </div>
                    </div>
                    <!-- End of .about-author -->

                    <!-- Facebook Comments -->
                    <div class="facebook-comments-section m-b-xs-60">
                        <div class="comment-box">
                            <h2>Comments</h2>
                        </div>
                        <!-- End of .comment-box -->
                        
                        <div class="fb-comments" 
                             data-href="<?php echo get_permalink(); ?>" 
                             data-width="100%" 
                             data-numposts="5"
                             data-colorscheme="light"
                             data-order-by="social">
                        </div>
                    </div>
                    <!-- End of Facebook Comments -->

                    <!-- In Case You Missed It Section -->
                    <div class="missed-it-section m-b-xs-60">
                        <div class="section-title m-b-xs-30">
                            <h4 class="axil-title">In Case You Missed It</h4>
                        </div>
                        <div class="row">
                            <?php
                            // Get popular and trending articles from last 30 days (more flexible)
                            $thirty_days_ago = date('Y-m-d H:i:s', strtotime('-30 days'));
                            
                            // Get popular posts (based on views, comments, shares)
                            $popular_posts = get_posts(array(
                                'post_type' => 'post',
                                'post_status' => 'publish',
                                'posts_per_page' => 6,
                                'orderby' => 'meta_value_num',
                                'meta_key' => 'post_views_count',
                                'order' => 'DESC',
                                'date_query' => array(
                                    array(
                                        'after' => $thirty_days_ago
                                    )
                                ),
                                'post__not_in' => array($current_post_id)
                            ));
                            
                            // If no popular posts found, get recent posts
                            if (empty($popular_posts)) {
                                $popular_posts = get_posts(array(
                                    'post_type' => 'post',
                                    'post_status' => 'publish',
                                    'posts_per_page' => 6,
                                    'orderby' => 'date',
                                    'order' => 'DESC',
                                    'post__not_in' => array($current_post_id)
                                ));
                            }
                            
                            // Get trending posts (recent posts with engagement)
                            $trending_posts = get_posts(array(
                                'post_type' => 'post',
                                'post_status' => 'publish',
                                'posts_per_page' => 6,
                                'orderby' => 'date',
                                'order' => 'DESC',
                                'date_query' => array(
                                    array(
                                        'after' => $thirty_days_ago
                                    )
                                ),
                                'post__not_in' => array($current_post_id)
                            ));
                            
                            // If no trending posts found, get more recent posts
                            if (empty($trending_posts)) {
                                $trending_posts = get_posts(array(
                                    'post_type' => 'post',
                                    'post_status' => 'publish',
                                    'posts_per_page' => 6,
                                    'orderby' => 'date',
                                    'order' => 'DESC',
                                    'post__not_in' => array($current_post_id)
                                ));
                            }
                            
                            // Combine and shuffle the posts
                            $all_missed_posts = array_merge($popular_posts, $trending_posts);
                            $all_missed_posts = array_unique($all_missed_posts, SORT_REGULAR);
                            $all_missed_posts = array_slice($all_missed_posts, 0, 8);
                            

                            
                            foreach ($all_missed_posts as $post) :
                                $thumbnail_id = get_post_thumbnail_id($post->ID);
                                $image_url = '';
                                if ($thumbnail_id) {
                                    $image_url = wp_get_attachment_image_url($thumbnail_id, 'medium');
                                } else {
                                    $image_url = get_stylesheet_directory_uri() . '/assets/images/new/hero.jpg';
                                }
                                
                                $categories = get_filtered_categories($post->ID);
                                $category_links = array();
                                if (!empty($categories)) {
                                    foreach ($categories as $category) {
                                        $category_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                    }
                                }
                            ?>
                            <div class="col-lg-6 col-md-6 m-b-xs-20">
                                <div class="nav-previous">
                                    <a href="<?php echo get_permalink($post->ID); ?>" class="d-flex align-items-center">
                                        <div class="nav-image-wrapper">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($post->post_title); ?>" class="img-fluid">
                                        </div>
                                        <div class="nav-content">
                                            <span class="nav-label">Recent Article</span>
                                            <h4 class="nav-title"><?php echo esc_html($post->post_title); ?></h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- End of In Case You Missed It Section -->

                    <!-- End of .post-navigation -->
                </main>
                <!-- End of main -->
            </div>
            <!--End of .col-auto  -->

            <?php include 'parts/shared/sidebar.php'; ?>
        </div>
        <!-- End of .row -->
    </div>
    <!-- End of .container -->
</div>
<!-- End of .post-single-wrapper -->

<!-- Image Gallery Section -->
<section class="image-gallery p-b-xs-30 post-section section-gap">
    <div class="container">
        <div class="section-title m-b-xs-10">
            <a href="#" class="d-block">
                <h2 class="axil-title">IMAGE GALLERY</h2>
            </a>
        </div>
        <!-- End of .section-title -->
        <div class="grid-wrapper">
            <div class="row">
                <?php
                // Get 3 gallery posts
                $gallery_posts = new WP_Query(array(
                    'post_type' => 'gallery',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));

                if ($gallery_posts->have_posts()) :
                    $gallery_count = 1;
                    while ($gallery_posts->have_posts()) : $gallery_posts->the_post();
                ?>
                <div class="col-lg-4 col-md-4">
                    <div class="axil-img-container flex-height-container gallery-container__type-2 m-b-xs-30" data-gallery="gallery-<?php echo get_the_ID(); ?>">
                        <div class="gallery-image-wrapper d-block h-100">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large', array('class' => 'w-100', 'alt' => get_the_title())); ?>
                            <?php else : ?>
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>" class="w-100">
                            <?php endif; ?>
                            <div class="grad-overlay grad-overlay__transparent"></div>
                            <div class="gallery-icon gallery-play-btn">
                                <i class="fas fa-expand"></i>
                            </div>
                        </div>
                        <div class="gallery-overlay">
                            <div class="post-cat-group badge-on-image">
                                <span class="post-cat cat-btn btn-big bg-primary-color">GALLERY</span>
                            </div>
                        </div>
                        <div class="media post-block grad-overlay__transparent position-absolute">
                            <div class="media-body media-body__big">
                                <div class="axil-media-bottom mt-auto">
                                    <h3 class="axil-post-title hover-line"><?php the_title(); ?></h3>
                                </div>
                            </div>
                            <!-- End of .media-body -->
                        </div>
                        <!-- End of .post-block -->
                    </div>
                </div>
                
                <!-- Hidden Gallery Slides for Modal -->
                <div style="display: none;">
                    <?php
                    // Get gallery images for this post
                    $gallery_images = get_post_meta(get_the_ID(), '_gallery_images', true);
                    if (!empty($gallery_images) && is_array($gallery_images)) {
                        foreach ($gallery_images as $image_data) {
                            if (!empty($image_data['image_id'])) {
                                $image_url = wp_get_attachment_image_url($image_data['image_id'], 'large');
                                $image_alt = get_post_meta($image_data['image_id'], '_wp_attachment_image_alt', true);
                                $caption = !empty($image_data['caption']) ? $image_data['caption'] : '';
                                ?>
                                <div class="gallery-slide" data-gallery="gallery-<?php echo get_the_ID(); ?>">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="gallery-slide-image">
                                    <?php if (!empty($caption)) : ?>
                                        <div class="gallery-slide-caption"><?php echo esc_html($caption); ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php
                            }
                        }
                    } else {
                        // Fallback to featured image if no gallery images
                        if (has_post_thumbnail()) {
                            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
                            ?>
                            <div class="gallery-slide" data-gallery="gallery-<?php echo get_the_ID(); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>" class="gallery-slide-image">
                                <div class="gallery-slide-caption"><?php the_title(); ?></div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <?php
                        $gallery_count++;
                    endwhile;
                    wp_reset_postdata();
                else:
                    // Fallback static content if no gallery posts found
                ?>
                <div class="col-lg-4 col-md-4">
                    <div class="axil-img-container flex-height-container gallery-container__type-2 m-b-xs-30" data-gallery="gallery-fallback-1">
                        <div class="gallery-image-wrapper d-block h-100">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/vs.jpg" alt="VS Achuthanandan Gallery" class="w-100">
                            <div class="grad-overlay grad-overlay__transparent"></div>
                            <div class="gallery-icon gallery-play-btn">
                                <i class="fas fa-expand"></i>
                            </div>
                        </div>
                        <div class="gallery-overlay">
                            <div class="post-cat-group badge-on-image">
                                <span class="post-cat cat-btn btn-big bg-primary-color">GALLERY</span>
                            </div>
                        </div>
                        <div class="media post-block grad-overlay__transparent position-absolute">
                            <div class="media-body media-body__big">
                                <div class="axil-media-bottom mt-auto">
                                    <h3 class="axil-post-title hover-line">ജനനായകന്റെ അന്ത്യയാത്ര: വി.എസ്. അച്യുതാനന്ദൻ വിട</h3>
                                </div>
                            </div>
                            <!-- End of .media-body -->
                        </div>
                        <!-- End of .post-block -->
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="axil-img-container flex-height-container gallery-container__type-2 m-b-xs-30" data-gallery="gallery-fallback-2">
                        <div class="gallery-image-wrapper d-block h-100">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/dubai1.jpg" alt="Dubai Festival Gallery" class="w-100">
                            <div class="grad-overlay grad-overlay__transparent"></div>
                            <div class="gallery-icon gallery-play-btn">
                                <i class="fas fa-expand"></i>
                            </div>
                        </div>
                        <div class="gallery-overlay">
                            <div class="post-cat-group badge-on-image">
                                <span class="post-cat cat-btn btn-big bg-primary-color">GALLERY</span>
                            </div>
                        </div>
                        <div class="media post-block grad-overlay__transparent position-absolute">
                            <div class="media-body media-body__big">
                                <div class="axil-media-bottom mt-auto">
                                    <h3 class="axil-post-title hover-line">വർണ്ണവിസ്മയങ്ങളുടെ ദുബായ്: ഫെസ്റ്റിവൽ കാഴ്ചകൾ</h3>
                                </div>
                            </div>
                            <!-- End of .media-body -->
                        </div>
                        <!-- End of .post-block -->
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="axil-img-container flex-height-container gallery-container__type-2 m-b-xs-30" data-gallery="gallery-fallback-3">
                        <div class="gallery-image-wrapper d-block h-100">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/uae.jpg" alt="UAE National Day Gallery" class="w-100">
                            <div class="grad-overlay grad-overlay__transparent"></div>
                            <div class="gallery-icon gallery-play-btn">
                                <i class="fas fa-expand"></i>
                            </div>
                        </div>
                        <div class="gallery-overlay">
                            <div class="post-cat-group badge-on-image">
                                <span class="post-cat cat-btn btn-big bg-primary-color">GALLERY</span>
                            </div>
                        </div>
                        <div class="media post-block grad-overlay__transparent position-absolute">
                            <div class="media-body media-body__big">
                                <div class="axil-media-bottom mt-auto">
                                    <h3 class="axil-post-title hover-line">ചതുർവർണ്ണ പതാകയേന്തി ഒരു ജനത: ദേശീയ ദിനാഘോഷം</h3>
                                </div>
                            </div>
                            <!-- End of .media-body -->
                        </div>
                        <!-- End of .post-block -->
                    </div>
                </div>
                
                <!-- Hidden Gallery Slides for Modal (Fallback) -->
                <div style="display: none;">
                    <div class="gallery-slide" data-gallery="gallery-fallback-1">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/vs.jpg" alt="VS Achuthanandan Gallery" class="gallery-slide-image">
                        <div class="gallery-slide-caption">ജനനായകന്റെ അന്ത്യയാത്ര: വി.എസ്. അച്യുതാനന്ദൻ വിട</div>
                    </div>
                    <div class="gallery-slide" data-gallery="gallery-fallback-2">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/dubai1.jpg" alt="Dubai Festival Gallery" class="gallery-slide-image">
                        <div class="gallery-slide-caption">വർണ്ണവിസ്മയങ്ങളുടെ ദുബായ്: ഫെസ്റ്റിവൽ കാഴ്ചകൾ</div>
                    </div>
                    <div class="gallery-slide" data-gallery="gallery-fallback-3">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/uae.jpg" alt="UAE National Day Gallery" class="gallery-slide-image">
                        <div class="gallery-slide-caption">ചതുർവർണ്ണ പതാകയേന്തി ഒരു ജനത: ദേശീയ ദിനാഘോഷം</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <!-- End of .row -->
        </div>
        <!-- End of .grid-wrapper -->
    </div>
    <!-- End of .container -->
</section>

<?php endwhile; ?>

<?php include 'parts/shared/footer.php'; ?>

<script>
    function copyToClipboard() {
        const currentUrl = window.location.href;
        const copyBtn = document.getElementById('copyLinkBtn');
        const copyText = document.getElementById('copyText');

        // Create a temporary input element
        const tempInput = document.createElement('input');
        tempInput.value = currentUrl;
        document.body.appendChild(tempInput);

        // Select and copy the text
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        try {
            document.execCommand('copy');
            // Update button text to show success
            copyText.textContent = 'Copied!';
            copyBtn.style.backgroundColor = '#28a745'; // Green color for success

            // Reset button after 2 seconds
            setTimeout(() => {
                copyText.textContent = 'Copy Link';
                copyBtn.style.backgroundColor = ''; // Reset to original color
            }, 2000);
        } catch (err) {
            console.error('Failed to copy: ', err);
            copyText.textContent = 'Failed!';
            copyBtn.style.backgroundColor = '#dc3545'; // Red color for error

            // Reset button after 2 seconds
            setTimeout(() => {
                copyText.textContent = 'Copy Link';
                copyBtn.style.backgroundColor = ''; // Reset to original color
            }, 2000);
        }

        // Remove the temporary input element
        document.body.removeChild(tempInput);
    }
</script>



<?php include 'parts/shared/html-footer.php'; ?>