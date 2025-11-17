<?php 
// Check if this is a single Happening post
if (get_post_type() === 'happening') {
    include 'parts/shared/html-header.php'; 
    include 'parts/shared/header.php'; 
} else {
    // If not a Happening post, redirect to 404 or show error
    wp_die('This template is for Happening posts only.');
}
?>

<section class="banner section-gap">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="post-title-wrapper">
                    <div class="d-flex align-items-center flex-nowrap mb-3">
                        <div class="post-cat-group flex-shrink-0">
                            <?php
                            $categories = get_filtered_categories();
                            if (!empty($categories)) {
                                foreach ($categories as $category) {
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-red-one">' . esc_html($category->name) . '</a>';
                                }
                            }
                            ?>
                        </div>
                        <div class="post-time ms-3">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo get_the_date(); ?>
                        </div>
                    </div>

                    <h2 class="axil-post-title hover-line"><?php echo get_the_title(); ?></h2>
                    <div class="post-metas banner-post-metas">
                        <ul class="list-inline">
                            <li><i class="dot">.</i><?php echo get_the_date(); ?></li>
                            <li><a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><i class="feather icon-user"></i><?php echo get_the_author(); ?></a></li>
                            <?php if (has_tag()): ?>
                                <li><i class="feather icon-tag"></i><?php the_tags('', ', '); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                <?php else: ?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="" class="img-fluid">
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Happening Content -->
<div class="happening-content-wrapper section-gap">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="happening-content">
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                    
                    <!-- Social Share -->
                    <div class="social-share-section mt-4">
                        <h4>Share this happening:</h4>
                        <ul class="social-share social-share__with-bg social-share__horizontal">
                            <li>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>"
                                    target="_blank"
                                    title="Share on Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>"
                                    target="_blank"
                                    title="Share on X (Twitter)">
                                    <i class="fab fa-x-twitter"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' - ' . get_permalink()); ?>"
                                    target="_blank"
                                    title="Share on WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="copyHappeningLink(); return false;" title="Copy Link">
                                    <i class="fas fa-link"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Comments -->
                    <?php if (comments_open() || get_comments_number()): ?>
                        <div class="comments-section mt-5">
                            <?php comments_template(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="happening-sidebar">
                    <!-- Related Happenings -->
                    <div class="related-happenings">
                        <h4 class="sidebar-title">
                            <i class="fas fa-calendar-alt"></i> Related Happenings
                        </h4>
                        <?php
                        $related_happenings = new WP_Query(array(
                            'post_type' => 'happening',
                            'posts_per_page' => 5,
                            'post__not_in' => array(get_the_ID()),
                            'category__in' => wp_get_post_categories(get_the_ID()),
                            'orderby' => 'date',
                            'order' => 'DESC'
                        ));
                        
                        if ($related_happenings->have_posts()):
                        ?>
                            <div class="related-posts-list">
                                <?php while ($related_happenings->have_posts()): $related_happenings->the_post(); ?>
                                    <div class="related-post-item">
                                        <div class="post-thumbnail">
                                            <?php if (has_post_thumbnail()): ?>
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail('thumbnail', array('class' => 'img-fluid')); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="post-content">
                                            <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                                            <div class="post-meta">
                                                <span class="post-date"><?php echo get_the_date(); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php 
                        endif;
                        wp_reset_postdata();
                        ?>
                    </div>
                    
                    <!-- Categories -->
                    <div class="happening-categories mt-4">
                        <h4 class="sidebar-title">
                            <i class="fas fa-tags"></i> Categories
                        </h4>
                        <ul class="category-list">
                            <?php
                            $categories = get_categories(array(
                                'taxonomy' => 'category',
                                'hide_empty' => true,
                            ));
                            
                            foreach ($categories as $category) {
                                echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . ' <span class="count">(' . $category->count . ')</span></a></li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'parts/shared/footer.php'; ?>

<script>
    // Copy functionality for happening link
    function copyHappeningLink() {
        const url = window.location.href;
        const title = '<?php echo esc_js(get_the_title()); ?>';
        const contentToCopy = `${title}\n\n${url}`;

        navigator.clipboard.writeText(contentToCopy).then(() => {
            // Show success feedback
            const copyButton = document.querySelector('a[onclick*="copyHappeningLink"]');
            if (copyButton) {
                const originalContent = copyButton.innerHTML;
                copyButton.innerHTML = '<i class="fas fa-check"></i> Copied!';
                copyButton.style.color = '#28a745';
                
                setTimeout(() => {
                    copyButton.innerHTML = originalContent;
                    copyButton.style.color = '';
                }, 2000);
            }
        }).catch(err => {
            console.error('Failed to copy: ', err);
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = contentToCopy;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            // Show success feedback
            const copyButton = document.querySelector('a[onclick*="copyHappeningLink"]');
            if (copyButton) {
                const originalContent = copyButton.innerHTML;
                copyButton.innerHTML = '<i class="fas fa-check"></i> Copied!';
                copyButton.style.color = '#28a745';
                
                setTimeout(() => {
                    copyButton.innerHTML = originalContent;
                    copyButton.style.color = '';
                }, 2000);
            }
        });
    }
</script>

<?php include 'parts/shared/html-footer.php'; ?>