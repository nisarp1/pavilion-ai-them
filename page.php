<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/sub-header' ) ); ?>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );?>
			<main id="main" role="main">
			    <!-- page banner  -->
				<header class="page-banner full-width-banner pt-110">
					<div class="stretch">
						<img alt="image description" src="<?php echo $feat_image; ?>">
					</div>
					<div class="container">
						<div class="row">
							<div class="col-xs-12">
								<div class="holder">
									<h1 class="heading text-capitalize"><?php the_title(); ?></h1>
									<p></p>
								</div>

							</div>
						</div>
					</div>
				</header>
				<div class="contact-block container">
                    <!-- contact message -->
					<div class="row contact-message">
				<?php the_content(); ?>
				</div>
				</div>
				
		</main>
<?php endwhile; ?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>