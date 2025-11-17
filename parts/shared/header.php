<?php
// Load core functions
if (!function_exists('home_url')) {
    require_once __DIR__ . '/../../core.php';
}
?>
<body>
<!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience and security.</p>
<![endif]-->


<!-- Main contents
================================================ -->
<div class="main-content">

<div class="side-nav side-nav__left">
<div class="side-nav-inner nicescroll-container">
<form action="<?php echo home_url('/'); ?>" method="get" class="side-nav-search-form">
<div class="form-group search-field">
<input type="text" class="search-field" name="s" placeholder="Search..." value="<?php echo get_search_query(); ?>">
<button class="side-nav-search-btn" type="submit"><i class="fas fa-search"></i></button>
</div>
<!-- End of .side-nav-search-form -->
</form>
<!-- End of .side-nav-search-form -->
<div class="side-nav-content">
<div class="row ">
<div class="col-lg-6">
<ul class="main-navigation side-navigation list-inline flex-column">
<li><a href="<?php echo home_url('/latest/'); ?>">News</a></li>
<li><a href="#">Promotional Services</a></li>
<li><a href="#">Trending Content</a></li>
<li><a href="#">Corporate Videos</a></li>
<li><a href="<?php echo home_url('/contact/'); ?>">Contact</a></li>
</ul>
</div>
<div class="col-lg-6">
<div class="axil-contact-info-inner">
<h5 class="h5 m-b-xs-10">
Contact Information
</h5>
<div class="axil-contact-info">
<address class="address">
<p class="m-b-xs-30  mid grey-dark-three ">Byline Gulf FZE, <br>Dubai, UAE</p>
<div class="h5 m-b-xs-5">For inquiries and collaborations.</div>
<div>
<a class="tel" href="tel:+971521142984"><i class="fas fa-phone"></i>+971 521142984</a>
</div>
<div>
<a class="tel" href="tel:+971581786840"><i class="fas fa-phone"></i>+971 581786840</a>
</div>
<div>
<a class="tel" href="mailto:bylinegulf@gmail.com"><i class="fas fa-envelope"></i>bylinegulf@gmail.com</a>
</div>
</address>
<div class="contact-social-share m-t-xs-30">
<div class="axil-social-title h5">Follow Us</div>
<ul class="social-share social-share__with-bg">
<li><a href="https://www.instagram.com/byline_gulf?igsh=ZTE4YWkwNG52bjl1" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a></li>
<li><a href="https://youtube.com/@bylinegulf?si=mMJjeJN0wExblBC2" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a></li>
<li><a href="https://www.tiktok.com/@byline_gulf?_t=ZS-8zMA8eT6zpa&_r=1" target="_blank" rel="noopener noreferrer"><i class="fab fa-tiktok"></i></a></li>
</ul>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- End of .side-nav-inner -->
<div class="close-sidenav" id="close-sidenav">
<div></div>
<div></div>
</div>
</div>
<!-- End of .side-nav -->

<!-- Header starts -->
<header class="page-header wireframe-header">
    <div class="container">
        <?php
        // Set timezone for UAE (Dubai)
        date_default_timezone_set('Asia/Dubai');
        $uae_date = date('d M, Y');

        // Set timezone for India
        date_default_timezone_set('Asia/Kolkata');
        $india_time = date('h:i A');
        $india_date = date('d M, Y');

        // Reset to UAE timezone for consistency
        date_default_timezone_set('Asia/Dubai');
        ?>

        <div class="wireframe-header__primary-row">
            <div class="wireframe-header__meta-group wireframe-header__meta-group--left">
                <span class="wireframe-header__meta">🇮🇳 <?php echo $india_date; ?> · <?php echo $india_time; ?></span>
                <a class="wireframe-header__link" href="#">Advertise with us</a>
            </div>

            <div class="wireframe-header__logo">
                <div class="wireframe-header__logo-circle">
                    <a href="<?php echo home_url(); ?>" class="wireframe-header__logo-link" aria-label="<?php bloginfo('name'); ?>">
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/new/logo-icon.svg'); ?>" alt="<?php bloginfo('name'); ?>" class="wireframe-header__logo-img">
                    </a>
                </div>
                
            </div>

            <div class="wireframe-header__meta-group wireframe-header__meta-group--right">
                <a class="wireframe-header__link" href="<?php echo home_url('/contact/'); ?>">Contact us</a>
                <a class="wireframe-header__link" href="#">About</a>
                <a class="wireframe-header__link" href="#">Support</a>
            </div>
        </div>

        <div class="wireframe-header__nav-row">
            <div class="wireframe-header__nav half-nav wireframe-header__nav--left">
                <ul class="wireframe-header__nav-list">
                    <li><a href="<?php echo home_url(); ?>">Home</a></li>
                    <li><a href="<?php echo home_url('/latest/'); ?>">Latest</a></li>
                    <li><a href="<?php echo get_safe_category_link('cricket'); ?>">Cricket</a></li>
                    <li><a href="<?php echo get_safe_category_link('football'); ?>">Football</a></li>
                </ul>
            </div>

            <div class="wireframe-header__logo wordmark-only">
                <a href="<?php echo home_url(); ?>" class="wireframe-header__wordmark" aria-label="<?php bloginfo('name'); ?>">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/new/logo.svg'); ?>" alt="<?php bloginfo('name'); ?>">
                </a>
            </div>

            <div class="wireframe-header__nav half-nav wireframe-header__nav--right">
                <ul class="wireframe-header__nav-list">
                    <li><a href="<?php echo get_safe_category_link('ipl'); ?>">IPL</a></li>
                    <li><a href="<?php echo get_safe_category_link('isl'); ?>">ISL</a></li>
                    <li><a href="<?php echo get_safe_category_link('epl'); ?>">EPL</a></li>
                    <li><a href="<?php echo get_safe_category_link('worldcup'); ?>">World Cup</a></li>
                    <li><a href="<?php echo home_url('/contact/'); ?>">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>
<!-- End of .page-header -->