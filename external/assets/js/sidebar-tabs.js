/**
 * Sidebar Tabbed Content JavaScript
 * 
 * Handles tab switching and interactive features for the sidebar
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Initialize sidebar tabs
        initSidebarTabs();
        
        // Add hover effects and interactions
        initSidebarInteractions();
        
        // Initialize Related Posts Interactions
        initRelatedPosts();
        
    });
    
    /**
     * Initialize sidebar tab functionality
     */
    function initSidebarTabs() {
        const $tabLinks = $('.sidebar-post-widget .nav-pills .nav-link');
        const $tabPanes = $('.sidebar-post-widget .tab-pane');
        
        $tabLinks.on('click', function(e) {
            e.preventDefault();
            
            const $this = $(this);
            const targetId = $this.attr('href');
            
            // Remove active class from all tabs and panes
            $tabLinks.removeClass('active');
            $tabPanes.removeClass('show active');
            
            // Add active class to clicked tab and target pane
            $this.addClass('active');
            $(targetId).addClass('show active');
            
            // Add loading animation
            $(targetId).addClass('loading');
            setTimeout(function() {
                $(targetId).removeClass('loading');
            }, 300);
        });
    }
    
    /**
     * Initialize sidebar interactions and effects
     */
    function initSidebarInteractions() {
        
        // Add hover effects to post blocks
        $('.sidebar-post-widget .post-block').hover(
            function() {
                $(this).addClass('hovered');
            },
            function() {
                $(this).removeClass('hovered');
            }
        );
        
        // Add click tracking for analytics
        $('.sidebar-post-widget .axil-post-title a').on('click', function() {
            const postUrl = $(this).attr('href');
            const postTitle = $(this).text();
            
            // Track sidebar post clicks (you can integrate with Google Analytics here)
            console.log('Sidebar post clicked:', postTitle, postUrl);
            
            // Optional: Send to analytics service
            // trackSidebarPostClick(postTitle, postUrl);
        });
        
        // Add lazy loading for images
        $('.sidebar-post-widget img').each(function() {
            const $img = $(this);
            const src = $img.attr('src');
            
            if (src && !$img.hasClass('lazy-loaded')) {
                $img.addClass('lazy-loading');
                
                // Simple lazy loading
                const img = new Image();
                img.onload = function() {
                    $img.removeClass('lazy-loading').addClass('lazy-loaded');
                };
                img.src = src;
            }
        });
        
        // Add smooth scrolling for category links
        $('.sidebar-post-widget .post-cat').on('click', function(e) {
            const href = $(this).attr('href');
            
            // If it's a category link, add smooth scroll
            if (href && href.includes('category')) {
                e.preventDefault();
                
                $('html, body').animate({
                    scrollTop: $(href).offset().top - 100
                }, 800);
            }
        });
    }
    
    /**
     * Track sidebar post clicks (optional analytics integration)
     */
    function trackSidebarPostClick(postTitle, postUrl) {
        // You can integrate with Google Analytics, Facebook Pixel, or other analytics services here
        
        // Example for Google Analytics 4
        if (typeof gtag !== 'undefined') {
            gtag('event', 'sidebar_post_click', {
                'event_category': 'sidebar',
                'event_label': postTitle,
                'value': 1
            });
        }
        
        // Example for Facebook Pixel
        if (typeof fbq !== 'undefined') {
            fbq('track', 'ViewContent', {
                content_name: postTitle,
                content_category: 'sidebar'
            });
        }
    }
    
    /**
     * Refresh sidebar content (for AJAX updates)
     */
    function refreshSidebarContent() {
        const $sidebar = $('.sidebar-post-widget');
        
        if ($sidebar.length) {
            $sidebar.addClass('refreshing');
            
            // You can add AJAX call here to refresh content
            // For now, just remove the refreshing class
            setTimeout(function() {
                $sidebar.removeClass('refreshing');
            }, 500);
        }
    }
    
    /**
     * Show trending indicator animation
     */
    function showTrendingAnimation() {
        $('.post-stats .fa-fire').each(function() {
            const $fire = $(this);
            
            // Add pulsing animation
            $fire.addClass('trending-pulse');
            
            // Remove animation after 3 seconds
            setTimeout(function() {
                $fire.removeClass('trending-pulse');
            }, 3000);
        });
    }
    
    /**
     * Update post stats in real-time (if needed)
     */
    function updatePostStats() {
        $('.post-stats').each(function() {
            const $stats = $(this);
            const $views = $stats.find('.view-count');
            const $comments = $stats.find('.comment-count');
            
            // You can add AJAX calls here to update stats in real-time
            // For now, this is a placeholder
        });
    }
    
    /**
     * Initialize Related Posts Interactions
     */
    function initRelatedPosts() {
        // Add lazy loading for related post images (both sidebar and inline)
        const relatedPostImages = document.querySelectorAll('.related-post-card img, .related-posts-inline img');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.style.opacity = '1';
                        img.style.transform = 'scale(1)';
                        observer.unobserve(img);
                    }
                });
            });
            
            relatedPostImages.forEach(img => {
                img.style.opacity = '0';
                img.style.transform = 'scale(0.95)';
                img.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                imageObserver.observe(img);
            });
        }
        
        // Add click tracking for related posts (both sidebar and inline)
        const relatedPostLinks = document.querySelectorAll('.related-post-card a, .related-posts-inline a');
        relatedPostLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const postTitle = this.closest('.related-post-card, .post-block').querySelector('.post-title a, .axil-post-title a').textContent;
                trackRelatedPostClick(postTitle, this.href);
            });
        });
        
        // Add hover effects for better UX (both sidebar and inline)
        const relatedPostCards = document.querySelectorAll('.related-post-card, .related-posts-inline .post-block.small-block');
        relatedPostCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    }
    
    /**
     * Track Related Post Click
     * 
     * @param string postTitle Title of the clicked post
     * @param string postUrl URL of the clicked post
     */
    function trackRelatedPostClick(postTitle, postUrl) {
        // Placeholder for analytics tracking
        console.log('Related post clicked:', postTitle, postUrl);
        
        // You can integrate with Google Analytics, Facebook Pixel, etc.
        if (typeof gtag !== 'undefined') {
            gtag('event', 'click', {
                'event_category': 'Related Posts',
                'event_label': postTitle,
                'value': 1
            });
        }
    }
    
    // Expose functions globally for external use
    window.SidebarTabs = {
        refresh: refreshSidebarContent,
        showTrending: showTrendingAnimation,
        updateStats: updatePostStats
    };
    
})(jQuery);
