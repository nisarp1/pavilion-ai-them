<?php
/**
 * Post Author Display Metabox
 * Allows hiding/showing author box per post
 */

// Add metabox to posts
function byline_add_author_display_metabox() {
    add_meta_box(
        'byline_author_display',
        __('Author Display Settings', 'byline'),
        'byline_author_display_metabox_callback',
        'post',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'byline_add_author_display_metabox');

// Metabox callback
function byline_author_display_metabox_callback($post) {
    // Add nonce for security
    wp_nonce_field('byline_author_display_nonce', 'byline_author_display_nonce_field');
    
    // Get current value
    $hide_author = get_post_meta($post->ID, '_hide_author_box', true);
    ?>
    <div class="byline-author-display-metabox">
        <p>
            <label>
                <input type="checkbox" name="hide_author_box" value="1" <?php checked($hide_author, '1'); ?> />
                <?php _e('Hide author box on this post', 'byline'); ?>
            </label>
        </p>
        <p class="description">
            <?php _e('Check this box to hide the author information on this specific post only.', 'byline'); ?>
        </p>
        
        <?php
        // Show author info preview
        $author_id = $post->post_author;
        $author_name = get_the_author_meta('display_name', $author_id);
        $author_bio = get_the_author_meta('description', $author_id);
        $author_picture = get_author_profile_picture($author_id, 'thumbnail');
        $author_title = get_user_meta($author_id, 'author_title', true);
        ?>
        
        <hr style="margin: 15px 0;">
        
        <div class="author-preview" style="margin-top: 10px;">
            <p><strong><?php _e('Current Author:', 'byline'); ?></strong></p>
            <?php if ($author_picture) : ?>
                <img src="<?php echo esc_url($author_picture); ?>" alt="<?php echo esc_attr($author_name); ?>" style="width: 60px; height: 60px; border-radius: 50%; margin-bottom: 10px;">
            <?php endif; ?>
            <p>
                <strong><?php echo esc_html($author_name); ?></strong>
                <?php if ($author_title) : ?>
                    <br><em><?php echo esc_html($author_title); ?></em>
                <?php endif; ?>
            </p>
            
            <?php if (!$author_picture && !$author_bio) : ?>
                <p style="color: #d63638; font-size: 12px;">
                    <span class="dashicons dashicons-warning" style="font-size: 16px; vertical-align: middle;"></span>
                    <?php _e('Author has no profile picture or bio set. Author box may not display on frontend.', 'byline'); ?>
                </p>
            <?php elseif (!$author_picture) : ?>
                <p style="color: #d63638; font-size: 12px;">
                    <span class="dashicons dashicons-warning" style="font-size: 16px; vertical-align: middle;"></span>
                    <?php _e('Author has no profile picture. Please upload one in user profile.', 'byline'); ?>
                </p>
            <?php elseif (!$author_bio) : ?>
                <p style="color: #dba617; font-size: 12px;">
                    <span class="dashicons dashicons-info" style="font-size: 16px; vertical-align: middle;"></span>
                    <?php _e('Author has no biography set.', 'byline'); ?>
                </p>
            <?php endif; ?>
            
            <p style="font-size: 12px; margin-top: 10px;">
                <a href="<?php echo admin_url('user-edit.php?user_id=' . $author_id); ?>" target="_blank">
                    <?php _e('Edit Author Profile', 'byline'); ?> →
                </a>
            </p>
        </div>
    </div>
    
    <style>
        .byline-author-display-metabox .author-preview {
            padding: 10px;
            background: #f6f7f7;
            border-radius: 4px;
        }
        .byline-author-display-metabox .description {
            font-size: 12px;
            color: #646970;
        }
    </style>
    <?php
}

// Save metabox data
function byline_save_author_display_metabox($post_id) {
    // Check nonce
    if (!isset($_POST['byline_author_display_nonce_field']) || 
        !wp_verify_nonce($_POST['byline_author_display_nonce_field'], 'byline_author_display_nonce')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save the value
    if (isset($_POST['hide_author_box'])) {
        update_post_meta($post_id, '_hide_author_box', '1');
    } else {
        delete_post_meta($post_id, '_hide_author_box');
    }
}
add_action('save_post', 'byline_save_author_display_metabox');

// Add column to posts list
function byline_add_author_display_column($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'author') {
            $new_columns['author_display'] = __('Author Box', 'byline');
        }
    }
    return $new_columns;
}
add_filter('manage_posts_columns', 'byline_add_author_display_column');

// Display column content
function byline_author_display_column_content($column, $post_id) {
    if ($column === 'author_display') {
        $hide_author = get_post_meta($post_id, '_hide_author_box', true);
        if ($hide_author === '1') {
            echo '<span style="color: #d63638;">Hidden</span>';
        } else {
            $author_id = get_post_field('post_author', $post_id);
            $has_picture = get_author_profile_picture($author_id);
            $has_bio = get_the_author_meta('description', $author_id);
            
            if ($has_picture && $has_bio) {
                echo '<span style="color: #00a32a;">Visible</span>';
            } elseif ($has_picture || $has_bio) {
                echo '<span style="color: #dba617;">Partial</span>';
            } else {
                echo '<span style="color: #d63638;">No Data</span>';
            }
        }
    }
}
add_action('manage_posts_custom_column', 'byline_author_display_column_content', 10, 2);

