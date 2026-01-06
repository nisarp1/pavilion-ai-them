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

    <!-- Floating Hamburger Menu - Global -->
    <button id="side-nav-toggler" class="hamburger-menu side-nav-toggler" type="button"
        onclick="var sn=document.querySelector('.side-nav'); var html=document.querySelector('html'); if(sn){sn.classList.toggle('opened');} if(html){html.classList.toggle('side-nav-opened');}"
        style="position: fixed !important; bottom: 30px !important; right: 30px !important; z-index: 2147483647 !important; width: 60px !important; height: 60px !important; border-radius: 50% !important; background-color: #009688 !important; color: #ffffff !important; border: none !important; display: flex !important; align-items: center !important; justify-content: center !important; box-shadow: 0 4px 15px rgba(0,0,0,0.3) !important;">
        <i class="fas fa-bars"></i>
    </button>


    <!-- Main contents
================================================ -->
    <div class="main-content">

        <div class="side-nav side-nav__left">
            <div class="side-nav-inner nicescroll-container">
                <form action="<?php echo home_url('/'); ?>" method="get" class="side-nav-search-form">
                    <div class="form-group search-field">
                        <input type="text" class="search-field" name="s" placeholder="Search..."
                            value="<?php echo get_search_query(); ?>">
                        <button class="side-nav-search-btn" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                    <!-- End of .side-nav-search-form -->
                </form>
                <!-- End of .side-nav-search-form -->
                <div class="side-nav-content">
                    <div class="row">
                        <div class="col-12">
                            <ul class="side-nav-menu" style="list-style: none; padding: 0; margin: 0;">
                                <li><a href="#">Advertise With Us</a></li>
                                <li><a href="<?php echo home_url('/contact/'); ?>">Contact Us</a></li>
                                <li><a href="#">About</a></li>
                                <li><a href="#">Support</a></li>
                            </ul>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="contact-social-share">
                                <div class="axil-social-title h5">Follow Us</div>
                                <ul class="social-share social-share__with-bg">
                                    <li><a href="https://www.facebook.com/pavilionendofficial" target="_blank"
                                            rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="https://www.instagram.com/pavilionendofficial/" target="_blank"
                                            rel="noopener noreferrer"><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="https://www.youtube.com/channel/UCT8NfnrJhJMhpao3bWWDiWg"
                                            target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a>
                                    </li>
                                    <li><a href="https://www.threads.com/@pavilionendofficial" target="_blank"
                                            rel="noopener noreferrer"><i class="fab fa-threads"></i></a></li>
                                </ul>
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
                        <span class="wireframe-header__meta">🇮🇳 <?php echo $india_date; ?> ·
                            <?php echo $india_time; ?></span>
                        <a class="wireframe-header__link" href="#">Advertise with us</a>
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
                        <a href="<?php echo home_url(); ?>" class="wireframe-header__wordmark"
                            aria-label="<?php bloginfo('name'); ?>">
                            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/new/logo.svg'); ?>"
                                alt="<?php bloginfo('name'); ?>">
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