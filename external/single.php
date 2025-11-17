<?php

/**
 * The Template for displaying all single posts
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 */
?>
<?php Starkers_Utilities::get_template_parts(array('parts/shared/html-header', 'parts/shared/header-white')); ?>

<main>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article <?php post_class('article article--news article--p0 post-sb-page'); ?>>
            <div class="post-sb-page__main-pic post-sb-page__main-pic--inner">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail(); ?>
                    </div>
                <?php endif; ?>
                <div class="post-sb-page__title">
                    <?php $categories = get_filtered_categories();
                    if (!empty($categories)) : ?>
                        <a class="post-sb-page__tag" href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>"><?php echo esc_html($categories[0]->name); ?></a>
                    <?php endif; ?>
                    <h1 class="post-sb-page__heading"><?php the_title(); ?></h1>
                </div>
                <div class="opacity-medium bg-extra-dark-gray"></div>
            </div>
            <div class="post-sb-page__wrapper">
                <!-- Post Content -->
                <section class="post-sb post-sb--full">
                    <div class="article__main article__main--width-small container">
                        <div class="post-sb__wrapper">
                            <div class="post-sb__content">
                                <div class="post-sb__date">
                                    <a href="#" class="author"><?php the_author(); ?></a> on <?php the_date(); ?>
                                </div>
                                <div class="post-sb__title"><?php the_title(); ?></div>
                                <div class="post-sb__text"><?php the_content(); ?></div>
                                <!-- Add more dynamic content here -->
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </article>
    <?php endwhile;
    endif; ?>
</main>

<?php Starkers_Utilities::get_template_parts(array('parts/shared/footer', 'parts/shared/html-footer')); ?>