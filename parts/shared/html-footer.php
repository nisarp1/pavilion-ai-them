<!-- Javascripts
======================================= -->

<!-- jQuery (CDN) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.4.1/jquery-migrate.min.js"
    integrity="sha512-KgffulL3mxrOsDicgQWA11O6q6oKeWcV00VxgfJw4TcM8XRQT8Df9EsrYxDf7tpVpfl3qcYD96BpyPvA4d1oQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Vendor Libraries (CDN) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"
    integrity="sha512-0QbL0ph8Tc8g5bLhfVzSqxe9GERORsKhIn1IrpxDAgUsbBGz/V7iSav2zzW325XGd1OMLdL4UiqRJj702IeqnQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"
    integrity="sha512-CEiA+78TpP9KAIPzqBvxUv8hy41jyI3f2uHi7DGp/Y/Ka973qgSdybNegXD896g4Rner399OQQ6v0/UBI7ZzRw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"
    integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"
    integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js"
    integrity="sha512-Zq2BOxyhvnRFXu0+WE6ojpZLOU2jdnqbrM1hmVdGzyeCa1DgM3X5Q4A/Is9xA1IkbUeDd7755dNNI/PzSf2Pew=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/shortcuts/infinite.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"
    integrity="sha512-d8F1J2kyiRowBB/8/pAWsqUl0wSEOkG5KATkVV4slfblq9VRQ6MyDZVxWl2tWd+mPhuCbpTB4M7uU/x9FlgQ9Q=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"
    integrity="sha512-IsNh5E3eYy3tr/JiX2Yx4vsCtu7pxF6W2hJ8D805H01WJ7x9iKkaD6PG9sX3J9h9m6A8/6J9q6/7/5h7/6/5w=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"
    integrity="sha512-zMfrMAZYAlNScJJ2U+LJx(0, 0, 0, 0)aP+pL/Z6/7/5/4/3/2/1/0==" crossorigin="anonymous"
    referrerpolicy="no-referrer"></script>
<!-- IF ie -->
<script src="https://cdn.jsdelivr.net/npm/css-vars-ponyfill@2"></script>
<!-- Plugins -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>assets/js/plugins.js"></script>
<!-- Custom Script -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>assets/js/main.js"></script>
<!-- Sidebar Tabs Script -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>assets/js/sidebar-tabs.js"></script>
<!-- Page-specific Fonts Script -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>assets/js/page-specific-fonts.js"></script>
<!-- Exchange Rate Widget Script -->
<script>
    // Debug: Check what's being loaded
    console.log('🔍 Loading exchange rates script');
</script>
<script
    src="<?php echo get_stylesheet_directory_uri(); ?>assets/js/exchange-rates-simple.js?v=<?php echo time(); ?>"></script>


<!-- Social Media SDKs (Deferred for Performance) -->
<div id="fb-root"></div>
<script>
    // Defer loading of heavy social media scripts until user interaction
    function dpLoadSocialScripts() {
        if (window.dpSocialScriptsLoaded) return;
        window.dpSocialScriptsLoaded = true;

        console.log('Loading social media SDKs...');

        // Facebook SDK
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.crossOrigin = "anonymous";
            js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        // Twitter/X SDK
        var twitterScript = document.createElement('script');
        twitterScript.setAttribute('src', 'https://platform.twitter.com/widgets.js');
        twitterScript.setAttribute('charset', 'utf-8');
        twitterScript.setAttribute('async', 'true');
        document.body.appendChild(twitterScript);

        // Instagram SDK
        var instaScript = document.createElement('script');
        instaScript.setAttribute('src', '//www.instagram.com/embed.js');
        instaScript.setAttribute('async', 'true');
        document.body.appendChild(instaScript);
    }

    // Load on user interaction
    ['mousemove', 'scroll', 'touchstart', 'click', 'keydown'].forEach(function (event) {
        window.addEventListener(event, dpLoadSocialScripts, { once: true, passive: true });
    });

    // Fallback: Load after 5 seconds if no interaction
    setTimeout(dpLoadSocialScripts, 5000);
</script>



<!-- LIVE Badge Script - Commented Out
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
-->



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