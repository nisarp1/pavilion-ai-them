<?php
// Load core functions
require_once __DIR__ . '/core.php';
?>
<?php include 'parts/shared/html-header.php'; ?>
<?php include 'parts/shared/header.php'; ?>

<?php
$current_post = get_current_post();
if ($current_post) {
    setup_postdata($current_post);
}
?>
<?php if ($current_post) : ?>

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
                                    // Handle both array and object formats
                                    $cat_id = is_array($category) ? (isset($category['id']) ? $category['id'] : null) : (isset($category->id) ? $category->id : (isset($category->term_id) ? $category->term_id : null));
                                    $cat_name = is_array($category) ? (isset($category['name']) ? $category['name'] : '') : (isset($category->name) ? $category->name : '');
                                    if ($cat_id && $cat_name) {
                                        echo '<a href="' . esc_url(get_category_link($cat_id)) . '" class="post-cat color-blue-three">' . esc_html($cat_name) . '</a>';
                                        $cat_count++;
                                        if ($cat_count >= 3) break; // Limit to 3 categories
                                    }
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
                            <li><?php echo get_the_date(); ?></li>
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
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>" class="img-fluid">
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
                                <?php
                                // Get full URL for sharing (with domain)
                                $share_url = get_full_url(get_permalink());
                                $share_title = get_the_title();
                                ?>
                                <ul class="social-share social-share__with-bg social-share__vertical">
                                    <li>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($share_url); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($share_title); ?>&url=<?php echo urlencode($share_url); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="fab fa-x-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://wa.me/?text=<?php echo urlencode($share_title . ' - ' . $share_url); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="copyToClipboard(event); return false;" title="Copy Link">
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
                    $all_posts = get_all_posts(array('posts_per_page' => 1000));
                    
                    $current_post_id = get_the_ID();
                    $current_post_index = -1;
                    
                    // Find current post index
                    foreach ($all_posts as $index => $post_item) {
                        if (isset($post_item['id']) && $post_item['id'] == $current_post_id) {
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
                    
                    // Convert array posts to objects for compatibility
                    if ($prev_post && is_array($prev_post)) {
                        $prev_post_obj = (object)$prev_post;
                        $prev_post_obj->ID = isset($prev_post['id']) ? $prev_post['id'] : null;
                        $prev_post_obj->post_title = isset($prev_post['title']) ? $prev_post['title'] : '';
                        $prev_post = $prev_post_obj;
                    }
                    if ($next_post && is_array($next_post)) {
                        $next_post_obj = (object)$next_post;
                        $next_post_obj->ID = isset($next_post['id']) ? $next_post['id'] : null;
                        $next_post_obj->post_title = isset($next_post['title']) ? $next_post['title'] : '';
                        $next_post = $next_post_obj;
                    }
                    

                    
                    ?>
                    <div class="post-navigation m-b-xs-60">
                        <div class="row">
                            <div class="col-lg-6">
                                <?php
                                if (!empty($prev_post)) :
                                    // Handle both array and object formats
                                    $prev_id = is_array($prev_post) ? (isset($prev_post['id']) ? $prev_post['id'] : null) : (isset($prev_post->ID) ? $prev_post->ID : (isset($prev_post->id) ? $prev_post->id : null));
                                    $prev_title = is_array($prev_post) ? (isset($prev_post['title']) ? $prev_post['title'] : '') : (isset($prev_post->post_title) ? $prev_post->post_title : (isset($prev_post->title) ? $prev_post->title : ''));
                                    $prev_image_url = $prev_id ? get_the_post_thumbnail_url($prev_id, 'medium') : '';
                                ?>
                                <div class="nav-previous">
                                    <a href="<?php echo $prev_id ? get_permalink($prev_id) : '#'; ?>" class="d-flex align-items-center">
                                        <div class="nav-image-wrapper">
                                            <img src="<?php echo esc_url($prev_image_url); ?>" alt="<?php echo esc_attr($prev_title); ?>" class="img-fluid">
                                        </div>
                                        <div class="nav-content">
                                            <span class="nav-label">Previous Post</span>
                                            <h4 class="nav-title"><?php echo esc_html($prev_title); ?></h4>
                                        </div>
                                    </a>
                                </div>
                                <?php else : ?>
                                <div class="nav-previous nav-disabled">
                                    <div class="d-flex align-items-center">
                                        <div class="nav-image-wrapper">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>assets/images/new/hero.jpg" alt="No previous post" class="img-fluid">
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
                                    // Handle both array and object formats
                                    $next_id = is_array($next_post) ? (isset($next_post['id']) ? $next_post['id'] : null) : (isset($next_post->ID) ? $next_post->ID : (isset($next_post->id) ? $next_post->id : null));
                                    $next_title = is_array($next_post) ? (isset($next_post['title']) ? $next_post['title'] : '') : (isset($next_post->post_title) ? $next_post->post_title : (isset($next_post->title) ? $next_post->title : ''));
                                    $next_image_url = $next_id ? get_the_post_thumbnail_url($next_id, 'medium') : '';
                                ?>
                                <div class="nav-next">
                                    <a href="<?php echo $next_id ? get_permalink($next_id) : '#'; ?>" class="d-flex align-items-center">
                                        <div class="nav-content">
                                            <span class="nav-label">Next Post</span>
                                            <h4 class="nav-title"><?php echo esc_html($next_title); ?></h4>
                                        </div>
                                        <div class="nav-image-wrapper">
                                            <img src="<?php echo esc_url($next_image_url); ?>" alt="<?php echo esc_attr($next_title); ?>" class="img-fluid">
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
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>assets/images/new/hero.jpg" alt="No next post" class="img-fluid">
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
                            $recent_posts = get_all_posts(array(
                                'posts_per_page' => 4,
                                'post__not_in' => array($current_post_id)
                            ));
                            
                            foreach ($recent_posts as $post_item) :
                                $image_url = get_the_post_thumbnail_url($post_item['id'], 'medium');
                            ?>
                            <div class="col-lg-6 col-md-6 m-b-xs-20">
                                <div class="nav-previous">
                                    <a href="<?php echo get_permalink($post_item['id']); ?>" class="d-flex align-items-center">
                                        <div class="nav-image-wrapper">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($post_item['title']); ?>" class="img-fluid">
                                        </div>
                                        <div class="nav-content">
                                            <span class="nav-label">Recent Post</span>
                                            <h4 class="nav-title"><?php echo esc_html($post_item['title']); ?></h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php 
                    // Check if author box should be displayed for this post
                    if (should_show_author_box(null, get_the_ID())) : 
                        $author_id = get_the_author_meta('ID');
                        $author_bio = get_the_author_meta('description', $author_id);
                        $author_title = get_user_meta($author_id, 'author_title', true);
                        $author_social = get_author_social_links($author_id);
                        $author_picture = get_author_profile_picture($author_id, 'medium');
                        $author_name = get_the_author_meta('display_name', $author_id);
                    ?>
                    <div class="about-author m-b-xs-60">
                        <div class="media">
                            <?php 
                            // Display author profile picture (only if uploaded)
                            if ($author_picture) : 
                            ?>
                            <div class="author-avatar">
                                <img src="<?php echo esc_url($author_picture); ?>" alt="<?php echo esc_attr($author_name); ?>" />
                            </div>
                            <?php endif; ?>
                            
                            <div class="media-body">
                                <div class="media-body-title">
                                    <h3><a href="<?php echo get_author_posts_url($author_id); ?>"><?php echo esc_html($author_name); ?></a></h3>
                                    <?php if ($author_title) : ?>
                                        <p class="author-title"><?php echo esc_html($author_title); ?></p>
                                    <?php endif; ?>
                                </div>
                                <!-- End of .media-body-title -->

                                <div class="media-body-content">
                                    <?php if ($author_bio) : ?>
                                        <p><?php echo wp_kses_post($author_bio); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($author_social)) : ?>
                                    <ul class="social-share social-share__with-bg">
                                        <?php foreach ($author_social as $platform => $social) : ?>
                                        <li>
                                            <a href="<?php echo esc_url($social['url']); ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                title="<?php echo esc_attr($social['label']); ?>">
                                                <i class="<?php echo esc_attr($social['icon']); ?>"></i>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                    <!-- End of .social-share -->
                                </div>
                                <!-- End of .media-body-content -->
                            </div>
                        </div>
                    </div>
                    <!-- End of .about-author -->
                    <?php endif; ?>

                    <!-- Facebook Comments -->
                    <div class="facebook-comments-section m-b-xs-60">
                        <div class="comment-box">
                            <h2>Comments</h2>
                        </div>
                        <!-- End of .comment-box -->
                        
                        <div class="fb-comments" 
                             data-href="<?php echo esc_url(get_full_url(get_permalink())); ?>" 
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
                            
                            // Get popular posts (based on views) - sort by views descending
                            $all_posts_for_missed = get_all_posts(array('posts_per_page' => 20, 'post__not_in' => array($current_post_id)));
                            usort($all_posts_for_missed, function($a, $b) {
                                $views_a = isset($a['views']) ? $a['views'] : 0;
                                $views_b = isset($b['views']) ? $b['views'] : 0;
                                return $views_b - $views_a;
                            });
                            $popular_posts = array_slice($all_posts_for_missed, 0, 6);
                            
                            // If no popular posts found, get recent posts
                            if (empty($popular_posts)) {
                                $popular_posts = get_all_posts(array(
                                    'posts_per_page' => 6,
                                    'post__not_in' => array($current_post_id)
                                ));
                            }
                            
                            // Get trending posts (recent posts with engagement)
                            $trending_posts = get_all_posts(array(
                                'posts_per_page' => 6,
                                'post__not_in' => array($current_post_id)
                            ));
                            
                            // If no trending posts found, get more recent posts
                            if (empty($trending_posts)) {
                                $trending_posts = get_all_posts(array(
                                    'posts_per_page' => 6,
                                    'post__not_in' => array($current_post_id)
                                ));
                            }
                            
                            // Combine and shuffle the posts
                            $all_missed_posts = array_merge($popular_posts, $trending_posts);
                            $all_missed_posts = array_unique($all_missed_posts, SORT_REGULAR);
                            $all_missed_posts = array_slice($all_missed_posts, 0, 8);
                            

                            
                            foreach ($all_missed_posts as $post_item) :
                                $image_url = get_the_post_thumbnail_url($post_item['id'], 'medium');
                                
                                $categories = get_filtered_categories($post_item['id']);
                                $category_links = array();
                                if (!empty($categories)) {
                                    foreach ($categories as $category) {
                                        // Handle both array and object formats
                                        $cat_id = is_array($category) ? (isset($category['id']) ? $category['id'] : null) : (isset($category->id) ? $category->id : (isset($category->term_id) ? $category->term_id : null));
                                        $cat_name = is_array($category) ? (isset($category['name']) ? $category['name'] : '') : (isset($category->name) ? $category->name : '');
                                        if ($cat_id && $cat_name) {
                                            $category_links[] = '<a href="' . esc_url(get_category_link($cat_id)) . '" class="post-cat color-blue-three">' . esc_html($cat_name) . '</a>';
                                        }
                                    }
                                }
                            ?>
                            <div class="col-lg-6 col-md-6 m-b-xs-20">
                                <div class="nav-previous">
                                    <a href="<?php echo get_permalink($post_item['id']); ?>" class="d-flex align-items-center">
                                        <div class="nav-image-wrapper">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($post_item['title']); ?>" class="img-fluid">
                                        </div>
                                        <div class="nav-content">
                                            <span class="nav-label">Recent Article</span>
                                            <h4 class="nav-title"><?php echo esc_html($post_item['title']); ?></h4>
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
            <!--End of .col-lg-8  -->

            <div class="col-lg-4">
                <?php include 'parts/shared/sidebar.php'; ?>
            </div>
            <!--End of .col-lg-4  -->
        </div>
        <!-- End of .row -->
    </div>
    <!-- End of .container -->
</div>
<!-- End of .post-single-wrapper -->

<!-- Web Stories Section -->
<?php
$resolve_single_story_image = function ($path) {
    if (empty($path)) {
        return get_stylesheet_directory_uri() . '/assets/images/new/hero.jpg';
    }

    if (is_string($path) && strpos($path, 'http') === 0) {
        return $path;
    }

    if (is_numeric($path)) {
        $media_url = pavilion_get_media_url($path);
        if (!empty($media_url)) {
            return $media_url;
        }
    }

    if (is_string($path)) {
        $normalized = ltrim($path, '/');
        if (strpos($normalized, 'media/') === 0) {
            $api_base = rtrim(PAVILION_API_BASE_URL, '/api');
            return $api_base . '/' . $normalized;
        }

        return get_stylesheet_directory_uri() . '/' . $normalized;
    }

    return get_stylesheet_directory_uri() . '/assets/images/new/hero.jpg';
};
?>
<section class="webstories-section p-b-xs-30 post-section section-gap">
    <div class="container">
        <div class="section-title m-b-xs-10">
            <a href="#" class="d-block">
                <h2 class="axil-title">WEB STORIES</h2>
            </a>
        </div>
        <?php
        $single_webstories = get_gallery_posts(12);
        $twenty_four_hours_ago = strtotime('-24 hours');

        if (!empty($single_webstories)) {
            $single_webstories = array_filter($single_webstories, function($story) use ($twenty_four_hours_ago) {
                $published_raw = '';

                if (!empty($story['published_at'])) {
                    $published_raw = $story['published_at'];
                } elseif (!empty($story['date'])) {
                    $published_raw = $story['date'];
                } elseif (!empty($story['created_at'])) {
                    $published_raw = $story['created_at'];
                }

                if (empty($published_raw)) {
                    return false;
                }

                $timestamp = strtotime($published_raw);

                if (!$timestamp) {
                    return false;
                }

                return $timestamp >= $twenty_four_hours_ago;
            });

            $single_webstories = array_values($single_webstories);
            $single_webstories = array_slice($single_webstories, 0, 6);
        }

        if (!empty($single_webstories)) :
        ?>
        <div class="webstories-track" role="list">
            <?php foreach ($single_webstories as $index => $story) :
                $story_id = $story['id'] ?? ($index + 1);
                $story_key = 'webstory-single-' . $story_id;
                $story_title = $story['title'] ?? 'Web Story';
                $story_featured = $story['featured_image'] ?? ($story['featured_image_url'] ?? '');
                $story_image_url = $resolve_single_story_image($story_featured);
                $story_published = $story['published_at'] ?? ($story['date'] ?? ($story['created_at'] ?? ''));
            ?>
            <article class="webstory-card" role="listitem" data-story="<?php echo esc_attr($story_key); ?>" data-cover="<?php echo esc_url($story_image_url); ?>" data-title="<?php echo esc_attr($story_title); ?>" data-published="<?php echo esc_attr($story_published); ?>">
                <button type="button" class="webstory-trigger" aria-label="<?php echo esc_attr($story_title); ?>" data-story="<?php echo esc_attr($story_key); ?>" data-cover="<?php echo esc_url($story_image_url); ?>" data-title="<?php echo esc_attr($story_title); ?>" data-published="<?php echo esc_attr($story_published); ?>">
                    <span class="webstory-thumb">
                        <span class="webstory-thumb-fill" style="background-image: url('<?php echo esc_url($story_image_url); ?>');" aria-hidden="true"></span>
                        <span class="webstory-chip">Web Story</span>
                        <span class="webstory-title"><?php echo esc_html($story_title); ?></span>
                    </span>
                </button>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="webstory-hidden" aria-hidden="true">
            <?php foreach ($single_webstories as $index => $story) :
                $story_id = $story['id'] ?? ($index + 1);
                $story_key = 'webstory-single-' . $story_id;
                $story_title = $story['title'] ?? 'Web Story';
                $story_featured = $story['featured_image'] ?? ($story['featured_image_url'] ?? '');
                $story_fallback_image = $resolve_single_story_image($story_featured);
                $story_images = get_gallery_images($story_id);

                $slides_markup = '';
                $has_valid_slide = false;

                if (!empty($story_images)) {
                    ob_start();
                    foreach ($story_images as $image_data) {
                        $image_url = $image_data['url'] ?? ($image_data['image_url'] ?? '');
                        if (empty($image_url) && !empty($image_data['image_id'])) {
                            $image_url = pavilion_get_media_url($image_data['image_id']);
                        }
                        $image_url = $resolve_single_story_image($image_url);
                        if (empty($image_url)) {
                            continue;
                        }
                        $has_valid_slide = true;
                        $image_caption = $image_data['caption'] ?? '';
                        ?>
                        <div class="webstory-slide" data-story="<?php echo esc_attr($story_key); ?>" data-caption="<?php echo esc_attr($image_caption); ?>">
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($story_title); ?>" class="webstory-slide-image">
                            <?php if (!empty($image_caption)) : ?>
                                <div class="webstory-slide-caption"><?php echo esc_html($image_caption); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                    $slides_markup = ob_get_clean();
                }

                if (!$has_valid_slide) {
                    ob_start();
                    ?>
                    <div class="webstory-slide" data-story="<?php echo esc_attr($story_key); ?>" data-caption="<?php echo esc_attr($story_title); ?>">
                        <img src="<?php echo esc_url($story_fallback_image); ?>" alt="<?php echo esc_attr($story_title); ?>" class="webstory-slide-image">
                        <div class="webstory-slide-caption"><?php echo esc_html($story_title); ?></div>
                    </div>
                    <?php
                    $slides_markup = ob_get_clean();
                }
            ?>
            <div class="webstory-slide-collection" data-story="<?php echo esc_attr($story_key); ?>">
                <?php echo $slides_markup; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else :
            $fallback_stories = array(
                array(
                    'key' => 'webstory-single-fallback-1',
                    'title' => 'ജനനായകന്റെ അന്ത്യയാത്ര: വി.എസ്. അച്യുതാനന്ദൻ വിട',
                    'image' => get_stylesheet_directory_uri() . '/assets/images/new/vs.jpg',
                ),
                array(
                    'key' => 'webstory-single-fallback-2',
                    'title' => 'വർണ്ണവിസ്മയങ്ങളുടെ ദുബായ്: ഫെസ്റ്റിവൽ കാഴ്ചകൾ',
                    'image' => get_stylesheet_directory_uri() . '/assets/images/new/dubai1.jpg',
                ),
                array(
                    'key' => 'webstory-single-fallback-3',
                    'title' => 'ചതുർവർണ്ണ പതാകയേന്തി ഒരു ജനത: ദേശീയ ദിനാഘോഷം',
                    'image' => get_stylesheet_directory_uri() . '/assets/images/new/uae.jpg',
                ),
            );
        ?>
        <div class="webstories-track webstories-track--fallback" role="list">
            <?php foreach ($fallback_stories as $story) : ?>
            <article class="webstory-card" role="listitem" data-story="<?php echo esc_attr($story['key']); ?>" data-cover="<?php echo esc_url($story['image']); ?>" data-title="<?php echo esc_attr($story['title']); ?>">
                <button type="button" class="webstory-trigger" aria-label="<?php echo esc_attr($story['title']); ?>" data-story="<?php echo esc_attr($story['key']); ?>" data-cover="<?php echo esc_url($story['image']); ?>" data-title="<?php echo esc_attr($story['title']); ?>">
                    <span class="webstory-thumb">
                        <span class="webstory-thumb-fill" style="background-image: url('<?php echo esc_url($story['image']); ?>');" aria-hidden="true"></span>
                        <span class="webstory-chip">Web Story</span>
                        <span class="webstory-title"><?php echo esc_html($story['title']); ?></span>
                    </span>
                </button>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="webstory-hidden" aria-hidden="true">
            <?php foreach ($fallback_stories as $story) : ?>
            <div class="webstory-slide-collection" data-story="<?php echo esc_attr($story['key']); ?>">
                <div class="webstory-slide" data-story="<?php echo esc_attr($story['key']); ?>" data-caption="<?php echo esc_attr($story['title']); ?>">
                    <img src="<?php echo esc_url($story['image']); ?>" alt="<?php echo esc_attr($story['title']); ?>" class="webstory-slide-image">
                    <div class="webstory-slide-caption"><?php echo esc_html($story['title']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php endif; ?>

<?php include 'parts/shared/footer.php'; ?>

<script>
    function copyToClipboard(event) {
        // Use the full URL from the page
        const currentUrl = window.location.href;
        
        // Modern clipboard API with fallback
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(currentUrl).then(function() {
                showCopySuccess(event);
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
                fallbackCopyToClipboard(currentUrl, event);
            });
        } else {
            fallbackCopyToClipboard(currentUrl, event);
        }
    }

    function fallbackCopyToClipboard(text, event) {
        // Create a temporary input element
        const tempInput = document.createElement('input');
        tempInput.value = text;
        tempInput.style.position = 'fixed';
        tempInput.style.opacity = '0';
        document.body.appendChild(tempInput);

        // Select and copy the text
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess(event);
            } else {
                showCopyError(event);
            }
        } catch (err) {
            console.error('Fallback copy failed: ', err);
            showCopyError(event);
        }

        // Remove the temporary input element
        document.body.removeChild(tempInput);
    }

    function showCopySuccess(event) {
        const btn = event ? event.currentTarget : null;
        if (btn) {
            const icon = btn.querySelector('i');
            const originalIcon = icon.className;
            
            // Change to check icon
            icon.className = 'fas fa-check';
            btn.style.backgroundColor = '#28a745';
            
            // Show tooltip
            btn.setAttribute('title', 'Copied!');
            
            // Reset after 2 seconds
            setTimeout(() => {
                icon.className = originalIcon;
                btn.style.backgroundColor = '';
                btn.setAttribute('title', 'Copy Link');
            }, 2000);
        } else {
            alert('Link copied to clipboard!');
        }
    }

    function showCopyError(event) {
        const btn = event ? event.currentTarget : null;
        if (btn) {
            const icon = btn.querySelector('i');
            const originalIcon = icon.className;
            
            // Change to error icon
            icon.className = 'fas fa-times';
            btn.style.backgroundColor = '#dc3545';
            
            // Reset after 2 seconds
            setTimeout(() => {
                icon.className = originalIcon;
                btn.style.backgroundColor = '';
            }, 2000);
        } else {
            alert('Failed to copy link!');
        }
    }
</script>



<?php include 'parts/shared/html-footer.php'; ?>