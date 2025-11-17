<body>
<!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience and security.</p>
<![endif]-->


<!-- Main contents
================================================ -->
<div class="main-content">

<div class="side-nav side-nav__left">
<div class="side-nav-inner nicescroll-container">
<form action="#" class="side-nav-search-form">
<div class="form-group search-field">
<input type="text" class="search-field" name="search-field" placeholder="Search...">
<button class="side-nav-search-btn"><i class="fas fa-search"></i></button>
</div>
<!-- End of .side-nav-search-form -->
</form>
<!-- End of .side-nav-search-form -->
<div class="side-nav-content">
<div class="row ">
<div class="col-lg-6">
<ul class="main-navigation side-navigation list-inline flex-column">
<li><a href="#">News</a></li>
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
<header class="page-header">
<div class="header-top header-top__style-two">
<div class="container">
<div class="row justify-content-between align-items-center">
<div class="col-md-4">
<ul class="header-top-nav list-inline justify-content-center justify-content-md-start">
<li class="current-date">
<?php
// Set timezone for UAE (Dubai)
date_default_timezone_set('Asia/Dubai');
$uae_time = date('h:i A');
$uae_date = date('d M, Y');

// Set timezone for India
date_default_timezone_set('Asia/Kolkata');
$india_time = date('h:i A');
$india_date = date('d M, Y');

// Reset to UAE timezone for consistency
date_default_timezone_set('Asia/Dubai');
?>
<span style="">
🇦🇪 <?php echo $uae_time; ?> | <?php echo $uae_date; ?>
</span>
</li>
<li class="current-date">
<span style="">
🇮🇳 <?php echo $india_time; ?> | <?php echo $india_date; ?>
</span>
</li>
</ul>
<!-- End of .header-top-nav -->
</div>

<div class="brand-logo-container col-md-4">
<a href="<?php echo home_url(); ?>">
<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/logo.svg" alt="<?php bloginfo('name'); ?>" class="brand-logo">
</a>
</div>
<!-- End of .brand-logo-container -->

<div class="col-md-4">
<ul class="ml-auto social-share header-top__social-share justify-content-end">
<li><a href="https://www.instagram.com/byline_gulf?igsh=ZTE4YWkwNG52bjl1" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a></li>
<li><a href="https://youtube.com/@bylinegulf?si=mMJjeJN0wExblBC2" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a></li>
<li><a href="https://www.tiktok.com/@byline_gulf?_t=ZS-8zMA8eT6zpa&_r=1" target="_blank" rel="noopener noreferrer"><i class="fab fa-tiktok"></i></a></li>
<li><a href="#"><i class="fab fa-x-twitter"></i></a></li>
</ul>
<ul class="ml-auto header-top__links justify-content-end" class="header-top__links">
<li class="header-link"><a href="#">Advertise with Us</a></li>
<li class="header-link"><a href="#">About Us</a></li>
                <li class="header-link"><a href="<?php echo home_url('/contact/'); ?>">Contact Us</a></li>
</ul>
</div>
</div>
<!-- End of .row -->
</div>
<!-- End of .container -->
</div>
<!-- End of .header-top -->

<nav class="navbar navbar__style-four bg-color-white">
<div class="container">
<div class="navbar-inner justify-content-between">

<div class="navbar-toggler-wrapper">
<a href="#" class="side-nav-toggler" id="side-nav-toggler">
<span></span>
<span></span>
<span></span>
</a>
</div>
<div class="brand-logo-container text-center d-lg-none">
<a href="<?php echo home_url(); ?>">
<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/logo.svg" alt="<?php bloginfo('name'); ?>" class="brand-logo">
</a>
</div>
<div class="main-nav-wrapper">
<ul class="main-navigation list-inline" id="main-menu">
<li><a href="<?php echo home_url(); ?>">Home</a></li>
<li><a href="<?php echo home_url(); ?>">Latest</a></li>
<li><a href="<?php echo get_safe_category_link('uae'); ?>">UAE</a></li>
<li class="has-dropdown">
<a href="<?php echo get_safe_category_link('gulf'); ?>">Gulf</a>
<ul class="submenu">
<li><a href="<?php echo get_safe_category_link('saudi'); ?>">Saudi</a></li>
<li><a href="<?php echo get_safe_category_link('qatar'); ?>">Qatar</a></li>
<li><a href="<?php echo get_safe_category_link('oman'); ?>">Oman</a></li>
<li><a href="<?php echo get_safe_category_link('kuwait'); ?>">Kuwait</a></li>
<li><a href="<?php echo get_safe_category_link('bahrain'); ?>">Bahrain</a></li>
<li><a href="<?php echo get_safe_category_link('yemen'); ?>">Yemen</a></li>
</ul>
</li>
<li><a href="<?php echo get_safe_category_link('kerala'); ?>">Kerala</a></li>
<li><a href="<?php echo get_safe_category_link('india'); ?>">India</a></li>
<li class="has-dropdown">
<a href="<?php echo get_safe_category_link('world'); ?>">World</a>
<ul class="submenu">
<li><a href="<?php echo get_safe_category_link('middle-east'); ?>">Middle East</a></li>
<li><a href="<?php echo get_safe_category_link('asia'); ?>">Asia</a></li>
<li><a href="<?php echo get_safe_category_link('europe'); ?>">Europe</a></li>
<li><a href="<?php echo get_safe_category_link('america'); ?>">America</a></li>
</ul>
</li>
<li class="has-dropdown">
<a href="<?php echo get_safe_category_link('entertainment'); ?>">Entertainment</a>
<ul class="submenu">
<li><a href="<?php echo get_safe_category_link('movies'); ?>">Movies</a></li>
<li><a href="<?php echo get_safe_category_link('events'); ?>">Events</a></li>
<li><a href="<?php echo get_safe_category_link('lifestyle'); ?>">Lifestyle</a></li>
</ul>
</li>
<li><a href="<?php echo get_safe_category_link('sports'); ?>">Sports</a></li>
<li class="has-dropdown">
<a href="<?php echo get_safe_category_link('business'); ?>">Business</a>
<ul class="submenu">
<li><a href="<?php echo get_safe_category_link('finance'); ?>">Finance</a></li>
<li><a href="<?php echo get_safe_category_link('tech'); ?>">Tech</a></li>
</ul>
</li>
<li><a href="#">Job</a></li>
<li><a href="<?php echo home_url('/contact/'); ?>">Contact</a></li>
</ul>
</div>
<div class="main-nav-toggler d-block d-lg-none" id="main-nav-toggler">
<div class="toggler-inner">
<span></span>
<span></span>
<span></span>
</div>
</div>
<div class="navbar-extra-features">
<form action="#" class="navbar-search">
<div class="search-field">
<input type="text" class="navbar-search-field" placeholder="Search Here...">
<button class="navbar-search-btn" type="button"><i
class="fal fa-search"></i></button>
</div>
<a href="#" class="navbar-search-close"><i class="fal fa-times"></i></a>
</form>
<a href="#" class="nav-search-field-toggler mr-0" data-toggle="nav-search-feild"><i
class="far fa-search"></i></a>
</div>
</div>
</div>
<!-- End of .container -->
</nav>
<!-- End of .navbar -->
</header>
<!-- End of .page-header -->