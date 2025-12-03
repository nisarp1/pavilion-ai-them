<?php
// Load core functions
if (!function_exists('home_url')) {
    require_once __DIR__ . '/../../core.php';
}
?>
<footer class="page-footer bg-grey-dark-key">
    <div class="container">
        <div class="footer-top">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="footer-widget">
                        <h2 class="footer-widget-title">
                            Submit Your Story
                        </h2>

                        <ul class="footer-nav">
                            <li><a href="#">Regional Sports</a></li>
                            <li><a href="#">Featured Stories</a></li>
                            <li><a href="#">Match Videos</a></li>
                            <li><a href="#">Highlights</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="footer-widget">
                        <h2 class="footer-widget-title">
                            Collaborate With Us
                        </h2>

                        <ul class="footer-nav">
                            <li><a href="#">Advertise With Us</a></li>
                            <li><a href="#">Get Featured With Us</a></li>
                            <li><a href="#">Business Promotion</a></li>
                            <li><a href="#">Video Solutions</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="footer-widget">
                        <h2 class="footer-widget-title">
                            Quick Links
                        </h2>

                        <ul class="footer-nav">
                            <li><a href="#">EPL</a></li>
                            <li><a href="#">International Friendlies</a></li>
                            <li><a href="#">India vs SA</a></li>
                            <li><a href="#">Regional Cricket</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="footer-widget">
                        <h2 class="footer-widget-title">
                            About Pavilion End
                        </h2>

                        <ul class="footer-nav">
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Our Mission</a></li>
                            <li><a href="#">Why Us</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-mid">
            <div class="row align-items-center">
                <div class="col-md">
                    <div class="footer-logo-container">
                        <a href="#">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/footer-logo.svg"
                                alt="footer logo" class="footer-logo">
                        </a>
                    </div>
                </div>
                <div class="col-md-auto">
                    <div class="footer-social-share-wrapper">
                        <div class="footer-social-share">
                            <div class="axil-social-title">Follow Us</div>
                            <ul class="social-share social-share__with-bg">
                                <li><a href="https://www.facebook.com/pavilionendofficial" target="_blank"
                                        rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="https://www.instagram.com/pavilionendofficial/" target="_blank"
                                        rel="noopener noreferrer"><i class="fab fa-instagram"></i></a></li>
                                <li><a href="https://www.youtube.com/channel/UCT8NfnrJhJMhpao3bWWDiWg" target="_blank"
                                        rel="noopener noreferrer"><i class="fab fa-youtube"></i></a></li>
                                <li><a href="https://www.threads.com/@pavilionendofficial" target="_blank"
                                        rel="noopener noreferrer"><i class="fab fa-threads"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <ul class="footer-bottom-links">
                <li><a href="#">Terms of Use</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="<?php echo home_url('/contact/'); ?>">Contact Us</a></li>
            </ul>
            <div class="axil-copyright-txt">
                <li>© 2025. All rights reserved by Pavilion End.</li>
            </div>
        </div>
</footer>

</div>
<!-- End of .main-content -->