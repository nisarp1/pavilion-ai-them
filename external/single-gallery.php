<?php include 'parts/shared/html-header.php'; ?>
<?php include 'parts/shared/header.php'; ?>

<!-- Gallery Single Post Section -->
<section class="gallery-single-post p-b-xs-30 post-section section-gap">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <main class="axil-content">
                    <?php while (have_posts()) : the_post(); ?>
                    <article class="gallery-post">
                        <!-- Gallery Title -->
                        <header class="gallery-header m-b-xs-30">
                            <h1 class="gallery-title"><?php the_title(); ?></h1>
                            <div class="gallery-meta">
                                <div class="d-flex align-items-center flex-nowrap">
                                    <div class="post-cat-group flex-shrink-0">
                                        <span class="post-cat cat-btn bg-primary-color">GALLERY</span>
                                    </div>
                                    <div class="post-time ms-3 flex-shrink-0">
                                        <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                    </div>
                                </div>
                            </div>
                        </header>

                        <!-- Gallery Featured Image -->
                        <?php if (has_post_thumbnail()) : ?>
                        <div class="gallery-featured-image m-b-xs-30">
                            <?php the_post_thumbnail('large', array('class' => 'img-fluid w-100', 'alt' => get_the_title())); ?>
                        </div>
                        <?php endif; ?>

                        <!-- Gallery Content -->
                        <div class="gallery-content">
                            <?php the_content(); ?>
                        </div>

                        <!-- Gallery Description -->
                        <?php if (get_the_excerpt()) : ?>
                        <div class="gallery-excerpt m-b-xs-30">
                            <p class="excerpt-text"><?php echo get_the_excerpt(); ?></p>
                        </div>
                        <?php endif; ?>

                        <!-- Gallery Images -->
                        <?php
                        $gallery_images = get_gallery_images();
                        if (!empty($gallery_images)) :
                        ?>
                        <div class="gallery-images-grid m-b-xs-30">
                            <h3 class="section-title">Gallery Images</h3>
                            <div class="row">
                                <?php foreach ($gallery_images as $index => $image_data) : ?>
                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-xs-20">
                                    <div class="gallery-image-item">
                                        <a href="javascript:void(0);" class="gallery-lightbox" data-gallery="gallery-<?php echo get_the_ID(); ?>">
                                            <?php echo wp_get_attachment_image($image_data['image_id'], 'medium', false, array('class' => 'img-fluid', 'alt' => $image_data['caption'] ?: get_the_title())); ?>
                                        </a>
                                        <?php if (!empty($image_data['caption'])) : ?>
                                        <div class="gallery-caption">
                                            <p><?php echo esc_html($image_data['caption']); ?></p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Hidden Gallery Slides for Modal -->
                        <div style="display: none;">
                            <?php foreach ($gallery_images as $index => $image_data) : ?>
                            <div class="gallery-slide" data-gallery="gallery-<?php echo get_the_ID(); ?>">
                                <img src="<?php echo wp_get_attachment_image_url($image_data['image_id'], 'large'); ?>" alt="<?php echo esc_attr($image_data['caption'] ?: get_the_title()); ?>">
                                <?php if (!empty($image_data['caption'])) : ?>
                                <div class="gallery-slide-caption">
                                    <h3><?php echo esc_html($image_data['caption']); ?></h3>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Gallery Navigation -->
                        <nav class="gallery-navigation m-b-xs-30">
                            <div class="d-flex justify-content-between">
                                <div class="nav-previous">
                                    <?php previous_post_link('%link', '<i class="fas fa-chevron-left"></i> Previous Gallery'); ?>
                                </div>
                                <div class="nav-next">
                                    <?php next_post_link('%link', 'Next Gallery <i class="fas fa-chevron-right"></i>'); ?>
                                </div>
                            </div>
                        </nav>

                        <!-- Related Galleries -->
                        <div class="related-galleries m-b-xs-30">
                            <h3 class="section-title">Related Galleries</h3>
                            <div class="row">
                                <?php
                                $related_galleries = new WP_Query(array(
                                    'post_type' => 'gallery',
                                    'posts_per_page' => 3,
                                    'post__not_in' => array(get_the_ID()),
                                    'post_status' => 'publish',
                                    'orderby' => 'date',
                                    'order' => 'DESC'
                                ));

                                if ($related_galleries->have_posts()) :
                                    while ($related_galleries->have_posts()) : $related_galleries->the_post();
                                ?>
                                <div class="col-lg-4 col-md-6">
                                    <a href="<?php the_permalink(); ?>" class="block-link">
                                        <div class="gallery-thumbnail">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                                            <?php else : ?>
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title_attribute(); ?>" class="img-fluid">
                                            <?php endif; ?>
                                            <div class="gallery-overlay">
                                                <div class="gallery-icon">
                                                    <i class="fas fa-images"></i>
                                                </div>
                                            </div>
                                            <div class="gallery-info">
                                                <h4 class="gallery-title"><?php the_title(); ?></h4>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <?php
                                    endwhile;
                                    wp_reset_postdata();
                                endif;
                                ?>
                            </div>
                        </div>

                    </article>
                    <?php endwhile; ?>
                </main>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <aside class="post-sidebar">
                    <!-- Gold Exchange Rate Widget -->
                    <div class="exchange-widget bg-grey-light-three m-b-xs-20">
                        <div class="section-title m-b-xs-10">
                            <a href="#" class="d-block">
                                <h2 class="axil-title">Gold Exchange Rates</h2>
                            </a>
                            <div class="last-updated">Last updated: <span id="gold-last-updated">--</span></div>
                        </div>
                        <div class="exchange-rates">
                            <div class="rate-item">
                                <div class="currency-info">
                                    <span class="currency-name">Gold (24K)</span>
                                </div>
                                <div class="rate-values">
                                    <span class="rate-value" id="gold-aed">--</span>
                                    <span class="currency-symbol">AED</span>
                                    <span class="rate-value" id="gold-inr">--</span>
                                    <span class="currency-symbol">INR</span>
                                </div>
                            </div>
                        </div>
                        <div class="widget-footer">
                            <small class="text-muted">Rates per gram</small>
                        </div>
                    </div>

                    <!-- Currency Exchange Rate Widget -->
                    <div class="exchange-widget bg-grey-light-three m-b-xs-40">
                        <div class="section-title m-b-xs-10">
                            <a href="#" class="d-block">
                                <h2 class="axil-title">Currency Exchange Rates</h2>
                            </a>
                            <div class="last-updated">Last updated: <span id="currency-last-updated">--</span></div>
                        </div>
                        <div class="exchange-rates">
                            <div class="rate-item">
                                <div class="currency-info">
                                    <span class="currency-name">USD/AED</span>
                                </div>
                                <div class="rate-values">
                                    <span class="rate-value" id="usd-aed">--</span>
                                </div>
                            </div>
                            <div class="rate-item">
                                <div class="currency-info">
                                    <span class="currency-name">AED/INR</span>
                                </div>
                                <div class="rate-values">
                                    <span class="rate-value" id="aed-inr">--</span>
                                </div>
                            </div>
                        </div>
                        <div class="widget-footer">
                            <small class="text-muted">Live rates from central banks</small>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>

<?php include 'parts/shared/footer.php'; ?>
<?php include 'parts/shared/html-footer.php'; ?> 