<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="zxx">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
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
        rel="stylesheet">
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
    <link rel="stylesheet" type="text/css"
        href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/iconfont.css">

    <link rel="stylesheet" type="text/css"
        href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/bootstrap.min.css">

    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/owl.carousel.min.css">

    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/slick.css">

    <link rel="stylesheet" type="text/css"
        href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/magnific-popup.css">

    <link rel="stylesheet" type="text/css"
        href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/vendor/animate.css">

    <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/style.css">


</head>