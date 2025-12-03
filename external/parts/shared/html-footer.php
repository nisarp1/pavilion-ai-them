<!-- Javascripts
======================================= -->

<!-- jQuery -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/jquery.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/jquery-migrate.min.js"></script>
<!-- jQuery Easing Plugin -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/easing-1.3.js"></script>
<!-- Waypoints js -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/jquery.waypoints.min.js"></script>
<!-- Owl Carousel JS -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/owl.carousel.min.js"></script>
<!-- Slick Carousel JS -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/slick.min.js"></script>
<!-- Bootstrap js -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/bootstrap.bundle.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/isotope.pkgd.min.js"></script>
<!-- Counter up js -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/jquery.counterup.js"></script>
<!-- Magnific Popup js -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/jquery.magnific-popup.min.js"></script>
<!-- Nicescroll js -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/vendor/jquery.nicescroll.min.js"></script>
<!-- IF ie -->
<script src="https://cdn.jsdelivr.net/npm/css-vars-ponyfill@2"></script>
<!-- Plugins -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/plugins.js"></script>
<!-- Custom Script -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/main.js"></script>
<!-- Sidebar Tabs Script -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/sidebar-tabs.js"></script>
<!-- Page-specific Fonts Script -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/page-specific-fonts.js"></script>
<!-- Exchange Rate Widget Script -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/exchange-rates-simple.js"></script>
<!-- Debug Script -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/debug-exchange-rates.js"></script>

<!-- Facebook SDK -->
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0" nonce="random_nonce"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
document.querySelectorAll('.live-card').forEach(function(card) {
// Find the first image inside the card
var img = card.querySelector('img');
if (img) {
// If the image is inside a link, use the link's parent for wrapping
var wrapper = img.parentElement;
if (wrapper.tagName.toLowerCase() === 'a') {
// If the <a> is not already inside a .post-image-wrapper, wrap it
if (!wrapper.parentElement.classList.contains('post-image-wrapper')) {
var newWrapper = document.createElement('div');
newWrapper.className = 'post-image-wrapper';
newWrapper.style.position = 'relative';
wrapper.parentNode.insertBefore(newWrapper, wrapper);
newWrapper.appendChild(wrapper);
wrapper = newWrapper;
} else {
wrapper = wrapper.parentElement;
}
} else if (!wrapper.classList.contains('post-image-wrapper')) {
// If the image is not inside a .post-image-wrapper, wrap it
var newWrapper = document.createElement('div');
newWrapper.className = 'post-image-wrapper';
newWrapper.style.position = 'relative';
img.parentNode.insertBefore(newWrapper, img);
newWrapper.appendChild(img);
wrapper = newWrapper;
}
wrapper.style.position = 'relative';
// Only add the badge if it doesn't already exist
if (!wrapper.querySelector('.cmp-live-label')) {
var badge = document.createElement('span');
badge.className = 'cmp-live-label';
badge.innerHTML = '<span class="live-blink-circle"><span class="live-dot"></span></span>LIVE';
wrapper.appendChild(badge);
}
}
		});
});
</script>



<!-- Webstory Modal -->
<div id="webstoryModal" class="webstory-modal" aria-hidden="true">
    <div class="webstory-modal-content" role="dialog" aria-modal="true" aria-live="polite">
        <button type="button" class="webstory-close" aria-label="Close web story">&times;</button>
        <div class="webstory-progress" aria-hidden="true"></div>
        <div class="webstory-stage">
            <button type="button" class="webstory-tap-zone tap-prev" aria-label="Previous story"></button>
            <div class="webstory-slides">
                <!-- Webstory slides will be dynamically populated -->
            </div>
            <div class="webstory-caption" aria-live="polite">
                <p class="webstory-caption-text"></p>
            </div>
            <button type="button" class="webstory-tap-zone tap-next" aria-label="Next story"></button>
        </div>
    </div>
</div>

</body>

</html>