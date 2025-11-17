<?php
/**
 * Sidebar Tabbed Content Algorithms
 * 
 * This file contains algorithms for:
 * - Recent Posts
 * - Popular Posts (based on views, comments, social shares)
 * - Trending Posts (based on engagement velocity and recency)
 */

/**
 * Get Recent Posts
 * 
 * @param int $limit Number of posts to retrieve (default: 5)
 * @param array $exclude_ids Array of post IDs to exclude
 * @return array Array of recent posts
 */
function get_recent_posts_sidebar($limit = 5, $exclude_ids = array()) {
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => '_thumbnail_id',
                'compare' => 'EXISTS'
            )
        )
    );
    
    // Exclude current post and specified IDs
    if (!empty($exclude_ids)) {
        $args['post__not_in'] = $exclude_ids;
    }
    
    // Exclude current post if we're on a single post page
    if (is_single()) {
        $current_post_id = get_the_ID();
        if (!isset($args['post__not_in'])) {
            $args['post__not_in'] = array();
        }
        $args['post__not_in'][] = $current_post_id;
    }
    
    $recent_posts = new WP_Query($args);
    
    return $recent_posts->posts;
}

/**
 * Get Popular Posts
 * 
 * Algorithm combines:
 * - Post views (40% weight)
 * - Comments count (30% weight)
 * - Social shares (20% weight)
 * - Post age penalty (10% weight)
 * 
 * @param int $limit Number of posts to retrieve (default: 5)
 * @param array $exclude_ids Array of post IDs to exclude
 * @param int $days_back Number of days to look back (default: 30)
 * @return array Array of popular posts
 */
function get_popular_posts_sidebar($limit = 5, $exclude_ids = array(), $days_back = 30) {
    global $wpdb;
    
    // Exclude current post if we're on a single post page
    if (is_single()) {
        $current_post_id = get_the_ID();
        $exclude_ids[] = $current_post_id;
    }
    
    $exclude_sql = '';
    if (!empty($exclude_ids)) {
        $exclude_ids = array_map('intval', $exclude_ids);
        $exclude_sql = "AND p.ID NOT IN (" . implode(',', $exclude_ids) . ")";
    }
    
    // Calculate date threshold
    $date_threshold = date('Y-m-d H:i:s', strtotime("-{$days_back} days"));
    
    // Complex query to calculate popularity score
    $query = "
        SELECT 
            p.ID,
            p.post_title,
            p.post_date,
            p.post_excerpt,
            p.post_name,
            p.guid,
            pm.meta_value as thumbnail_id,
            COALESCE(views.count, 0) as view_count,
            COALESCE(comments.count, 0) as comment_count,
            COALESCE(shares.count, 0) as share_count,
            (
                (COALESCE(views.count, 0) * 0.4) +
                (COALESCE(comments.count, 0) * 0.3) +
                (COALESCE(shares.count, 0) * 0.2) +
                (GREATEST(0, 1 - (DATEDIFF(NOW(), p.post_date) / {$days_back})) * 0.1)
            ) as popularity_score
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
        LEFT JOIN (
            SELECT post_id, meta_value as count
            FROM {$wpdb->postmeta}
            WHERE meta_key = 'post_views_count'
        ) views ON p.ID = views.post_id
        LEFT JOIN (
            SELECT comment_post_ID as post_id, COUNT(*) as count
            FROM {$wpdb->comments}
            WHERE comment_approved = '1'
            GROUP BY comment_post_ID
        ) comments ON p.ID = comments.post_id
        LEFT JOIN (
            SELECT post_id, meta_value as count
            FROM {$wpdb->postmeta}
            WHERE meta_key = 'social_shares_count'
        ) shares ON p.ID = shares.post_id
        WHERE p.post_type = 'post'
        AND p.post_status = 'publish'
        AND p.post_date >= '{$date_threshold}'
        AND pm.meta_value IS NOT NULL
        {$exclude_sql}
        ORDER BY popularity_score DESC
        LIMIT {$limit}
    ";
    
    $popular_posts = $wpdb->get_results($query);
    
    // Convert to WP_Post objects
    $posts = array();
    foreach ($popular_posts as $post_data) {
        $post = get_post($post_data->ID);
        if ($post) {
            $post->popularity_score = $post_data->popularity_score;
            $post->view_count = $post_data->view_count;
            $post->comment_count = $post_data->comment_count;
            $post->share_count = $post_data->share_count;
            $posts[] = $post;
        }
    }
    
    return $posts;
}

/**
 * Get Trending Posts (Simplified Fallback)
 * 
 * A simpler version that uses basic metrics when the complex algorithm fails
 * 
 * @param int $limit Number of posts to retrieve (default: 5)
 * @param array $exclude_ids Array of post IDs to exclude
 * @return array Array of trending posts
 */
function get_trending_posts_simple($limit = 5, $exclude_ids = array()) {
    global $wpdb;
    
    // Exclude current post if we're on a single post page
    if (is_single()) {
        $current_post_id = get_the_ID();
        $exclude_ids[] = $current_post_id;
    }
    
    $exclude_sql = '';
    if (!empty($exclude_ids)) {
        $exclude_ids = array_map('intval', $exclude_ids);
        $exclude_sql = "AND p.ID NOT IN (" . implode(',', $exclude_ids) . ")";
    }
    
    // Simple query based on views and comments
    $query = "
        SELECT 
            p.ID,
            p.post_title,
            p.post_date,
            p.post_excerpt,
            p.post_name,
            p.guid,
            pm.meta_value as thumbnail_id,
            COALESCE(views.count, 0) as view_count,
            COALESCE(comments.count, 0) as comment_count,
            (
                COALESCE(views.count, 0) + (COALESCE(comments.count, 0) * 10) +
                (GREATEST(0, 1 - (DATEDIFF(NOW(), p.post_date) / 30)) * 100)
            ) as simple_score
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
        LEFT JOIN (
            SELECT post_id, meta_value as count
            FROM {$wpdb->postmeta}
            WHERE meta_key = 'post_views_count'
        ) views ON p.ID = views.post_id
        LEFT JOIN (
            SELECT comment_post_ID as post_id, COUNT(*) as count
            FROM {$wpdb->comments}
            WHERE comment_approved = '1'
            GROUP BY comment_post_ID
        ) comments ON p.ID = comments.post_id
        WHERE p.post_type = 'post'
        AND p.post_status = 'publish'
        AND p.post_date >= DATE_SUB(NOW(), INTERVAL 60 DAY)
        AND pm.meta_value IS NOT NULL
        {$exclude_sql}
        ORDER BY simple_score DESC
        LIMIT {$limit}
    ";
    
    $trending_posts = $wpdb->get_results($query);
    
    // Convert to WP_Post objects
    $posts = array();
    foreach ($trending_posts as $post_data) {
        $post = get_post($post_data->ID);
        if ($post) {
            $post->simple_score = $post_data->simple_score;
            $post->view_count = $post_data->view_count;
            $post->comment_count = $post_data->comment_count;
            $posts[] = $post;
        }
    }
    
    return $posts;
}

/**
 * Get Trending Posts
 * 
 * Algorithm considers:
 * - Engagement velocity (recent activity vs total activity)
 * - Recency boost (newer posts get higher scores)
 * - Social signals (shares, comments in last 24-48 hours)
 * - Category trending (posts from trending categories)
 * 
 * @param int $limit Number of posts to retrieve (default: 5)
 * @param array $exclude_ids Array of post IDs to exclude
 * @return array Array of trending posts
 */
function get_trending_posts_sidebar($limit = 5, $exclude_ids = array()) {
    global $wpdb;
    
    // Exclude current post if we're on a single post page
    if (is_single()) {
        $current_post_id = get_the_ID();
        $exclude_ids[] = $current_post_id;
    }
    
    $exclude_sql = '';
    if (!empty($exclude_ids)) {
        $exclude_ids = array_map('intval', $exclude_ids);
        $exclude_sql = "AND p.ID NOT IN (" . implode(',', $exclude_ids) . ")";
    }
    
    // Calculate time thresholds - make it less restrictive
    $last_24_hours = date('Y-m-d H:i:s', strtotime('-24 hours'));
    $last_48_hours = date('Y-m-d H:i:s', strtotime('-48 hours'));
    $last_30_days = date('Y-m-d H:i:s', strtotime('-30 days')); // Changed from 7 days to 30 days
    
    // Get trending categories (categories with most recent activity)
    $trending_categories = get_trending_categories();
    
    // Simplified query that's less restrictive
    $query = "
        SELECT 
            p.ID,
            p.post_title,
            p.post_date,
            p.post_excerpt,
            p.post_name,
            p.guid,
            pm.meta_value as thumbnail_id,
            COALESCE(recent_views.count, 0) as recent_views,
            COALESCE(total_views.count, 0) as total_views,
            COALESCE(recent_comments.count, 0) as recent_comments,
            COALESCE(total_comments.count, 0) as total_comments,
            COALESCE(recent_shares.count, 0) as recent_shares,
            COALESCE(total_shares.count, 0) as total_shares,
            (
                -- Recent engagement velocity (40% weight)
                (COALESCE(recent_views.count, 0) * 0.2) +
                (COALESCE(recent_comments.count, 0) * 0.15) +
                (COALESCE(recent_shares.count, 0) * 0.05) +
                
                -- Recency boost (30% weight) - less aggressive
                (GREATEST(0, 1 - (DATEDIFF(NOW(), p.post_date) / 30)) * 0.3) +
                
                -- Engagement ratio (20% weight)
                (CASE 
                    WHEN COALESCE(total_views.count, 0) > 0 
                    THEN (COALESCE(recent_views.count, 0) / COALESCE(total_views.count, 0)) * 0.1
                    ELSE 0 
                END) +
                (CASE 
                    WHEN COALESCE(total_comments.count, 0) > 0 
                    THEN (COALESCE(recent_comments.count, 0) / COALESCE(total_comments.count, 0)) * 0.1
                    ELSE 0 
                END) +
                
                -- Category trending boost (10% weight) - simplified
                (CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM {$wpdb->term_relationships} tr
                        JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                        WHERE tr.object_id = p.ID AND tt.taxonomy = 'category'
                    ) THEN 0.1
                    ELSE 0 
                END)
            ) as trending_score
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
        
        -- Recent views (last 24 hours)
        LEFT JOIN (
            SELECT post_id, meta_value as count
            FROM {$wpdb->postmeta}
            WHERE meta_key = 'post_views_count_24h'
        ) recent_views ON p.ID = recent_views.post_id
        
        -- Total views
        LEFT JOIN (
            SELECT post_id, meta_value as count
            FROM {$wpdb->postmeta}
            WHERE meta_key = 'post_views_count'
        ) total_views ON p.ID = total_views.post_id
        
        -- Recent comments (last 24 hours)
        LEFT JOIN (
            SELECT comment_post_ID as post_id, COUNT(*) as count
            FROM {$wpdb->comments}
            WHERE comment_approved = '1' AND comment_date >= '{$last_24_hours}'
            GROUP BY comment_post_ID
        ) recent_comments ON p.ID = recent_comments.post_id
        
        -- Total comments
        LEFT JOIN (
            SELECT comment_post_ID as post_id, COUNT(*) as count
            FROM {$wpdb->comments}
            WHERE comment_approved = '1'
            GROUP BY comment_post_ID
        ) total_comments ON p.ID = total_comments.post_id
        
        -- Recent shares (last 24 hours)
        LEFT JOIN (
            SELECT post_id, meta_value as count
            FROM {$wpdb->postmeta}
            WHERE meta_key = 'social_shares_count_24h'
        ) recent_shares ON p.ID = recent_shares.post_id
        
        -- Total shares
        LEFT JOIN (
            SELECT post_id, meta_value as count
            FROM {$wpdb->postmeta}
            WHERE meta_key = 'social_shares_count'
        ) total_shares ON p.ID = total_shares.post_id
        
        WHERE p.post_type = 'post'
        AND p.post_status = 'publish'
        AND p.post_date >= '{$last_30_days}'
        AND pm.meta_value IS NOT NULL
        {$exclude_sql}
        ORDER BY trending_score DESC
        LIMIT {$limit}
    ";
    
    $trending_posts = $wpdb->get_results($query);
    

    
    // If no posts found with complex algorithm, try simple algorithm
    if (empty($trending_posts)) {
        return get_trending_posts_simple($limit, $exclude_ids);
    }
    
    // Convert to WP_Post objects
    $posts = array();
    foreach ($trending_posts as $post_data) {
        $post = get_post($post_data->ID);
        if ($post) {
            $post->trending_score = $post_data->trending_score;
            $post->recent_views = $post_data->recent_views;
            $post->total_views = $post_data->total_views;
            $post->recent_comments = $post_data->recent_comments;
            $post->total_comments = $post_data->total_comments;
            $post->recent_shares = $post_data->recent_shares;
            $post->total_shares = $post_data->total_shares;
            $posts[] = $post;
        }
    }
    
    return $posts;
}

/**
 * Get Trending Categories
 * 
 * Identifies categories with the most recent activity
 * 
 * @param int $limit Number of categories to return (default: 5)
 * @return array Array of category IDs
 */
function get_trending_categories($limit = 5) {
    global $wpdb;
    
    $last_7_days = date('Y-m-d H:i:s', strtotime('-7 days'));
    
    $query = "
        SELECT 
            tt.term_id,
            COUNT(p.ID) as post_count,
            SUM(COALESCE(views.count, 0)) as total_views,
            SUM(COALESCE(comments.count, 0)) as total_comments
        FROM {$wpdb->term_taxonomy} tt
        JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
        JOIN {$wpdb->posts} p ON tr.object_id = p.ID
        LEFT JOIN (
            SELECT post_id, meta_value as count
            FROM {$wpdb->postmeta}
            WHERE meta_key = 'post_views_count'
        ) views ON p.ID = views.post_id
        LEFT JOIN (
            SELECT comment_post_ID as post_id, COUNT(*) as count
            FROM {$wpdb->comments}
            WHERE comment_approved = '1'
            GROUP BY comment_post_ID
        ) comments ON p.ID = comments.post_id
        WHERE tt.taxonomy = 'category'
        AND p.post_type = 'post'
        AND p.post_status = 'publish'
        AND p.post_date >= '{$last_7_days}'
        GROUP BY tt.term_id
        ORDER BY (post_count * 0.4 + total_views * 0.4 + total_comments * 0.2) DESC
        LIMIT {$limit}
    ";
    
    $trending_categories = $wpdb->get_col($query);
    
    return $trending_categories;
}

/**
 * Update Post Views Count
 * 
 * Increments the view count for a post
 * 
 * @param int $post_id Post ID
 */
function update_post_views_count($post_id) {
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
    
    // Also update 24-hour views
    $count_24h_key = 'post_views_count_24h';
    $count_24h = get_post_meta($post_id, $count_24h_key, true);
    
    if ($count_24h == '') {
        $count_24h = 0;
    } else {
        $count_24h++;
    }
    update_post_meta($post_id, $count_24h_key, $count_24h);
}

/**
 * Update Social Shares Count
 * 
 * Increments the social shares count for a post
 * 
 * @param int $post_id Post ID
 * @param int $increment Amount to increment (default: 1)
 */
function update_social_shares_count($post_id, $increment = 1) {
    $count_key = 'social_shares_count';
    $count = get_post_meta($post_id, $count_key, true);
    
    if ($count == '') {
        $count = 0;
    }
    $count += $increment;
    update_post_meta($post_id, $count_key, $count);
    
    // Also update 24-hour shares
    $count_24h_key = 'social_shares_count_24h';
    $count_24h = get_post_meta($post_id, $count_24h_key, true);
    
    if ($count_24h == '') {
        $count_24h = 0;
    }
    $count_24h += $increment;
    update_post_meta($post_id, $count_24h_key, $count_24h);
}

/**
 * Clean Up Old View Counts
 * 
 * Resets 24-hour view counts daily
 * This should be called via cron job
 */
function cleanup_old_view_counts() {
    global $wpdb;
    
    // Reset 24-hour view counts
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'post_views_count_24h'");
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'social_shares_count_24h'");
}

/**
 * Format Post Data for Sidebar Display
 * 
 * @param WP_Post $post Post object
 * @return array Formatted post data
 */
function format_post_for_sidebar($post) {
    $categories = get_filtered_categories($post->ID);
    $category_links = array();
    
    if (!empty($categories)) {
        foreach ($categories as $category) {
            $category_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
        }
    }
    
    $thumbnail_id = get_post_thumbnail_id($post->ID);
    $image_url = '';
    if ($thumbnail_id) {
        $image_url = wp_get_attachment_image_url($thumbnail_id, 'medium');
    } else {
        $image_url = get_stylesheet_directory_uri() . '/assets/images/new/hero.jpg';
    }
    
    return array(
        'id' => $post->ID,
        'title' => $post->post_title,
        'excerpt' => wp_trim_words($post->post_excerpt ?: $post->post_content, 15, '...'),
        'url' => get_permalink($post->ID),
        'image_url' => $image_url,
        'categories' => $category_links,
        'time_ago' => meks_time_ago($post->post_date),
        'view_count' => get_post_meta($post->ID, 'post_views_count', true) ?: 0,
        'comment_count' => get_comments_number($post->ID),
        'share_count' => get_post_meta($post->ID, 'social_shares_count', true) ?: 0
    );
}

/**
 * Track Post View
 * 
 * Hook to track post views
 */
function track_post_view() {
    if (is_single()) { // Removed the !is_user_logged_in() restriction
        $post_id = get_the_ID();
        update_post_views_count($post_id);
    }
}
add_action('wp_head', 'track_post_view');

/**
 * Get Related Posts
 * 
 * Algorithm considers:
 * - Same categories (40% weight)
 * - Same tags (30% weight)
 * - Content similarity (20% weight)
 * - Recency (10% weight)
 * 
 * @param int $post_id Current post ID
 * @param int $limit Number of posts to retrieve (default: 4)
 * @return array Array of related posts
 */
function get_related_posts($post_id, $limit = 4) {
    global $wpdb;
    
    // Get current post categories and tags
    $categories = wp_get_post_categories($post_id);
    $tags = wp_get_post_tags($post_id);
    $tag_ids = array();
    if (!empty($tags)) {
        $tag_ids = wp_list_pluck($tags, 'term_id');
    }
    

    
    // If no categories or tags, fall back to recent posts
    if (empty($categories) && empty($tag_ids)) {
        return get_recent_posts_sidebar($limit, array($post_id));
    }
    
    // Simplified query that's more reliable
    $category_condition = '';
    $tag_condition = '';
    
    if (!empty($categories)) {
        $category_ids = implode(',', array_map('intval', $categories));
        $category_condition = "
            OR EXISTS (
                SELECT 1 FROM {$wpdb->term_relationships} tr
                JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                WHERE tr.object_id = p.ID AND tt.taxonomy = 'category' AND tt.term_id IN ({$category_ids})
            )
        ";
    }
    
    if (!empty($tag_ids)) {
        $tag_ids_str = implode(',', array_map('intval', $tag_ids));
        $tag_condition = "
            OR EXISTS (
                SELECT 1 FROM {$wpdb->term_relationships} tr
                JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                WHERE tr.object_id = p.ID AND tt.taxonomy = 'post_tag' AND tt.term_id IN ({$tag_ids_str})
            )
        ";
    }
    
    // Simplified query without complex scoring
    $query = "
        SELECT DISTINCT
            p.ID,
            p.post_title,
            p.post_date,
            p.post_excerpt,
            p.post_name,
            p.guid,
            pm.meta_value as thumbnail_id
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
        WHERE p.post_type = 'post'
        AND p.post_status = 'publish'
        AND p.ID != {$post_id}
        AND pm.meta_value IS NOT NULL
        AND (
            {$category_condition}
            {$tag_condition}
        )
        ORDER BY p.post_date DESC
        LIMIT {$limit}
    ";
    

    
    $related_posts = $wpdb->get_results($query);
    

    
    // If not enough related posts found, add recent posts from same categories
    if (count($related_posts) < $limit && !empty($categories)) {
        $found_ids = wp_list_pluck($related_posts, 'ID');
        $found_ids[] = $post_id;
        $found_ids_str = implode(',', array_map('intval', $found_ids));
        
        $additional_query = "
            SELECT 
                p.ID,
                p.post_title,
                p.post_date,
                p.post_excerpt,
                p.post_name,
                p.guid,
                pm.meta_value as thumbnail_id
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            WHERE p.post_type = 'post'
            AND p.post_status = 'publish'
            AND p.ID NOT IN ({$found_ids_str})
            AND pm.meta_value IS NOT NULL
            AND EXISTS (
                SELECT 1 FROM {$wpdb->term_relationships} tr
                JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                WHERE tr.object_id = p.ID AND tt.taxonomy = 'category' AND tt.term_id IN (" . implode(',', array_map('intval', $categories)) . ")
            )
            ORDER BY p.post_date DESC
            LIMIT " . ($limit - count($related_posts)) . "
        ";
        
        $additional_posts = $wpdb->get_results($additional_query);
        $related_posts = array_merge($related_posts, $additional_posts);
        

    }
    
    // Convert to WP_Post objects
    $posts = array();
    foreach ($related_posts as $post_data) {
        $post = get_post($post_data->ID);
        if ($post) {
            $post->relatedness_score = isset($post_data->relatedness_score) ? $post_data->relatedness_score : 0;
            $posts[] = $post;
        }
    }
    

    
    return $posts;
}

/**
 * Insert Related Posts into Content
 * 
 * This function modifies the content to insert related posts after a certain height
 * 
 * @param string $content The post content
 * @param int $post_id The post ID
 * @return string Modified content with related posts
 */
function insert_related_posts_into_content($content, $post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // Only add related posts to single posts
    if (!is_single() || !$post_id) {
        return $content;
    }
    

    
    // Get related posts
    $related_posts = get_related_posts($post_id, 4);
    

    
    if (empty($related_posts)) {
        return $content;
    }
    
    // Create related posts HTML
    $related_html = '<div class="related-posts-inline m-t-xs-40 m-b-xs-40">';
    $related_html .= '<div class="section-title m-b-xs-30">';
    $related_html .= '<h4 class="axil-title">Related Articles</h4>';
    $related_html .= '</div>';
    $related_html .= '<div class="row">';
    
    foreach ($related_posts as $post) {
        $post_data = format_post_for_sidebar($post);
        $related_html .= '<div class="col-lg-6 col-md-6">';
        $related_html .= '<div class="nav-previous">';
        $related_html .= '<a href="' . esc_url($post_data['url']) . '" class="d-flex align-items-center">';
        $related_html .= '<div class="nav-image-wrapper">';
        $related_html .= '<img src="' . esc_url($post_data['image_url']) . '" alt="' . esc_attr($post_data['title']) . '" class="img-fluid">';
        $related_html .= '</div>';
        $related_html .= '<div class="nav-content">';
        $related_html .= '<span class="nav-label">Related Article</span>';
        $related_html .= '<h4 class="nav-title">' . esc_html($post_data['title']) . '</h4>';
        $related_html .= '</div>';
        $related_html .= '</a>';
        $related_html .= '</div>';
        $related_html .= '</div>';
    }
    
    $related_html .= '</div>';
    $related_html .= '</div>';
    
    // Check if content already has related posts
    if (strpos($content, 'related-posts-inline') !== false) {
        return $content;
    }
    
    // Calculate insertion position based on content length and structure
    $content_length = strlen($content);
    $paragraphs = explode('</p>', $content);
    $paragraph_count = count($paragraphs);
    

    
    // Determine insertion position - always try to insert early in the content
    $insert_position = 0;
    
    if ($paragraph_count > 3) {
        // For longer articles, insert after 1st paragraph (early in content)
        $insert_position = 1;
    } elseif ($paragraph_count > 1) {
        // For medium articles, insert after 1st paragraph
        $insert_position = 1;
    } else {
        // For short articles, insert at the end
        $insert_position = $paragraph_count - 1;
    }
    
    // Ensure insert position is valid
    $insert_position = max(0, min($insert_position, $paragraph_count - 1));
    

    
    // Insert related posts
    if ($paragraph_count > 1) {
        $paragraphs[$insert_position] .= $related_html;
        $modified_content = implode('</p>', $paragraphs);
        

    } else {
        // If only one paragraph or no paragraphs, add at the end
        $modified_content = $content . $related_html;
        

    }
    

    
    return $modified_content;
}



/**
 * Simple Related Posts Fallback
 * 
 * This function provides a simple fallback to ensure related posts are always shown
 * 
 * @param string $content The post content
 * @return string Modified content with related posts
 */
function simple_related_posts_fallback($content) {
    // Only add to single posts
    if (!is_single()) {
        return $content;
    }
    
    $post_id = get_the_ID();
    
    // Get recent posts as fallback
    $recent_posts = get_recent_posts_sidebar(4, array($post_id));
    
    if (empty($recent_posts)) {
        return $content;
    }
    
    // Create simple related posts HTML
    $related_html = '<div class="related-posts-inline m-t-xs-40 m-b-xs-40">';
    $related_html .= '<div class="section-title m-b-xs-30">';
    $related_html .= '<h4 class="axil-title">Related Articles</h4>';
    $related_html .= '</div>';
    $related_html .= '<div class="row">';
    
    foreach ($recent_posts as $post) {
        $post_data = format_post_for_sidebar($post);
        $related_html .= '<div class="col-lg-6 col-md-6">';
        $related_html .= '<div class="media post-block small-block">';
        $related_html .= '<div class="post-image-wrapper">';
        $related_html .= '<a href="' . esc_url($post_data['url']) . '" class="align-self-center">';
        $related_html .= '<img class="img-fluid" src="' . esc_url($post_data['image_url']) . '" alt="' . esc_attr($post_data['title']) . '">';
        $related_html .= '</a>';
        $related_html .= '</div>';
        $related_html .= '<div class="media-body">';
        $related_html .= '<h4 class="axil-post-title small-card-title">';
        $related_html .= '<a href="' . esc_url($post_data['url']) . '">' . esc_html($post_data['title']) . '</a>';
        $related_html .= '</h4>';
        $related_html .= '</div>';
        $related_html .= '</div>';
        $related_html .= '</div>';
    }
    
    $related_html .= '</div>';
    $related_html .= '</div>';
    
    // Add to the end of content if no related posts were found
    return $content . $related_html;
}

// Content filter is now hooked in functions.php

/**
 * Format Post Data for Related Posts Display
 * 
 * @param WP_Post $post Post object
 * @return array Formatted post data
 */
function format_post_for_related($post) {
    $categories = get_filtered_categories($post->ID);
    $category_links = array();
    
    if (!empty($categories)) {
        foreach ($categories as $category) {
            $category_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
        }
    }
    
    $thumbnail_id = get_post_thumbnail_id($post->ID);
    $image_url = '';
    if ($thumbnail_id) {
        $image_url = wp_get_attachment_image_url($thumbnail_id, 'medium');
    } else {
        $image_url = get_stylesheet_directory_uri() . '/assets/images/new/hero.jpg';
    }
    
    return array(
        'id' => $post->ID,
        'title' => $post->post_title,
        'excerpt' => wp_trim_words($post->post_excerpt ?: $post->post_content, 12, '...'),
        'url' => get_permalink($post->ID),
        'image_url' => $image_url,
        'categories' => $category_links,
        'time_ago' => meks_time_ago($post->post_date),
        'relatedness_score' => isset($post->relatedness_score) ? $post->relatedness_score : 0
    );
}

/**
 * Populate Test Data for Debugging
 * 
 * This function adds some test view and share data to recent posts
 * for testing the trending algorithm
 * 
 * @param int $posts_count Number of posts to populate (default: 10)
 */
function populate_test_data_for_trending($posts_count = 10) {
    global $wpdb;
    
    // Get recent posts
    $recent_posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $posts_count,
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    foreach ($recent_posts as $post) {
        // Add some random view counts
        $total_views = rand(10, 1000);
        $recent_views = rand(1, $total_views / 10);
        
        update_post_meta($post->ID, 'post_views_count', $total_views);
        update_post_meta($post->ID, 'post_views_count_24h', $recent_views);
        
        // Add some random share counts
        $total_shares = rand(0, 100);
        $recent_shares = rand(0, $total_shares / 5);
        
        update_post_meta($post->ID, 'social_shares_count', $total_shares);
        update_post_meta($post->ID, 'social_shares_count_24h', $recent_shares);
    }
    
    return count($recent_posts);
}

/**
 * Schedule Cleanup Cron Job
 * 
 * Set up daily cleanup of old view counts
 */
function schedule_view_count_cleanup() {
    if (!wp_next_scheduled('cleanup_view_counts')) {
        wp_schedule_event(time(), 'daily', 'cleanup_view_counts');
    }
}
add_action('wp', 'schedule_view_count_cleanup');

/**
 * Hook for cleanup cron job
 */
add_action('cleanup_view_counts', 'cleanup_old_view_counts');

/**
 * Recommended For You Algorithm
 * 
 * Uses browser cookies to track user behavior and provide personalized recommendations
 */

/**
 * Track user behavior via cookies
 * 
 * @param int $post_id Post ID being viewed
 */
function track_user_behavior($post_id) {
    if (!is_single() || !$post_id) {
        return;
    }
    
    // Get current user session ID
    $session_id = isset($_COOKIE['byline_session']) ? $_COOKIE['byline_session'] : uniqid('session_', true);
    
    // Set session cookie if not exists (30 days expiry)
    if (!isset($_COOKIE['byline_session'])) {
        setcookie('byline_session', $session_id, time() + (30 * 24 * 60 * 60), '/');
    }
    
    // Get post categories
    $categories = get_filtered_categories($post_id);
    $category_ids = array();
    if (!empty($categories)) {
        foreach ($categories as $category) {
            $category_ids[] = $category->term_id;
        }
    }
    
    // Get current timestamp
    $timestamp = time();
    
    // Store view data in cookie
    $view_data = array(
        'post_id' => $post_id,
        'category_ids' => $category_ids,
        'timestamp' => $timestamp,
        'session_id' => $session_id
    );
    
    // Get existing view history
    $view_history = isset($_COOKIE['byline_view_history']) ? json_decode(stripslashes($_COOKIE['byline_view_history']), true) : array();
    
    // Add current view to history (keep last 20 views)
    $view_history[] = $view_data;
    if (count($view_history) > 20) {
        $view_history = array_slice($view_history, -20);
    }
    
    // Store updated history
    setcookie('byline_view_history', json_encode($view_history), time() + (30 * 24 * 60 * 60), '/');
    
    // Track reading time (estimate based on content length)
    $post = get_post($post_id);
    $word_count = str_word_count(strip_tags($post->post_content));
    $estimated_reading_time = max(1, round($word_count / 200)); // 200 words per minute
    
    // Store reading time data
    $reading_data = array(
        'post_id' => $post_id,
        'reading_time' => $estimated_reading_time,
        'timestamp' => $timestamp
    );
    
    $reading_history = isset($_COOKIE['byline_reading_history']) ? json_decode(stripslashes($_COOKIE['byline_reading_history']), true) : array();
    $reading_history[] = $reading_data;
    if (count($reading_history) > 50) {
        $reading_history = array_slice($reading_history, -50);
    }
    
    setcookie('byline_reading_history', json_encode($reading_history), time() + (30 * 24 * 60 * 60), '/');
}

/**
 * Get user preferences from cookies (Enhanced)
 * 
 * @return array User preferences
 */
function get_user_preferences() {
    $preferences = array(
        'favorite_categories' => array(),
        'reading_patterns' => array(),
        'time_preferences' => array(),
        'content_length_preference' => 'medium',
        'recently_viewed' => array()
    );
    
    // Analyze view history for category preferences
    $view_history = isset($_COOKIE['byline_view_history']) ? json_decode(stripslashes($_COOKIE['byline_view_history']), true) : array();
    
    if (!empty($view_history)) {
        $category_counts = array();
        $category_recency_scores = array();
        $recent_views = array_slice($view_history, -20); // Increased to last 20 views
        
        // Track recently viewed posts (last 10 posts)
        $recent_post_ids = array();
        foreach ($recent_views as $view) {
            if (isset($view['post_id'])) {
                $recent_post_ids[] = intval($view['post_id']);
            }
        }
        $preferences['recently_viewed'] = array_slice(array_unique($recent_post_ids), -10);
        
        // Analyze category preferences with recency weight
        foreach ($recent_views as $index => $view) {
            if (isset($view['category_ids']) && is_array($view['category_ids'])) {
                // Give more weight to recent views
                $recency_weight = ($index + 1) / count($recent_views);
                
                foreach ($view['category_ids'] as $cat_id) {
                    $category_counts[$cat_id] = isset($category_counts[$cat_id]) ? $category_counts[$cat_id] + 1 : 1;
                    $category_recency_scores[$cat_id] = isset($category_recency_scores[$cat_id]) 
                        ? $category_recency_scores[$cat_id] + $recency_weight 
                        : $recency_weight;
                }
            }
        }
        
        // Combine frequency and recency for better scoring
        $category_scores = array();
        foreach ($category_counts as $cat_id => $count) {
            $frequency_score = $count / count($recent_views);
            $recency_score = isset($category_recency_scores[$cat_id]) ? $category_recency_scores[$cat_id] : 0;
            $category_scores[$cat_id] = ($frequency_score * 0.6) + ($recency_score * 0.4);
        }
        
        // Sort by combined score and get top 5 categories
        arsort($category_scores);
        $preferences['favorite_categories'] = array_slice(array_keys($category_scores), 0, 5);
    }
    
    // Analyze reading patterns with more granularity
    $reading_history = isset($_COOKIE['byline_reading_history']) ? json_decode(stripslashes($_COOKIE['byline_reading_history']), true) : array();
    
    if (!empty($reading_history)) {
        $reading_times = array_column($reading_history, 'reading_time');
        
        if (!empty($reading_times)) {
            // Use median instead of average for better representation
            sort($reading_times);
            $count = count($reading_times);
            $median_reading_time = ($count % 2 == 0) 
                ? ($reading_times[$count/2 - 1] + $reading_times[$count/2]) / 2
                : $reading_times[floor($count/2)];
            
            // More nuanced classification
            if ($median_reading_time < 1.5) {
                $preferences['content_length_preference'] = 'short';
            } elseif ($median_reading_time > 6) {
                $preferences['content_length_preference'] = 'long';
            } else {
                $preferences['content_length_preference'] = 'medium';
            }
            
            // Store reading patterns for additional insights
            $preferences['reading_patterns'] = array(
                'median_time' => $median_reading_time,
                'avg_time' => array_sum($reading_times) / count($reading_times),
                'session_count' => count($reading_times)
            );
        }
    }
    
    // Analyze time preferences
    if (!empty($view_history)) {
        $hour_counts = array();
        foreach ($view_history as $view) {
            if (isset($view['timestamp'])) {
                $hour = date('H', $view['timestamp']);
                $hour_counts[$hour] = isset($hour_counts[$hour]) ? $hour_counts[$hour] + 1 : 1;
            }
        }
        
        if (!empty($hour_counts)) {
            arsort($hour_counts);
            $preferences['time_preferences'] = array_keys(array_slice($hour_counts, 0, 3));
        }
    }
    
    return $preferences;
}

/**
 * Calculate recommendation score for a post
 * 
 * @param int $post_id Post ID
 * @param array $user_preferences User preferences
 * @return float Recommendation score
 */
function calculate_recommendation_score($post_id, $user_preferences) {
    $score = 0.0;
    
    // Get post data
    $post = get_post($post_id);
    $categories = get_filtered_categories($post_id);
    $category_ids = array();
    if (!empty($categories)) {
        $category_ids = wp_list_pluck($categories, 'term_id');
    }
    
    // Category preference score (40% weight)
    if (!empty($user_preferences['favorite_categories']) && !empty($category_ids)) {
        $category_matches = array_intersect($user_preferences['favorite_categories'], $category_ids);
        if (!empty($category_matches)) {
            $score += 0.4 * (count($category_matches) / count($user_preferences['favorite_categories']));
        }
    }
    
    // Content length preference score (20% weight)
    $word_count = str_word_count(strip_tags($post->post_content));
    $estimated_reading_time = max(1, round($word_count / 200));
    
    switch ($user_preferences['content_length_preference']) {
        case 'short':
            if ($estimated_reading_time <= 3) $score += 0.2;
            elseif ($estimated_reading_time <= 5) $score += 0.1;
            break;
        case 'long':
            if ($estimated_reading_time >= 8) $score += 0.2;
            elseif ($estimated_reading_time >= 5) $score += 0.1;
            break;
        default: // medium
            if ($estimated_reading_time >= 3 && $estimated_reading_time <= 7) $score += 0.2;
            break;
    }
    
    // Recency score (20% weight) - prefer recent posts
    $days_old = (time() - strtotime($post->post_date)) / (24 * 60 * 60);
    if ($days_old <= 1) {
        $score += 0.2;
    } elseif ($days_old <= 3) {
        $score += 0.15;
    } elseif ($days_old <= 7) {
        $score += 0.1;
    } elseif ($days_old <= 30) {
        $score += 0.05;
    }
    
    // Engagement score (20% weight) - based on views and shares
    $views = get_post_meta($post_id, 'post_views_count', true);
    $shares = get_post_meta($post_id, 'social_shares_count', true);
    
    $engagement_score = 0;
    if ($views > 1000) $engagement_score += 0.1;
    if ($views > 500) $engagement_score += 0.05;
    if ($shares > 50) $engagement_score += 0.05;
    if ($shares > 20) $engagement_score += 0.05;
    
    $score += $engagement_score;
    
    return $score;
}

/**
 * Get recommended posts for current user
 * 
 * @param int $limit Number of posts to return
 * @param array $exclude_ids Post IDs to exclude
 * @return array Recommended posts
 */
function get_recommended_posts($limit = 6, $exclude_ids = array()) {
    // Get user preferences
    $user_preferences = get_user_preferences();
    
    // Check if we have sufficient user data
    $has_user_data = !empty($user_preferences['favorite_categories']) || 
                     !empty($user_preferences['recently_viewed']);
    
    // If no user history at all, return random recent posts
    if (!$has_user_data) {
        return get_random_recent_posts($limit, $exclude_ids);
    }
    
    // Get candidate posts (increased pool for better selection)
    $candidate_posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 100, // Larger pool for better recommendations
        'orderby' => 'date',
        'order' => 'DESC',
        'post__not_in' => $exclude_ids,
        'date_query' => array(
            array(
                'after' => '60 days ago' // Focus on recent content
            )
        )
    ));
    
    // If not enough candidates, supplement with trending posts
    if (count($candidate_posts) < $limit) {
        $trending = get_trending_posts($limit - count($candidate_posts), $exclude_ids);
        return array_merge($candidate_posts, $trending);
    }
    
    // Calculate scores for each post with enhanced algorithm
    $scored_posts = array();
    $viewed_post_ids = isset($user_preferences['recently_viewed']) ? $user_preferences['recently_viewed'] : array();
    
    foreach ($candidate_posts as $post) {
        // Skip if user recently viewed this post
        if (in_array($post->ID, $viewed_post_ids)) {
            continue;
        }
        
        $score = calculate_enhanced_recommendation_score($post->ID, $user_preferences);
        
        $scored_posts[] = array(
            'post' => $post,
            'score' => $score
        );
    }
    
    // If we don't have enough scored posts, fallback to random
    if (empty($scored_posts)) {
        return get_random_recent_posts($limit, $exclude_ids);
    }
    
    // Sort by score (highest first)
    usort($scored_posts, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });
    
    // Get top scoring posts with diversity
    $recommended_posts = get_diverse_recommendations($scored_posts, $limit);
    
    // If we still don't have enough, supplement with random posts
    if (count($recommended_posts) < $limit) {
        $needed = $limit - count($recommended_posts);
        $already_included = array_merge($exclude_ids, wp_list_pluck($recommended_posts, 'ID'));
        $random_posts = get_random_recent_posts($needed, $already_included);
        $recommended_posts = array_merge($recommended_posts, $random_posts);
    }
    
    return $recommended_posts;
}

/**
 * Calculate enhanced recommendation score with multiple factors
 * 
 * @param int $post_id Post ID
 * @param array $user_preferences User preferences
 * @return float Recommendation score (0-1)
 */
function calculate_enhanced_recommendation_score($post_id, $user_preferences) {
    $score = 0.0;
    
    // Get post data
    $post = get_post($post_id);
    $categories = get_filtered_categories($post_id);
    $category_ids = !empty($categories) ? wp_list_pluck($categories, 'term_id') : array();
    
    // 1. CATEGORY MATCH SCORE (35% weight)
    if (!empty($user_preferences['favorite_categories']) && !empty($category_ids)) {
        $category_matches = array_intersect($user_preferences['favorite_categories'], $category_ids);
        if (!empty($category_matches)) {
            // Give higher weight to top favorite categories
            $match_score = 0;
            foreach ($category_matches as $cat_id) {
                $position = array_search($cat_id, $user_preferences['favorite_categories']);
                // First favorite gets 1.0, second gets 0.8, third gets 0.6, etc.
                $match_score += max(0.4, 1.0 - ($position * 0.2));
            }
            $score += 0.35 * min(1.0, $match_score / count($category_matches));
        }
    }
    
    // 2. CONTENT SIMILARITY SCORE (25% weight)
    // Based on reading time preference
    $word_count = str_word_count(strip_tags($post->post_content));
    $estimated_reading_time = max(1, round($word_count / 200));
    
    switch ($user_preferences['content_length_preference']) {
        case 'short':
            if ($estimated_reading_time <= 3) $score += 0.25;
            elseif ($estimated_reading_time <= 5) $score += 0.15;
            elseif ($estimated_reading_time <= 7) $score += 0.08;
            break;
        case 'long':
            if ($estimated_reading_time >= 10) $score += 0.25;
            elseif ($estimated_reading_time >= 7) $score += 0.18;
            elseif ($estimated_reading_time >= 5) $score += 0.10;
            break;
        default: // medium
            if ($estimated_reading_time >= 4 && $estimated_reading_time <= 8) $score += 0.25;
            elseif ($estimated_reading_time >= 3 && $estimated_reading_time <= 10) $score += 0.15;
            break;
    }
    
    // 3. RECENCY SCORE (20% weight) - Time decay
    $days_old = (time() - strtotime($post->post_date)) / (24 * 60 * 60);
    if ($days_old <= 1) {
        $score += 0.20;
    } elseif ($days_old <= 3) {
        $score += 0.17;
    } elseif ($days_old <= 7) {
        $score += 0.14;
    } elseif ($days_old <= 14) {
        $score += 0.10;
    } elseif ($days_old <= 30) {
        $score += 0.06;
    } else {
        // Gradual decay for older posts
        $score += max(0, 0.06 * (1 - (($days_old - 30) / 30)));
    }
    
    // 4. ENGAGEMENT & QUALITY SCORE (15% weight)
    $views = intval(get_post_meta($post_id, 'post_views_count', true));
    $shares = intval(get_post_meta($post_id, 'social_shares_count', true));
    $comments_count = wp_count_comments($post_id)->approved;
    
    $engagement_score = 0;
    
    // Views score (max 0.06)
    if ($views > 5000) $engagement_score += 0.06;
    elseif ($views > 2000) $engagement_score += 0.05;
    elseif ($views > 1000) $engagement_score += 0.04;
    elseif ($views > 500) $engagement_score += 0.03;
    elseif ($views > 100) $engagement_score += 0.02;
    
    // Shares score (max 0.05)
    if ($shares > 100) $engagement_score += 0.05;
    elseif ($shares > 50) $engagement_score += 0.04;
    elseif ($shares > 20) $engagement_score += 0.03;
    elseif ($shares > 10) $engagement_score += 0.02;
    
    // Comments score (max 0.04)
    if ($comments_count > 50) $engagement_score += 0.04;
    elseif ($comments_count > 20) $engagement_score += 0.03;
    elseif ($comments_count > 10) $engagement_score += 0.02;
    elseif ($comments_count > 5) $engagement_score += 0.01;
    
    $score += min(0.15, $engagement_score);
    
    // 5. TRENDING BOOST (5% weight)
    // Check if post is in a trending category
    $trending_categories = get_trending_categories();
    if (!empty($category_ids) && !empty($trending_categories)) {
        $trending_matches = array_intersect($category_ids, $trending_categories);
        if (!empty($trending_matches)) {
            $score += 0.05;
        }
    }
    
    // Add small randomization to prevent stagnation (±2%)
    $randomization = (mt_rand(-20, 20) / 1000);
    $score += $randomization;
    
    return max(0, min(1, $score)); // Clamp between 0 and 1
}

/**
 * Get diverse recommendations to avoid showing too many similar posts
 * 
 * @param array $scored_posts Array of posts with scores
 * @param int $limit Number of posts to return
 * @return array Diversified post array
 */
function get_diverse_recommendations($scored_posts, $limit) {
    $selected_posts = array();
    $selected_categories = array();
    $category_count = array();
    
    // First pass: get high-scoring posts with category diversity
    foreach ($scored_posts as $scored_post) {
        if (count($selected_posts) >= $limit) {
            break;
        }
        
        $post = $scored_post['post'];
        $categories = get_filtered_categories($post->ID);
        $category_ids = !empty($categories) ? wp_list_pluck($categories, 'term_id') : array();
        
        // Check category diversity
        $category_overlap = array_intersect($category_ids, $selected_categories);
        
        // Allow post if it's high scoring or brings category diversity
        if ($scored_post['score'] > 0.5 || empty($category_overlap) || count($selected_posts) === 0) {
            $selected_posts[] = $post;
            
            // Track categories (limit 2 posts per category)
            foreach ($category_ids as $cat_id) {
                $selected_categories[] = $cat_id;
                $category_count[$cat_id] = isset($category_count[$cat_id]) ? $category_count[$cat_id] + 1 : 1;
            }
        }
    }
    
    // Second pass: if we still need more posts, be less strict
    if (count($selected_posts) < $limit) {
        foreach ($scored_posts as $scored_post) {
            if (count($selected_posts) >= $limit) {
                break;
            }
            
            $post = $scored_post['post'];
            $post_id = $post->ID;
            
            // Skip if already selected
            if (in_array($post_id, wp_list_pluck($selected_posts, 'ID'))) {
                continue;
            }
            
            $selected_posts[] = $post;
        }
    }
    
    return $selected_posts;
}

/**
 * Get random recent posts as fallback when no user data available
 * 
 * @param int $limit Number of posts to return
 * @param array $exclude_ids Post IDs to exclude
 * @return array Random recent posts
 */
function get_random_recent_posts($limit = 6, $exclude_ids = array()) {
    $posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $limit * 3, // Get more to randomize from
        'orderby' => 'date',
        'order' => 'DESC',
        'post__not_in' => $exclude_ids,
        'date_query' => array(
            array(
                'after' => '30 days ago'
            )
        )
    ));
    
    // If we don't have enough recent posts, get any posts
    if (count($posts) < $limit) {
        $posts = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $limit * 2,
            'orderby' => 'rand',
            'post__not_in' => $exclude_ids
        ));
    }
    
    // Shuffle and return requested number
    shuffle($posts);
    return array_slice($posts, 0, $limit);
}

/**
 * Display recommended posts widget
 * 
 * @param int $limit Number of posts to display
 */
function display_recommended_posts_widget($limit = 6) {
    // Track current post view
    if (is_single()) {
        track_user_behavior(get_the_ID());
    }
    
    // Get recommended posts
    $recommended_posts = get_recommended_posts($limit);
    
    if (empty($recommended_posts)) {
        return;
    }
    
    // Display widget
    echo '<div class="recommended-posts-widget bg-grey-light-three m-b-xs-40">';
    echo '<div class="section-title m-b-xs-20">';
    echo '<h3 class="axil-title">Recommended For You</h3>';
    echo '<div class="recommendation-info">';
    echo '<small class="text-muted">Based on your reading history</small>';
    echo '</div>';
    echo '</div>';
    
    echo '<div class="recommended-posts-list">';
    foreach ($recommended_posts as $post) {
        $post_data = format_post_for_sidebar($post);
        echo '<div class="media post-block small-block recommended-card">';
        echo '<div class="post-image-wrapper">';
        echo '<a href="' . esc_url($post_data['url']) . '" class="align-self-center">';
        echo '<img class="img-fluid" src="' . esc_url($post_data['image_url']) . '" alt="' . esc_attr($post_data['title']) . '">';
        echo '</a>';
        echo '</div>';
        echo '<div class="media-body">';
        echo '<h4 class="axil-post-title small-card-title">';
        echo '<a href="' . esc_url($post_data['url']) . '">' . esc_html($post_data['title']) . '</a>';
        echo '</h4>';
        echo '<div class="post-meta">';
        echo '<span class="post-time"><i class="far fa-clock"></i> ' . esc_html($post_data['time_ago']) . '</span>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
}

/**
 * Hook to track user behavior on single posts
 */
add_action('wp_head', function() {
    if (is_single()) {
        track_user_behavior(get_the_ID());
    }
});

/**
 * Add JavaScript for enhanced tracking
 */
function add_recommendation_tracking_js() {
    if (is_single()) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Track time spent on page
            let startTime = Date.now();
            let maxTime = 0;
            
            // Track scroll depth
            let maxScroll = 0;
            
            window.addEventListener('scroll', function() {
                const scrollPercent = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
                maxScroll = Math.max(maxScroll, scrollPercent);
            });
            
            // Track when user leaves page
            window.addEventListener('beforeunload', function() {
                const timeSpent = Math.round((Date.now() - startTime) / 1000);
                maxTime = Math.max(maxTime, timeSpent);
                
                // Store engagement data in cookie
                const engagementData = {
                    post_id: <?php echo get_the_ID(); ?>,
                    time_spent: maxTime,
                    scroll_depth: maxScroll,
                    timestamp: Date.now()
                };
                
                let engagementHistory = JSON.parse(localStorage.getItem('byline_engagement') || '[]');
                engagementHistory.push(engagementData);
                
                // Keep only last 50 engagements
                if (engagementHistory.length > 50) {
                    engagementHistory = engagementHistory.slice(-50);
                }
                
                localStorage.setItem('byline_engagement', JSON.stringify(engagementHistory));
            });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'add_recommendation_tracking_js');

/**
 * Get Trending Posts - Main function
 * 
 * This is the main trending posts function that can be called from other parts of the theme.
 * It uses the simple trending algorithm as a fallback when no user preferences are available.
 * 
 * @param int $limit Number of posts to retrieve (default: 5)
 * @param array $exclude_ids Array of post IDs to exclude
 * @return array Array of trending posts
 */
function get_trending_posts($limit = 5, $exclude_ids = array()) {
    // Use the simple trending algorithm as the main trending function
    return get_trending_posts_simple($limit, $exclude_ids);
}
?>
