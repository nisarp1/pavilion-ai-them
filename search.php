<?php
/**
 * Search results page
 * 
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 */

// Debug information
echo "<!-- Search template loaded -->";
echo "<!-- Search query: " . get_search_query() . " -->";
echo "<!-- Found posts: " . $wp_query->found_posts . " -->";
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<script>
document.body.classList.add('search-page');
</script>

<main class="search-page-main">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="search-results-wrapper">
                    <div class="search-header">
                        <h1 class="search-title">Search Results</h1>
                        <p class="search-query">Results for: <strong>"<?php echo get_search_query(); ?>"</strong></p>
                    </div>

                    <?php if ( have_posts() ): ?>
                        <div class="search-results">
                            <?php while ( have_posts() ) : the_post(); ?>
                                <article class="search-result-item">
                                    <div class="search-result-content">
                                        <h2 class="search-result-title">
                                            <a href="<?php the_permalink(); ?>" title="Permalink to <?php the_title(); ?>" rel="bookmark">
                                                <?php the_title(); ?>
                                            </a>
                                        </h2>
                                        
                                        <div class="search-result-meta">
                                            <span class="search-result-date">
                                                <i class="far fa-clock"></i>
                                                <?php echo get_the_date(); ?>
                                            </span>
                                            <span class="search-result-category">
                                                <?php
                                                $categories = get_filtered_categories();
                                                if (!empty($categories)) {
                                                    echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        
                                        <div class="search-result-excerpt">
                                            <?php 
                                            $excerpt = get_the_excerpt();
                                            if (empty($excerpt)) {
                                                $excerpt = wp_trim_words(get_the_content(), 30, '...');
                                            }
                                            echo $excerpt;
                                            ?>
                                        </div>
                                        
                                        <div class="search-result-footer">
                                            <a href="<?php the_permalink(); ?>" class="read-more-btn">Read More</a>
                                        </div>
                                    </div>
                                    
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="search-result-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </article>
                            <?php endwhile; ?>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="search-pagination">
                            <?php
                            the_posts_pagination(array(
                                'mid_size' => 2,
                                'prev_text' => __('Previous', 'textdomain'),
                                'next_text' => __('Next', 'textdomain'),
                            ));
                            ?>
                        </div>
                        
                    <?php else: ?>
                        <div class="no-search-results">
                            <div class="no-results-content">
                                <h2>No results found</h2>
                                <p>Sorry, no results were found for "<strong><?php echo get_search_query(); ?></strong>". Please try again with different keywords.</p>
                                
                                <div class="search-suggestions">
                                    <h3>Search Suggestions:</h3>
                                    <ul>
                                        <li>Check your spelling</li>
                                        <li>Try different keywords</li>
                                        <li>Try more general keywords</li>
                                        <li>Try fewer keywords</li>
                                    </ul>
                                </div>
                                
                                <div class="search-again">
                                    <form action="<?php echo home_url('/'); ?>" method="get" class="search-again-form">
                                        <div class="form-group">
                                            <input type="text" name="s" placeholder="Try searching again..." value="<?php echo get_search_query(); ?>">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <?php include 'parts/shared/sidebar.php'; ?>
            </div>
        </div>
    </div>
</main>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>