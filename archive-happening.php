<?php
/**
 * The template for displaying happening archives
 */

include 'parts/shared/html-header.php'; 
include 'parts/shared/header.php'; 
?>

<section class="banner section-gap">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="archive-header">
                    <h1 class="archive-title">
                        <i class="fas fa-calendar-alt"></i> 
                        <?php 
                        if (is_category()) {
                            single_cat_title('Happenings in ');
                        } elseif (is_tag()) {
                            single_tag_title('Happenings tagged with ');
                        } else {
                            echo 'All Happenings';
                        }
                        ?>
                    </h1>
                    <?php if (get_the_archive_description()): ?>
                        <div class="archive-description">
                            <?php the_archive_description(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="happening-archive-wrapper section-gap">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <?php if (have_posts()): ?>
                    <div class="happening-grid">
                        <?php while (have_posts()): the_post(); ?>
                            <article class="happening-item">
                                <div class="happening-thumbnail">
                                    <?php if (has_post_thumbnail()): ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="" class="img-fluid">
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="happening-content">
                                    <div class="happening-meta">
                                        <span class="happening-date">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo get_the_date(); ?>
                                        </span>
                                        <?php if (has_category()): ?>
                                            <span class="happening-category">
                                                <i class="fas fa-tag"></i>
                                                <?php the_category(', '); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h2 class="happening-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    
                                    <div class="happening-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                    
                                    <div class="happening-author">
                                        <i class="fas fa-user"></i>
                                        By <?php the_author(); ?>
                                    </div>
                                    
                                    <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                        Read More <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="pagination-wrapper">
                        <?php
                        the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => '<i class="fas fa-chevron-left"></i> Previous',
                            'next_text' => 'Next <i class="fas fa-chevron-right"></i>',
                        ));
                        ?>
                    </div>
                    
                <?php else: ?>
                    <div class="no-happenings">
                        <h2>No Happenings Found</h2>
                        <p>Sorry, no happenings match your criteria. Please try again with different search terms or browse our categories.</p>
                        <a href="<?php echo home_url(); ?>" class="btn btn-primary">Back to Home</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="happening-sidebar">
                    <!-- Search -->
                    <div class="sidebar-widget">
                        <h4 class="widget-title">
                            <i class="fas fa-search"></i> Search Happenings
                        </h4>
                        <form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
                            <input type="hidden" name="post_type" value="happening">
                            <div class="input-group">
                                <input type="search" class="form-control" placeholder="Search happenings..." value="<?php echo get_search_query(); ?>" name="s">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Categories -->
                    <div class="sidebar-widget">
                        <h4 class="widget-title">
                            <i class="fas fa-tags"></i> Categories
                        </h4>
                        <ul class="category-list">
                            <?php
                            $categories = get_categories(array(
                                'taxonomy' => 'category',
                                'hide_empty' => true,
                            ));
                            
                            foreach ($categories as $category) {
                                $category_link = add_query_arg('post_type', 'happening', get_category_link($category->term_id));
                                echo '<li><a href="' . esc_url($category_link) . '">' . esc_html($category->name) . ' <span class="count">(' . $category->count . ')</span></a></li>';
                            }
                            ?>
                        </ul>
                    </div>
                    
                    <!-- Recent Happenings -->
                    <div class="sidebar-widget">
                        <h4 class="widget-title">
                            <i class="fas fa-clock"></i> Recent Happenings
                        </h4>
                        <?php
                        $recent_happenings = new WP_Query(array(
                            'post_type' => 'happening',
                            'posts_per_page' => 5,
                            'orderby' => 'date',
                            'order' => 'DESC'
                        ));
                        
                        if ($recent_happenings->have_posts()):
                        ?>
                            <div class="recent-happenings-list">
                                <?php while ($recent_happenings->have_posts()): $recent_happenings->the_post(); ?>
                                    <div class="recent-happening-item">
                                        <div class="recent-thumbnail">
                                            <?php if (has_post_thumbnail()): ?>
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail('thumbnail', array('class' => 'img-fluid')); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="recent-content">
                                            <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                                            <div class="recent-meta">
                                                <span class="recent-date"><?php echo get_the_date(); ?></span>
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
                    
                    <!-- Tags -->
                    <div class="sidebar-widget">
                        <h4 class="widget-title">
                            <i class="fas fa-tags"></i> Popular Tags
                        </h4>
                        <div class="tag-cloud">
                            <?php
                            $tags = get_tags(array(
                                'number' => 20,
                                'orderby' => 'count',
                                'order' => 'DESC'
                            ));
                            
                            if ($tags) {
                                foreach ($tags as $tag) {
                                    $tag_link = add_query_arg('post_type', 'happening', get_tag_link($tag->term_id));
                                    echo '<a href="' . esc_url($tag_link) . '" class="tag-link">' . esc_html($tag->name) . '</a>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'parts/shared/footer.php'; ?>
<?php include 'parts/shared/html-footer.php'; ?> 