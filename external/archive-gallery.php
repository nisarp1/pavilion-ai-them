<?php include 'parts/shared/html-header.php'; ?>
<?php include 'parts/shared/header.php'; ?>

<!-- Gallery Archive Section -->
<section class="gallery-archive p-b-xs-30 post-section section-gap">
    <div class="container">
        <div class="section-title m-b-xs-30">
            <h1 class="axil-title">Gallery Archives</h1>
        </div>

        <div class="row">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                <div class="col-lg-4 col-md-6 col-sm-6 m-b-xs-30">
                    <a href="<?php the_permalink(); ?>" class="block-link">
                        <div class="axil-img-container flex-height-container gallery-container__type-2">
                            <div class="gallery-image-wrapper d-block h-100">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', array('class' => 'w-100', 'alt' => get_the_title())); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>" class="w-100">
                                <?php endif; ?>
                                <div class="grad-overlay grad-overlay__transparent"></div>
                                <div class="gallery-icon gallery-play-btn">
                                    <i class="fas fa-expand"></i>
                                </div>
                            </div>
                            <div class="gallery-overlay">
                                <div class="post-cat-group badge-on-image">
                                    <span class="post-cat cat-btn btn-big bg-primary-color">GALLERY</span>
                                </div>
                            </div>
                            <div class="media post-block grad-overlay__transparent position-absolute">
                                <div class="media-body media-body__big">
                                    <div class="axil-media-bottom mt-auto">
                                        <h3 class="axil-post-title hover-line"><?php the_title(); ?></h3>
                                        <div class="gallery-meta">
                                            <div class="post-time">
                                                <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of .media-body -->
                            </div>
                            <!-- End of .post-block -->
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>

                <!-- Pagination -->
                <div class="col-12">
                    <div class="pagination-wrapper">
                        <?php
                        the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => '<i class="fas fa-chevron-left"></i> Previous',
                            'next_text' => 'Next <i class="fas fa-chevron-right"></i>',
                        ));
                        ?>
                    </div>
                </div>

            <?php else : ?>
                <div class="col-12">
                    <div class="no-galleries-found">
                        <h3>No galleries found</h3>
                        <p>Sorry, no galleries match your criteria.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'parts/shared/footer.php'; ?>
<?php include 'parts/shared/html-footer.php'; ?> 