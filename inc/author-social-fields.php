<?php
/**
 * Add Social Media Fields to Author Profile
 */

// Enqueue media uploader script
function byline_enqueue_user_profile_scripts($hook) {
    if ($hook !== 'profile.php' && $hook !== 'user-edit.php' && $hook !== 'user-new.php') {
        return;
    }
    
    wp_enqueue_media();
    wp_enqueue_script('byline-user-profile', get_template_directory_uri() . '/assets/js/user-profile-upload.js', array('jquery'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'byline_enqueue_user_profile_scripts');

// Add custom user contact methods
function byline_add_author_social_fields($user_contact) {
    // Add new social media fields
    $user_contact['facebook']  = __('Facebook URL', 'byline');
    $user_contact['twitter']   = __('Twitter/X URL', 'byline');
    $user_contact['instagram'] = __('Instagram URL', 'byline');
    $user_contact['linkedin']  = __('LinkedIn URL', 'byline');
    $user_contact['youtube']   = __('YouTube URL', 'byline');
    $user_contact['whatsapp']  = __('WhatsApp Number (with country code)', 'byline');
    
    return $user_contact;
}
add_filter('user_contactmethods', 'byline_add_author_social_fields');

// Add custom author profile fields in user profile
function byline_author_profile_fields($user) {
    $user_id = is_object($user) ? $user->ID : 0;
    $profile_picture_id = get_user_meta($user_id, 'author_profile_picture', true);
    $profile_picture_url = $profile_picture_id ? wp_get_attachment_image_url($profile_picture_id, 'medium') : '';
    ?>
    <h3><?php _e('Author Information', 'byline'); ?></h3>
    
    <table class="form-table">
        <!-- Profile Picture Upload -->
        <tr>
            <th><label for="author_profile_picture"><?php _e('Profile Picture', 'byline'); ?></label></th>
            <td>
                <div class="author-profile-picture-wrapper">
                    <div class="author-profile-picture-preview" style="margin-bottom: 10px;">
                        <?php if ($profile_picture_url) : ?>
                            <img src="<?php echo esc_url($profile_picture_url); ?>" style="max-width: 150px; height: auto; border-radius: 50%; border: 3px solid #ddd;" />
                        <?php else : ?>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/default-avatar.png" style="max-width: 150px; height: auto; border-radius: 50%; border: 3px solid #ddd; display: none;" class="default-avatar" />
                            <p class="no-image-text"><?php _e('No profile picture uploaded', 'byline'); ?></p>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="author_profile_picture" id="author_profile_picture" value="<?php echo esc_attr($profile_picture_id); ?>" />
                    <button type="button" class="button button-secondary" id="upload_author_picture_button">
                        <?php echo $profile_picture_url ? __('Change Picture', 'byline') : __('Upload Picture', 'byline'); ?>
                    </button>
                    <?php if ($profile_picture_url) : ?>
                        <button type="button" class="button button-secondary" id="remove_author_picture_button" style="margin-left: 5px;"><?php _e('Remove Picture', 'byline'); ?></button>
                    <?php endif; ?>
                    <br />
                    <span class="description"><?php _e('Upload a profile picture. This will be displayed on single post pages. Picture must be uploaded to show author box on frontend.', 'byline'); ?></span>
                </div>
            </td>
        </tr>
        
        <!-- Author Bio -->
        <tr>
            <th><label for="author_bio"><?php _e('Author Biography', 'byline'); ?></label></th>
            <td>
                <textarea name="author_bio" id="author_bio" rows="5" class="large-text"><?php echo esc_textarea(get_user_meta($user_id, 'description', true)); ?></textarea>
                <br />
                <span class="description"><?php _e('Write a short bio about the author. This will be displayed on single post pages.', 'byline'); ?></span>
            </td>
        </tr>
        
        <!-- Author Title -->
        <tr>
            <th><label for="author_title"><?php _e('Author Title/Position', 'byline'); ?></label></th>
            <td>
                <input type="text" name="author_title" id="author_title" 
                       value="<?php echo esc_attr(get_user_meta($user_id, 'author_title', true)); ?>" 
                       class="regular-text" />
                <br />
                <span class="description"><?php _e('E.g., Senior Reporter, News Editor, Contributor', 'byline'); ?></span>
            </td>
        </tr>
    </table>
    
    <style>
        .author-profile-picture-wrapper .author-profile-picture-preview img {
            max-width: 150px;
            height: auto;
            border-radius: 50%;
        }
        .author-profile-picture-wrapper .no-image-text {
            color: #666;
            font-style: italic;
        }
    </style>
    <?php
}
add_action('show_user_profile', 'byline_author_profile_fields');
add_action('edit_user_profile', 'byline_author_profile_fields');
add_action('user_new_form', 'byline_author_profile_fields');

// Save custom user profile fields
function byline_save_author_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    // Save profile picture
    if (isset($_POST['author_profile_picture'])) {
        update_user_meta($user_id, 'author_profile_picture', absint($_POST['author_profile_picture']));
    }
    
    // Save author bio
    if (isset($_POST['author_bio'])) {
        update_user_meta($user_id, 'description', sanitize_textarea_field($_POST['author_bio']));
    }
    
    // Save author title
    if (isset($_POST['author_title'])) {
        update_user_meta($user_id, 'author_title', sanitize_text_field($_POST['author_title']));
    }
}
add_action('personal_options_update', 'byline_save_author_profile_fields');
add_action('edit_user_profile_update', 'byline_save_author_profile_fields');
add_action('user_register', 'byline_save_author_profile_fields');

// Helper function to check if author box should be displayed
function should_show_author_box($author_id = null, $post_id = null) {
    // Get current post ID if not provided
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // Check if author box is explicitly hidden for this post
    $hide_author = get_post_meta($post_id, '_hide_author_box', true);
    if ($hide_author === '1') {
        return false;
    }
    
    // Get author ID if not provided
    if (!$author_id) {
        $author_id = get_the_author_meta('ID');
    }
    
    // If no valid author ID, don't show
    if (!$author_id) {
        return false;
    }
    
    // Get profile picture
    $profile_picture_id = get_user_meta($author_id, 'author_profile_picture', true);
    
    // Get author bio
    $author_bio = get_the_author_meta('description', $author_id);
    
    // Show if at least one of the following is true:
    // 1. Has profile picture
    // 2. Has bio
    // We'll show the box but may show partial information
    if ($profile_picture_id || !empty($author_bio)) {
        return true;
    }
    
    // Don't show if no picture and no bio
    return false;
}

// Helper function to get author profile picture
function get_author_profile_picture($author_id = null, $size = 'medium') {
    if (!$author_id) {
        $author_id = get_the_author_meta('ID');
    }
    
    $profile_picture_id = get_user_meta($author_id, 'author_profile_picture', true);
    
    if ($profile_picture_id) {
        return wp_get_attachment_image_url($profile_picture_id, $size);
    }
    
    return false;
}

// Helper function to get author social media links
function get_author_social_links($author_id = null) {
    if (!$author_id) {
        $author_id = get_the_author_meta('ID');
    }
    
    $social_links = array();
    
    // Facebook
    $facebook = get_the_author_meta('facebook', $author_id);
    if ($facebook) {
        $social_links['facebook'] = array(
            'url' => esc_url($facebook),
            'icon' => 'fab fa-facebook-f',
            'label' => 'Facebook'
        );
    }
    
    // Twitter/X
    $twitter = get_the_author_meta('twitter', $author_id);
    if ($twitter) {
        $social_links['twitter'] = array(
            'url' => esc_url($twitter),
            'icon' => 'fab fa-x-twitter',
            'label' => 'Twitter/X'
        );
    }
    
    // Instagram
    $instagram = get_the_author_meta('instagram', $author_id);
    if ($instagram) {
        $social_links['instagram'] = array(
            'url' => esc_url($instagram),
            'icon' => 'fab fa-instagram',
            'label' => 'Instagram'
        );
    }
    
    // LinkedIn
    $linkedin = get_the_author_meta('linkedin', $author_id);
    if ($linkedin) {
        $social_links['linkedin'] = array(
            'url' => esc_url($linkedin),
            'icon' => 'fab fa-linkedin-in',
            'label' => 'LinkedIn'
        );
    }
    
    // YouTube
    $youtube = get_the_author_meta('youtube', $author_id);
    if ($youtube) {
        $social_links['youtube'] = array(
            'url' => esc_url($youtube),
            'icon' => 'fab fa-youtube',
            'label' => 'YouTube'
        );
    }
    
    // WhatsApp
    $whatsapp = get_the_author_meta('whatsapp', $author_id);
    if ($whatsapp) {
        // Clean the number (remove spaces, dashes, etc.)
        $clean_number = preg_replace('/[^0-9+]/', '', $whatsapp);
        $social_links['whatsapp'] = array(
            'url' => 'https://wa.me/' . $clean_number,
            'icon' => 'fab fa-whatsapp',
            'label' => 'WhatsApp'
        );
    }
    
    return $social_links;
}


