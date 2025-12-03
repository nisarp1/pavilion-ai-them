<?php







/**

 * Starkers functions and definitions

 *

 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.

 *

  * @package 	WordPress

  * @subpackage 	Starkers

  * @since 		Starkers 4.0

 */



/* ========================================================================================================================



Required external files



======================================================================================================================== */



require_once('external/starkers-utilities.php');

// Include author social media fields
require_once(get_template_directory() . '/inc/author-social-fields.php');

// Include post author metabox
require_once(get_template_directory() . '/inc/post-author-metabox.php');

/**
 * Get categories excluding "Featured" category
 * This helper function filters out the "Featured" category from display
 */
function get_filtered_categories($post_id = null)
{
    $categories = get_the_category($post_id);
    if (empty($categories)) {
        return array();
    }

    // Filter out "Featured" category
    $filtered_categories = array_filter($categories, function ($category) {
        return strtolower($category->name) !== 'featured';
    });

    // Re-index array to avoid gaps
    return array_values($filtered_categories);
}

/**
 * Enhanced Open Graph and Social Media Meta Tags
 */
function byline_gulf_enhanced_meta_tags()
{
    // Only add meta tags if not already handled by the header
    if (is_admin())
        return;

    // Add structured data for better SEO
    if (is_single() || is_page()) {
        global $post;
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'description' => wp_trim_words(get_the_excerpt(), 25, '...'),
            'image' => has_post_thumbnail() ? get_the_post_thumbnail_url($post->ID, 'large') : get_stylesheet_directory_uri() . '/assets/images/new/Logo.png',
            'author' => array(
                '@type' => 'Organization',
                'name' => 'Byline Gulf FZE'
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => 'Byline Gulf FZE',
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_stylesheet_directory_uri() . '/assets/images/new/Logo.png'
                )
            ),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id' => get_permalink()
            )
        );

        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
    }
}
add_action('wp_head', 'byline_gulf_enhanced_meta_tags', 1);



/* ========================================================================================================================



Theme specific settings



Uncomment register_nav_menus to enable a single menu with the title of "Primary Navigation" in your theme



======================================================================================================================== */



add_theme_support('post-thumbnails');



register_nav_menus(array('primary' => 'Primary Navigation'));



/* ========================================================================================================================



Actions and Filters



======================================================================================================================== */



add_action('wp_enqueue_scripts', 'starkers_script_enqueuer');

// Enqueue social media platform scripts for embedded content
add_action('wp_enqueue_scripts', 'enqueue_social_media_platform_scripts');

// Enqueue social media platform scripts for embedded content
function enqueue_social_media_platform_scripts()
{
    // Facebook SDK
    wp_enqueue_script(
        'facebook-sdk',
        'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0',
        array(),
        null,
        true
    );

    // Twitter/X Widgets
    wp_enqueue_script(
        'twitter-widgets',
        'https://platform.twitter.com/widgets.js',
        array(),
        null,
        true
    );

    // TikTok Embed
    wp_enqueue_script(
        'tiktok-embed',
        'https://www.tiktok.com/embed.js',
        array(),
        null,
        true
    );

    // Instagram Embed
    wp_enqueue_script(
        'instagram-embed',
        'https://www.instagram.com/embed.js',
        array(),
        null,
        true
    );
}



add_filter('body_class', array('Starkers_Utilities', 'add_slug_to_body_class'));



// Enqueue TinyMCE for LiveBlog WYSIWYG editors
add_action('admin_enqueue_scripts', 'enqueue_liveblog_wysiwyg_scripts');



/* ========================================================================================================================



Custom Post Types - include custom post types and taxonimies here e.g.



e.g. require_once( 'custom-post-types/your-custom-post-type.php' );



======================================================================================================================== */







/* ========================================================================================================================



Scripts



======================================================================================================================== */



/**

 * Add scripts via wp_head()

 *

 * @return void

 * @author Keir Whitaker

 */



function starkers_script_enqueuer()
{
    // Enqueue main assets/css/style.css first
    wp_enqueue_style('byline-main-style', get_stylesheet_directory_uri() . '/assets/css/style.css', array(), '1.0.0');

    // Enqueue main style.css
    wp_enqueue_style('byline-style', get_stylesheet_uri(), array(), '1.0.0');

    // Enqueue custom fonts
    wp_enqueue_style('byline-custom-fonts', get_stylesheet_directory_uri() . '/assets/css/custom-fonts.css', array('byline-main-style', 'byline-style'), '1.0.1');

    // Enqueue font fix CSS LAST to ensure browser compatibility
    wp_enqueue_style('byline-font-fix', get_stylesheet_directory_uri() . '/assets/css/font-fix.css', array('byline-custom-fonts'), '1.0.0');
}



/* ========================================================================================================================



Comments



======================================================================================================================== */



/**

 * Custom callback for outputting comments 

 *

 * @return void

 * @author Keir Whitaker

 */

function starkers_comment($comment, $args, $depth)
{

    $GLOBALS['comment'] = $comment;

    ?>

    <?php if ($comment->comment_approved == '1'): ?>

        <li>

            <article id="comment-<?php comment_ID() ?>">

                <?php echo get_avatar($comment); ?>

                <h4><?php comment_author_link() ?></h4>

                <time><a href="#comment-<?php comment_ID() ?>" pubdate><?php comment_date() ?> at
                        <?php comment_time() ?></a></time>

                <?php comment_text() ?>

            </article>

        <?php endif;

}

function remove_cssjs_ver($src)
{

    if (strpos($src, '?ver='))

        $src = remove_query_arg('ver', $src);

    return $src;

}

add_filter('style_loader_src', 'remove_cssjs_ver', 10, 2);



// Breadcrumbs

function custom_breadcrumbs()
{



    // Settings

    $separator = '&gt;';

    $breadcrums_id = 'breadcrumbs';

    $breadcrums_class = 'breadcrumbs';

    $home_title = 'Homepage';



    // If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)

    $custom_taxonomy = 'product_cat';



    // Get the query & post information

    global $post, $wp_query;



    // Do not display on the homepage

    if (!is_front_page()) {



        // Build the breadcrums

        echo '<ul id="' . $breadcrums_id . '" class="' . $breadcrums_class . '">';



        // Home page

        echo '<li class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a></li>';



        if (is_archive() && !is_tax() && !is_category() && !is_tag()) {



            echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . post_type_archive_title($prefix, false) . '</strong></li>';



        } else if (is_archive() && is_tax() && !is_category() && !is_tag()) {



            // If post is a custom post type

            $post_type = get_post_type();



            // If it is a custom post type display name and link

            if ($post_type != 'post') {



                $post_type_object = get_post_type_object($post_type);

                $post_type_archive = get_post_type_archive_link($post_type);



                echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';

                echo '<li class="separator"> ' . $separator . ' </li>';



            }



            $custom_tax_name = get_queried_object()->name;

            echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . $custom_tax_name . '</strong></li>';



        } else if (is_single()) {



            // If post is a custom post type

            $post_type = get_post_type();



            // If it is a custom post type display name and link

            if ($post_type != 'post') {



                $post_type_object = get_post_type_object($post_type);

                $post_type_archive = get_post_type_archive_link($post_type);



                echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';

                echo '<li class="separator"> ' . $separator . ' </li>';



            }



            // Get post category info

            $category = get_filtered_categories();



            if (!empty($category)) {



                // Get last category post is in

                $last_category = end(array_values($category));



                // Get parent any categories and create array

                $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','), ',');

                $cat_parents = explode(',', $get_cat_parents);



                // Loop through parent categories and store in variable $cat_display

                $cat_display = '';

                foreach ($cat_parents as $parents) {

                    $cat_display .= '<li class="item-cat">' . $parents . '</li>';

                    $cat_display .= '<li class="separator"> ' . $separator . ' </li>';

                }



            }



            // If it's a custom post type within a custom taxonomy

            $taxonomy_exists = taxonomy_exists($custom_taxonomy);

            if (empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {



                $taxonomy_terms = get_the_terms($post->ID, $custom_taxonomy);

                $cat_id = $taxonomy_terms[0]->term_id;

                $cat_nicename = $taxonomy_terms[0]->slug;

                $cat_link = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);

                $cat_name = $taxonomy_terms[0]->name;



            }

            function extractUTubeVidId($url)
            {
                /*
                 * type1: http://www.youtube.com/watch?v=9Jr6OtgiOIw
                 * type2: http://www.youtube.com/watch?v=9Jr6OtgiOIw&feature=related
                 * type3: http://youtu.be/9Jr6OtgiOIw
                 */
                $vid_id = "";
                $flag = false;
                if (isset($url) && !empty($url)) {
                    /*case1 and 2*/
                    $parts = explode("?", $url);
                    if (isset($parts) && !empty($parts) && is_array($parts) && count($parts) > 1) {
                        $params = explode("&", $parts[1]);
                        if (isset($params) && !empty($params) && is_array($params)) {
                            foreach ($params as $param) {
                                $kv = explode("=", $param);
                                if (isset($kv) && !empty($kv) && is_array($kv) && count($kv) > 1) {
                                    if ($kv[0] == 'v') {
                                        $vid_id = $kv[1];
                                        $flag = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    /*case 3*/
                    if (!$flag) {
                        $needle = "youtu.be/";
                        $pos = null;
                        $pos = strpos($url, $needle);
                        if ($pos !== false) {
                            $start = $pos + strlen($needle);
                            $vid_id = substr($url, $start, 11);
                            $flag = true;
                        }
                    }
                    /*case 3*/
                    if (!$flag) {
                        $needle = "youtube.com/shorts/";
                        $pos = null;
                        $pos = strpos($url, $needle);
                        if ($pos !== false) {
                            $start = $pos + strlen($needle);
                            $vid_id = substr($url, $start, 11);
                            $flag = true;
                        }
                    }
                }
                return $vid_id;
            }









            // Check if the post is in a category

            if (!empty($last_category)) {

                echo $cat_display;

                echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';



                // Else if post is in a custom taxonomy

            } else if (!empty($cat_id)) {



                echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '"><a class="bread-cat bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';

                echo '<li class="separator"> ' . $separator . ' </li>';

                echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';



            } else {



                echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';



            }



        } else if (is_category()) {



            // Category page

            echo '<li class="item-current item-cat"><strong class="bread-current bread-cat">' . single_cat_title('', false) . '</strong></li>';



        } else if (is_page()) {



            // Standard page

            if ($post->post_parent) {



                // If child page, get parents 

                $anc = get_post_ancestors($post->ID);



                // Get parents in the right order

                $anc = array_reverse($anc);



                // Parent page loop

                if (!isset($parents))
                    $parents = null;

                foreach ($anc as $ancestor) {

                    $parents .= '<li class="item-parent item-parent-' . $ancestor . '"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';

                }



                // Display parent pages

                echo $parents;



                // Current page

                echo '<li class="item-current item-' . $post->ID . '"><strong title="' . get_the_title() . '"> ' . get_the_title() . '</strong></li>';



            } else {



                // Just display current page if not parents

                echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</strong></li>';



            }



        } else if (is_tag()) {



            // Tag page



            // Get tag information

            $term_id = get_query_var('tag_id');

            $taxonomy = 'post_tag';

            $args = 'include=' . $term_id;

            $terms = get_terms($taxonomy, $args);

            $get_term_id = $terms[0]->term_id;

            $get_term_slug = $terms[0]->slug;

            $get_term_name = $terms[0]->name;



            // Display the tag name

            echo '<li class="item-current item-tag-' . $get_term_id . ' item-tag-' . $get_term_slug . '"><strong class="bread-current bread-tag-' . $get_term_id . ' bread-tag-' . $get_term_slug . '">' . $get_term_name . '</strong></li>';



        } elseif (is_day()) {



            // Day archive



            // Year link

            echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';

            echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';



            // Month link

            echo '<li class="item-month item-month-' . get_the_time('m') . '"><a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</a></li>';

            echo '<li class="separator separator-' . get_the_time('m') . '"> ' . $separator . ' </li>';



            // Day display

            echo '<li class="item-current item-' . get_the_time('j') . '"><strong class="bread-current bread-' . get_the_time('j') . '"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</strong></li>';



        } else if (is_month()) {



            // Month Archive



            // Year link

            echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';

            echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';



            // Month display

            echo '<li class="item-month item-month-' . get_the_time('m') . '"><strong class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</strong></li>';



        } else if (is_year()) {



            // Display year archive

            echo '<li class="item-current item-current-' . get_the_time('Y') . '"><strong class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</strong></li>';



        } else if (is_author()) {



            // Auhor archive



            // Get the author information

            global $author;

            $userdata = get_userdata($author);



            // Display author name

            echo '<li class="item-current item-current-' . $userdata->user_nicename . '"><strong class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</strong></li>';



        } else if (get_query_var('paged')) {



            // Paginated archives

            echo '<li class="item-current item-current-' . get_query_var('paged') . '"><strong class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">' . __('Page') . ' ' . get_query_var('paged') . '</strong></li>';



        } else if (is_search()) {



            // Search results page

            echo '<li class="item-current item-current-' . get_search_query() . '"><strong class="bread-current bread-current-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</strong></li>';



        } elseif (is_404()) {



            // 404 page

            echo '<li>' . 'Error 404' . '</li>';

        }



        echo '</ul>';



    }



}



function new_submenu_class($menu)
{

    $menu = preg_replace('/ class="sub-menu"/', '/ class="dropdown-menu" /', $menu);

    return $menu;

}



add_filter('wp_nav_menu', 'new_submenu_class');



function new_mainmenu_class($menu)
{

    $menu = preg_replace('/ class="menu-item"/', '/ class="dropdown simple-dropdown" /', $menu);

    return $menu;

}



add_filter('wp_nav_menu', 'new_mainmenu_class');









function add_specific_menu_atts($atts, $item, $args)
{



    $menu_items = array(34, 35);



    if (in_array($item->ID, $menu_items)) {

        $atts['data-toggle'] = 'dropdown';

    }



    return $atts;

}

add_filter('nav_menu_link_attributes', 'add_specific_menu_atts', 10, 3);

function meks_time_ago()
{
    return human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ' . __('ago');
}


function job_taxonomy_dropdowns()
{
    $taxonomies = array('job-category', 'job-time', 'job-location'); // Replace with your actual taxonomy slugs

    foreach ($taxonomies as $taxonomy) {
        add_meta_box(
            'job_' . $taxonomy . '_metabox',
            ucfirst(str_replace('_', ' ', $taxonomy)),
            'job_taxonomy_dropdown_callback',
            'job',
            'side',
            'default',
            array('taxonomy' => $taxonomy)
        );
    }
}
add_action('add_meta_boxes', 'job_taxonomy_dropdowns');

function job_taxonomy_dropdown_callback($post, $box)
{
    $taxonomy = $box['args']['taxonomy'];
    $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));

    if ($terms) {
        echo '<select name="' . $taxonomy . '">';
        foreach ($terms as $term) {
            echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
        }
        echo '</select>';
    }
}

// Register Custom Post Type
function custom_register_project_post_type()
{

    $labels = array(
        'name' => _x('Projects', 'Post Type General Name', 'text_domain'),
        'singular_name' => _x('Project', 'Post Type Singular Name', 'text_domain'),
        'menu_name' => __('Projects', 'text_domain'),
        'name_admin_bar' => __('Project', 'text_domain'),
        'archives' => __('Project Archives', 'text_domain'),
        'attributes' => __('Project Attributes', 'text_domain'),
        'parent_item_colon' => __('Parent Project:', 'text_domain'),
        'all_items' => __('All Projects', 'text_domain'),
        'add_new_item' => __('Add New Project', 'text_domain'),
        'add_new' => __('Add New', 'text_domain'),
        'new_item' => __('New Project', 'text_domain'),
        'edit_item' => __('Edit Project', 'text_domain'),
        'update_item' => __('Update Project', 'text_domain'),
        'view_item' => __('View Project', 'text_domain'),
        'view_items' => __('View Projects', 'text_domain'),
        'search_items' => __('Search Project', 'text_domain'),
        'not_found' => __('Project Not found', 'text_domain'),
        'not_found_in_trash' => __('Project Not found in Trash', 'text_domain'),
        'featured_image' => __('Featured Image', 'text_domain'),
        'set_featured_image' => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image' => __('Use as featured image', 'text_domain'),
        'insert_into_item' => __('Insert into project', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this project', 'text_domain'),
        'items_list' => __('Projects list', 'text_domain'),
        'items_list_navigation' => __('Projects list navigation', 'text_domain'),
        'filter_items_list' => __('Filter projects list', 'text_domain'),
    );
    $args = array(
        'label' => __('Project', 'text_domain'),
        'description' => __('Post Type Description', 'text_domain'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'page-attributes'),
        'taxonomies' => array('category', 'post_tag'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );
    register_post_type('project', $args);

}
add_action('init', 'custom_register_project_post_type', 0);

// Register LiveBlog Post Type
function custom_register_liveblog_post_type()
{
    $labels = array(
        'name' => _x('LiveBlogs', 'Post Type General Name', 'text_domain'),
        'singular_name' => _x('LiveBlog', 'Post Type Singular Name', 'text_domain'),
        'menu_name' => __('LiveBlogs', 'text_domain'),
        'name_admin_bar' => __('LiveBlog', 'text_domain'),
        'archives' => __('LiveBlog Archives', 'text_domain'),
        'attributes' => __('LiveBlog Attributes', 'text_domain'),
        'parent_item_colon' => __('Parent LiveBlog:', 'text_domain'),
        'all_items' => __('All LiveBlogs', 'text_domain'),
        'add_new_item' => __('Add New LiveBlog', 'text_domain'),
        'add_new' => __('Add New', 'text_domain'),
        'new_item' => __('New LiveBlog', 'text_domain'),
        'edit_item' => __('Edit LiveBlog', 'text_domain'),
        'update_item' => __('Update LiveBlog', 'text_domain'),
        'view_item' => __('View LiveBlog', 'text_domain'),
        'view_items' => __('View LiveBlogs', 'text_domain'),
        'search_items' => __('Search LiveBlog', 'text_domain'),
        'not_found' => __('LiveBlog Not found', 'text_domain'),
        'not_found_in_trash' => __('LiveBlog Not found in Trash', 'text_domain'),
        'featured_image' => __('Featured Image', 'text_domain'),
        'set_featured_image' => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image' => __('Use as featured image', 'text_domain'),
        'insert_into_item' => __('Insert into liveblog', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this liveblog', 'text_domain'),
        'items_list' => __('LiveBlogs list', 'text_domain'),
        'items_list_navigation' => __('LiveBlogs list navigation', 'text_domain'),
        'filter_items_list' => __('Filter liveblogs list', 'text_domain'),
    );
    $args = array(
        'label' => __('LiveBlog', 'text_domain'),
        'description' => __('LiveBlog Post Type for live updates', 'text_domain'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'page-attributes'),
        'taxonomies' => array('category', 'post_tag'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-megaphone',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'liveblog'),
    );
    register_post_type('liveblog', $args);
}
add_action('init', 'custom_register_liveblog_post_type', 0);

// Register Gallery Post Type
function custom_register_gallery_post_type()
{
    $labels = array(
        'name' => _x('Galleries', 'Post Type General Name', 'text_domain'),
        'singular_name' => _x('Gallery', 'Post Type Singular Name', 'text_domain'),
        'menu_name' => __('Galleries', 'text_domain'),
        'name_admin_bar' => __('Gallery', 'text_domain'),
        'archives' => __('Gallery Archives', 'text_domain'),
        'attributes' => __('Gallery Attributes', 'text_domain'),
        'parent_item_colon' => __('Parent Gallery:', 'text_domain'),
        'all_items' => __('All Galleries', 'text_domain'),
        'add_new_item' => __('Add New Gallery', 'text_domain'),
        'add_new' => __('Add New', 'text_domain'),
        'new_item' => __('New Gallery', 'text_domain'),
        'edit_item' => __('Edit Gallery', 'text_domain'),
        'update_item' => __('Update Gallery', 'text_domain'),
        'view_item' => __('View Gallery', 'text_domain'),
        'view_items' => __('View Galleries', 'text_domain'),
        'search_items' => __('Search Gallery', 'text_domain'),
        'not_found' => __('Gallery Not found', 'text_domain'),
        'not_found_in_trash' => __('Gallery Not found in Trash', 'text_domain'),
        'featured_image' => __('Featured Image', 'text_domain'),
        'set_featured_image' => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image' => __('Use as featured image', 'text_domain'),
        'insert_into_item' => __('Insert into gallery', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this gallery', 'text_domain'),
        'items_list' => __('Galleries list', 'text_domain'),
        'items_list_navigation' => __('Galleries list navigation', 'text_domain'),
        'filter_items_list' => __('Filter galleries list', 'text_domain'),
    );
    $args = array(
        'label' => __('Gallery', 'text_domain'),
        'description' => __('Gallery post type for image galleries', 'text_domain'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'page-attributes'),
        'taxonomies' => array('category', 'post_tag'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-format-gallery',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );
    register_post_type('gallery', $args);
}
add_action('init', 'custom_register_gallery_post_type', 0);

// Register Happening Post Type
function custom_register_happening_post_type()
{
    $labels = array(
        'name' => _x('Happenings', 'Post Type General Name', 'text_domain'),
        'singular_name' => _x('Happening', 'Post Type Singular Name', 'text_domain'),
        'menu_name' => __('Happenings', 'text_domain'),
        'name_admin_bar' => __('Happening', 'text_domain'),
        'archives' => __('Happening Archives', 'text_domain'),
        'attributes' => __('Happening Attributes', 'text_domain'),
        'parent_item_colon' => __('Parent Happening:', 'text_domain'),
        'all_items' => __('All Happenings', 'text_domain'),
        'add_new_item' => __('Add New Happening', 'text_domain'),
        'add_new' => __('Add New', 'text_domain'),
        'new_item' => __('New Happening', 'text_domain'),
        'edit_item' => __('Edit Happening', 'text_domain'),
        'update_item' => __('Update Happening', 'text_domain'),
        'view_item' => __('View Happening', 'text_domain'),
        'view_items' => __('View Happenings', 'text_domain'),
        'search_items' => __('Search Happening', 'text_domain'),
        'not_found' => __('Happening Not found', 'text_domain'),
        'not_found_in_trash' => __('Happening Not found in Trash', 'text_domain'),
        'featured_image' => __('Featured Image', 'text_domain'),
        'set_featured_image' => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image' => __('Use as featured image', 'text_domain'),
        'insert_into_item' => __('Insert into happening', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this happening', 'text_domain'),
        'items_list' => __('Happenings list', 'text_domain'),
        'items_list_navigation' => __('Happenings list navigation', 'text_domain'),
        'filter_items_list' => __('Filter happenings list', 'text_domain'),
    );
    $args = array(
        'label' => __('Happening', 'text_domain'),
        'description' => __('Happening Post Type for events and updates', 'text_domain'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'page-attributes'),
        'taxonomies' => array('category', 'post_tag'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 7,
        'menu_icon' => 'dashicons-calendar-alt',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'happening'),
    );
    register_post_type('happening', $args);

    // Force block editor support for happening post type
    add_post_type_support('happening', 'editor');

    // Flush rewrite rules to ensure the updated post type registration takes effect
    flush_rewrite_rules();
}
add_action('init', 'custom_register_happening_post_type', 0);

// COMPREHENSIVE BLOCK EDITOR FORCING FOR HAPPENING POST TYPE
// This is a multi-layered approach to ensure the Block Editor is used

// 1. Early hook to force block editor (highest priority)
function force_block_editor_for_happening_early($use_block_editor, $post_type)
{
    if ($post_type === 'happening') {
        return true;
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'force_block_editor_for_happening_early', 1, 2);

// Force block editor at init stage
function force_block_editor_for_happening_at_init()
{
    add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
        if ($post_type === 'happening') {
            return true;
        }
        return $use_block_editor;
    }, 99999, 2);
}
add_action('init', 'force_block_editor_for_happening_at_init', 1);

// Force block editor at plugins_loaded stage (even earlier)
function force_block_editor_for_happening_at_plugins_loaded()
{
    add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
        if ($post_type === 'happening') {
            return true;
        }
        return $use_block_editor;
    }, 999999, 2);
}
add_action('plugins_loaded', 'force_block_editor_for_happening_at_plugins_loaded', 1);

// 2. Remove any classic editor filters that might interfere
function remove_classic_editor_filters_for_happening()
{
    global $post_type, $pagenow;

    if (($pagenow === 'post.php' || $pagenow === 'post-new.php') && $post_type === 'happening') {
        // Remove classic editor filters
        remove_filter('use_block_editor_for_post_type', '__return_false');
        remove_filter('use_block_editor_for_post', '__return_false');

        // Remove classic editor plugin filters if they exist
        if (function_exists('classic_editor_plugin_settings')) {
            remove_filter('use_block_editor_for_post_type', 'classic_editor_plugin_settings');
        }
    }
}
add_action('admin_init', 'remove_classic_editor_filters_for_happening', 1);

// 3. Force block editor interface loading
function force_block_editor_interface_for_happening()
{
    global $post_type, $pagenow;

    if (($pagenow === 'post.php' || $pagenow === 'post-new.php') && $post_type === 'happening') {
        // Ensure block editor scripts are loaded
        add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
            if ($post_type === 'happening') {
                return true;
            }
            return $use_block_editor;
        }, 999, 2);

        // Remove classic editor from admin
        add_filter('classic_editor_enabled_editors_for_post_type', function ($editors, $post_type) {
            if ($post_type === 'happening') {
                return array('block');
            }
            return $editors;
        }, 999, 2);

        // Force block editor as default
        add_filter('classic_editor_plugin_settings', function ($settings) {
            global $post_type;
            if ($post_type === 'happening') {
                $settings['editor'] = 'block';
                $settings['allow-users'] = false;
            }
            return $settings;
        }, 999);
    }
}
add_action('admin_init', 'force_block_editor_interface_for_happening', 5);

// 4. Override any TinyMCE settings for happening post type
function override_tinymce_for_happening($init, $editor_id)
{
    global $post;

    if ($post && $post->post_type === 'happening') {
        // Disable TinyMCE for happening post type
        return false;
    }

    return $init;
}
add_filter('tiny_mce_before_init', 'override_tinymce_for_happening', 999, 2);

// 5. Ensure block editor is loaded for happening post type
function ensure_block_editor_scripts_for_happening($hook)
{
    global $post_type;

    if (in_array($hook, array('post.php', 'post-new.php')) && $post_type === 'happening') {
        // Force block editor scripts
        wp_enqueue_script('wp-block-library');
        wp_enqueue_script('wp-format-library');
        wp_enqueue_script('wp-editor');

        // Remove classic editor scripts
        wp_dequeue_script('editor');
        wp_dequeue_script('quicktags');
    }
}
add_action('admin_enqueue_scripts', 'ensure_block_editor_scripts_for_happening', 999);

// 6. Final fallback - force block editor at the latest possible moment
function final_force_block_editor_for_happening($use_block_editor, $post_type)
{
    if ($post_type === 'happening') {
        return true;
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'final_force_block_editor_for_happening', 9999, 2);

// 7. Debug function to check what's happening
function debug_happening_editor_settings()
{
    if (isset($_GET['debug_editor']) && current_user_can('manage_options')) {
        $post_type = 'happening';

        // Test our filters directly
        $test_value = true;
        $test_value = apply_filters('use_block_editor_for_post_type', $test_value, $post_type);

        $use_block_editor = use_block_editor_for_post_type($post_type);

        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
        echo '<h3>Editor Debug Info for Happening Post Type:</h3>';
        echo '<p><strong>Post Type:</strong> ' . $post_type . '</p>';
        echo '<p><strong>Block Editor Enabled:</strong> ' . ($use_block_editor ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>Our Filter Test Result:</strong> ' . ($test_value ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>Classic Editor Plugin Active:</strong> ' . (is_plugin_active('classic-editor/classic-editor.php') ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>WordPress Version:</strong> ' . get_bloginfo('version') . '</p>';
        echo '<p><strong>Classic Editor Option:</strong> ' . get_option('classic-editor-replace') . '</p>';
        echo '<p><strong>Classic Editor Allow Users:</strong> ' . get_option('classic-editor-allow-users') . '</p>';
        echo '<p><strong>Current Page:</strong> ' . $_SERVER['REQUEST_URI'] . '</p>';
        echo '<p><strong>Admin Page:</strong> ' . (isset($_GET['page']) ? $_GET['page'] : 'N/A') . '</p>';

        // Check if our post type is registered
        $post_type_obj = get_post_type_object($post_type);
        echo '<p><strong>Post Type Registered:</strong> ' . ($post_type_obj ? 'Yes' : 'No') . '</p>';
        if ($post_type_obj) {
            echo '<p><strong>Supports Editor:</strong> ' . (post_type_supports($post_type, 'editor') ? 'Yes' : 'No') . '</p>';
            echo '<p><strong>Show in REST:</strong> ' . ($post_type_obj->show_in_rest ? 'Yes' : 'No') . '</p>';
        }

        // Check all active filters
        global $wp_filter;
        if (isset($wp_filter['use_block_editor_for_post_type'])) {
            echo '<p><strong>Active Filters:</strong></p>';
            echo '<ul>';
            foreach ($wp_filter['use_block_editor_for_post_type']->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $id => $callback) {
                    echo '<li>Priority ' . $priority . ': ' . $id . '</li>';
                }
            }
            echo '</ul>';
        }

        echo '</div>';
    }
}
add_action('admin_notices', 'debug_happening_editor_settings');

// 8. Clear cache and force refresh
function clear_happening_editor_cache()
{
    if (isset($_GET['clear_editor_cache']) && current_user_can('manage_options')) {
        // Clear any cached options
        delete_option('classic-editor-replace');
        delete_option('classic-editor-allow-users');

        // Clear post type cache
        wp_cache_delete('post_types', 'posts');

        // Force flush rewrite rules
        flush_rewrite_rules();

        // Clear any transients
        delete_transient('block_editor_settings');

        // Clear object cache if available
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        // Redirect to remove the query parameter
        wp_redirect(admin_url('edit.php?post_type=happening&editor_cache_cleared=1'));
        exit;
    }
}
add_action('admin_init', 'clear_happening_editor_cache');

// Force re-register happening post type with updated settings
function force_reregister_happening_post_type()
{
    if (isset($_GET['reregister_happening']) && current_user_can('manage_options')) {
        // Unregister the post type first
        unregister_post_type('happening');

        // Clear post type cache
        wp_cache_delete('post_types', 'posts');

        // Re-register with updated settings
        custom_register_happening_post_type();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Redirect to remove the query parameter
        wp_redirect(admin_url('edit.php?post_type=happening&reregistered=1'));
        exit;
    }
}
add_action('admin_init', 'force_reregister_happening_post_type');

// 9. Show success message after clearing cache
function show_editor_cache_cleared_message()
{
    if (isset($_GET['editor_cache_cleared'])) {
        echo '<div class="notice notice-success is-dismissible"><p>Editor cache cleared successfully. Please try creating or editing a Happening post now.</p></div>';
    }
    if (isset($_GET['reregistered'])) {
        echo '<div class="notice notice-success is-dismissible"><p>Happening post type re-registered with updated settings. Please try creating or editing a Happening post now.</p></div>';
    }
}
add_action('admin_notices', 'show_editor_cache_cleared_message');

// Flush rewrite rules for happening post type
function flush_happening_rewrite_rules()
{
    if (get_option('happening_flush_rewrite_rules') !== 'done') {
        flush_rewrite_rules();
        update_option('happening_flush_rewrite_rules', 'done');
    }
}
add_action('init', 'flush_happening_rewrite_rules');

// Add Gallery Images Meta Box
function add_gallery_images_meta_box()
{
    add_meta_box(
        'gallery_images_meta_box',
        'Gallery Images',
        'gallery_images_meta_box_callback',
        'gallery',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_gallery_images_meta_box');

// Gallery Images Meta Box Callback
function gallery_images_meta_box_callback($post)
{
    wp_nonce_field('gallery_images_meta_box', 'gallery_images_meta_box_nonce');

    // Get existing gallery images
    $gallery_images = get_post_meta($post->ID, '_gallery_images', true);
    if (!is_array($gallery_images)) {
        $gallery_images = array();
    }

    echo '<div id="gallery-images-container">';
    echo '<p><strong>Add up to 10 images with captions for your gallery:</strong></p>';

    for ($i = 0; $i < 10; $i++) {
        $image_id = isset($gallery_images[$i]['image_id']) ? $gallery_images[$i]['image_id'] : '';
        $caption = isset($gallery_images[$i]['caption']) ? $gallery_images[$i]['caption'] : '';
        $image_url = '';

        if ($image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
        }

        echo '<div class="gallery-image-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9;">';
        echo '<h4 style="margin-top: 0;">Image ' . ($i + 1) . '</h4>';

        echo '<div style="display: flex; gap: 15px; align-items: flex-start;">';

        // Image preview
        echo '<div style="flex: 0 0 150px;">';
        echo '<div id="image-preview-' . $i . '" style="width: 150px; height: 150px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; background: #fff; margin-bottom: 10px;">';
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" style="max-width: 100%; max-height: 100%; object-fit: cover;">';
        } else {
            echo '<span style="color: #999;">No image selected</span>';
        }
        echo '</div>';
        echo '<input type="button" class="button" value="Select Image" onclick="selectGalleryImage(' . $i . ')">';
        echo '<input type="button" class="button" value="Remove Image" onclick="removeGalleryImage(' . $i . ')" style="margin-left: 5px;">';
        echo '</div>';

        // Image ID and Caption fields
        echo '<div style="flex: 1;">';
        echo '<p><label><strong>Image ID:</strong></label><br>';
        echo '<input type="text" id="gallery_image_id_' . $i . '" name="gallery_images[' . $i . '][image_id]" value="' . esc_attr($image_id) . '" style="width: 100%;" readonly>';
        echo '</p>';

        echo '<p><label><strong>Caption:</strong></label><br>';
        echo '<textarea id="gallery_caption_' . $i . '" name="gallery_images[' . $i . '][caption]" style="width: 100%; height: 80px;">' . esc_textarea($caption) . '</textarea>';
        echo '</p>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }

    echo '</div>';

    // JavaScript for image selection
    echo '<script>
    function selectGalleryImage(index) {
        var frame = wp.media({
            title: "Select Gallery Image",
            multiple: false
        });
        
        frame.on("select", function() {
            var attachment = frame.state().get("selection").first().toJSON();
            document.getElementById("gallery_image_id_" + index).value = attachment.id;
            document.getElementById("image-preview-" + index).innerHTML = \'<img src="\' + attachment.sizes.thumbnail.url + \'" style="max-width: 100%; max-height: 100%; object-fit: cover;">\';
        });
        
        frame.open();
    }
    
    function removeGalleryImage(index) {
        document.getElementById("gallery_image_id_" + index).value = "";
        document.getElementById("gallery_caption_" + index).value = "";
        document.getElementById("image-preview-" + index).innerHTML = \'<span style="color: #999;">No image selected</span>\';
    }
    </script>';
}

// Save Gallery Images Meta Box Data
function save_gallery_images_meta_box($post_id)
{
    // Check if nonce is valid
    if (!isset($_POST['gallery_images_meta_box_nonce']) || !wp_verify_nonce($_POST['gallery_images_meta_box_nonce'], 'gallery_images_meta_box')) {
        return;
    }

    // Check if user has permissions to save data
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if not an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Save gallery images data
    if (isset($_POST['gallery_images'])) {
        $gallery_images = array();

        foreach ($_POST['gallery_images'] as $index => $image_data) {
            if (!empty($image_data['image_id'])) {
                $gallery_images[] = array(
                    'image_id' => intval($image_data['image_id']),
                    'caption' => sanitize_textarea_field($image_data['caption'])
                );
            }
        }

        update_post_meta($post_id, '_gallery_images', $gallery_images);
    } else {
        delete_post_meta($post_id, '_gallery_images');
    }
}
add_action('save_post', 'save_gallery_images_meta_box');

// Helper function to get gallery images
function get_gallery_images($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $gallery_images = get_post_meta($post_id, '_gallery_images', true);
    if (!is_array($gallery_images)) {
        return array();
    }

    return $gallery_images;
}

// Helper function to get post views count
function get_post_views($post_id)
{
    $count_key = 'post_views_count';
    $count = get_post_meta($post_id, $count_key, true);
    if ($count == '') {
        delete_post_meta($post_id, $count_key);
        add_post_meta($post_id, $count_key, '0');
        return "0";
    }
    return $count;
}

// Helper function to set post views count
function set_post_views($post_id)
{
    $count_key = 'post_views_count';
    $count = get_post_meta($post_id, $count_key, true);
    if ($count == '') {
        $count = 0;
        delete_post_meta($post_id, $count_key);
        add_post_meta($post_id, $count_key, '0');
    } else {
        $count++;
        update_post_meta($post_id, $count_key, $count);
    }
}

// Helper function to get share count (placeholder)
function get_share_count()
{
    // This is a placeholder function
    // You can implement actual share counting logic here
    return "0";
}

// Track post views
function track_post_views()
{
    if (is_single()) {
        set_post_views(get_the_ID());
    }
}
add_action('wp_head', 'track_post_views');

// Helper function to safely get category link
function get_safe_category_link($slug)
{
    $category = get_category_by_slug($slug);
    if ($category) {
        return get_category_link($category->term_id);
    }
    return home_url(); // Fallback to home page if category doesn't exist
}

/**
 * Check if a post belongs to video category and return video play button
 */
function get_video_play_button($post_id = null)
{
    $categories = get_filtered_categories($post_id);

    if (!empty($categories)) {
        foreach ($categories as $category) {
            $slug = is_object($category) ? ($category->slug ?? '') : (isset($category['slug']) ? $category['slug'] : '');

            if ($slug === 'video') {
                return '<div class="video-popup video-play-btn"></div>';
            }
        }
    }

    return '';
}

// Disable Gutenberg for LiveBlog post type
function disable_gutenberg_for_liveblog($use_block_editor, $post_type)
{
    if ($post_type === 'liveblog') {
        return false;
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'disable_gutenberg_for_liveblog', 100, 2);

// LiveBlog Meta Boxes for Additional Post Blocks
function add_liveblog_meta_boxes()
{
    add_meta_box(
        'liveblog_additional_blocks',
        'Additional Post Blocks',
        'liveblog_additional_blocks_callback',
        'liveblog',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_liveblog_meta_boxes');

function liveblog_additional_blocks_callback($post)
{
    wp_nonce_field('liveblog_additional_blocks_nonce', 'liveblog_additional_blocks_nonce');

    // Get existing custom blocks
    $custom_blocks = get_post_meta($post->ID, '_liveblog_custom_blocks', true);
    if (!is_array($custom_blocks)) {
        $custom_blocks = array();
    }

    // Debug logging
    error_log('LiveBlog callback - Post ID: ' . $post->ID);
    error_log('LiveBlog callback - Custom blocks from meta: ' . print_r($custom_blocks, true));
    error_log('LiveBlog callback - Number of blocks: ' . count($custom_blocks));

    ?>
        <div id="liveblog-custom-editor">
            <div class="editor-header">
                <h3>LiveBlog Custom Editor</h3>
                <p>Use this specialized editor to create rich LiveBlog content with different block types.</p>
            </div>

            <div class="editor-content">
                <!-- Blocks will be loaded by JavaScript -->
            </div>

            <div class="editor-footer">
                <button type="button" class="button button-primary" onclick="saveLiveBlogContent()">Save LiveBlog
                    Content</button>
                <button type="button" class="button button-secondary" onclick="loadLiveBlogContent()">Load Saved
                    Content</button>
            </div>
        </div>

        <script>
            // Initialize custom blocks from PHP data
            <?php
            $json_data = json_encode($custom_blocks);
            if ($json_data === false) {
                $json_data = '[]';
                error_log('LiveBlog callback - JSON encode failed: ' . json_last_error_msg());
            }
            ?>
            console.log('PHP custom_blocks data:', <?php echo $json_data; ?>);
            window.liveblogCustomBlocks = <?php echo $json_data; ?>;
            console.log('window.liveblogCustomBlocks set to:', window.liveblogCustomBlocks);
        </script>
        <?php
}

// Helper function to render custom blocks
function renderCustomBlock($block, $index)
{
    // Debug: Log the block data being rendered
    error_log('LiveBlog renderCustomBlock - Block data for index ' . $index . ': ' . print_r($block, true));

    $blockType = isset($block['type']) ? $block['type'] : 'text';
    $content = isset($block['content']) ? $block['content'] : '';
    $timestamp = isset($block['timestamp']) ? $block['timestamp'] : '';
    $timeDateContent = isset($block['timeDateContent']) ? $block['timeDateContent'] : '';
    $autoEmbedUrls = isset($block['autoEmbedUrls']) ? $block['autoEmbedUrls'] : '[]';

    // Format timestamp for display and datetime-local input
    $formattedTimestamp = '';
    $datetimeLocalValue = '';
    if ($timestamp) {
        $date = new DateTime($timestamp);
        $formattedTimestamp = $date->format('Y-m-d H:i:s');
        $datetimeLocalValue = $date->format('Y-m-d\TH:i');
    }

    // Get draft status
    $isDraft = isset($block['isDraft']) ? $block['isDraft'] : false;
    $draftClass = $isDraft ? ' draft-block' : '';

    switch ($blockType) {
        case 'text':
            $titleColumn = isset($block['titleColumn']) ? $block['titleColumn'] : '';
            echo '<div class="liveblog-block text-block' . $draftClass . '" data-block-type="text" data-index="' . $index . '" data-timestamp="' . esc_attr($timestamp) . '">';
            echo '<div class="block-title-column">';
            echo '<div class="title-content" contenteditable="true">' . wp_kses_post($titleColumn) . '</div>';
            echo '<div class="title-timestamp">';
            echo '<span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time">' . esc_html($formattedTimestamp) . '</span>';
            echo '</div>';
            echo '</div>';
            echo '<div class="block-content" contenteditable="true">' . wp_kses_post($content) . '</div>';
            // Add auto-embed area if it exists
            if ($autoEmbedUrls && $autoEmbedUrls !== '[]') {
                // Debug: Log the autoEmbedUrls
                error_log('LiveBlog renderCustomBlock - autoEmbedUrls: ' . $autoEmbedUrls);

                // Store URLs in data attribute for JavaScript to regenerate embeds
                echo '<div class="auto-embed-area" data-embed-urls="' . esc_attr($autoEmbedUrls) . '"></div>';
                error_log('LiveBlog renderCustomBlock - Created auto-embed-area div with data-embed-urls: ' . esc_attr($autoEmbedUrls));
            } else {
                error_log('LiveBlog renderCustomBlock - No autoEmbedUrls or empty array: ' . $autoEmbedUrls);
            }
            echo '<div class="block-actions">';
            echo '<button class="add-block-btn" data-type="text">+ Add Text</button>';
            echo '<button class="add-block-btn" data-type="image">+ Add Image</button>';
            echo '<button class="add-block-btn" data-type="video">+ Add Video</button>';
            echo '<button class="add-block-btn" data-type="quote">+ Add Quote</button>';
            echo '<button class="add-block-btn" data-type="social">+ Add Social</button>';
            echo '<button class="publish-block-btn">' . ($isDraft ? 'Publish' : 'Update') . '</button>';
            echo '<button class="draft-block-btn">' . ($isDraft ? 'Drafted' : 'Save Draft') . '</button>';
            echo '<button class="remove-block-btn">Remove</button>';
            echo '</div></div>';
            break;
        case 'time-date':
            echo '<div class="liveblog-block time-date-block' . $draftClass . '" data-block-type="time-date" data-index="' . $index . '" data-timestamp="' . esc_attr($timestamp) . '">';
            echo '<div class="block-header"><span class="block-icon">📅</span><span class="block-title">Time/Date Update</span></div>';
            echo '<div class="block-timestamp-container">';
            echo '<label>Timestamp:</label>';
            echo '<input type="datetime-local" class="block-timestamp-input" value="' . esc_attr($datetimeLocalValue) . '">';
            echo '<span class="block-timestamp-display">' . esc_html($formattedTimestamp) . '</span>';
            echo '</div>';
            echo '<div class="time-date-content" contenteditable="true">' . wp_kses_post($timeDateContent) . '</div>';
            echo '<div class="block-actions">';
            echo '<button class="add-block-btn" data-type="text">+ Add Text</button>';
            echo '<button class="add-block-btn" data-type="image">+ Add Image</button>';
            echo '<button class="add-block-btn" data-type="video">+ Add Video</button>';
            echo '<button class="add-block-btn" data-type="quote">+ Add Quote</button>';
            echo '<button class="add-block-btn" data-type="social">+ Add Social</button>';
            echo '<button class="save-block-btn">Save Block</button>';
            echo '<button class="draft-block-btn">Save as Draft</button>';
            echo '<button class="remove-block-btn">Remove</button>';
            echo '</div></div>';
            break;
        case 'image':
            $imageId = isset($block['imageId']) ? intval($block['imageId']) : 0;
            $imageCaption = isset($block['imageCaption']) ? $block['imageCaption'] : '';
            $imageUrl = $imageId ? wp_get_attachment_url($imageId) : '';
            echo '<div class="liveblog-block image-block' . $draftClass . '" data-block-type="image" data-index="' . $index . '" data-timestamp="' . esc_attr($timestamp) . '" data-image-id="' . $imageId . '">';
            echo '<div class="block-header"><span class="block-icon">🖼️</span><span class="block-title">Image Block</span></div>';
            echo '<div class="block-timestamp-container">';
            echo '<label>Timestamp:</label>';
            echo '<span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time">' . esc_html($formattedTimestamp) . '</span>';
            echo '</div>';
            echo '<div class="image-upload-area">';
            if ($imageUrl) {
                echo '<img src="' . esc_url($imageUrl) . '" class="uploaded-image" style="max-width: 100%; height: auto;">';
            } else {
                echo '<div class="upload-placeholder">Click to upload image or drag and drop</div>';
            }
            echo '<input type="file" class="image-upload-input" accept="image/*" style="display: none;">';
            echo '</div>';
            echo '<div class="image-caption" contenteditable="true">' . wp_kses_post($imageCaption) . '</div>';
            echo '<div class="block-actions">';
            echo '<button class="add-block-btn" data-type="text">+ Add Text</button>';
            echo '<button class="add-block-btn" data-type="image">+ Add Image</button>';
            echo '<button class="add-block-btn" data-type="video">+ Add Video</button>';
            echo '<button class="add-block-btn" data-type="quote">+ Add Quote</button>';
            echo '<button class="add-block-btn" data-type="social">+ Add Social</button>';
            echo '<button class="publish-block-btn">' . ($isDraft ? 'Publish' : 'Update') . '</button>';
            echo '<button class="draft-block-btn">' . ($isDraft ? 'Drafted' : 'Save Draft') . '</button>';
            echo '<button class="remove-block-btn">Remove</button>';
            echo '</div></div>';
            break;
        case 'video':
            $videoUrl = isset($block['videoUrl']) ? $block['videoUrl'] : '';
            $videoCaption = isset($block['videoCaption']) ? $block['videoCaption'] : '';
            $videoEmbed = isset($block['videoEmbed']) ? $block['videoEmbed'] : '';
            echo '<div class="liveblog-block video-block' . $draftClass . '" data-block-type="video" data-index="' . $index . '" data-timestamp="' . esc_attr($timestamp) . '">';
            echo '<div class="block-header"><span class="block-icon">🎥</span><span class="block-title">Video Block</span></div>';
            echo '<div class="block-timestamp-container">';
            echo '<label>Timestamp:</label>';
            echo '<span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time">' . esc_html($formattedTimestamp) . '</span>';
            echo '</div>';
            echo '<div class="video-input-area">';
            echo '<input type="url" class="video-url" value="' . esc_attr($videoUrl) . '" placeholder="Enter video URL...">';
            echo '<button class="embed-video-btn">Embed</button>';
            echo '</div>';
            if ($videoEmbed) {
                echo '<div class="video-embed-area">' . $videoEmbed . '</div>';
            } else {
                echo '<div class="video-embed-area"></div>';
            }
            echo '<div class="video-caption" contenteditable="true">' . wp_kses_post($videoCaption) . '</div>';
            echo '<div class="block-actions">';
            echo '<button class="add-block-btn" data-type="text">+ Add Text</button>';
            echo '<button class="add-block-btn" data-type="image">+ Add Image</button>';
            echo '<button class="add-block-btn" data-type="video">+ Add Video</button>';
            echo '<button class="add-block-btn" data-type="quote">+ Add Quote</button>';
            echo '<button class="add-block-btn" data-type="social">+ Add Social</button>';
            echo '<button class="publish-block-btn">' . ($isDraft ? 'Publish' : 'Update') . '</button>';
            echo '<button class="draft-block-btn">' . ($isDraft ? 'Drafted' : 'Save Draft') . '</button>';
            echo '<button class="remove-block-btn">Remove</button>';
            echo '</div></div>';
            break;
        case 'quote':
            $quoteContent = isset($block['quoteContent']) ? $block['quoteContent'] : '';
            $quoteAttribution = isset($block['quoteAttribution']) ? $block['quoteAttribution'] : '';
            echo '<div class="liveblog-block quote-block' . $draftClass . '" data-block-type="quote" data-index="' . $index . '" data-timestamp="' . esc_attr($timestamp) . '">';
            echo '<div class="block-header"><span class="block-icon">💬</span><span class="block-title">Quote Block</span></div>';
            echo '<div class="block-timestamp-container">';
            echo '<label>Timestamp:</label>';
            echo '<span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time">' . esc_html($formattedTimestamp) . '</span>';
            echo '</div>';
            echo '<div class="quote-content" contenteditable="true">' . wp_kses_post($quoteContent) . '</div>';
            echo '<div class="quote-attribution" contenteditable="true">' . wp_kses_post($quoteAttribution) . '</div>';
            echo '<div class="block-actions">';
            echo '<button class="add-block-btn" data-type="text">+ Add Text</button>';
            echo '<button class="add-block-btn" data-type="image">+ Add Image</button>';
            echo '<button class="add-block-btn" data-type="video">+ Add Video</button>';
            echo '<button class="add-block-btn" data-type="quote">+ Add Quote</button>';
            echo '<button class="add-block-btn" data-type="social">+ Add Social</button>';
            echo '<button class="publish-block-btn">' . ($isDraft ? 'Publish' : 'Update') . '</button>';
            echo '<button class="draft-block-btn">' . ($isDraft ? 'Drafted' : 'Save Draft') . '</button>';
            echo '<button class="remove-block-btn">Remove</button>';
            echo '</div></div>';
            break;
        case 'social':
            $socialUrl = isset($block['socialUrl']) ? $block['socialUrl'] : '';
            $socialEmbed = isset($block['socialEmbed']) ? $block['socialEmbed'] : '';
            echo '<div class="liveblog-block social-block' . $draftClass . '" data-block-type="social" data-index="' . $index . '" data-timestamp="' . esc_attr($timestamp) . '">';
            echo '<div class="block-header"><span class="block-icon">📱</span><span class="block-title">Social Block</span></div>';
            echo '<div class="block-timestamp-container">';
            echo '<label>Timestamp:</label>';
            echo '<span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time">' . esc_html($formattedTimestamp) . '</span>';
            echo '</div>';
            echo '<div class="social-input-area">';
            echo '<input type="url" class="social-url" value="' . esc_attr($socialUrl) . '" placeholder="Enter social media URL...">';
            echo '<button class="embed-social-btn">Embed</button>';
            echo '</div>';
            if ($socialEmbed) {
                echo '<div class="social-embed-area">' . $socialEmbed . '</div>';
            } else {
                echo '<div class="social-embed-area"></div>';
            }
            echo '<div class="block-actions">';
            echo '<button class="add-block-btn" data-type="text">+ Add Text</button>';
            echo '<button class="add-block-btn" data-type="image">+ Add Image</button>';
            echo '<button class="add-block-btn" data-type="video">+ Add Video</button>';
            echo '<button class="add-block-btn" data-type="quote">+ Add Quote</button>';
            echo '<button class="add-block-btn" data-type="social">+ Add Social</button>';
            echo '<button class="publish-block-btn">' . ($isDraft ? 'Publish' : 'Update') . '</button>';
            echo '<button class="draft-block-btn">' . ($isDraft ? 'Drafted' : 'Save Draft') . '</button>';
            echo '<button class="remove-block-btn">Remove</button>';
            echo '</div></div>';
            break;
    }
}

function save_liveblog_additional_blocks($post_id)
{
    if (!isset($_POST['liveblog_additional_blocks_nonce']) || !wp_verify_nonce($_POST['liveblog_additional_blocks_nonce'], 'liveblog_additional_blocks_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if this is a liveblog post type
    $post_type = get_post_type($post_id);
    if ($post_type !== 'liveblog') {
        return;
    }

    // If we're using the custom editor (which saves via AJAX), don't interfere
    // Only process traditional form data if it exists
    if (isset($_POST['liveblog_blocks'])) {
        $blocks = array();
        $current_time = current_time('mysql');

        // Debug logging
        error_log("DEBUG: save_liveblog_additional_blocks - Processing " . count($_POST['liveblog_blocks']) . " blocks");

        foreach ($_POST['liveblog_blocks'] as $index => $block_data) {
            if (!empty($block_data['title']) || !empty($block_data['content'])) {
                // Get existing block data to preserve timestamps and content
                $existing_blocks = get_post_meta($post_id, '_liveblog_custom_blocks', true);
                $existing_block = isset($existing_blocks[$index]) ? $existing_blocks[$index] : array();

                // Get content - prefer form data, fallback to existing content if empty
                $content = wp_kses_post($block_data['content']);
                error_log("DEBUG: save_liveblog_additional_blocks - Block $index raw content from POST: " . strlen($block_data['content']) . " chars");
                error_log("DEBUG: save_liveblog_additional_blocks - Block $index content after wp_kses_post: " . strlen($content) . " chars");

                if (empty($content) && !empty($existing_block['content'])) {
                    $content = $existing_block['content'];
                    error_log("DEBUG: save_liveblog_additional_blocks - Using existing content for block $index");
                }

                error_log("DEBUG: save_liveblog_additional_blocks - Block $index final content length: " . strlen($content));

                $block = array(
                    'title' => sanitize_text_field($block_data['title']),
                    'content' => $content,
                    'media_id' => intval($block_data['media_id']),
                    'media_url' => esc_url_raw($block_data['media_url']),
                    'timestamp' => sanitize_text_field($block_data['timestamp']),
                    'status' => sanitize_text_field($block_data['status'] ?? $existing_block['status'] ?? 'draft'),
                    'last_modified' => $current_time
                );

                // Preserve created date if it exists
                if (!empty($existing_block['created_date'])) {
                    $block['created_date'] = $existing_block['created_date'];
                } else {
                    $block['created_date'] = $current_time;
                }

                // Set published date if status is published and wasn't published before
                if ($block['status'] === 'published' && empty($existing_block['published_date'])) {
                    $block['published_date'] = $current_time;
                } elseif (!empty($existing_block['published_date'])) {
                    $block['published_date'] = $existing_block['published_date'];
                }

                $blocks[] = $block;
            }
        }
        update_post_meta($post_id, '_liveblog_custom_blocks', $blocks);
    }
    // Don't delete blocks if no traditional form data - the custom editor saves via AJAX
}

add_action('save_post', 'save_liveblog_additional_blocks');

// AJAX handlers for LiveBlog block operations
add_action('wp_ajax_publish_liveblog_block', 'publish_liveblog_block_ajax');
add_action('wp_ajax_draft_liveblog_block', 'draft_liveblog_block_ajax');
add_action('wp_ajax_update_liveblog_block', 'update_liveblog_block_ajax');
add_action('wp_ajax_add_liveblog_block', 'add_liveblog_block_ajax');

function publish_liveblog_block_ajax()
{
    // Check if nonce is provided
    if (!isset($_POST['nonce'])) {
        wp_send_json_error('No nonce provided');
        return;
    }

    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'liveblog_block_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }

    // Check if post_id is provided
    if (!isset($_POST['post_id'])) {
        wp_send_json_error('No post_id provided');
        return;
    }

    // Check if block_index is provided
    if (!isset($_POST['block_index'])) {
        wp_send_json_error('No block_index provided');
        return;
    }

    $post_id = intval($_POST['post_id']);
    $block_index = intval($_POST['block_index']);

    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error('Unauthorized');
        return;
    }

    $blocks = get_post_meta($post_id, '_liveblog_custom_blocks', true);

    if (!is_array($blocks) || !isset($blocks[$block_index])) {
        wp_send_json_error('Block not found');
        return;
    }

    $blocks[$block_index]['status'] = 'published';
    $blocks[$block_index]['published_date'] = current_time('mysql');
    $blocks[$block_index]['last_modified'] = current_time('mysql');

    $update_result = update_post_meta($post_id, '_liveblog_custom_blocks', $blocks);

    if ($update_result === false) {
        wp_send_json_error('Failed to update post meta');
        return;
    }

    wp_send_json_success(array(
        'message' => 'Block published successfully',
        'published_date' => $blocks[$block_index]['published_date']
    ));
}

function draft_liveblog_block_ajax()
{
    check_ajax_referer('liveblog_block_nonce', 'nonce');

    $post_id = intval($_POST['post_id']);
    $block_index = intval($_POST['block_index']);

    if (!current_user_can('edit_post', $post_id)) {
        wp_die('Unauthorized');
    }

    $blocks = get_post_meta($post_id, '_liveblog_custom_blocks', true);
    if (!is_array($blocks) || !isset($blocks[$block_index])) {
        wp_send_json_error('Block not found');
    }

    $blocks[$block_index]['status'] = 'draft';
    $blocks[$block_index]['last_modified'] = current_time('mysql');

    update_post_meta($post_id, '_liveblog_custom_blocks', $blocks);

    wp_send_json_success(array(
        'message' => 'Block saved as draft'
    ));
}

function update_liveblog_block_ajax()
{
    check_ajax_referer('liveblog_block_nonce', 'nonce');

    $post_id = intval($_POST['post_id']);
    $block_index = intval($_POST['block_index']);
    $block_data = $_POST['block_data'];

    if (!current_user_can('edit_post', $post_id)) {
        wp_die('Unauthorized');
    }

    $blocks = get_post_meta($post_id, '_liveblog_custom_blocks', true);
    if (!is_array($blocks) || !isset($blocks[$block_index])) {
        wp_send_json_error('Block not found');
    }

    // Update block data
    $blocks[$block_index]['title'] = sanitize_text_field($block_data['title']);
    $blocks[$block_index]['content'] = wp_kses_post($block_data['content']);
    $blocks[$block_index]['media_id'] = intval($block_data['media_id']);
    $blocks[$block_index]['media_url'] = esc_url_raw($block_data['media_url']);
    $blocks[$block_index]['timestamp'] = sanitize_text_field($block_data['timestamp']);
    $blocks[$block_index]['last_modified'] = current_time('mysql');

    // Preserve existing status if not provided in the update
    if (isset($block_data['status'])) {
        $blocks[$block_index]['status'] = sanitize_text_field($block_data['status']);
    }

    update_post_meta($post_id, '_liveblog_custom_blocks', $blocks);

    wp_send_json_success(array(
        'message' => 'Block updated successfully'
    ));
}

// Helper function to get LiveBlog additional blocks
function get_liveblog_additional_blocks($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $blocks = get_post_meta($post_id, '_liveblog_custom_blocks', true);
    if (!is_array($blocks)) {
        return array();
    }

    return $blocks;
}

// Helper function to get only published LiveBlog blocks
function get_published_liveblog_blocks($post_id = null)
{
    $blocks = get_liveblog_additional_blocks($post_id);

    // Filter only published blocks
    $published_blocks = array_filter($blocks, function ($block) {
        return isset($block['status']) && $block['status'] === 'published';
    });

    // Sort blocks by timestamp in descending order (newest first)
    usort($published_blocks, function ($a, $b) use ($blocks) {
        $timestamp_a = isset($a['timestamp']) ? strtotime($a['timestamp']) : 0;
        $timestamp_b = isset($b['timestamp']) ? strtotime($b['timestamp']) : 0;

        // If timestamps are equal, sort by creation order (newer blocks first)
        if ($timestamp_a === $timestamp_b) {
            // Use the array key as a fallback for creation order
            $key_a = array_search($a, $blocks);
            $key_b = array_search($b, $blocks);
            return $key_b - $key_a; // Descending order
        }

        return $timestamp_b - $timestamp_a; // Descending order
    });

    return $published_blocks;
}

// Template redirect for LiveBlog posts
function liveblog_template_redirect()
{
    if (is_singular('liveblog')) {
        $template = get_template_directory() . '/liveblog.php';
        if (file_exists($template)) {
            include $template;
            exit;
        }
    }
}
add_action('template_redirect', 'liveblog_template_redirect');

// Flush permalinks on theme activation to register new post types
function flush_rewrite_rules_on_activation()
{
    // Register the post type first
    custom_register_liveblog_post_type();

    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'flush_rewrite_rules_on_activation');

// Also flush on init to ensure post types are registered
function ensure_liveblog_rewrite_rules()
{
    if (get_option('liveblog_rewrite_flushed') != 'yes') {
        flush_rewrite_rules();
        update_option('liveblog_rewrite_flushed', 'yes');
    }
}
add_action('init', 'ensure_liveblog_rewrite_rules', 20);

// Clean up auto-draft blocks older than 7 days
function cleanup_liveblog_auto_drafts()
{
    $liveblogs = get_posts(array(
        'post_type' => 'liveblog',
        'posts_per_page' => -1,
        'post_status' => 'any'
    ));

    $cutoff_date = date('Y-m-d H:i:s', strtotime('-7 days'));

    foreach ($liveblogs as $liveblog) {
        $blocks = get_post_meta($liveblog->ID, '_liveblog_custom_blocks', true);
        if (!is_array($blocks))
            continue;

        $updated = false;
        foreach ($blocks as $index => $block) {
            if (isset($block['status']) && $block['status'] === 'auto-draft') {
                if (isset($block['created_date']) && $block['created_date'] < $cutoff_date) {
                    unset($blocks[$index]);
                    $updated = true;
                }
            }
        }

        if ($updated) {
            $blocks = array_values($blocks); // Re-index array
            update_post_meta($liveblog->ID, '_liveblog_custom_blocks', $blocks);
        }
    }
}

// Run cleanup daily
if (!wp_next_scheduled('liveblog_auto_draft_cleanup')) {
    wp_schedule_event(time(), 'daily', 'liveblog_auto_draft_cleanup');
}
add_action('liveblog_auto_draft_cleanup', 'cleanup_liveblog_auto_drafts');

// Add LiveBlog block count to admin columns
function add_liveblog_admin_columns($columns)
{
    $columns['block_count'] = 'Blocks';
    $columns['published_blocks'] = 'Published';
    return $columns;
}
add_filter('manage_liveblog_posts_columns', 'add_liveblog_admin_columns');

function populate_liveblog_admin_columns($column, $post_id)
{
    if ($column === 'block_count') {
        $blocks = get_liveblog_additional_blocks($post_id);
        echo count($blocks);
    } elseif ($column === 'published_blocks') {
        $published_blocks = get_published_liveblog_blocks($post_id);
        echo count($published_blocks);
    }
}
add_action('manage_liveblog_posts_custom_column', 'populate_liveblog_admin_columns', 10, 2);

// Migrate existing blocks to have status (backward compatibility)
function migrate_liveblog_blocks_status()
{
    $liveblogs = get_posts(array(
        'post_type' => 'liveblog',
        'posts_per_page' => -1,
        'post_status' => 'any'
    ));

    foreach ($liveblogs as $liveblog) {
        $blocks = get_post_meta($liveblog->ID, '_liveblog_custom_blocks', true);
        if (!is_array($blocks))
            continue;

        $updated = false;
        foreach ($blocks as $index => $block) {
            if (!isset($block['status'])) {
                $blocks[$index]['status'] = 'published'; // Assume existing blocks are published
                $blocks[$index]['created_date'] = current_time('mysql');
                $blocks[$index]['last_modified'] = current_time('mysql');
                $updated = true;
            }
        }

        if ($updated) {
            update_post_meta($liveblog->ID, '_liveblog_custom_blocks', $blocks);
        }
    }
}

// Run migration once on theme activation
add_action('after_switch_theme', 'migrate_liveblog_blocks_status');

// Enqueue TinyMCE scripts for LiveBlog WYSIWYG editors
function enqueue_liveblog_wysiwyg_scripts($hook)
{
    global $post_type;

    // Only load on LiveBlog post edit pages
    if ($post_type === 'liveblog' && in_array($hook, array('post.php', 'post-new.php'))) {
        wp_enqueue_editor();
        wp_enqueue_media();

        // Override default TinyMCE settings to exclude print plugin
        add_filter('tiny_mce_before_init', 'override_tinymce_settings', 10, 2);
    }
}

// Enqueue social media embedder scripts
function enqueue_social_media_embedder_scripts($hook)
{
    global $post_type;

    // Load on post edit pages and LiveBlog edit pages
    if (
        in_array($hook, array('post.php', 'post-new.php')) &&
        (in_array($post_type, array('post', 'liveblog')) || !$post_type)
    ) {

        wp_enqueue_script(
            'social-media-embedder',
            get_stylesheet_directory_uri() . '/assets/js/social-media-embedder.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'enqueue_social_media_embedder_scripts');

// Override TinyMCE settings to exclude print plugin
function override_tinymce_settings($init, $editor_id)
{
    // Ensure print plugin is not included
    if (isset($init['plugins'])) {
        $plugins = explode(',', $init['plugins']);
        $plugins = array_filter($plugins, function ($plugin) {
            return trim($plugin) !== 'print';
        });
        $init['plugins'] = implode(',', $plugins);
    }

    return $init;
}
add_filter('tiny_mce_before_init', 'override_tinymce_settings', 10, 2);

function add_liveblog_block_ajax()
{
    // Check if nonce is provided
    if (!isset($_POST['nonce'])) {
        wp_send_json_error('No nonce provided');
        return;
    }

    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'liveblog_block_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }

    // Check if post_id is provided
    if (!isset($_POST['post_id'])) {
        wp_send_json_error('No post_id provided');
        return;
    }

    // Check if block_index is provided
    if (!isset($_POST['block_index'])) {
        wp_send_json_error('No block_index provided');
        return;
    }

    // Check if block_data is provided
    if (!isset($_POST['block_data'])) {
        wp_send_json_error('No block_data provided');
        return;
    }

    $post_id = intval($_POST['post_id']);
    $block_index = intval($_POST['block_index']);
    $block_data = $_POST['block_data'];

    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error('Unauthorized');
        return;
    }

    $blocks = get_post_meta($post_id, '_liveblog_custom_blocks', true);
    if (!is_array($blocks)) {
        $blocks = array();
    }

    // Add the new block
    $blocks[$block_index] = array(
        'title' => sanitize_text_field($block_data['title']),
        'content' => wp_kses_post($block_data['content']),
        'media_id' => intval($block_data['media_id']),
        'media_url' => esc_url_raw($block_data['media_url']),
        'timestamp' => sanitize_text_field($block_data['timestamp']),
        'status' => sanitize_text_field($block_data['status']),
        'created_date' => sanitize_text_field($block_data['created_date']),
        'last_modified' => sanitize_text_field($block_data['last_modified'])
    );

    $update_result = update_post_meta($post_id, '_liveblog_custom_blocks', $blocks);

    if ($update_result === false) {
        wp_send_json_error('Failed to update post meta');
        return;
    }

    wp_send_json_success(array(
        'message' => 'Block added successfully',
        'block_index' => $block_index
    ));
}

// Enqueue custom block editor scripts and styles for LiveBlog
function enqueue_liveblog_custom_editor_scripts($hook)
{
    global $post_type;

    if (in_array($hook, array('post.php', 'post-new.php')) && $post_type === 'liveblog') {
        wp_enqueue_script(
            'liveblog-custom-editor',
            get_stylesheet_directory_uri() . '/assets/js/liveblog-custom-editor.js',
            array('jquery', 'wp-editor'),
            '1.0.0',
            true
        );

        wp_enqueue_style(
            'liveblog-custom-editor-style',
            get_stylesheet_directory_uri() . '/assets/css/liveblog-custom-editor.css',
            array(),
            '1.0.0'
        );

        // Localize script with AJAX URL and nonce
        wp_localize_script('liveblog-custom-editor', 'liveblogEditorAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('liveblog_editor_nonce'),
            'post_id' => get_the_ID()
        ));
    }
}
add_action('admin_enqueue_scripts', 'enqueue_liveblog_custom_editor_scripts');

// Enqueue LiveBlog frontend styles
function enqueue_liveblog_frontend_styles()
{
    if (is_singular('liveblog')) {
        wp_enqueue_style('liveblog-frontend', get_template_directory_uri() . '/assets/css/liveblog-frontend.css', array(), '1.0.0');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_liveblog_frontend_styles');

// AJAX handler for uploading images in LiveBlog custom editor
function upload_liveblog_image_ajax()
{
    check_ajax_referer('liveblog_editor_nonce', 'nonce');

    if (!current_user_can('upload_files')) {
        wp_die('Insufficient permissions');
    }

    if (!isset($_FILES['image'])) {
        wp_send_json_error('No image file provided');
    }

    $file = $_FILES['image'];
    $upload = wp_handle_upload($file, array('test_form' => false));

    if (isset($upload['error'])) {
        wp_send_json_error($upload['error']);
    }

    $attachment_id = wp_insert_attachment(array(
        'post_title' => sanitize_text_field($file['name']),
        'post_content' => '',
        'post_status' => 'inherit',
        'post_mime_type' => $upload['type']
    ), $upload['file']);

    if (is_wp_error($attachment_id)) {
        wp_send_json_error('Failed to create attachment');
    }

    wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $upload['file']));

    wp_send_json_success(array(
        'id' => $attachment_id,
        'url' => $upload['url']
    ));
}
add_action('wp_ajax_upload_liveblog_image', 'upload_liveblog_image_ajax');

// AJAX handler for auto-saving LiveBlog blocks
function auto_save_liveblog_blocks_ajax()
{
    check_ajax_referer('liveblog_editor_nonce', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_die('Insufficient permissions');
    }

    $post_id = intval($_POST['post_id']);
    $blocks = json_decode(stripslashes($_POST['blocks']), true);

    if (!$post_id || !$blocks) {
        wp_send_json_error('Invalid data provided');
    }

    // Save blocks as post meta
    update_post_meta($post_id, '_liveblog_custom_blocks', $blocks);

    wp_send_json_success('Blocks auto-saved successfully');
}
add_action('wp_ajax_auto_save_liveblog_blocks', 'auto_save_liveblog_blocks_ajax');



// AJAX handler for saving LiveBlog content
function save_liveblog_content_ajax()
{
    check_ajax_referer('liveblog_editor_nonce', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_die('Insufficient permissions');
    }

    $post_id = intval($_POST['post_id']);
    $blocks = json_decode(stripslashes($_POST['blocks']), true);

    // Debug: Log the blocks data to see if autoEmbed is present
    error_log('LiveBlog save_liveblog_content_ajax - Blocks data: ' . print_r($blocks, true));
    error_log('LiveBlog save_liveblog_content_ajax - Number of blocks to save: ' . count($blocks));

    if (!$post_id || !$blocks) {
        wp_send_json_error('Invalid data provided');
    }

    // Save blocks as post meta
    update_post_meta($post_id, '_liveblog_custom_blocks', $blocks);

    // Verify the save by reading back the data
    $saved_blocks = get_post_meta($post_id, '_liveblog_custom_blocks', true);
    error_log('LiveBlog save_liveblog_content_ajax - Saved blocks verification: ' . print_r($saved_blocks, true));
    error_log('LiveBlog save_liveblog_content_ajax - Number of blocks after save: ' . count($saved_blocks));

    wp_send_json_success('LiveBlog content saved successfully');
}
add_action('wp_ajax_save_liveblog_content', 'save_liveblog_content_ajax');

// Custom Block Editor functionality for Happening post type
function enqueue_happening_block_editor_scripts($hook)
{
    global $post_type;

    if (in_array($hook, array('post.php', 'post-new.php')) && $post_type === 'happening') {
        wp_enqueue_script(
            'happening-block-editor-custom',
            get_template_directory_uri() . '/js/happening-block-editor.js',
            array('wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'wp-element', 'wp-i18n', 'wp-components'),
            '1.0.0',
            true
        );

        wp_enqueue_style(
            'happening-block-editor-custom',
            get_template_directory_uri() . '/css/happening-block-editor.css',
            array('wp-edit-post'),
            '1.0.0'
        );

        // Localize script with data
        wp_localize_script('happening-block-editor-custom', 'happeningBlockEditor', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('happening_block_editor_nonce'),
            'postId' => get_the_ID(),
        ));
    }
}
add_action('admin_enqueue_scripts', 'enqueue_happening_block_editor_scripts');

// Add custom meta fields for happening blocks
function add_happening_block_meta_boxes()
{
    add_meta_box(
        'happening_block_meta',
        'Happening Block Settings',
        'happening_block_meta_callback',
        'happening',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'add_happening_block_meta_boxes');

function happening_block_meta_callback($post)
{
    wp_nonce_field('happening_block_meta_nonce', 'happening_block_meta_nonce');

    $block_meta = get_post_meta($post->ID, '_happening_block_meta', true);
    if (!is_array($block_meta)) {
        $block_meta = array();
    }

    echo '<div id="happening-block-meta-container">';
    echo '<p><strong>Block-specific settings will appear here when you add blocks to the editor.</strong></p>';
    echo '<div id="happening-block-meta-fields"></div>';
    echo '</div>';

    echo '<script type="text/javascript">
        window.happeningBlockMeta = ' . json_encode($block_meta) . ';
    </script>';
}

function save_happening_block_meta($post_id)
{
    if (
        !isset($_POST['happening_block_meta_nonce']) ||
        !wp_verify_nonce($_POST['happening_block_meta_nonce'], 'happening_block_meta_nonce')
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['happening_block_meta'])) {
        update_post_meta($post_id, '_happening_block_meta', $_POST['happening_block_meta']);
    }
}
add_action('save_post', 'save_happening_block_meta');

// AJAX handler for saving block meta
function save_happening_block_meta_ajax()
{
    check_ajax_referer('happening_block_editor_nonce', 'nonce');

    $post_id = intval($_POST['post_id']);
    $block_data = $_POST['block_data'];
    $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'draft';

    if (!current_user_can('edit_post', $post_id)) {
        wp_die('Unauthorized');
    }

    // Add status to block data
    if (is_array($block_data)) {
        foreach ($block_data as $index => $data) {
            if (is_array($data)) {
                $block_data[$index]['status'] = $status;
                $block_data[$index]['timestamp'] = current_time('mysql');
            }
        }
    }

    update_post_meta($post_id, '_happening_block_meta', $block_data);
    wp_send_json_success('Block meta saved successfully');
}
add_action('wp_ajax_save_happening_block_meta', 'save_happening_block_meta_ajax');

// AJAX handler for saving all blocks data
function save_happening_blocks_data_ajax()
{
    check_ajax_referer('happening_block_editor_nonce', 'nonce');

    $post_id = intval($_POST['post_id']);
    $blocks_data = json_decode(stripslashes($_POST['blocks_data']), true);
    $action_type = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : 'draft';

    if (!current_user_can('edit_post', $post_id)) {
        wp_die('Unauthorized');
    }

    // Process and sanitize blocks data
    $sanitized_blocks = array();
    if (is_array($blocks_data)) {
        foreach ($blocks_data as $block) {
            $sanitized_block = array(
                'index' => intval($block['index']),
                'header' => sanitize_text_field($block['header']),
                'content' => wp_kses_post($block['content']),
                'date' => sanitize_text_field($block['date']),
                'time' => sanitize_text_field($block['time']),
                'status' => $action_type,
                'timestamp' => current_time('mysql')
            );
            $sanitized_blocks[] = $sanitized_block;
        }
    }

    // Save blocks data
    update_post_meta($post_id, '_happening_blocks_data', $sanitized_blocks);

    // Also update the individual block meta for backward compatibility
    $block_meta = array();
    foreach ($sanitized_blocks as $block) {
        $block_meta[$block['index']] = array(
            'header' => $block['header'],
            'content' => $block['content'],
            'date' => $block['date'],
            'time' => $block['time'],
            'status' => $block['status']
        );
    }
    update_post_meta($post_id, '_happening_block_meta', $block_meta);

    wp_send_json_success('All blocks saved successfully');
}
add_action('wp_ajax_save_happening_blocks_data', 'save_happening_blocks_data_ajax');

// AJAX handler for saving individual LiveBlog blocks
function save_liveblog_block_ajax()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'liveblog_editor_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $post_id = intval($_POST['post_id']);
    $block_data = json_decode(stripslashes($_POST['block']), true);

    // Debug: Log the block data to see if autoEmbed is present
    error_log('LiveBlog save_liveblog_block_ajax - Block data: ' . print_r($block_data, true));

    if (!$post_id || !$block_data) {
        wp_send_json_error('Invalid data');
    }

    // Check if user can edit this post
    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error('Permission denied');
    }

    // Get existing blocks
    $existing_blocks = get_post_meta($post_id, '_liveblog_custom_blocks', true);
    if (!is_array($existing_blocks)) {
        $existing_blocks = array();
    }

    // Check if this is an update to an existing block
    $block_index = isset($block_data['index']) ? intval($block_data['index']) : -1;

    if ($block_index >= 0 && $block_index < count($existing_blocks)) {
        // Update existing block
        $existing_blocks[$block_index] = array(
            'type' => sanitize_text_field($block_data['type']),
            'timestamp' => sanitize_text_field($block_data['timestamp']),
            'content' => wp_kses_post($block_data['content']),
            'titleColumn' => wp_kses_post($block_data['titleColumn']),
            'timeDateContent' => wp_kses_post($block_data['timeDateContent']),
            'imageId' => intval($block_data['imageId']),
            'imageCaption' => wp_kses_post($block_data['imageCaption']),
            'videoUrl' => esc_url_raw($block_data['videoUrl']),
            'videoCaption' => wp_kses_post($block_data['videoCaption']),
            'videoEmbed' => wp_kses_post($block_data['videoEmbed']),
            'quoteContent' => wp_kses_post($block_data['quoteContent']),
            'quoteAttribution' => wp_kses_post($block_data['quoteAttribution']),
            'socialUrl' => esc_url_raw($block_data['socialUrl']),
            'socialEmbed' => wp_kses_post($block_data['socialEmbed']),
            'autoEmbedUrls' => isset($block_data['autoEmbedUrls']) ? sanitize_text_field($block_data['autoEmbedUrls']) : '[]',
            'isDraft' => boolval($block_data['isDraft']),
            'saved_at' => current_time('mysql')
        );
    } else {
        // Add new block
        $existing_blocks[] = array(
            'type' => sanitize_text_field($block_data['type']),
            'timestamp' => sanitize_text_field($block_data['timestamp']),
            'content' => wp_kses_post($block_data['content']),
            'titleColumn' => wp_kses_post($block_data['titleColumn']),
            'timeDateContent' => wp_kses_post($block_data['timeDateContent']),
            'imageId' => intval($block_data['imageId']),
            'imageCaption' => wp_kses_post($block_data['imageCaption']),
            'videoUrl' => esc_url_raw($block_data['videoUrl']),
            'videoCaption' => wp_kses_post($block_data['videoCaption']),
            'videoEmbed' => wp_kses_post($block_data['videoEmbed']),
            'quoteContent' => wp_kses_post($block_data['quoteContent']),
            'quoteAttribution' => wp_kses_post($block_data['quoteAttribution']),
            'socialUrl' => esc_url_raw($block_data['socialUrl']),
            'socialEmbed' => wp_kses_post($block_data['socialEmbed']),
            'autoEmbedUrls' => isset($block_data['autoEmbedUrls']) ? sanitize_text_field($block_data['autoEmbedUrls']) : '[]',
            'isDraft' => boolval($block_data['isDraft']),
            'saved_at' => current_time('mysql')
        );
    }

    // Save the updated blocks
    update_post_meta($post_id, '_liveblog_custom_blocks', $existing_blocks);

    wp_send_json_success('Block saved successfully');
}
add_action('wp_ajax_save_liveblog_block', 'save_liveblog_block_ajax');

// Function to get happening blocks data
function get_happening_blocks_data($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $blocks_data = get_post_meta($post_id, '_happening_blocks_data', true);
    if (!is_array($blocks_data)) {
        // Fallback to individual block meta
        $block_meta = get_post_meta($post_id, '_happening_block_meta', true);
        if (is_array($block_meta)) {
            $blocks_data = array();
            foreach ($block_meta as $index => $meta) {
                $blocks_data[] = array_merge($meta, array('index' => $index));
            }
        }
    }

    return $blocks_data;
}

// Function to display happening block meta on frontend
function display_happening_block_meta($content)
{
    // Only for happening post type and single posts
    if (get_post_type() !== 'happening' || !is_single()) {
        return $content;
    }

    $blocks_data = get_happening_blocks_data();
    if (!is_array($blocks_data) || empty($blocks_data)) {
        return $content;
    }

    // Create the block meta display
    $meta_display = '<div class="happening-block-meta-display">';
    $meta_display .= '<h3 class="happening-meta-title">📅 Happening Timeline</h3>';
    $meta_display .= '<div class="happening-timeline">';

    foreach ($blocks_data as $block) {
        if (!empty($block['header']) || !empty($block['content']) || !empty($block['date']) || !empty($block['time'])) {
            $meta_display .= '<div class="happening-timeline-item">';

            if (!empty($block['header'])) {
                $meta_display .= '<h4 class="happening-block-title">' . esc_html($block['header']) . '</h4>';
            }

            if (!empty($block['content'])) {
                $meta_display .= '<div class="happening-block-content">' . wp_kses_post($block['content']) . '</div>';
            }

            if (!empty($block['date']) || !empty($block['time'])) {
                $meta_display .= '<div class="happening-block-datetime">';
                if (!empty($block['date'])) {
                    $formatted_date = date('F j, Y', strtotime($block['date']));
                    $meta_display .= '<span class="happening-date"><i class="fas fa-calendar"></i> ' . esc_html($formatted_date) . '</span>';
                }
                if (!empty($block['time'])) {
                    $formatted_time = date('g:i A', strtotime($block['time']));
                    $meta_display .= '<span class="happening-time"><i class="fas fa-clock"></i> ' . esc_html($formatted_time) . '</span>';
                }
                $meta_display .= '</div>';
            }

            if (!empty($block['status'])) {
                $status_class = $block['status'] === 'published' ? 'published' : 'draft';
                $meta_display .= '<span class="happening-status ' . $status_class . '">' . esc_html(ucfirst($block['status'])) . '</span>';
            }

            $meta_display .= '</div>';
        }
    }

    $meta_display .= '</div>';
    $meta_display .= '</div>';

    // Insert the meta display before the content
    return $meta_display . $content;
}
add_filter('the_content', 'display_happening_block_meta');

/**
 * Add admin menu for sidebar algorithms
 */
function add_sidebar_algorithms_admin_menu()
{
    add_submenu_page(
        'tools.php',
        'Sidebar Algorithms',
        'Sidebar Algorithms',
        'manage_options',
        'sidebar-algorithms',
        'sidebar_algorithms_admin_page'
    );
}
add_action('admin_menu', 'add_sidebar_algorithms_admin_menu');

/**
 * Admin page for sidebar algorithms
 */
function sidebar_algorithms_admin_page()
{
    // Handle test data population
    if (isset($_POST['populate_test_data']) && current_user_can('manage_options')) {
        include_once get_template_directory() . '/parts/shared/sidebar-algorithms.php';
        $posts_updated = populate_test_data_for_trending(10);
        echo '<div class="notice notice-success"><p>Test data populated for ' . $posts_updated . ' posts!</p></div>';
    }

    // Get statistics
    global $wpdb;
    $total_posts = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'");
    $posts_with_views = $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = 'post_views_count'");
    $posts_with_shares = $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = 'social_shares_count'");

    ?>
        <div class="wrap">
            <h1>Sidebar Algorithms Dashboard</h1>

            <div class="card">
                <h2>Statistics</h2>
                <table class="form-table">
                    <tr>
                        <th>Total Published Posts:</th>
                        <td><?php echo number_format($total_posts); ?></td>
                    </tr>
                    <tr>
                        <th>Posts with View Counts:</th>
                        <td><?php echo number_format($posts_with_views); ?></td>
                    </tr>
                    <tr>
                        <th>Posts with Share Counts:</th>
                        <td><?php echo number_format($posts_with_shares); ?></td>
                    </tr>
                </table>
            </div>

            <div class="card">
                <h2>Test Data</h2>
                <p>Populate test data for recent posts to test the trending algorithm:</p>
                <form method="post">
                    <?php wp_nonce_field('populate_test_data', 'test_data_nonce'); ?>
                    <input type="submit" name="populate_test_data" class="button button-primary" value="Populate Test Data">
                </form>
            </div>

            <div class="card">
                <h2>Algorithm Information</h2>
                <h3>Recent Posts</h3>
                <p>Simple chronological sorting of the latest posts with featured images.</p>

                <h3>Popular Posts</h3>
                <p>Weighted scoring based on:</p>
                <ul>
                    <li>Post views (40% weight)</li>
                    <li>Comments count (30% weight)</li>
                    <li>Social shares (20% weight)</li>
                    <li>Post age penalty (10% weight)</li>
                </ul>

                <h3>Trending Posts</h3>
                <p>Advanced scoring based on:</p>
                <ul>
                    <li>Recent engagement velocity (40% weight)</li>
                    <li>Recency boost (30% weight)</li>
                    <li>Engagement ratio (20% weight)</li>
                    <li>Category trending boost (10% weight)</li>
                </ul>
            </div>
        </div>
        <?php
}

/**
 * Hook related posts content filter
 */
function hook_related_posts_content_filter()
{
    // Include the sidebar algorithms file
    include_once get_template_directory() . '/parts/shared/sidebar-algorithms.php';

    // Hook the content filter
    add_filter('the_content', 'insert_related_posts_into_content', 15);
}
add_action('wp', 'hook_related_posts_content_filter');

/**
 * Hook recommended posts functionality
 */
function hook_recommended_posts_functionality()
{
    // Include the sidebar algorithms file
    include_once get_template_directory() . '/parts/shared/sidebar-algorithms.php';
}
add_action('wp', 'hook_recommended_posts_functionality');

/**
 * Shortcode for displaying recommended posts
 * Usage: [recommended_posts limit="6"]
 */
function recommended_posts_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'limit' => 6
    ), $atts);

    ob_start();
    display_recommended_posts_widget($atts['limit']);
    return ob_get_clean();
}
add_shortcode('recommended_posts', 'recommended_posts_shortcode');

/**
 * Add recommended posts to admin dashboard
 */
function add_recommended_posts_admin_menu()
{
    add_submenu_page(
        'tools.php',
        'Recommended Posts',
        'Recommended Posts',
        'manage_options',
        'recommended-posts',
        'recommended_posts_admin_page'
    );
}
add_action('admin_menu', 'add_recommended_posts_admin_menu');

/**
 * Admin page for recommended posts
 */
function recommended_posts_admin_page()
{
    include_once get_template_directory() . '/parts/shared/sidebar-algorithms.php';

    // Get user preferences for current admin
    $user_preferences = get_user_preferences();

    // Get recommended posts
    $recommended_posts = get_recommended_posts(10);

    ?>
        <div class="wrap">
            <h1>Recommended Posts Dashboard</h1>

            <div class="card">
                <h2>Your Preferences (Based on Cookie Data)</h2>
                <table class="form-table">
                    <tr>
                        <th>Favorite Categories:</th>
                        <td>
                            <?php
                            if (!empty($user_preferences['favorite_categories'])) {
                                $category_names = array();
                                foreach ($user_preferences['favorite_categories'] as $cat_id) {
                                    $category = get_term($cat_id, 'category');
                                    if ($category) {
                                        $category_names[] = $category->name;
                                    }
                                }
                                echo implode(', ', $category_names);
                            } else {
                                echo 'No category preferences yet';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Content Length Preference:</th>
                        <td><?php echo ucfirst($user_preferences['content_length_preference']); ?></td>
                    </tr>
                    <tr>
                        <th>Active Hours:</th>
                        <td>
                            <?php
                            if (!empty($user_preferences['time_preferences'])) {
                                echo implode(', ', $user_preferences['time_preferences']) . ' (24h format)';
                            } else {
                                echo 'No time preferences yet';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="card">
                <h2>Recommended Posts for You</h2>
                <?php if (!empty($recommended_posts)): ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Categories</th>
                                <th>Date</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recommended_posts as $post):
                                $score = calculate_recommendation_score($post->ID, $user_preferences);
                                $categories = get_filtered_categories($post->ID);
                                $category_names = array();
                                foreach ($categories as $category) {
                                    $category_names[] = $category->name;
                                }
                                ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo get_permalink($post->ID); ?>" target="_blank">
                                            <?php echo $post->post_title; ?>
                                        </a>
                                    </td>
                                    <td><?php echo implode(', ', $category_names); ?></td>
                                    <td><?php echo get_the_date('', $post->ID); ?></td>
                                    <td><?php echo number_format($score, 3); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No recommended posts found. Try viewing some posts to build your preferences!</p>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>How to Use</h2>
                <h3>Widget Display</h3>
                <p>Add this code to any template file to display recommended posts:</p>
                <code>&lt;?php display_recommended_posts_widget(6); ?&gt;</code>

                <h3>Shortcode</h3>
                <p>Use this shortcode in posts or pages:</p>
                <code>[recommended_posts limit="6"]</code>

                <h3>Sidebar Integration</h3>
                <p>Add to sidebar.php or any widget area:</p>
                <code>&lt;?php display_recommended_posts_widget(); ?&gt;</code>
            </div>
        </div>
        <?php
}

// Add custom rewrite rule for latest page
function add_latest_rewrite_rule()
{
    // Rule for paginated latest page
    add_rewrite_rule('^latest/page/?([0-9]{1,})/?$', 'index.php?latest=1&paged=$matches[1]', 'top');
    // Rule for base latest page
    add_rewrite_rule('^latest/?$', 'index.php?latest=1', 'top');
}
add_action('init', 'add_latest_rewrite_rule');

// Add custom query var for latest page
function add_latest_query_var($vars)
{
    $vars[] = 'latest';
    return $vars;
}
add_filter('query_vars', 'add_latest_query_var');

// Template redirect for latest page
function latest_template_redirect()
{
    if (get_query_var('latest')) {
        $template = locate_template('page-latest.php');
        if ($template) {
            include $template;
            exit;
        }
    }
}
add_action('template_redirect', 'latest_template_redirect');

// Flush rewrite rules when theme is activated
function flush_latest_rewrite_rules()
{
    add_latest_rewrite_rule();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'flush_latest_rewrite_rules');