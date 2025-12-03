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

    // Social media sharing image priority:
// 1. Custom social share image (if exists)
// 2. Site logo PNG (better for social media than SVG)
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
    $theme_base = get_theme_base_path();
    $og_image = $base_url . $theme_base . '/assets/images/new/pavilion-end-social-share.jpg';

    // Check if custom social share image exists, otherwise use site logo PNG
    if (!file_exists(__DIR__ . '/../../assets/images/new/pavilion-end-social-share.jpg')) {
        // Use the main site logo PNG (Facebook/WhatsApp prefer PNG/JPG over SVG)
        $og_image = $base_url . $theme_base . '/assets/images/new/Logo.png';
    }

    // Check if we're on a single post/page
    if (is_single() || is_page()) {
        $og_title = get_the_title() . ' - ' . $site_name;
        $og_description = wp_trim_words(get_the_excerpt(), 25, '...');
        if (empty($og_description)) {
            $og_description = 'Read the latest sports news and updates from Pavilion End.';
        }

        // Get featured image - prioritize large size for better social media display
        if (has_post_thumbnail()) {
            $post_id = get_the_ID();
            $og_image = $base_url . get_the_post_thumbnail_url($post_id, 'full');
            // Fallback to large if full is too big
            if (!$og_image) {
                $og_image = $base_url . get_the_post_thumbnail_url($post_id, 'large');
            }
        } else {
            // If no featured image, use the default social share image
            $theme_base = get_theme_base_path();
            $default_share_image = $base_url . $theme_base . '/assets/images/new/pavilion-end-social-share.jpg';
            if (file_exists(__DIR__ . '/../../assets/images/new/pavilion-end-social-share.jpg')) {
                $og_image = $default_share_image;
            }
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
    <meta name="twitter:card" content="summary_large_image">
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
    <meta property="og:image:type"
        content="<?php echo (strpos($og_image, '.png') !== false) ? 'image/png' : 'image/jpeg'; ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
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