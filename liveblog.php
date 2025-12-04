<?php 
// Check if this is a single LiveBlog post
if (get_post_type() === 'liveblog') {
    include 'parts/shared/html-header.php'; 
    include 'parts/shared/header.php'; 
    
    // Get the additional post blocks
    $additional_blocks = get_liveblog_additional_blocks();
    
    // Get only published blocks
    $published_blocks = get_published_liveblog_blocks();
} else {
    // If not a LiveBlog post, redirect to 404 or show error
    wp_die('This template is for LiveBlog posts only.');
}
?>

<section class="banner section-gap">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="post-title-wrapper">
                    <div class="d-flex align-items-center flex-nowrap mb-3">
                        <div class="post-cat-group flex-shrink-0">
                            <a href="#" class="post-cat color-red-one">LIVE</a>
                            <a href="#" class="post-cat color-blue-three">CRICKET</a>
                            <a href="#" class="post-cat color-yellow-one">SPORTS</a>
                        </div>
                        <div class="post-time ms-3" class="post-time live">
                            <i class="fas fa-circle" class="clock-icon pulse"></i>LIVE NOW
                        </div>
                    </div>

                    <h2 class="axil-post-title hover-line"><?php echo get_the_title(); ?></h2>
                    <div class="post-metas banner-post-metas">
                        <ul class="list-inline">
                            <li><i class="dot">.</i><?php echo get_the_date(); ?></li>
                            <li><a href="#"><i class="feather icon-activity"></i>അപ്ഡേറ്റ് ചെയ്തത് 8 മിനിറ്റ് മുമ്പ്</a></li>
                            <li><a href="#"><i class="feather icon-user"></i><?php echo get_the_author(); ?></a></li>
                        </ul>
                    </div>
                    <!-- End of .post-metas -->

                </div>
                <!-- End of .post-title-wrapper -->
            </div>
            <!-- End of .col-lg-6 -->

            <div class="col-lg-6">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                <?php else: ?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.png" alt="" class="img-fluid">
                <?php endif; ?>
            </div>
        </div>
        <!-- End of .row -->
    </div>
    <!-- End of .container -->
</section>
<!-- End of .banner -->


<!-- Live Blog Content -->
<div class="live-blog-wrapper section-gap" class="live-blog-wrapper">
    <div class="container">
        <div class="row">

            <!-- Main Live Blog Content -->
            <div class="col-lg-8">
                <div class="live-blog-content">
                    <?php 
                    // Get only published blocks
                    $published_blocks = get_published_liveblog_blocks();
                    
                    // Debug output
                    echo "<!-- DEBUG: Published blocks count: " . count($published_blocks) . " -->\n";
                    if (empty($published_blocks)) {
                        echo "<!-- DEBUG: No published blocks found -->\n";
                    } else {
                        echo "<!-- DEBUG: Published blocks found -->\n";
                        foreach ($published_blocks as $debug_index => $debug_block) {
                            echo "<!-- DEBUG: Block " . $debug_index . " - Title: " . esc_html($debug_block['title']) . " - Status: " . esc_html($debug_block['status']) . " -->\n";
                        }
                    }
                    ?>
                    <?php if (!empty($published_blocks)): ?>
                        <?php foreach ($published_blocks as $index => $block): ?>
                                                    <!-- Live Blog Entry <?php echo $index + 1; ?> -->
                        <div class="live-blog-entry"<?php echo ($index === 0) ? ' style="border-left: 3px solid red;"' : ''; ?>>
                                <div class="entry-header" class="entry-header">
                                    <div class="entry-timestamp">
                                        <?php 
                                        if (!empty($block['timestamp'])) {
                                            // Create DateTime object from the block's timestamp
                                            $block_time = new DateTime($block['timestamp']);
                                            
                                            // Convert to IST (Asia/Kolkata)
                                            $ist_timezone = new DateTimeZone('Asia/Kolkata');
                                            $ist_time = clone $block_time;
                                            $ist_time->setTimezone($ist_timezone);
                                            
                                            // Convert to UAE (Asia/Dubai)
                                            $uae_timezone = new DateTimeZone('Asia/Dubai');
                                            $uae_time = clone $block_time;
                                            $uae_time->setTimezone($uae_timezone);
                                            
                                            // Display in format: "Aug 2, 2025 09:34 IST / 08:04 UAE"
                                            echo $uae_time->format('M j, Y') . ' ' . $ist_time->format('H:i') . ' IST / ' . $uae_time->format('H:i') . ' UAE';
                                        } else {
                                            // Fallback to post date if no block timestamp
                                            $post_time = new DateTime(get_the_date('Y-m-d H:i:s'));
                                            
                                            // Convert to IST
                                            $ist_timezone = new DateTimeZone('Asia/Kolkata');
                                            $ist_time = clone $post_time;
                                            $ist_time->setTimezone($ist_timezone);
                                            
                                            // Convert to UAE
                                            $uae_timezone = new DateTimeZone('Asia/Dubai');
                                            $uae_time = clone $post_time;
                                            $uae_time->setTimezone($uae_timezone);
                                            
                                            echo $uae_time->format('M j, Y') . ' ' . $ist_time->format('H:i') . ' IST / ' . $uae_time->format('H:i') . ' UAE';
                                        }
                                        ?>
                                    </div>
                                    <div class="entry-actions">
                                        <ul class="social-share social-share__with-bg social-share__horizontal">
                                            <li>
                                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                                                    target="_blank"
                                                    title="Share on Facebook">
                                                    <i class="fab fa-facebook-f"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($block['title']); ?>&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                                                    target="_blank"
                                                    title="Share on X (Twitter)">
                                                    <i class="fab fa-x-twitter"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://wa.me/?text=<?php echo urlencode($block['title'] . ' - ' . 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                                                    target="_blank"
                                                    title="Share on WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="copyLiveBlogEntry(this); return false;" title="Copy Link">
                                                    <i class="fas fa-link"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="entry-content">
                                    <?php if (!empty($block['title'])): ?>
                                        <h3><?php echo esc_html($block['title']); ?></h3>
                                    <?php else: ?>
                                        <h3><?php echo esc_html('Update #' . ($index + 1)); ?></h3>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($block['content'])): ?>
                                        <p class="entry-description">
                                            <?php echo wp_kses_post($block['content']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($block['media_url'])): ?>
                                        <div class="entry-media">
                                            <img src="<?php echo esc_url($block['media_url']); ?>" alt="<?php echo esc_attr($block['title']); ?>" class="img-fluid" style="max-width: 100%; height: auto; margin: 15px 0; border-radius: 8px;">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Social Media Embeds Area -->
                                    <?php if (!empty($block['autoEmbedUrls'])): ?>
                                        <div class="social-embeds-container">
                                            <div class="auto-embed-area" data-embed-urls="<?php echo esc_attr($block['autoEmbedUrls']); ?>"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if (($index + 1) % 2 == 0): ?>
                                <!-- Advertisement -->
                                <div class="advertisement-section m-b-xs-20">
                                    <a href="#" class="d-block">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/advt-long.jpg" alt="Advertisement" class="img-fluid" class="advertisement-image">
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Default content if no additional blocks -->
                        <div class="live-blog-entry" class="live-blog-entry">
                            <div class="entry-header" class="entry-header">
                                <div class="entry-timestamp">
                                    <?php 
                                    // Fallback to post date if no block timestamp
                                    $post_time = new DateTime(get_the_date('Y-m-d H:i:s'));
                                    
                                    // Convert to IST
                                    $ist_timezone = new DateTimeZone('Asia/Kolkata');
                                    $ist_time = clone $post_time;
                                    $ist_time->setTimezone($ist_timezone);
                                    
                                    // Convert to UAE
                                    $uae_timezone = new DateTimeZone('Asia/Dubai');
                                    $uae_time = clone $post_time;
                                    $uae_time->setTimezone($uae_timezone);
                                    
                                    echo $uae_time->format('M j, Y') . ' ' . $ist_time->format('H:i') . ' IST / ' . $uae_time->format('H:i') . ' UAE';
                                    ?>
                                </div>
                                <div class="entry-actions">
                                    <ul class="social-share social-share__with-bg social-share__horizontal">
                                        <li>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                                                target="_blank"
                                                title="Share on Facebook">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                                                target="_blank"
                                                title="Share on X (Twitter)">
                                                <i class="fab fa-x-twitter"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' - ' . 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                                                target="_blank"
                                                title="Share on WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" onclick="copyLiveBlogEntry(this); return false;" title="Copy Link">
                                                <i class="fas fa-link"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="entry-content">
                                <h3><?php echo get_the_title(); ?></h3>
                                <p class="entry-description">
                                    <?php echo get_the_excerpt(); ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar">
                    <!-- Live Blog Updates Widget -->
                    <div class="widget widget-live-updates">
                        <h4 class="widget-title">Live Updates</h4>
                        <div class="live-updates-list">
                            <?php if (!empty($published_blocks)): ?>
                                <?php foreach ($published_blocks as $index => $block): ?>
                                    <div class="update-item">
                                        <div class="update-time">
                                            <?php 
                                            if (!empty($block['timestamp'])) {
                                                $block_time = new DateTime($block['timestamp']);
                                                $uae_timezone = new DateTimeZone('Asia/Dubai');
                                                $uae_time = clone $block_time;
                                                $uae_time->setTimezone($uae_timezone);
                                                echo $uae_time->format('H:i');
                                            }
                                            ?>
                                        </div>
                                        <div class="update-content">
                                            <h5><?php echo esc_html($block['title'] ?: 'Update #' . ($index + 1)); ?></h5>
                                            <p><?php echo wp_trim_words($block['content'], 20); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No live updates available yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Social Media Feed Widget -->
                    <div class="widget widget-social-feed">
                        <h4 class="widget-title">Social Media Feed</h4>
                        <div class="social-feed-container">
                            <!-- This will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Frontend JavaScript for LiveBlog -->
<script>
(function($) {
    'use strict';
    
    // Function to create embed for social media URLs
    function createSocialEmbed(url) {
        // Twitter/X
        if (url.includes('twitter.com') || url.includes('x.com')) {
            const tweetId = extractTweetId(url);
            if (tweetId) {
                return `<div class="social-embed-item twitter-embed">
                    <blockquote class="twitter-tweet" data-theme="light" data-dnt="true">
                        <a href="${url}"></a>
                    </blockquote>
                </div>`;
            }
        }
        
        // Facebook
        if (url.includes('facebook.com')) {
            return `<div class="social-embed-item facebook-embed">
                <div class="fb-post" data-href="${url}" data-width="500"></div>
            </div>`;
        }
        
        // Instagram
        if (url.includes('instagram.com')) {
            const postId = extractInstagramPostId(url);
            if (postId) {
                return `<div class="social-embed-item instagram-embed">
                    <iframe src="https://www.instagram.com/p/${postId}/embed/" width="400" height="480" frameborder="0" scrolling="no" allowtransparency="true"></iframe>
                </div>`;
            }
        }
        
        // YouTube
        if (url.includes('youtube.com') || url.includes('youtu.be')) {
            const videoId = extractYouTubeVideoId(url);
            if (videoId) {
                return `<div class="social-embed-item youtube-embed">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>
                </div>`;
            }
        }
        
        // Generic link
        return `<div class="social-embed-item generic-embed">
            <a href="${url}" target="_blank" class="social-link-preview">
                <div class="link-preview">
                    <div class="link-url">${url}</div>
                    <div class="link-text">Click to view original post</div>
                </div>
            </a>
        </div>`;
    }
    
    // Helper functions for extracting IDs
    function extractTweetId(url) {
        const match = url.match(/twitter\.com\/\w+\/status\/(\d+)/) || url.match(/x\.com\/\w+\/status\/(\d+)/);
        return match ? match[1] : null;
    }
    
    function extractInstagramPostId(url) {
        const match = url.match(/instagram\.com\/p\/([a-zA-Z0-9_-]+)/);
        return match ? match[1] : null;
    }
    
    function extractYouTubeVideoId(url) {
        if (url.includes('youtu.be/')) {
            return url.split('youtu.be/')[1].split('?')[0];
        } else if (url.includes('youtube.com/watch?v=')) {
            return url.split('v=')[1].split('&')[0];
        }
        return null;
    }
    
    // Function to regenerate embeds from stored URLs
    function regenerateFrontendEmbeds() {
        console.log('regenerateFrontendEmbeds called');
        const embedAreas = $('.auto-embed-area[data-embed-urls]');
        console.log('Found embed areas:', embedAreas.length);
        
        embedAreas.each(function(index) {
            const embedArea = $(this);
            const urlsJson = embedArea.attr('data-embed-urls');
            
            if (urlsJson && urlsJson !== '[]') {
                try {
                    const urls = JSON.parse(urlsJson);
                    console.log('Processing URLs for area', index, ':', urls);
                    
                    if (urls && urls.length > 0) {
                        // Clear existing content
                        embedArea.empty();
                        
                        // Create embeds for each URL
                        urls.forEach((url, urlIndex) => {
                            const embedCode = createSocialEmbed(url);
                            if (embedCode) {
                                embedArea.append(embedCode);
                                console.log('Created embed for URL:', url);
                            }
                        });
                        
                        // Initialize platform-specific scripts
                        initializeSocialScripts(embedArea);
                    }
                } catch (e) {
                    console.error('Error parsing stored embed URLs:', e);
                }
            }
        });
    }
    
    // Function to initialize social media scripts
    function initializeSocialScripts(container) {
        // Twitter/X widgets
        if (container.find('.twitter-tweet').length > 0) {
            if (window.twttr && window.twttr.widgets) {
                window.twttr.widgets.load(container[0]);
            } else {
                // Load Twitter widgets script
                const script = document.createElement('script');
                script.src = 'https://platform.twitter.com/widgets.js';
                script.charset = 'utf-8';
                script.async = true;
                document.head.appendChild(script);
            }
        }
        
        // Facebook SDK
        if (container.find('.fb-post').length > 0) {
            if (!window.FB) {
                // Load Facebook SDK
                const script = document.createElement('script');
                script.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0';
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
            }
        }
    }
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        console.log('LiveBlog frontend initialized');
        
        // Regenerate embeds after a delay to ensure DOM is ready
        setTimeout(function() {
            regenerateFrontendEmbeds();
        }, 1000);
        
        // Also regenerate when window loads (for external scripts)
        $(window).on('load', function() {
            setTimeout(function() {
                regenerateFrontendEmbeds();
            }, 500);
        });
    });
    
})(jQuery);
</script>

<!-- Copy LiveBlog Entry Function -->
<script>
function copyLiveBlogEntry(element) {
    const entry = $(element).closest('.live-blog-entry');
    const title = entry.find('h3').text();
    const url = window.location.href;
    const textToCopy = title + ' - ' + url;
    
    navigator.clipboard.writeText(textToCopy).then(function() {
        // Show success message
        const notification = $('<div class="copy-notification">Link copied to clipboard!</div>');
        $('body').append(notification);
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>

<!-- Social Media Feed Widget Population -->
<script>
(function($) {
    'use strict';
    
    function populateSocialFeedWidget() {
        const socialFeedContainer = $('.social-feed-container');
        const embedAreas = $('.auto-embed-area[data-embed-urls]');
        
        if (embedAreas.length === 0) {
            socialFeedContainer.html('<p>No social media posts available.</p>');
            return;
        }
        
        let allEmbeds = [];
        
        embedAreas.each(function() {
            const embedArea = $(this);
            const urlsJson = embedArea.attr('data-embed-urls');
            
            if (urlsJson && urlsJson !== '[]') {
                try {
                    const urls = JSON.parse(urlsJson);
                    urls.forEach(url => {
                        allEmbeds.push({
                            url: url,
                            embed: createSocialEmbed(url)
                        });
                    });
                } catch (e) {
                    console.error('Error parsing URLs for social feed:', e);
                }
            }
        });
        
        if (allEmbeds.length > 0) {
            // Take the first 3 embeds for the widget
            const widgetEmbeds = allEmbeds.slice(0, 3);
            let widgetHtml = '';
            
            widgetEmbeds.forEach(embed => {
                widgetHtml += `<div class="widget-social-item">${embed.embed}</div>`;
            });
            
            socialFeedContainer.html(widgetHtml);
            
            // Initialize scripts for widget
            initializeSocialScripts(socialFeedContainer);
        } else {
            socialFeedContainer.html('<p>No social media posts available.</p>');
        }
    }
    
    // Initialize social feed widget
    $(document).ready(function() {
        setTimeout(function() {
            populateSocialFeedWidget();
        }, 1500);
    });
    
})(jQuery);
</script>

<?php include 'parts/shared/footer.php'; ?>