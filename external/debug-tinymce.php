<?php
/**
 * Debug script to identify TinyMCE print plugin error
 * Add this to your theme's functions.php temporarily to debug
 */

// Hook into admin head to add debugging
add_action('admin_head', 'debug_tinymce_errors');

function debug_tinymce_errors() {
    // Only run on LiveBlog post edit screens
    global $post_type;
    if ($post_type !== 'liveblog') {
        return;
    }
    
    ?>
    <script type="text/javascript">
    console.log('=== TinyMCE Debug Start ===');
    
    // Check if TinyMCE is loaded
    if (typeof tinymce !== 'undefined') {
        console.log('TinyMCE is loaded');
        console.log('TinyMCE version:', tinymce.majorVersion + '.' + tinymce.minorVersion);
        
        // Check available plugins
        if (typeof tinymce.PluginManager !== 'undefined') {
            console.log('Available plugins:', Object.keys(tinymce.PluginManager.urls));
        }
    } else {
        console.log('TinyMCE is NOT loaded');
    }
    
    // Monitor for any script loading errors
    window.addEventListener('error', function(e) {
        if (e.filename && e.filename.includes('tinymce')) {
            console.error('TinyMCE Error:', e);
        }
    });
    
    // Check for any existing TinyMCE configurations
    if (typeof tinyMCEPreInit !== 'undefined') {
        console.log('tinyMCEPreInit found:', tinyMCEPreInit);
    }
    
    console.log('=== TinyMCE Debug End ===');
    </script>
    <?php
} 