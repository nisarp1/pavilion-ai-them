<!-- Team India News Widget -->
<div class="team-india-widget bg-grey-light-three m-b-xs-20">
    <div class="section-title m-b-xs-10">
        <a href="<?php echo get_safe_category_link('team-india'); ?>" class="d-block">
            <h2 class="axil-title">Team India Updates</h2>
        </a>
    </div>
    <div class="team-india-news-list">
        <?php
        // Get Team India posts
        $team_india_category = get_category_by_slug('team-india');
        // Debug output (remove in production)
        // echo "<!-- DEBUG: Category found: " . ($team_india_category ? 'YES' : 'NO') . " -->";
        if ($team_india_category) {
            $team_india_posts = new WP_Query(array(
                'category_name' => 'team-india',
                'posts_per_page' => 5,
                'status' => 'published'
            ));
            // Debug output (remove in production)
            // echo "<!-- DEBUG: Posts found: " . $team_india_posts->post_count . " -->";

            if ($team_india_posts->have_posts()) :
                while ($team_india_posts->have_posts()) : $team_india_posts->the_post();
        ?>
        <div class="media post-block small-block">
            <div class="post-image-wrapper">
                <a href="<?php echo esc_url(get_permalink()); ?>" class="align-self-center">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                    <?php else : ?>
                        <img class="img-fluid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>">
                    <?php endif; ?>
                    <?php echo get_video_play_button(); ?>
                </a>
            </div>
            <div class="media-body">
                <h4 class="axil-post-title small-card-title">
                    <a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a>
                </h4>
                <div class="d-flex align-items-baseline">
                    <div class="post-cat-group">
                        <?php
                        $categories = get_filtered_categories();
                        if (!empty($categories)) {
                            $category_links = array();
                            foreach ($categories as $category) {
                                if ($category->slug !== 'editors-pick' && $category->slug !== 'featured' && $category->slug !== 'team-india') {
                                    $category_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat">' . esc_html($category->name) . '</a>';
                                }
                            }
                            if (!empty($category_links)) {
                                echo implode(', ', array_slice($category_links, 0, 1));
                            }
                        }
                        ?>
                    </div>
                    <div class="post-time">
                        <i class="far fa-clock"></i><?php echo meks_time_ago(); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of .post-block -->
        <?php
                endwhile;
                wp_reset_postdata();
            else :
        ?>
        <div class="no-posts-message">
            <p>No Team India news available at the moment.</p>
        </div>
        <?php
            endif;
        } else {
        ?>
        <div class="no-posts-message">
            <p>Team India category not found.</p>
        </div>
        <?php
        }
        ?>
    </div>
    <div class="widget-footer">
        <a href="<?php echo get_safe_category_link('team-india'); ?>" class="btn btn-link btn-small">View All Team India News →</a>
    </div>
</div>
<!-- End of Team India News Widget -->

