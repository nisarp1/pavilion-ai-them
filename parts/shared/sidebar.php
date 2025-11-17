<?php 
// Load core functions if not already loaded
if (!function_exists('get_recent_posts_sidebar')) {
    if (file_exists(__DIR__ . '/../../core.php')) {
        require_once __DIR__ . '/../../core.php';
    } elseif (file_exists(__DIR__ . '/../../../core.php')) {
        require_once __DIR__ . '/../../../core.php';
    }
}

// Get posts for each tab
$recent_posts = get_recent_posts_sidebar(5);
$popular_posts = get_popular_posts_sidebar(5);
$trending_posts = get_trending_posts_sidebar(5);



// Fallback: if no trending posts, use recent posts
if (empty($trending_posts) && !empty($recent_posts)) {
    $trending_posts = array_slice($recent_posts, 0, 5);
}

// Check if we're using simple algorithm (posts will have simple_score property)
$using_simple_algorithm = !empty($trending_posts) && isset($trending_posts[0]->simple_score);
?>
<div class="bg-color-white p-t-xs-15">
<aside class="post-sidebar">

<?php 
// Show Team India news widget on single post pages
if (is_single()) : 
    include get_template_directory() . '/parts/shared/team-india-news-widget.php';
endif;
?>

<?php 
// Hide Recent/Popular/Trending tabs on category pages and single posts
if (!is_category() && !is_single()) : 
?>
<div class="post-widget sidebar-post-widget m-b-xs-40">
<ul class="nav nav-pills row no-gutters">
<li class="nav-item col">
<a class="nav-link active" data-bs-toggle="pill" href="#recent-post">Recent</a>
</li>
<li class="nav-item col">
<a class="nav-link" data-bs-toggle="pill" href="#popular-post">Popular</a>
</li>
<li class="nav-item col">
<a class="nav-link" data-bs-toggle="pill" href="#comments">Trending</a>
</li>
</ul>
<div class="tab-content">
<div class="tab-pane fade show active" id="recent-post">
<div class="axil-content">
<?php if (!empty($recent_posts)) : ?>
    <?php foreach ($recent_posts as $post) : 
        $post_data = format_post_for_sidebar($post);
    ?>
    <div class="media post-block small-block">
        <div class="post-image-wrapper">
            <a href="<?php echo esc_url($post_data['url']); ?>" class="align-self-center">
                <img class="img-fluid" src="<?php echo esc_url($post_data['image_url']); ?>" alt="<?php echo esc_attr($post_data['title']); ?>">
            </a>
            <!-- The badge will be injected here by JS -->
        </div>
        <div class="media-body">
            <h4 class="axil-post-title small-card-title">
                <a href="<?php echo esc_url($post_data['url']); ?>"><?php echo esc_html($post_data['title']); ?></a>
            </h4>
            <div class="d-flex align-items-baseline">
                <div class="post-cat-group">
                    <?php echo implode(', ', $post_data['categories']); ?>
                </div>
                <div class="post-time">
                    <i class="far fa-clock"></i><?php echo esc_html($post_data['time_ago']); ?>
                </div>
            </div>
        </div>
    </div>
    <!-- End of .post-block -->
    <?php endforeach; ?>
<?php else : ?>
    <div class="no-posts-message">
        <p>No recent posts available.</p>
    </div>
<?php endif; ?>
</div>
<!-- End of .content -->
</div>
<!-- End of .tab-pane -->
<div class="tab-pane fade" id="popular-post">
<div class="axil-content">
<?php if (!empty($popular_posts)) : ?>
    <?php foreach ($popular_posts as $post) : 
        $post_data = format_post_for_sidebar($post);
    ?>
    <div class="media post-block small-block">
        <div class="post-image-wrapper">
            <a href="<?php echo esc_url($post_data['url']); ?>" class="align-self-center">
                <img class="img-fluid" src="<?php echo esc_url($post_data['image_url']); ?>" alt="<?php echo esc_attr($post_data['title']); ?>">
            </a>
            <!-- The badge will be injected here by JS -->
        </div>
        <div class="media-body">
            <h4 class="axil-post-title small-card-title">
                <a href="<?php echo esc_url($post_data['url']); ?>"><?php echo esc_html($post_data['title']); ?></a>
            </h4>
            <div class="d-flex align-items-baseline">
                <div class="post-cat-group">
                    <?php echo implode(', ', $post_data['categories']); ?>
                </div>
                <div class="post-time">
                    <i class="far fa-clock"></i><?php echo esc_html($post_data['time_ago']); ?>
                </div>
            </div>
            <div class="post-stats">
                <small class="text-muted">
                    <i class="far fa-eye"></i> <?php echo number_format($post_data['view_count']); ?> views
                    <i class="far fa-comment ml-2"></i> <?php echo number_format($post_data['comment_count']); ?> comments
                </small>
            </div>
        </div>
    </div>
    <!-- End of .post-block -->
    <?php endforeach; ?>
<?php else : ?>
    <div class="no-posts-message">
        <p>No popular posts available.</p>
    </div>
<?php endif; ?>
</div>
<!-- End of .content -->
</div>
<!-- End of .tab-pane -->
<div class="tab-pane fade" id="comments">
<div class="axil-content">
<?php if (!empty($trending_posts)) : ?>
    <?php foreach ($trending_posts as $post) : 
        $post_data = format_post_for_sidebar($post);
    ?>
    <div class="media post-block small-block">
        <div class="post-image-wrapper">
            <a href="<?php echo esc_url($post_data['url']); ?>" class="align-self-center">
                <img class="img-fluid" src="<?php echo esc_url($post_data['image_url']); ?>" alt="<?php echo esc_attr($post_data['title']); ?>">
            </a>
            <!-- The badge will be injected here by JS -->
        </div>
        <div class="media-body">
            <h4 class="axil-post-title small-card-title">
                <a href="<?php echo esc_url($post_data['url']); ?>"><?php echo esc_html($post_data['title']); ?></a>
            </h4>
            <div class="d-flex align-items-baseline">
                <div class="post-cat-group">
                    <?php echo implode(', ', $post_data['categories']); ?>
                </div>
                <div class="post-time">
                    <i class="far fa-clock"></i><?php echo esc_html($post_data['time_ago']); ?>
                </div>
            </div>

        </div>
    </div>
    <!-- End of .post-block -->
    <?php endforeach; ?>
<?php else : ?>
    <div class="no-posts-message">
        <p>No trending posts available.</p>
    </div>
<?php endif; ?>
</div>
<!-- End of .content -->
</div>
<!-- End of .tab-pane -->
</div>
<!-- End of .tab-content -->
</div>
<!-- End of .sidebar-post-widget -->
<?php 
endif; // End of if (!is_category()) - Hide Recent/Popular/Trending on category pages
?>
<div class="newsletter-widget weekly-newsletter bg-grey-light-three m-b-xs-40">
<div class="newsletter-content">
<div class="newsletter-icon">
<i class="feather icon-send"></i>
</div>
<div class="section-title m-b-xs-20">
<h3 class="axil-title">Subscribe To Our Weekly Newsletter</h3>
<p class="mid m-t-xs-10 m-b-xs-20 m-l-xs-15">No spam, notifications only about new
products,
updates.</p>
</div>
<!-- End of .section-title -->

<div class="subscription-form-wrapper">
<form action="#" class="subscription-form">
<div class="form-group form-group-small m-b-xs-20">
<label for="subscription-email">Enter Email Address</label>
<input type="text" name="subscription-email" id="subscription-email">
</div>
<div class="m-b-xs-0">
<button class="btn btn-primary btn-small">SUBSCRIBE</button>
</div>
</form>
<!-- End of .subscription-form -->
</div>
<!-- End of .subscription-form-wrapper -->
</div>
<!-- End of .newsletter-content -->
</div>
<!-- End of  .newsletter-widget -->




</aside>
<!-- End of .post-sidebar -->
</div>
<!-- End of .col-lg-4 -->