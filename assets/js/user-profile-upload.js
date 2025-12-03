jQuery(document).ready(function($) {
    var mediaUploader;
    
    // Upload Picture Button
    $('#upload_author_picture_button').on('click', function(e) {
        e.preventDefault();
        
        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Author Profile Picture',
            button: {
                text: 'Use This Picture'
            },
            multiple: false
        });
        
        // When a file is selected, grab the URL and set it as the value
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            
            // Set the hidden input value to the attachment ID
            $('#author_profile_picture').val(attachment.id);
            
            // Update the preview image
            $('.author-profile-picture-preview img').attr('src', attachment.url).show();
            $('.author-profile-picture-preview .default-avatar').hide();
            $('.author-profile-picture-preview .no-image-text').hide();
            
            // Update button text
            $('#upload_author_picture_button').text('Change Picture');
            
            // Show remove button if not already visible
            if ($('#remove_author_picture_button').length === 0) {
                $('#upload_author_picture_button').after('<button type="button" class="button button-secondary" id="remove_author_picture_button" style="margin-left: 5px;">Remove Picture</button>');
            } else {
                $('#remove_author_picture_button').show();
            }
        });
        
        // Open the uploader dialog
        mediaUploader.open();
    });
    
    // Remove Picture Button (delegated event for dynamically added button)
    $(document).on('click', '#remove_author_picture_button', function(e) {
        e.preventDefault();
        
        // Clear the hidden input
        $('#author_profile_picture').val('');
        
        // Reset the preview
        $('.author-profile-picture-preview img').attr('src', '').hide();
        $('.author-profile-picture-preview .no-image-text').show();
        
        // Update button text
        $('#upload_author_picture_button').text('Upload Picture');
        
        // Hide remove button
        $(this).hide();
    });
});

