<?php 
// Include the sidebar algorithms
include_once get_template_directory() . '/parts/shared/sidebar-algorithms.php';

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
<div class="col-lg-4 bg-color-white p-t-xs-15">
<aside class="post-sidebar">
<div class="advertisement-section m-b-xs-20">
<a href="#" class="d-block">
<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/advt.png" alt="Advertisement" class="img-fluid" style="width: 100%; border-radius: 8px;">
</a>
</div>
<!-- End Advertisement Section -->
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

<!-- Gold Exchange Rate Widget -->
<div class="exchange-widget bg-grey-light-three m-b-xs-20">
<div class="section-title m-b-xs-10">
<a href="#" class="d-block">
<h2 class="axil-title">Gold Exchange Rates</h2>
</a>
<div class="last-updated">Last updated: <span id="gold-last-updated">--</span></div>
</div>
<div class="exchange-rates">
<div class="rate-item">
<div class="currency-info">
<span class="currency-name">Gold (24K)</span>
</div>
<div class="rate-values">
<span class="rate-value" id="gold-aed">--</span>
<span class="currency-symbol">AED</span>
<span class="rate-value" id="gold-inr">--</span>
<span class="currency-symbol">INR</span>
</div>
</div>
</div>
<div class="widget-footer">
<small class="text-muted">Rates per gram</small>
</div>
</div>
<!-- End of Gold Exchange Rate Widget -->

<!-- Money Exchange Rate Widget -->
<div class="exchange-widget bg-grey-light-three m-b-xs-40">
<div class="section-title m-b-xs-10">
<a href="#" class="d-block">
<h2 class="axil-title">Currency Exchange Rates</h2>
</a>
<div class="last-updated">Last updated: <span id="currency-last-updated">--</span></div>
</div>
<div class="exchange-rates">
<div class="rate-item">
<div class="currency-info">
<span class="currency-name">USD to AED</span>
<span class="currency-symbol">$ → د.إ</span>
</div>
<div class="rate-value" id="usd-aed">--</div>
</div>
<div class="rate-item">
<div class="currency-info">
<span class="currency-name">USD to INR</span>
<span class="currency-symbol">$ → ₹</span>
</div>
<div class="rate-value" id="usd-inr">--</div>
</div>
<div class="rate-item">
<div class="currency-info">
<span class="currency-name">AED to INR</span>
<span class="currency-symbol">د.إ → ₹</span>
</div>
<div class="rate-value" id="aed-inr">--</div>
</div>
</div>
<div class="widget-footer">
<small class="text-muted">Live rates from exchange APIs</small>
</div>
</div>
<!-- End of Money Exchange Rate Widget --> <!-- Advertisement Section -->

<!-- Advertisement Section -->
<div class="advertisement-section m-b-xs-20">
<a href="#" class="d-block">
<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/advt.png" alt="Advertisement" class="img-fluid" style="width: 100%; border-radius: 8px;">
</a>
</div>
<!-- End Advertisement Section -->
<!-- Advertisement Section -->




</aside>
<!-- End of .post-sidebar -->
</div>
<!-- End of .col-lg-4 -->