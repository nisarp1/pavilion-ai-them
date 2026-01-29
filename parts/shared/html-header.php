<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <!-- Google Search Console Verification -->
    <!-- <meta name="google-site-verification" content="YOUR_VERIFICATION_CODE" /> -->

    <?php
    // Load core functions
    if (!function_exists('home_url')) {
        require_once __DIR__ . '/../../core.php';
    }

    // Get current page/post information
    $current_url = home_url($_SERVER['REQUEST_URI']);
    $site_name = get_bloginfo('name');
    $site_description = get_bloginfo('description');

    // Default values
    $og_title = $site_name . ' - Latest Sports News';
    $og_description = 'Stay updated with the latest sports news, cricket updates, football news, and more. Pavilion End brings you comprehensive sports coverage and updates.';

    // Base URL detection (handles proxies like Cloudflare/Railway)
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ? 'https' : 'http';
    $base_url = $protocol . '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
    $theme_base = get_theme_base_path();

    // Initialize Image Variables
    $og_image = '';
    $og_image_width = '';
    $og_image_height = '';
    $twitter_card_type = 'summary_large_image'; // Default to large image
    
    // 1. Try Custom Share Image (1200x630 recommended)
    $share_img_path = '/assets/images/new/pavilion-end-social-share.jpg';
    if (file_exists(__DIR__ . '/../..' . $share_img_path)) {
        $og_image = $base_url . $theme_base . $share_img_path;
        $og_image_width = '1200';
        $og_image_height = '630';
    } else {
        // 2. Fallback to Logo (usually square/small)
        $og_image = $base_url . $theme_base . '/assets/images/new/Logo.png';
        $twitter_card_type = 'summary'; // Switch to summary for smaller logo images
        // We don't verify dimensions for logo here, better to omit width/height than be wrong
    }

    // Check if we're on a single post/page
    if (is_single() || is_page()) {
        $og_title = get_the_title() . ' - ' . $site_name;
        $excerpt = get_the_excerpt();
        if (!empty($excerpt)) {
            $og_description = wp_trim_words($excerpt, 25, '...');
        }

        // Direct access to global post to ensure we get the image
        global $post;
        if (!isset($post)) {
            $post = get_current_post();
        }

        $thumbnail_url = '';

        // Try standard function first
        if (has_post_thumbnail()) {
            $post_id = get_the_ID();
            $thumbnail_url = get_the_post_thumbnail_url($post_id, 'full');
        }
        // Fallback: Check post object directly
        elseif (isset($post['featured_image']) && !empty($post['featured_image'])) {
            $thumbnail_url = $post['featured_image'];

            // Handle relative paths from API logic manually if needed
            if (strpos($thumbnail_url, 'http') !== 0) {
                if (strpos($thumbnail_url, '/media/') === 0) {
                    // API Path
                    if (defined('PAVILION_API_BASE_URL')) {
                        $api_base = rtrim(PAVILION_API_BASE_URL, '/api');
                        $thumbnail_url = $api_base . $thumbnail_url;
                    }
                } else {
                    // Local Theme Path
                    $thumbnail_url = $base_url . $theme_base . '/' . ltrim($thumbnail_url, '/');
                }
            }
        }

        if (!empty($thumbnail_url)) {
            // Determine if URL is absolute or relative
            if (strpos($thumbnail_url, 'http') === 0) {
                $og_image = $thumbnail_url;
            } else {
                $og_image = $base_url . $thumbnail_url;
            }

            // Reset dimensions as we can't easily check remote/dynamic image size without overhead
            $twitter_card_type = 'summary_large_image';
            $og_image_width = '';
            $og_image_height = '';
        }
    }

    // Check if we're on homepage
    if (is_home() || is_front_page()) {
        $og_title = $site_name . ' - Latest Sports News';
        $og_description = 'Stay updated with the latest sports news, cricket updates, football news, and more. Pavilion End brings you comprehensive sports coverage and updates.';
    }
    ?>

    <meta name="author" content="Pavilion End">
    <meta name="description" content="<?php echo esc_attr($og_description); ?>">
    <meta name="keywords"
        content="Sports News, Cricket, Football, IPL, ISL, EPL, World Cup, Malayalam Sports, India Sports, Kerala Sports">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Twitter Card meta tags -->
    <meta name="twitter:card" content="<?php echo esc_attr($twitter_card_type); ?>">
    <meta name="twitter:site" content="@pavilionendofficial">
    <meta name="twitter:creator" content="@pavilionendofficial">
    <meta name="twitter:title" content="<?php echo esc_attr($og_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($og_description); ?>">
    <meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
    <meta name="twitter:image:alt" content="<?php echo esc_attr($og_title); ?>">

    <!-- Open Graph meta tags -->
    <meta property="og:url" content="<?php echo esc_url($current_url); ?>">
    <meta property="og:title" content="<?php echo esc_attr($og_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($og_description); ?>">
    <meta property="og:type" content="<?php echo (is_single() || is_page()) ? 'article' : 'website'; ?>">
    <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <meta property="og:image:secure_url" content="<?php echo esc_url($og_image); ?>">

    <?php if (!empty($og_image_width)): ?>
        <meta property="og:image:width" content="<?php echo esc_attr($og_image_width); ?>">
    <?php endif; ?>

    <?php if (!empty($og_image_height)): ?>
        <meta property="og:image:height" content="<?php echo esc_attr($og_image_height); ?>">
    <?php endif; ?>

    <meta property="og:image:type"
        content="<?php echo (strpos($og_image, '.png') !== false) ? 'image/png' : 'image/jpeg'; ?>">
    <meta property="og:image:alt" content="<?php echo esc_attr($og_title); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>">
    <meta property="og:locale" content="en_US">

    <?php if (is_single() || is_page()): ?>
        <meta property="article:author" content="Pavilion End">
        <meta property="article:published_time" content="<?php echo get_the_date('c'); ?>">
        <meta property="article:modified_time" content="<?php echo get_the_modified_date('c'); ?>">
    <?php endif; ?>

    <!-- Additional SEO meta tags -->
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="google" content="notranslate">
    <meta name="format-detection" content="telephone=no">

    <!-- Additional social media meta tags -->
    <meta name="twitter:domain" content="<?php echo parse_url(home_url(), PHP_URL_HOST); ?>">
    <meta name="twitter:url" content="<?php echo esc_url($current_url); ?>">

    <!-- LinkedIn specific meta tags -->
    <meta property="linkedin:owner" content="Pavilion End">

    <!-- Pinterest specific meta tags -->
    <meta name="pinterest-rich-pin" content="true">

    <!-- WhatsApp specific meta tags -->
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Additional Open Graph tags for better sharing -->
    <meta property="og:updated_time" content="<?php echo current_time('c'); ?>">
    <meta property="og:see_also" content="<?php echo home_url(); ?>">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo esc_url($current_url); ?>">

    <title><?php echo esc_html($og_title); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png"
        href="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/favicon.png">
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/favicon.png">
    <link rel="icon" href="<?php echo home_url(); ?>favicon.ico" type="image/x-icon">

    <!-- Load Anek Malayalam from Google Fonts with better cross-browser support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link 
        href="https://fonts.googleapis.com/css2?family=Anek+Malayalam:wght@100;200;300;400;500;600;700;800&display=swap&subset=malayalam"
        rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Anek+Malayalam:wght@100;200;300;400;500;600;700;800&display=swap&subset=malayalam"
            rel="stylesheet">
    </noscript>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage"
        content="<?php echo get_stylesheet_directory_uri(); ?>/assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <!-- Custom fonts loaded via custom-fonts.css -->

    <!-- Font loading optimization script -->
    <script>
        // Ensure Anek Malayalam loads properly in all browsers
        document.addEventListener('DOMContentLoaded', function () {
            // Check if Anek Malayalam is loaded
            if (document.fonts && document.fonts.check) {
                document.fonts.load('400 16px "Anek Malayalam"').then(function () {
                    document.body.style.fontFamily = '"Anek Malayalam", sans-serif';
                }).catch(function () {
                    // Fallback to system fonts if Google Fonts fails
                    document.body.style.fontFamily = '"Noto Sans Malayalam", "Malayalam Sangam MN", sans-serif';
                });
            }
        });
    </script>
    <!-- Optimized CSS Loading -->
    <!-- Vendors (Layout Critical) -->
    <link rel="stylesheet" type="text/css" fetchpriority="high"
        href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/bootstrap.min.css">

    <!-- Vendors (Deferred/Non-Critical) -->
    <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/owl.carousel.min.css"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet"
            href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/owl.carousel.min.css">
    </noscript>

    <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/slick.css" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/slick.css">
    </noscript>

    <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/magnific-popup.css"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet"
            href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/magnific-popup.css">
    </noscript>

    <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/animate.css" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/animate.css">
    </noscript>

    <!-- FontAwesome (Deferred due to size - ~500KB) -->
    <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/fontawesome-all.min.css"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/fontawesome-all.min.css">
    </noscript>

    <!-- Custom Icons & Main Style (Critical) -->
    <style>
        @font-face {
            font-display: swap;
            font-family: "feather";
            src: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/fonts/feather.eot?t=1525787366991');
            src: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/fonts/feather.eot?t=1525787366991#iefix') format('embedded-opentype'),
                url('<?php echo get_stylesheet_directory_uri(); ?>/assets/fonts/feather.woff?t=1525787366991') format('woff'),
                url('<?php echo get_stylesheet_directory_uri(); ?>/assets/fonts/feather.ttf?t=1525787366991') format('truetype'),
                url('<?php echo get_stylesheet_directory_uri(); ?>/assets/fonts/feather.svg?t=1525787366991#feather') format('svg');
        }

        .feather {
            font-family: 'feather' !important;
            speak: none;
            font-style: normal;
            font-weight: normal;
            font-variant: normal;
            text-transform: none;
            line-height: 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .icon-alert-octagon:before {
            content: "\e81b";
        }

        .icon-alert-circle:before {
            content: "\e81c";
        }

        .icon-activity:before {
            content: "\e81d";
        }

        .icon-alert-triangle:before {
            content: "\e81e";
        }

        .icon-align-center:before {
            content: "\e81f";
        }

        .icon-airplay:before {
            content: "\e820";
        }

        .icon-align-justify:before {
            content: "\e821";
        }

        .icon-align-left:before {
            content: "\e822";
        }

        .icon-align-right:before {
            content: "\e823";
        }

        .icon-arrow-down-left:before {
            content: "\e824";
        }

        .icon-arrow-down-right:before {
            content: "\e825";
        }

        .icon-anchor:before {
            content: "\e826";
        }

        .icon-aperture:before {
            content: "\e827";
        }

        .icon-arrow-left:before {
            content: "\e828";
        }

        .icon-arrow-right:before {
            content: "\e829";
        }

        .icon-arrow-down:before {
            content: "\e82a";
        }

        .icon-arrow-up-left:before {
            content: "\e82b";
        }

        .icon-arrow-up-right:before {
            content: "\e82c";
        }

        .icon-arrow-up:before {
            content: "\e82d";
        }

        .icon-award:before {
            content: "\e82e";
        }

        .icon-bar-chart:before {
            content: "\e82f";
        }

        .icon-at-sign:before {
            content: "\e830";
        }

        .icon-bar-chart-2:before {
            content: "\e831";
        }

        .icon-battery-charging:before {
            content: "\e832";
        }

        .icon-bell-off:before {
            content: "\e833";
        }

        .icon-battery:before {
            content: "\e834";
        }

        .icon-bluetooth:before {
            content: "\e835";
        }

        .icon-bell:before {
            content: "\e836";
        }

        .icon-book:before {
            content: "\e837";
        }

        .icon-briefcase:before {
            content: "\e838";
        }

        .icon-camera-off:before {
            content: "\e839";
        }

        .icon-calendar:before {
            content: "\e83a";
        }

        .icon-bookmark:before {
            content: "\e83b";
        }

        .icon-box:before {
            content: "\e83c";
        }

        .icon-camera:before {
            content: "\e83d";
        }

        .icon-check-circle:before {
            content: "\e83e";
        }

        .icon-check:before {
            content: "\e83f";
        }

        .icon-check-square:before {
            content: "\e840";
        }

        .icon-cast:before {
            content: "\e841";
        }

        .icon-chevron-down:before {
            content: "\e842";
        }

        .icon-chevron-left:before {
            content: "\e843";
        }

        .icon-chevron-right:before {
            content: "\e844";
        }

        .icon-chevron-up:before {
            content: "\e845";
        }

        .icon-chevrons-down:before {
            content: "\e846";
        }

        .icon-chevrons-right:before {
            content: "\e847";
        }

        .icon-chevrons-up:before {
            content: "\e848";
        }

        .icon-chevrons-left:before {
            content: "\e849";
        }

        .icon-circle:before {
            content: "\e84a";
        }

        .icon-clipboard:before {
            content: "\e84b";
        }

        .icon-chrome:before {
            content: "\e84c";
        }

        .icon-clock:before {
            content: "\e84d";
        }

        .icon-cloud-lightning:before {
            content: "\e84e";
        }

        .icon-cloud-drizzle:before {
            content: "\e84f";
        }

        .icon-cloud-rain:before {
            content: "\e850";
        }

        .icon-cloud-off:before {
            content: "\e851";
        }

        .icon-codepen:before {
            content: "\e852";
        }

        .icon-cloud-snow:before {
            content: "\e853";
        }

        .icon-compass:before {
            content: "\e854";
        }

        .icon-copy:before {
            content: "\e855";
        }

        .icon-corner-down-right:before {
            content: "\e856";
        }

        .icon-corner-down-left:before {
            content: "\e857";
        }

        .icon-corner-left-down:before {
            content: "\e858";
        }

        .icon-corner-left-up:before {
            content: "\e859";
        }

        .icon-corner-up-left:before {
            content: "\e85a";
        }

        .icon-corner-up-right:before {
            content: "\e85b";
        }

        .icon-corner-right-down:before {
            content: "\e85c";
        }

        .icon-corner-right-up:before {
            content: "\e85d";
        }

        .icon-cpu:before {
            content: "\e85e";
        }

        .icon-credit-card:before {
            content: "\e85f";
        }

        .icon-crosshair:before {
            content: "\e860";
        }

        .icon-disc:before {
            content: "\e861";
        }

        .icon-delete:before {
            content: "\e862";
        }

        .icon-download-cloud:before {
            content: "\e863";
        }

        .icon-download:before {
            content: "\e864";
        }

        .icon-droplet:before {
            content: "\e865";
        }

        .icon-edit-2:before {
            content: "\e866";
        }

        .icon-edit:before {
            content: "\e867";
        }

        .icon-edit-1:before {
            content: "\e868";
        }

        .icon-external-link:before {
            content: "\e869";
        }

        .icon-eye:before {
            content: "\e86a";
        }

        .icon-feather:before {
            content: "\e86b";
        }

        .icon-facebook:before {
            content: "\e86c";
        }

        .icon-file-minus:before {
            content: "\e86d";
        }

        .icon-eye-off:before {
            content: "\e86e";
        }

        .icon-fast-forward:before {
            content: "\e86f";
        }

        .icon-file-text:before {
            content: "\e870";
        }

        .icon-film:before {
            content: "\e871";
        }

        .icon-file:before {
            content: "\e872";
        }

        .icon-file-plus:before {
            content: "\e873";
        }

        .icon-folder:before {
            content: "\e874";
        }

        .icon-filter:before {
            content: "\e875";
        }

        .icon-flag:before {
            content: "\e876";
        }

        .icon-globe:before {
            content: "\e877";
        }

        .icon-grid:before {
            content: "\e878";
        }

        .icon-heart:before {
            content: "\e879";
        }

        .icon-home:before {
            content: "\e87a";
        }

        .icon-github:before {
            content: "\e87b";
        }

        .icon-image:before {
            content: "\e87c";
        }

        .icon-inbox:before {
            content: "\e87d";
        }

        .icon-layers:before {
            content: "\e87e";
        }

        .icon-info:before {
            content: "\e87f";
        }

        .icon-instagram:before {
            content: "\e880";
        }

        .icon-layout:before {
            content: "\e881";
        }

        .icon-link-2:before {
            content: "\e882";
        }

        .icon-life-buoy:before {
            content: "\e883";
        }

        .icon-link:before {
            content: "\e884";
        }

        .icon-log-in:before {
            content: "\e885";
        }

        .icon-list:before {
            content: "\e886";
        }

        .icon-lock:before {
            content: "\e887";
        }

        .icon-log-out:before {
            content: "\e888";
        }

        .icon-loader:before {
            content: "\e889";
        }

        .icon-mail:before {
            content: "\e88a";
        }

        .icon-maximize-2:before {
            content: "\e88b";
        }

        .icon-map:before {
            content: "\e88c";
        }

        .icon-minimize:before {
            content: "\e88d";
        }

        .icon-map-pin:before {
            content: "\e88e";
        }

        .icon-menu:before {
            content: "\e88f";
        }

        .icon-message-circle:before {
            content: "\e890";
        }

        .icon-message-square:before {
            content: "\e891";
        }

        .icon-minimize-2:before {
            content: "\e892";
        }

        .icon-mic-off:before {
            content: "\e893";
        }

        .icon-minus-circle:before {
            content: "\e894";
        }

        .icon-mic:before {
            content: "\e895";
        }

        .icon-minus-square:before {
            content: "\e896";
        }

        .icon-minus:before {
            content: "\e897";
        }

        .icon-moon:before {
            content: "\e898";
        }

        .icon-monitor:before {
            content: "\e899";
        }

        .icon-more-vertical:before {
            content: "\e89a";
        }

        .icon-more-horizontal:before {
            content: "\e89b";
        }

        .icon-move:before {
            content: "\e89c";
        }

        .icon-music:before {
            content: "\e89d";
        }

        .icon-navigation-2:before {
            content: "\e89e";
        }

        .icon-navigation:before {
            content: "\e89f";
        }

        .icon-octagon:before {
            content: "\e8a0";
        }

        .icon-package:before {
            content: "\e8a1";
        }

        .icon-pause-circle:before {
            content: "\e8a2";
        }

        .icon-pause:before {
            content: "\e8a3";
        }

        .icon-percent:before {
            content: "\e8a4";
        }

        .icon-phone-call:before {
            content: "\e8a5";
        }

        .icon-phone-forwarded:before {
            content: "\e8a6";
        }

        .icon-phone-missed:before {
            content: "\e8a7";
        }

        .icon-phone-off:before {
            content: "\e8a8";
        }

        .icon-phone-incoming:before {
            content: "\e8a9";
        }

        .icon-phone:before {
            content: "\e8aa";
        }

        .icon-phone-outgoing:before {
            content: "\e8ab";
        }

        .icon-pie-chart:before {
            content: "\e8ac";
        }

        .icon-play-circle:before {
            content: "\e8ad";
        }

        .icon-play:before {
            content: "\e8ae";
        }

        .icon-plus-square:before {
            content: "\e8af";
        }

        .icon-plus-circle:before {
            content: "\e8b0";
        }

        .icon-plus:before {
            content: "\e8b1";
        }

        .icon-pocket:before {
            content: "\e8b2";
        }

        .icon-printer:before {
            content: "\e8b3";
        }

        .icon-power:before {
            content: "\e8b4";
        }

        .icon-radio:before {
            content: "\e8b5";
        }

        .icon-repeat:before {
            content: "\e8b6";
        }

        .icon-refresh-ccw:before {
            content: "\e8b7";
        }

        .icon-rewind:before {
            content: "\e8b8";
        }

        .icon-rotate-ccw:before {
            content: "\e8b9";
        }

        .icon-refresh-cw:before {
            content: "\e8ba";
        }

        .icon-rotate-cw:before {
            content: "\e8bb";
        }

        .icon-save:before {
            content: "\e8bc";
        }

        .icon-search:before {
            content: "\e8bd";
        }

        .icon-server:before {
            content: "\e8be";
        }

        .icon-scissors:before {
            content: "\e8bf";
        }

        .icon-share-2:before {
            content: "\e8c0";
        }

        .icon-share:before {
            content: "\e8c1";
        }

        .icon-shield:before {
            content: "\e8c2";
        }

        .icon-settings:before {
            content: "\e8c3";
        }

        .icon-skip-back:before {
            content: "\e8c4";
        }

        .icon-shuffle:before {
            content: "\e8c5";
        }

        .icon-sidebar:before {
            content: "\e8c6";
        }

        .icon-skip-forward:before {
            content: "\e8c7";
        }

        .icon-slack:before {
            content: "\e8c8";
        }

        .icon-slash:before {
            content: "\e8c9";
        }

        .icon-smartphone:before {
            content: "\e8ca";
        }

        .icon-square:before {
            content: "\e8cb";
        }

        .icon-speaker:before {
            content: "\e8cc";
        }

        .icon-star:before {
            content: "\e8cd";
        }

        .icon-stop-circle:before {
            content: "\e8ce";
        }

        .icon-sun:before {
            content: "\e8cf";
        }

        .icon-sunrise:before {
            content: "\e8d0";
        }

        .icon-tablet:before {
            content: "\e8d1";
        }

        .icon-tag:before {
            content: "\e8d2";
        }

        .icon-sunset:before {
            content: "\e8d3";
        }

        .icon-target:before {
            content: "\e8d4";
        }

        .icon-thermometer:before {
            content: "\e8d5";
        }

        .icon-thumbs-up:before {
            content: "\e8d6";
        }

        .icon-thumbs-down:before {
            content: "\e8d7";
        }

        .icon-toggle-left:before {
            content: "\e8d8";
        }

        .icon-toggle-right:before {
            content: "\e8d9";
        }

        .icon-trash-2:before {
            content: "\e8da";
        }

        .icon-trash:before {
            content: "\e8db";
        }

        .icon-trending-up:before {
            content: "\e8dc";
        }

        .icon-trending-down:before {
            content: "\e8dd";
        }

        .icon-triangle:before {
            content: "\e8de";
        }

        .icon-type:before {
            content: "\e8df";
        }

        .icon-twitter:before {
            content: "\e8e0";
        }

        .icon-upload:before {
            content: "\e8e1";
        }

        .icon-umbrella:before {
            content: "\e8e2";
        }

        .icon-upload-cloud:before {
            content: "\e8e3";
        }

        .icon-unlock:before {
            content: "\e8e4";
        }

        .icon-user-check:before {
            content: "\e8e5";
        }

        .icon-user-minus:before {
            content: "\e8e6";
        }

        .icon-user-plus:before {
            content: "\e8e7";
        }

        .icon-user-x:before {
            content: "\e8e8";
        }

        .icon-user:before {
            content: "\e8e9";
        }

        .icon-users:before {
            content: "\e8ea";
        }

        .icon-video-off:before {
            content: "\e8eb";
        }

        .icon-video:before {
            content: "\e8ec";
        }

        .icon-voicemail:before {
            content: "\e8ed";
        }

        .icon-volume-x:before {
            content: "\e8ee";
        }

        .icon-volume-2:before {
            content: "\e8ef";
        }

        .icon-volume-1:before {
            content: "\e8f0";
        }

        .icon-volume:before {
            content: "\e8f1";
        }

        .icon-watch:before {
            content: "\e8f2";
        }

        .icon-wifi:before {
            content: "\e8f3";
        }

        .icon-x-square:before {
            content: "\e8f4";
        }

        .icon-wind:before {
            content: "\e8f5";
        }

        .icon-x:before {
            content: "\e8f6";
        }

        .icon-x-circle:before {
            content: "\e8f7";
        }

        .icon-zap:before {
            content: "\e8f8";
        }

        .icon-zoom-in:before {
            content: "\e8f9";
        }

        .icon-zoom-out:before {
            content: "\e8fa";
        }

        .icon-command:before {
            content: "\e8fb";
        }

        .icon-cloud:before {
            content: "\e8fc";
        }

        .icon-hash:before {
            content: "\e8fd";
        }

        .icon-headphones:before {
            content: "\e8fe";
        }

        .icon-underline:before {
            content: "\e8ff";
        }

        .icon-italic:before {
            content: "\e900";
        }

        .icon-bold:before {
            content: "\e901";
        }

        .icon-crop:before {
            content: "\e902";
        }

        .icon-help-circle:before {
            content: "\e903";
        }

        .icon-paperclip:before {
            content: "\e904";
        }

        .icon-shopping-cart:before {
            content: "\e905";
        }

        .icon-tv:before {
            content: "\e906";
        }

        .icon-wifi-off:before {
            content: "\e907";
        }

        .icon-maximize:before {
            content: "\e908";
        }

        .icon-gitlab:before {
            content: "\e909";
        }

        .icon-sliders:before {
            content: "\e90a";
        }

        .icon-star-on:before {
            content: "\e90b";
        }

        .icon-heart-on:before {
            content: "\e90c";
        }

        .icon-archive:before {
            content: "\e90d";
        }

        .icon-arrow-down-circle:before {
            content: "\e90e";
        }

        .icon-arrow-up-circle:before {
            content: "\e90f";
        }

        .icon-arrow-left-circle:before {
            content: "\e910";
        }

        .icon-arrow-right-circle:before {
            content: "\e911";
        }

        .icon-bar-chart-line-:before {
            content: "\e912";
        }

        .icon-bar-chart-line:before {
            content: "\e913";
        }

        .icon-book-open:before {
            content: "\e914";
        }

        .icon-code:before {
            content: "\e915";
        }

        .icon-database:before {
            content: "\e916";
        }

        .icon-dollar-sign:before {
            content: "\e917";
        }

        .icon-folder-plus:before {
            content: "\e918";
        }

        .icon-gift:before {
            content: "\e919";
        }

        .icon-folder-minus:before {
            content: "\e91a";
        }

        .icon-git-commit:before {
            content: "\e91b";
        }

        .icon-git-branch:before {
            content: "\e91c";
        }

        .icon-git-pull-request:before {
            content: "\e91d";
        }

        .icon-git-merge:before {
            content: "\e91e";
        }

        .icon-linkedin:before {
            content: "\e91f";
        }

        .icon-hard-drive:before {
            content: "\e920";
        }

        .icon-more-vertical-:before {
            content: "\e921";
        }

        .icon-more-horizontal-:before {
            content: "\e922";
        }

        .icon-rss:before {
            content: "\e923";
        }

        .icon-send:before {
            content: "\e924";
        }

        .icon-shield-off:before {
            content: "\e925";
        }

        .icon-shopping-bag:before {
            content: "\e926";
        }

        .icon-terminal:before {
            content: "\e927";
        }

        .icon-truck:before {
            content: "\e928";
        }

        .icon-zap-off:before {
            content: "\e929";
        }

        .icon-youtube:before {
            content: "\e92a";
        }
    </style>

    <link rel="stylesheet" type="text/css" fetchpriority="high"
        href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/style.min.css">


</head>