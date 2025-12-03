<?php
// Use WordPress's built-in category functions
$current_category = get_queried_object();
$category_slug = $current_category->slug;
$category_name = $current_category->name;
$category_id = $current_category->term_id;

// Get pagination parameters
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts_per_page = 6; // Show 6 posts per page

// Debug information
echo "<!-- DEBUG: WordPress Category detected: " . $category_slug . " -->";
echo "<!-- DEBUG: Category name: " . $category_name . " -->";
echo "<!-- DEBUG: Category ID: " . $category_id . " -->";
echo "<!-- DEBUG: Current page: " . $paged . " -->";

// Category titles mapping
$category_titles = [
    'latest' => 'Latest News',
    'uae' => 'UAE News',
    'gulf' => 'Gulf News',
    'saudi' => 'Saudi Arabia News',
    'qatar' => 'Qatar News',
    'oman' => 'Oman News',
    'kuwait' => 'Kuwait News',
    'bahrain' => 'Bahrain News',
    'yemen' => 'Yemen News',
    'kerala' => 'Kerala News',
    'india' => 'India News',
    'world' => 'World News',
    'middle-east' => 'Middle East News',
    'asia' => 'Asia News',
    'europe' => 'Europe News',
    'america' => 'America News',
    'entertainment' => 'Entertainment',
    'movies' => 'Movies',
    'events' => 'Events',
    'lifestyle' => 'Lifestyle',
    'sports' => 'Sports',
    'business' => 'Business',
    'finance' => 'Finance',
    'tech' => 'Technology',
    'job' => 'Jobs'
];

$page_title = isset($category_titles[$category_slug]) ? $category_titles[$category_slug] : $category_name;

// Function to get featured posts from category with fallback
function get_featured_posts_from_category($category_id, $posts_needed, $paged = 1, $exclude_ids = array()) {
    $posts = array();
    
    // Get the Featured category
    $featured_category = get_category_by_slug('featured');
    if (!$featured_category) {
        echo "<!-- DEBUG: Featured category not found, using regular category posts -->";
        // If featured category doesn't exist, get regular category posts
        $category_query = new WP_Query(array(
            'cat' => $category_id,
            'posts_per_page' => $posts_needed,
            'paged' => $paged,
            'post_status' => 'publish',
            'post__not_in' => $exclude_ids
        ));
        
        if ($category_query->have_posts()) {
            while ($category_query->have_posts()) {
                $category_query->the_post();
                $posts[] = get_post();
            }
            wp_reset_postdata();
        }
        return $posts;
    }
    
    // First try to get posts that are both in the specific category AND the Featured category
    $featured_query = new WP_Query(array(
        'posts_per_page' => $posts_needed,
        'paged' => $paged,
        'post_status' => 'publish',
        'post__not_in' => $exclude_ids,
        'category__and' => array($category_id, $featured_category->term_id)
    ));
    
    echo "<!-- DEBUG: Found " . $featured_query->found_posts . " posts in both category and 'featured' -->";
    
    if ($featured_query->have_posts()) {
        while ($featured_query->have_posts()) {
            $featured_query->the_post();
            $posts[] = get_post();
        }
        wp_reset_postdata();
    }
    
    // If we don't have enough featured posts, get regular category posts
    if (count($posts) < $posts_needed) {
        $remaining_needed = $posts_needed - count($posts);
        $exclude_ids = array_merge($exclude_ids, wp_list_pluck($posts, 'ID'));
        
        $category_query = new WP_Query(array(
            'cat' => $category_id,
            'posts_per_page' => $remaining_needed,
            'paged' => $paged,
            'post_status' => 'publish',
            'post__not_in' => $exclude_ids
        ));
        
        if ($category_query->have_posts()) {
            while ($category_query->have_posts()) {
                $category_query->the_post();
                $posts[] = get_post();
            }
            wp_reset_postdata();
        }
    }
    
    // If still not enough, fill with recent posts
    if (count($posts) < $posts_needed) {
        $remaining_needed = $posts_needed - count($posts);
        $exclude_ids = array_merge($exclude_ids, wp_list_pluck($posts, 'ID'));
        
        $recent_query = new WP_Query(array(
            'posts_per_page' => $remaining_needed,
            'paged' => $paged,
            'post_status' => 'publish',
            'post__not_in' => $exclude_ids,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        if ($recent_query->have_posts()) {
            while ($recent_query->have_posts()) {
                $recent_query->the_post();
                $posts[] = get_post();
            }
            wp_reset_postdata();
        }
    }
    
    return $posts;
}

// Function to get editors pick posts from category with fallback
function get_editors_pick_from_category($category_id, $posts_needed, $paged = 1, $exclude_ids = array()) {
    $posts = array();
    
    // Get the Editor's Pick category
    $editors_pick_category = get_category_by_slug('editors-pick');
    if (!$editors_pick_category) {
        echo "<!-- DEBUG: Editor's Pick category not found, using regular category posts -->";
        // If editors-pick category doesn't exist, get regular category posts
        $category_query = new WP_Query(array(
            'cat' => $category_id,
            'posts_per_page' => $posts_needed,
            'paged' => $paged,
            'post_status' => 'publish',
            'post__not_in' => $exclude_ids
        ));
        
        if ($category_query->have_posts()) {
            while ($category_query->have_posts()) {
                $category_query->the_post();
                $posts[] = get_post();
            }
            wp_reset_postdata();
        }
        return $posts;
    }
    
    // First try to get posts that are both in the specific category AND the Editor's Pick category
    $editors_query = new WP_Query(array(
        'posts_per_page' => $posts_needed,
        'paged' => $paged,
        'post_status' => 'publish',
        'post__not_in' => $exclude_ids,
        'category__and' => array($category_id, $editors_pick_category->term_id)
    ));
    
    echo "<!-- DEBUG: Found " . $editors_query->found_posts . " posts in both category and 'editors-pick' -->";
    
    if ($editors_query->have_posts()) {
        while ($editors_query->have_posts()) {
            $editors_query->the_post();
            $posts[] = get_post();
        }
        wp_reset_postdata();
    }
    
    // If we don't have enough editors pick posts, get regular category posts
    if (count($posts) < $posts_needed) {
        $remaining_needed = $posts_needed - count($posts);
        $exclude_ids = array_merge($exclude_ids, wp_list_pluck($posts, 'ID'));
        
        $category_query = new WP_Query(array(
            'cat' => $category_id,
            'posts_per_page' => $remaining_needed,
            'paged' => $paged,
            'post_status' => 'publish',
            'post__not_in' => $exclude_ids
        ));
        
        if ($category_query->have_posts()) {
            while ($category_query->have_posts()) {
                $category_query->the_post();
                $posts[] = get_post();
            }
            wp_reset_postdata();
        }
    }
    
    // If still not enough, fill with recent posts
    if (count($posts) < $posts_needed) {
        $remaining_needed = $posts_needed - count($posts);
        $exclude_ids = array_merge($exclude_ids, wp_list_pluck($posts, 'ID'));
        
        $recent_query = new WP_Query(array(
            'posts_per_page' => $remaining_needed,
            'paged' => $paged,
            'post_status' => 'publish',
            'post__not_in' => $exclude_ids,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        if ($recent_query->have_posts()) {
            while ($recent_query->have_posts()) {
                $recent_query->the_post();
                $posts[] = get_post();
            }
            wp_reset_postdata();
        }
    }
    
    return $posts;
}
?>

<?php include 'parts/shared/html-header.php'; ?>
<?php include 'parts/shared/header.php'; ?>

<!-- Category Title Section -->
<div class="container px-4">
    <div class="category-header-section">
        <div class="category-header-wrapper">
            <h1 class="category-header-title"><?php echo $page_title; ?></h1>
        </div>
    </div>
</div>

<section class="post-section section-gap no-padding-top">

    <div class="container px-4">
        <div class="row">
            <div class="col-lg-4">
                <main class="axil-content medium-section">
                    <div class="section-title m-b-xs-10">
                        <a href="#" class="d-block">
                            <h2 class="axil-title">Recent Posts</h2>
                        </a>
                    </div>

                    <?php
                    // Get recent posts from all categories
                    $recent_posts = new WP_Query(array(
                        'posts_per_page' => 5,
                        'paged' => $paged,
                        'post_status' => 'publish',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));

                    if ($recent_posts->have_posts()) :
                        // First post as featured card
                        $first_post = true;
                        while ($recent_posts->have_posts()) : $recent_posts->the_post();
                            if ($first_post) :
                    ?>
                    <div class="live-card">
                        <a href="<?php the_permalink(); ?>" class="block-link">
                            <div class="flex-1">
                                <h3 class="featured-title"><?php the_title(); ?></h3>
                                <p class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                                <div class="d-flex align-items-center flex-nowrap">
                                    <div class="post-cat-group flex-shrink-0">
                                        <?php
                                        $categories = get_filtered_categories();
                                        if (!empty($categories)) {
                                            $cat_count = 0;
                                            foreach ($categories as $category) {
                                                if ($cat_count > 0) echo ', ';
                                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                $cat_count++;
                                                if ($cat_count >= 3) break; // Limit to 3 categories
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="post-time ms-3 flex-shrink-0">
                                        <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="post-image-wrapper">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', array('class' => 'img-fluid img-border-radius', 'alt' => get_the_title())); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>" class="img-fluid img-border-radius">
                                <?php endif; ?>
                                <?php
                                $categories = get_filtered_categories();
                                if (!empty($categories)) :
                                    $main_category = $categories[0];
                                ?>
                                <div class="post-cat-group badge-on-image">
                                    <a href="<?php echo esc_url(get_category_link($main_category->term_id)); ?>" class="post-cat cat-btn bg-primary-color"><?php echo esc_html($main_category->name); ?></a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                    <?php
                            $first_post = false;
                            else :
                    ?>
                    <a href="<?php the_permalink(); ?>" class="block-link">
                        <div class="media post-block small-block">
                            <div class="post-image-wrapper">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                                <?php else : ?>
                                    <img class="img-fluid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>">
                                <?php endif; ?>
                                <?php echo get_video_play_button(); ?>
                            </div>
                            <div class="media-body">
                                <a href="<?php the_permalink(); ?>" class="block-link">
                                    <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                </a>
                                <div class="d-flex align-items-center flex-nowrap">
                                    <div class="post-cat-group flex-shrink-0">
                                        <?php
                                        $categories = get_filtered_categories();
                                        if (!empty($categories)) {
                                            $cat_count = 0;
                                            foreach ($categories as $category) {
                                                if ($cat_count > 0) echo ', ';
                                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                $cat_count++;
                                                if ($cat_count >= 2) break; // Limit to 2 categories for smaller blocks
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="post-time ms-3 flex-shrink-0">
                                        <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php
                            endif;
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>

                    <!-- Pagination for Recent Posts -->
                    <?php if ($recent_posts->max_num_pages > 1) : ?>
                    <div class="pagination-wrapper mt-4">
                        <?php
                        echo paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'current' => $paged,
                            'total' => $recent_posts->max_num_pages,
                            'prev_text' => '&laquo; Previous',
                            'next_text' => 'Next &raquo;'
                        ));
                        ?>
                    </div>
                    <?php endif; ?>

                </main>
                <!-- End of .axil-content -->
            </div>
            <!-- End of .col-lg-4 -->
            <div class="col-lg-5">
                <aside class="main-lead-section">
                    <div class="section-title m-b-xs-10">
                        <a href="<?php echo get_category_link($category_id); ?>" class="d-block">
                            <h2 class="axil-title">Featured from <?php echo $page_title; ?></h2>
                        </a>
                    </div>

                    <?php
                    // Get featured posts from the specific category using the function
                    $featured_posts = get_featured_posts_from_category($category_id, 5, $paged, array());
                    
                    echo "<!-- DEBUG: Featured posts function returned " . count($featured_posts) . " posts -->";

                    if (!empty($featured_posts)) :
                        // First post as featured card
                        $first_post = true;
                        foreach ($featured_posts as $post) :
                            setup_postdata($post);
                            if ($first_post) :
                    ?>
                    <div class="live-card">
                        <a href="<?php the_permalink(); ?>" class="block-link">
                            <div class="flex-1">
                                <h3 class="featured-title"><?php the_title(); ?></h3>
                                <p class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                                <div class="d-flex align-items-center flex-nowrap">
                                    <div class="post-cat-group flex-shrink-0">
                                        <?php
                                        $categories = get_filtered_categories();
                                        if (!empty($categories)) {
                                            $cat_count = 0;
                                            foreach ($categories as $category_obj) {
                                                if ($cat_count > 0) echo ', ';
                                                echo '<a href="' . esc_url(get_category_link($category_obj->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category_obj->name) . '</a>';
                                                $cat_count++;
                                                if ($cat_count >= 3) break; // Limit to 3 categories
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="post-time ms-3 flex-shrink-0">
                                        <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="post-image-wrapper">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', array('class' => 'img-fluid img-border-radius', 'alt' => get_the_title())); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>" class="img-fluid img-border-radius">
                                <?php endif; ?>
                                <?php
                                $categories = get_filtered_categories();
                                if (!empty($categories)) :
                                    $main_category = $categories[0];
                                ?>
                                <div class="post-cat-group badge-on-image">
                                    <a href="<?php echo esc_url(get_category_link($main_category->term_id)); ?>" class="post-cat cat-btn bg-primary-color"><?php echo esc_html($main_category->name); ?></a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                    <?php
                            $first_post = false;
                            else :
                    ?>
                    <a href="<?php the_permalink(); ?>" class="block-link">
                        <div class="media post-block small-block">
                            <div class="post-image-wrapper">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                                <?php else : ?>
                                    <img class="img-fluid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>">
                                <?php endif; ?>
                                <?php echo get_video_play_button(); ?>
                            </div>
                            <div class="media-body">
                                <a href="<?php the_permalink(); ?>" class="block-link">
                                    <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                </a>
                                <div class="d-flex align-items-center flex-nowrap">
                                    <div class="post-cat-group flex-shrink-0">
                                        <?php
                                        $categories = get_filtered_categories();
                                        if (!empty($categories)) {
                                            $cat_count = 0;
                                            foreach ($categories as $category_obj) {
                                                if ($cat_count > 0) echo ', ';
                                                echo '<a href="' . esc_url(get_category_link($category_obj->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category_obj->name) . '</a>';
                                                $cat_count++;
                                                if ($cat_count >= 2) break; // Limit to 2 categories for smaller blocks
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="post-time ms-3 flex-shrink-0">
                                        <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php
                            endif;
                        endforeach;
                        wp_reset_postdata();
                    else :
                    ?>
                    <div class="no-posts-message">
                        <p>No featured posts found.</p>
                    </div>
                    <?php
                    endif;
                    ?>

                    <!-- Pagination for Featured Posts -->
                    <?php 
                    // Calculate total pages for featured posts
                    $featured_category = get_category_by_slug('featured');
                    if ($featured_category) {
                        $featured_total_query = new WP_Query(array(
                            'category__and' => array($category_id, $featured_category->term_id),
                            'posts_per_page' => -1,
                            'post_status' => 'publish'
                        ));
                        $featured_total_posts = $featured_total_query->found_posts;
                        $featured_max_pages = ceil($featured_total_posts / $posts_per_page);
                        
                        if ($featured_max_pages > 1) :
                    ?>
                    <div class="pagination-wrapper mt-4">
                        <?php
                        echo paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'current' => $paged,
                            'total' => $featured_max_pages,
                            'prev_text' => '&laquo; Previous',
                            'next_text' => 'Next &raquo;'
                        ));
                        ?>
                    </div>
                    <?php 
                        endif;
                        wp_reset_postdata();
                    }
                    ?>

                </aside>
                <!-- End of .post-sidebar -->
            </div>
            <!-- End of .col-lg-5 -->
            <div class="col-lg-3">
                <aside class="post-sidebar medium-section">
                    <div class="section-title m-b-xs-10">
                        <a href="<?php echo get_category_link($category_id); ?>" class="d-block">
                            <h2 class="axil-title">Editor's Pick from <?php echo $page_title; ?></h2>
                        </a>
                    </div>

                    <?php
                    // Get editors pick posts from the specific category using the function
                    $editors_pick_posts = get_editors_pick_from_category($category_id, $posts_per_page, $paged, array());
                    
                    echo "<!-- DEBUG: Editor's pick posts function returned " . count($editors_pick_posts) . " posts -->";

                    if (!empty($editors_pick_posts)) :
                        // First post as featured card
                        $first_post = true;
                        foreach ($editors_pick_posts as $post) :
                            setup_postdata($post);
                            if ($first_post) :
                    ?>
                    <div class="live-card">
                        <a href="<?php the_permalink(); ?>" class="block-link">
                            <div class="flex-1">
                                <h3 class="featured-title"><?php the_title(); ?></h3>
                                <p class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                                <div class="d-flex align-items-center flex-nowrap">
                                    <div class="post-cat-group flex-shrink-0">
                                        <?php
                                        $categories = get_filtered_categories();
                                        if (!empty($categories)) {
                                            $cat_count = 0;
                                            foreach ($categories as $category_obj) {
                                                if ($cat_count > 0) echo ', ';
                                                echo '<a href="' . esc_url(get_category_link($category_obj->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category_obj->name) . '</a>';
                                                $cat_count++;
                                                if ($cat_count >= 3) break; // Limit to 3 categories
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="post-time ms-3 flex-shrink-0">
                                        <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="post-image-wrapper">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', array('class' => 'img-fluid img-border-radius', 'alt' => get_the_title())); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>" class="img-fluid img-border-radius">
                                <?php endif; ?>
                                <?php
                                $categories = get_filtered_categories();
                                if (!empty($categories)) :
                                    $main_category = $categories[0];
                                ?>
                                <div class="post-cat-group badge-on-image">
                                    <a href="<?php echo esc_url(get_category_link($main_category->term_id)); ?>" class="post-cat cat-btn bg-primary-color"><?php echo esc_html($main_category->name); ?></a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                    <?php
                            $first_post = false;
                            else :
                    ?>
                    <a href="<?php the_permalink(); ?>" class="block-link">
                        <div class="media post-block small-block">
                            <a href="<?php the_permalink(); ?>" class="block-link">
                                <div>
                                    <h3 class="featured-title small-card-title"><?php the_title(); ?></h3>
                                    <div class="d-flex align-items-center flex-nowrap">
                                        <div class="post-cat-group flex-shrink-0">
                                            <?php
                                            $categories = get_filtered_categories();
                                            if (!empty($categories)) {
                                                $cat_count = 0;
                                                foreach ($categories as $category_obj) {
                                                    if ($cat_count > 0) echo ', ';
                                                    echo '<a href="' . esc_url(get_category_link($category_obj->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category_obj->name) . '</a>';
                                                    $cat_count++;
                                                    if ($cat_count >= 2) break; // Limit to 2 categories for smaller blocks
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="post-time ms-3 flex-shrink-0">
                                            <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </a>
                    <?php
                            endif;
                        endforeach;
                        wp_reset_postdata();
                    else :
                    ?>
                    <div class="no-posts-message">
                        <p>No editor's pick posts found.</p>
                    </div>
                    <?php
                    endif;
                    ?>

                    <!-- Pagination for Editor's Pick Posts -->
                    <?php 
                    // Calculate total pages for editors pick posts
                    $editors_pick_category = get_category_by_slug('editors-pick');
                    if ($editors_pick_category) {
                        $editors_total_query = new WP_Query(array(
                            'category__and' => array($category_id, $editors_pick_category->term_id),
                            'posts_per_page' => -1,
                            'post_status' => 'publish'
                        ));
                        $editors_total_posts = $editors_total_query->found_posts;
                        $editors_max_pages = ceil($editors_total_posts / $posts_per_page);
                        
                        if ($editors_max_pages > 1) :
                    ?>
                    <div class="pagination-wrapper mt-4">
                        <?php
                        echo paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'current' => $paged,
                            'total' => $editors_max_pages,
                            'prev_text' => '&laquo; Previous',
                            'next_text' => 'Next &raquo;'
                        ));
                        ?>
                    </div>
                    <?php 
                        endif;
                        wp_reset_postdata();
                    }
                    ?>

                    
                 </aside>
             </div>
            <!-- End of .col-lg-3 -->
        </div>
        <!-- End of .row -->
    </div>
    <!-- End of .container -->
</section>

<?php include 'parts/shared/footer.php'; ?>
<?php include 'parts/shared/html-footer.php'; ?>