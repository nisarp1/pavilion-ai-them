<?php
/*
 *Template Name:Home
 */

// Enable error reporting temporarily to debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Load core functions
require_once __DIR__ . '/core.php';

if (!function_exists('pavilion_resolve_gallery_image')) {
    function pavilion_resolve_gallery_image($path)
    {
        if (empty($path)) {
            return get_stylesheet_directory_uri() . '/assets/images/placeholder.png';
        }

        if (is_string($path) && strpos($path, 'http') === 0) {
            return $path;
        }

        if (is_numeric($path)) {
            $media_url = pavilion_get_media_url($path);
            if (!empty($media_url)) {
                return $media_url;
            }
        }

        if (is_string($path)) {
            $normalized = ltrim($path, '/');
            if (strpos($normalized, 'media/') === 0) {
                $api_base = defined('PAVILION_API_BASE_URL')
                    ? rtrim(PAVILION_API_BASE_URL, '/api')
                    : get_stylesheet_directory_uri();
                return $api_base . '/' . $normalized;
            }

            return get_stylesheet_directory_uri() . '/' . $normalized;
        }

        return get_stylesheet_directory_uri() . '/assets/images/placeholder.png';
    }
}
?>
<!-- mainslider -->

<?php include 'parts/shared/html-header.php'; ?>
<?php include 'parts/shared/header.php'; ?>

<section class="webstories-section p-b-xs-30 post-section section-gap">
    <div class="container">
        <div class="section-title m-b-xs-10">
            <a href="#" class="d-block">
                <h2 class="axil-title">WEB STORIES</h2>
            </a>
        </div>
        <?php
        $featured_webstories = get_gallery_posts(6, array(
            'latest' => false,
            'hours' => null,
            'include_slides' => true,
        ));

        $recent_webstories = get_gallery_posts(24, array(
            'latest' => true,
            'hours' => 24,
            'include_slides' => true,
        ));

        $sort_by_published = function ($a, $b) {
            $get_timestamp = function ($story) {
                if (empty($story) || !is_array($story)) {
                    return 0;
                }

                $published = $story['published_at'] ?? ($story['date'] ?? ($story['created_at'] ?? ''));
                $timestamp = $published ? strtotime($published) : 0;
                return $timestamp ?: 0;
            };

            $time_a = $get_timestamp($a);
            $time_b = $get_timestamp($b);

            if ($time_a === $time_b) {
                return 0;
            }

            return ($time_a < $time_b) ? 1 : -1;
        };

        if (!empty($featured_webstories)) {
            usort($featured_webstories, $sort_by_published);
        }

        if (!empty($recent_webstories)) {
            usort($recent_webstories, $sort_by_published);
        }

        $webstory_posts = array_slice($featured_webstories, 0, 6);

        if (empty($webstory_posts) && !empty($recent_webstories)) {
            $webstory_posts = array_slice($recent_webstories, 0, 6);
        }

        $recent_story_ids = array();
        foreach ($recent_webstories as $recent_story) {
            if (!empty($recent_story['id'])) {
                $recent_story_ids[$recent_story['id']] = true;
            }
        }

        $slide_source_posts = $recent_webstories;

        if (!empty($webstory_posts)) {
            foreach ($webstory_posts as $story_candidate) {
                $story_candidate_id = $story_candidate['id'] ?? null;
                if ($story_candidate_id && !isset($recent_story_ids[$story_candidate_id])) {
                    $slide_source_posts[] = $story_candidate;
                }
            }
        }

        $slide_source_posts = array_values($slide_source_posts);

        $featured_ids = array();
        foreach ($webstory_posts as $story_candidate) {
            if (!empty($story_candidate['id'])) {
                $featured_ids[] = $story_candidate['id'];
            }
        }

        $additional_recent_cards = array();
        if (!empty($recent_webstories)) {
            foreach ($recent_webstories as $recent_story) {
                $story_id = $recent_story['id'] ?? null;
                if ($story_id && !in_array($story_id, $featured_ids, true)) {
                    $additional_recent_cards[] = $recent_story;
                }
            }
        }

        if (!empty($webstory_posts)):
            ?>
            <div class="webstories-track" role="list">
                <?php foreach ($webstory_posts as $index => $webstory_post):
                    $story_id = $webstory_post['id'] ?? ($index + 1);
                    $story_key = 'webstory-' . $story_id;
                    $story_title = $webstory_post['title'] ?? 'Web Story';
                    $story_featured = $webstory_post['featured_image'] ?? ($webstory_post['featured_image_url'] ?? '');
                    $story_image_url = pavilion_resolve_gallery_image($story_featured);
                    $story_published = $webstory_post['published_at'] ?? ($webstory_post['date'] ?? ($webstory_post['created_at'] ?? ''));
                    ?>
                    <article class="webstory-card" role="listitem" data-story="<?php echo esc_attr($story_key); ?>"
                        data-cover="<?php echo esc_url($story_image_url); ?>" data-title="<?php echo esc_attr($story_title); ?>"
                        data-published="<?php echo esc_attr($story_published); ?>">
                        <button type="button" class="webstory-trigger" aria-label="<?php echo esc_attr($story_title); ?>"
                            data-story="<?php echo esc_attr($story_key); ?>"
                            data-cover="<?php echo esc_url($story_image_url); ?>"
                            data-title="<?php echo esc_attr($story_title); ?>"
                            data-published="<?php echo esc_attr($story_published); ?>">
                            <span class="webstory-thumb">
                                <span class="webstory-thumb-fill"
                                    style="background-image: url('<?php echo esc_url($story_image_url); ?>');"
                                    aria-hidden="true"></span>
                                <span class="webstory-chip">Web Story</span>
                                <span class="webstory-title"><?php echo esc_html($story_title); ?></span>
                            </span>
                        </button>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($additional_recent_cards)): ?>
                <div class="webstories-hidden-cards" aria-hidden="true" style="display:none;">
                    <?php foreach ($additional_recent_cards as $hidden_index => $hidden_story):
                        $story_id = $hidden_story['id'] ?? ('recent-' . $hidden_index);
                        $story_key = 'webstory-' . $story_id;
                        $story_title = $hidden_story['title'] ?? 'Web Story';
                        $story_featured = $hidden_story['featured_image'] ?? ($hidden_story['featured_image_url'] ?? '');
                        $story_image_url = pavilion_resolve_gallery_image($story_featured);
                        $story_published = $hidden_story['published_at'] ?? ($hidden_story['date'] ?? ($hidden_story['created_at'] ?? ''));
                        ?>
                        <article class="webstory-card webstory-card--hidden" role="listitem"
                            data-story="<?php echo esc_attr($story_key); ?>" data-cover="<?php echo esc_url($story_image_url); ?>"
                            data-title="<?php echo esc_attr($story_title); ?>"
                            data-published="<?php echo esc_attr($story_published); ?>">
                            <button type="button" class="webstory-trigger" aria-label="<?php echo esc_attr($story_title); ?>"
                                data-story="<?php echo esc_attr($story_key); ?>"
                                data-cover="<?php echo esc_url($story_image_url); ?>"
                                data-title="<?php echo esc_attr($story_title); ?>"
                                data-published="<?php echo esc_attr($story_published); ?>">
                                <span class="webstory-thumb">
                                    <span class="webstory-thumb-fill"
                                        style="background-image: url('<?php echo esc_url($story_image_url); ?>');"
                                        aria-hidden="true"></span>
                                    <span class="webstory-chip">Web Story</span>
                                    <span class="webstory-title"><?php echo esc_html($story_title); ?></span>
                                </span>
                            </button>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php
            $slide_posts = !empty($slide_source_posts) ? $slide_source_posts : $webstory_posts;
            ?>

            <div class="webstory-hidden" aria-hidden="true">
                <?php foreach ($slide_posts as $index => $webstory_post):
                    $story_id = $webstory_post['id'] ?? ($index + 1);
                    $story_key = 'webstory-' . $story_id;
                    $story_title = $webstory_post['title'] ?? 'Web Story';
                    $story_featured = $webstory_post['featured_image'] ?? ($webstory_post['featured_image_url'] ?? '');
                    $story_fallback_image = pavilion_resolve_gallery_image($story_featured);
                    $story_images = get_gallery_images($story_id);

                    $slides_markup = '';
                    $has_valid_slide = false;

                    if (!empty($story_images)) {
                        ob_start();
                        foreach ($story_images as $slide_index => $image_data) {
                            $image_url = $image_data['url'] ?? ($image_data['image_url'] ?? '');
                            if (empty($image_url) && !empty($image_data['image_id'])) {
                                $image_url = pavilion_get_media_url($image_data['image_id']);
                            }
                            $image_url = pavilion_resolve_gallery_image($image_url);
                            if (empty($image_url)) {
                                continue;
                            }
                            $has_valid_slide = true;
                            $image_caption = $image_data['caption'] ?? '';
                            ?>
                            <div class="webstory-slide" data-story="<?php echo esc_attr($story_key); ?>"
                                data-caption="<?php echo esc_attr($image_caption); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($story_title); ?>"
                                    class="webstory-slide-image">
                                <?php if (!empty($image_caption)): ?>
                                    <div class="webstory-slide-caption"><?php echo esc_html($image_caption); ?></div>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                        $slides_markup = ob_get_clean();
                    }

                    if (!$has_valid_slide) {
                        ob_start();
                        ?>
                        <div class="webstory-slide" data-story="<?php echo esc_attr($story_key); ?>"
                            data-caption="<?php echo esc_attr($story_title); ?>">
                            <img src="<?php echo esc_url($story_fallback_image); ?>" alt="<?php echo esc_attr($story_title); ?>"
                                class="webstory-slide-image">
                            <div class="webstory-slide-caption"><?php echo esc_html($story_title); ?></div>
                        </div>
                        <?php
                        $slides_markup = ob_get_clean();
                    }
                    ?>
                    <div class="webstory-slide-collection" data-story="<?php echo esc_attr($story_key); ?>">
                        <?php echo $slides_markup; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else:
            $fallback_stories = array(
                array(
                    'key' => 'webstory-fallback-1',
                    'title' => 'ജനനായകന്റെ അന്ത്യയാത്ര: വി.എസ്. അച്യുതാനന്ദൻ വിട',
                    'image' => get_stylesheet_directory_uri() . '/assets/images/new/vs.jpg',
                ),
                array(
                    'key' => 'webstory-fallback-2',
                    'title' => 'വർണ്ണവിസ്മയങ്ങളുടെ ദുബായ്: ഫെസ്റ്റിവൽ കാഴ്ചകൾ',
                    'image' => get_stylesheet_directory_uri() . '/assets/images/new/dubai1.jpg',
                ),
                array(
                    'key' => 'webstory-fallback-3',
                    'title' => 'ചതുർവർണ്ണ പതാകയേന്തി ഒരു ജനത: ദേശീയ ദിനാഘോഷം',
                    'image' => get_stylesheet_directory_uri() . '/assets/images/new/uae.jpg',
                ),
            );
            ?>
            <div class="webstories-track webstories-track--fallback" role="list">
                <?php foreach ($fallback_stories as $fallback_story): ?>
                    <article class="webstory-card" role="listitem" data-story="<?php echo esc_attr($fallback_story['key']); ?>"
                        data-cover="<?php echo esc_url($fallback_story['image']); ?>"
                        data-title="<?php echo esc_attr($fallback_story['title']); ?>">
                        <button type="button" class="webstory-trigger"
                            aria-label="<?php echo esc_attr($fallback_story['title']); ?>"
                            data-story="<?php echo esc_attr($fallback_story['key']); ?>"
                            data-cover="<?php echo esc_url($fallback_story['image']); ?>"
                            data-title="<?php echo esc_attr($fallback_story['title']); ?>">
                            <span class="webstory-thumb">
                                <span class="webstory-thumb-fill"
                                    style="background-image: url('<?php echo esc_url($fallback_story['image']); ?>');"
                                    aria-hidden="true"></span>
                                <span class="webstory-chip">Web Story</span>
                                <span class="webstory-title"><?php echo esc_html($fallback_story['title']); ?></span>
                            </span>
                        </button>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="webstory-hidden" aria-hidden="true">
                <?php foreach ($fallback_stories as $fallback_story): ?>
                    <div class="webstory-slide-collection" data-story="<?php echo esc_attr($fallback_story['key']); ?>">
                        <div class="webstory-slide" data-story="<?php echo esc_attr($fallback_story['key']); ?>"
                            data-caption="<?php echo esc_attr($fallback_story['title']); ?>">
                            <img src="<?php echo esc_url($fallback_story['image']); ?>"
                                alt="<?php echo esc_attr($fallback_story['title']); ?>" class="webstory-slide-image">
                            <div class="webstory-slide-caption"><?php echo esc_html($fallback_story['title']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="post-section section-gap">
    <div class="random-posts independent-scroll">
        <div class="container px-4">
            <div class="row">
                <div class="col-lg-3">
                    <main class="axil-content medium-section">
                        <div class="section-title m-b-xs-10">
                            <a href="<?php echo home_url('/latest'); ?>" class="d-block">
                                <h2 class="axil-title">Latest Updates</h2>
                            </a>
                        </div>

                        <?php
                        $featured_category = get_category_by_slug('featured');
                        $exclude_post_id = 0;

                        if ($featured_category) {
                            $top_featured_posts = get_all_posts(array(
                                'category_name' => 'featured',
                                'posts_per_page' => 1,
                                'status' => 'published'
                            ));

                            if (!empty($top_featured_posts)) {
                                $exclude_post_id = $top_featured_posts[0]['id'];
                            }
                        }

                        $latest_posts = get_all_posts(array(
                            'posts_per_page' => 5,
                            'status' => 'published',
                            'post__not_in' => $exclude_post_id > 0 ? array($exclude_post_id) : array()
                        ));

                        $latest_featured_post = !empty($latest_posts) ? array_shift($latest_posts) : null;
                        $latest_other_posts = array_slice($latest_posts, 0, 4);
                        ?>

                        <?php if ($latest_featured_post): ?>
                            <?php setup_postdata($latest_featured_post); ?>
                            <div class="live-card">
                                <a href="<?php the_permalink(); ?>" class="block-link">
                                    <div class="flex-1">
                                        <h3 class="featured-title"><?php the_title(); ?></h3>
                                        <div class="post-image-wrapper">
                                            <?php if (has_post_thumbnail()): ?>
                                                <?php the_post_thumbnail('large', array('class' => 'img-fluid img-border-radius', 'alt' => get_the_title())); ?>
                                            <?php else: ?>
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.png"
                                                    alt="<?php the_title_attribute(); ?>" class="img-fluid img-border-radius">
                                            <?php endif; ?>
                                            <?php
                                            $categories = get_filtered_categories();
                                            $main_category = null;
                                            if (!empty($categories)) {
                                                foreach ($categories as $category) {
                                                    if ($category->slug !== 'editors-pick' && $category->slug !== 'featured') {
                                                        $main_category = $category;
                                                        break;
                                                    }
                                                }
                                            }

                                            if ($main_category):
                                                ?>
                                                <div class="post-cat-group badge-on-image">
                                                    <a href="<?php echo esc_url(get_category_link($main_category->term_id)); ?>"
                                                        class="post-cat cat-btn bg-primary-color"><?php echo esc_html($main_category->name); ?></a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <p class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <div class="post-cat-group flex-shrink-0">
                                                <?php
                                                $categories = get_filtered_categories();
                                                if (!empty($categories)) {
                                                    $cat_count = 0;
                                                    foreach ($categories as $category) {
                                                        if ($category->slug === 'editors-pick' || $category->slug === 'featured') {
                                                            continue;
                                                        }
                                                        if ($cat_count > 0)
                                                            echo ', ';
                                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                        $cat_count++;
                                                        if ($cat_count >= 3)
                                                            break;
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="post-time ms-3 flex-shrink-0">
                                                <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>

                        <?php if (!empty($latest_other_posts)): ?>
                            <?php foreach ($latest_other_posts as $post_item):
                                setup_postdata($post_item); ?>
                                <a href="<?php the_permalink(); ?>" class="block-link">
                                    <div class="media post-block small-block">
                                        <a href="<?php the_permalink(); ?>" class="block-link">
                                            <div>
                                                <h3 class="featured-title"><?php the_title(); ?></h3>
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    <div class="post-cat-group flex-shrink-0">
                                                        <?php
                                                        $categories = get_filtered_categories();
                                                        if (!empty($categories)) {
                                                            $cat_count = 0;
                                                            foreach ($categories as $category) {
                                                                if ($category->slug === 'editors-pick' || $category->slug === 'featured') {
                                                                    continue;
                                                                }
                                                                if ($cat_count > 0)
                                                                    echo ', ';
                                                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                                $cat_count++;
                                                                if ($cat_count >= 2)
                                                                    break;
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="post-time ms-3 flex-shrink-0">
                                                        <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>



                    </main>
                    <!-- End of .axil-content -->
                </div>
                <!-- End of .col-lg-3 -->
                <div class="col-lg-5">
                    <aside class="main-lead-section">
                        <div class="section-title m-b-xs-10">
                            <a href="<?php echo get_safe_category_link('featured'); ?>" class="d-block">
                                <h2 class="axil-title">Top news of the day</h2>
                            </a>
                        </div>

                        <?php
                        $featured_category = get_category_by_slug('featured');
                        $top_news_posts = array();

                        if ($featured_category) {
                            $top_news_posts = get_all_posts(array(
                                'category_name' => 'featured',
                                'posts_per_page' => 5,
                                'status' => 'published'
                            ));
                        }

                        $top_news_featured = !empty($top_news_posts) ? array_shift($top_news_posts) : null;
                        $top_news_list = array_slice($top_news_posts, 0, 4);
                        ?>

                        <?php if ($top_news_featured):
                            setup_postdata($top_news_featured); ?>
                            <div class="live-card">
                                <a href="<?php the_permalink(); ?>" class="block-link">
                                    <div class="flex-1">
                                        <h3 class="featured-title"><?php the_title(); ?></h3>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <div class="post-cat-group flex-shrink-0">
                                                <?php
                                                $categories = get_filtered_categories();
                                                if (!empty($categories)) {
                                                    $cat_count = 0;
                                                    foreach ($categories as $category) {
                                                        if ($category->slug === 'editors-pick' || $category->slug === 'featured') {
                                                            continue;
                                                        }
                                                        if ($cat_count > 0)
                                                            echo ', ';
                                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                        $cat_count++;
                                                        if ($cat_count >= 3)
                                                            break;
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="post-time ms-3 flex-shrink-0">
                                                <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                            </div>
                                        </div>
                                        <div class="post-image-wrapper">
                                            <?php if (has_post_thumbnail()): ?>
                                                <a href="<?php the_permalink(); ?>" class="image-link">
                                                    <?php the_post_thumbnail('large', array('class' => 'img-fluid img-border-radius', 'alt' => get_the_title())); ?>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php the_permalink(); ?>" class="image-link">
                                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.png"
                                                        alt="<?php the_title_attribute(); ?>"
                                                        class="img-fluid img-border-radius">
                                                </a>
                                            <?php endif; ?>
                                            <?php
                                            $categories = get_filtered_categories();
                                            $main_category = null;
                                            if (!empty($categories)) {
                                                foreach ($categories as $category) {
                                                    if ($category->slug !== 'editors-pick' && $category->slug !== 'featured') {
                                                        $main_category = $category;
                                                        break;
                                                    }
                                                }
                                            }

                                            if ($main_category):
                                                ?>
                                                <div class="post-cat-group badge-on-image">
                                                    <a href="<?php echo esc_url(get_category_link($main_category->term_id)); ?>"
                                                        class="post-cat cat-btn bg-primary-color"><?php echo esc_html($main_category->name); ?></a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <p class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 12, '...'); ?></p>
                                    </div>
                                </a>
                            </div>
                            <?php wp_reset_postdata(); endif; ?>
                        <?php if (!empty($top_news_list)): ?>
                            <?php foreach ($top_news_list as $post_item):
                                setup_postdata($post_item); ?>
                                <a href="<?php the_permalink(); ?>" class="block-link">
                                    <div class="media post-block small-block">
                                        <div class="post-image-wrapper">
                                            <?php if (has_post_thumbnail()): ?>
                                                <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                                            <?php else: ?>
                                                <img class="img-fluid"
                                                    src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.png"
                                                    alt="<?php the_title_attribute(); ?>">
                                            <?php endif; ?>
                                            <?php echo get_video_play_button(); ?>
                                        </div>
                                        <div class="media-body">
                                            <a href="<?php the_permalink(); ?>" class="block-link">
                                                <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                            </a>
                                            <div class="d-flex align-items-center flex-nowrap">
                                                <div class="post-cat-group flex-shrink-0">
                                                    <?php
                                                    $categories = get_filtered_categories();
                                                    if (!empty($categories)) {
                                                        $cat_count = 0;
                                                        foreach ($categories as $category) {
                                                            if ($category->slug === 'editors-pick' || $category->slug === 'featured' || $category->slug === 'gulf' || $category->slug === 'uae') {
                                                                continue;
                                                            }
                                                            if ($cat_count > 0)
                                                                echo ', ';
                                                            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                            $cat_count++;
                                                            if ($cat_count >= 2)
                                                                break;
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <div class="post-time ms-3 flex-shrink-0">
                                                    <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>



                    </aside>
                    <!-- End of .post-sidebar -->
                </div>
                <!-- End of .col-lg-4 -->
                <div class="col-lg-4">
                    <aside class="post-sidebar">
                        <?php include 'parts/shared/team-india-news-widget.php'; ?>
                        <?php // Sports widgets temporarily disabled - will setup later
                        // include 'parts/shared/sports-widgets.php'; 
                        ?>
                    </aside>
                    <!-- End of .post-sidebar -->
                </div>
                <!-- End of .col-lg-4 -->
            </div>
            <!-- End of .row -->
        </div>
        <!-- End of .container -->
    </div>
    <!-- End of .random-posts -->
</section>

<section class="axil-video-posts section-gap">
    <div class="container">
        <div class="section-title m-b-xs-10">
            <a href="<?php echo get_safe_category_link('video'); ?>" class="d-block">
                <h2 class="axil-title">VIDEO STORIES</h2>
            </a>
        </div>
        <?php
        $video_posts = get_all_posts(array(
            'category_name' => 'video',
            'posts_per_page' => 10,
            'status' => 'published'
        ));

        $video_posts = array_slice($video_posts, 0, 10);
        ?>
        <?php if (!empty($video_posts)): ?>
            <div class="video-track" role="list">
                <?php
                foreach ($video_posts as $video_post_raw):
                    $video_post_instance = null;

                    if (is_object($video_post_raw) && isset($video_post_raw->ID)) {
                        $video_post_instance = (array) $video_post_raw;
                    } elseif (is_array($video_post_raw) && isset($video_post_raw['id'])) {
                        $video_post_instance = get_post((int) $video_post_raw['id']);
                        if ($video_post_instance && is_object($video_post_instance)) {
                            $video_post_instance = (array) $video_post_instance;
                        }
                    } elseif (is_array($video_post_raw)) {
                        $video_post_instance = $video_post_raw;
                    }

                    if (!is_array($video_post_instance) || empty($video_post_instance)) {
                        continue;
                    }

                    setup_postdata($video_post_instance);

                    $video_id = get_the_ID();
                    $thumb_url = get_the_post_thumbnail_url($video_id, 'large');
                    if (!$thumb_url) {
                        $thumb_url = get_stylesheet_directory_uri() . '/assets/images/placeholder.png';
                    }

                    $categories = get_filtered_categories($video_id);
                    $exclude_slugs = array('editors-pick', 'featured', 'gulf', 'uae', 'video');
                    $primary_category = null;

                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $slug = is_object($category) ? $category->slug : (isset($category['slug']) ? $category['slug'] : '');
                            if ($slug && !in_array($slug, $exclude_slugs, true)) {
                                $primary_category = $category;
                                break;
                            }
                        }
                    }

                    $category_label = $primary_category ? (is_object($primary_category) ? $primary_category->name : ($primary_category['name'] ?? '')) : 'Video';
                    ?>
                    <article class="webstory-card video-story-card" role="listitem">
                        <a href="<?php the_permalink(); ?>" class="video-story-trigger">
                            <span class="webstory-thumb">
                                <span class="webstory-thumb-fill"
                                    style="background-image: url('<?php echo esc_url($thumb_url); ?>');"
                                    aria-hidden="true"></span>
                                <span class="video-play-icon" aria-hidden="true"></span>
                                <span class="webstory-title"><?php the_title(); ?></span>
                                <span class="video-story-time"><i class="far fa-clock"></i>
                                    <?php echo meks_time_ago(); ?></span>
                            </span>
                        </a>
                    </article>
                    <?php
                endforeach;
                wp_reset_postdata();
                ?>
            </div>
        <?php else: ?>
            <p class="video-empty-state">Fresh video stories are on the way. Check back soon!</p>
        <?php endif; ?>
    </div>
</section>

<section class="post-section section-gap">
    <div class="random-posts">
        <div class="container px-4">
            <div class="section-title m-b-xs-10">
                <a href="<?php echo get_safe_category_link('regional-cricket'); ?>" class="d-block">
                    <h2 class="axil-title">REGIONAL CRICKET</h2>
                </a>
            </div>
            <?php
            $regional_posts = get_all_posts(array(
                'category_name' => 'regional-cricket',
                'posts_per_page' => 6,
                'status' => 'published'
            ));

            $regional_big_posts = array_slice($regional_posts, 0, 3);
            $regional_small_posts = array_slice($regional_posts, 3, 3);
            ?>

            <?php if (!empty($regional_big_posts)): ?>
                <div class="row">
                    <?php foreach ($regional_big_posts as $post_item):
                        setup_postdata($post_item); ?>
                        <div class="col-lg-4">
                            <div class="axil-img-container flex-height-container video-container__type-2 m-b-xs-30">
                                <a href="<?php the_permalink(); ?>" class="d-block h-100">
                                    <?php if (has_post_thumbnail()): ?>
                                        <?php the_post_thumbnail('large', array('class' => 'w-100', 'alt' => get_the_title())); ?>
                                    <?php else: ?>
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.png"
                                            alt="<?php the_title_attribute(); ?>" class="w-100">
                                    <?php endif; ?>
                                    <div class="grad-overlay grad-overlay__transparent"></div>
                                    <?php echo get_video_play_button(); ?>
                                </a>
                                <div class="post-image-wrapper">
                                    <?php
                                    $categories = get_filtered_categories();
                                    $main_category = null;
                                    if (!empty($categories)) {
                                        foreach ($categories as $category) {
                                            if (in_array($category->slug, array('editors-pick', 'featured', 'gulf', 'uae', 'video', 'regional-cricket'), true)) {
                                                continue;
                                            }
                                            $main_category = $category;
                                            break;
                                        }
                                    }
                                    if ($main_category):
                                        ?>
                                        <div class="post-cat-group badge-on-image">
                                            <a href="<?php echo esc_url(get_category_link($main_category->term_id)); ?>"
                                                class="post-cat cat-btn btn-big bg-primary-color"><?php echo esc_html($main_category->name); ?></a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="block-link">
                                    <div class="media post-block grad-overlay__transparent position-absolute">
                                        <div class="media-body media-body__big">
                                            <div class="axil-media-bottom mt-auto">
                                                <h3 class="axil-post-title hover-line kerala-news-title"><?php the_title(); ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($regional_small_posts)): ?>
                <div class="row">
                    <?php foreach ($regional_small_posts as $post_item):
                        setup_postdata($post_item); ?>
                        <div class="col-lg-4">
                            <a href="<?php the_permalink(); ?>" class="block-link">
                                <div class="media post-block small-block">
                                    <div class="post-image-wrapper">
                                        <?php if (has_post_thumbnail()): ?>
                                            <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                                        <?php else: ?>
                                            <img class="img-fluid"
                                                src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg"
                                                alt="<?php the_title_attribute(); ?>">
                                        <?php endif; ?>
                                        <?php echo get_video_play_button(); ?>
                                    </div>
                                    <div class="media-body">
                                        <h4 class="axil-post-title"><?php the_title(); ?></h4>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <div class="post-cat-group flex-shrink-0">
                                                <?php
                                                $categories = get_filtered_categories();
                                                if (!empty($categories)) {
                                                    $cat_count = 0;
                                                    foreach ($categories as $category) {
                                                        if (in_array($category->slug, array('editors-pick', 'featured', 'gulf', 'uae', 'video', 'regional-cricket'), true)) {
                                                            continue;
                                                        }
                                                        if ($cat_count > 0)
                                                            echo ', ';
                                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                        $cat_count++;
                                                        if ($cat_count >= 2) {
                                                            break;
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="post-time ms-3 flex-shrink-0">
                                                <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-12">
                        <p class="text-white">No Regional Cricket articles available at the moment.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="post-section section-gap bg-grey-light-one">
    <div class="random-posts independent-scroll">
        <div class="container px-4">
            <div class="row">
                <div class="col-lg-4">
                    <main class="axil-content big-section">
                        <?php
                        $club_league_slugs = array(
                            'club-football-updates',
                            'club-football',
                            'english-premier-league',
                            'premier-league',
                            'epl',
                            'la-liga',
                            'laliga',
                            'serie-a',
                            'bundesliga',
                            'saudi-pro-league',
                            'ligue-1',
                            'champions-league',
                            'uefa-champions-league',
                            'football'
                        );

                        $club_posts_map = array();
                        $club_category_link = '#';

                        foreach ($club_league_slugs as $league_slug) {
                            $league_category = get_category_by_slug($league_slug);

                            if (!$league_category) {
                                continue;
                            }

                            if ($club_category_link === '#') {
                                $club_category_link = get_safe_category_link($league_slug);
                            }

                            $league_posts = get_all_posts(array(
                                'category_name' => $league_slug,
                                'posts_per_page' => 6,
                                'status' => 'published'
                            ));

                            if (empty($league_posts)) {
                                continue;
                            }

                            foreach ($league_posts as $league_post) {
                                $post_id = null;

                                if (is_object($league_post) && isset($league_post->ID)) {
                                    $post_id = $league_post->ID;
                                } elseif (is_array($league_post) && isset($league_post['id'])) {
                                    $post_id = $league_post['id'];
                                }

                                if (!$post_id) {
                                    continue;
                                }

                                if (!isset($club_posts_map[$post_id])) {
                                    $club_posts_map[$post_id] = $league_post;
                                }
                            }

                            if (count($club_posts_map) >= 10) {
                                break;
                            }
                        }

                        $gulf_posts = array_values($club_posts_map);

                        if (!empty($gulf_posts)) {
                            usort($gulf_posts, function ($a, $b) {
                                $a_time = is_object($a) ? strtotime($a->post_date ?? $a->post_date_gmt ?? '1970-01-01') : strtotime($a['date'] ?? $a['published_at'] ?? '1970-01-01');
                                $b_time = is_object($b) ? strtotime($b->post_date ?? $b->post_date_gmt ?? '1970-01-01') : strtotime($b['date'] ?? $b['published_at'] ?? '1970-01-01');

                                if ($a_time === $b_time) {
                                    return 0;
                                }

                                return ($a_time > $b_time) ? -1 : 1;
                            });
                        }

                        $gulf_posts = array_slice($gulf_posts, 0, 5);
                        $gulf_featured = !empty($gulf_posts) ? array_shift($gulf_posts) : null;
                        $gulf_other_posts = $gulf_posts;
                        ?>
                        <div class="section-title m-b-xs-10">
                            <a href="<?php echo esc_url($club_category_link); ?>" class="d-block">
                                <h2 class="axil-title">Club Football Updates</h2>
                            </a>
                        </div>

                        <?php
                        if ($gulf_featured):
                            $featured_post_instance = null;

                            if (is_object($gulf_featured) && isset($gulf_featured->ID)) {
                                $featured_post_instance = (array) $gulf_featured;
                            } elseif (is_array($gulf_featured) && isset($gulf_featured['id'])) {
                                $featured_post_instance = get_post((int) $gulf_featured['id']);
                            } elseif (is_array($gulf_featured)) {
                                $featured_post_instance = $gulf_featured;
                            }

                            if (is_array($featured_post_instance) && !empty($featured_post_instance)) {
                                setup_postdata($featured_post_instance);
                            } else {
                                $featured_post_instance = null;
                            }

                            if ($featured_post_instance):
                                ?>
                                <div class="live-card">
                                    <div class="flex-1">
                                        <a href="<?php the_permalink(); ?>" class="title-link">
                                            <h3 class="featured-title"><?php the_title(); ?></h3>
                                        </a>
                                        <p class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <div class="post-cat-group flex-shrink-0">
                                                <?php
                                                $categories = get_filtered_categories();
                                                if (!empty($categories)) {
                                                    $cat_count = 0;
                                                    foreach ($categories as $category) {
                                                        if ($category->slug === 'editors-pick' || $category->slug === 'featured' || $category->slug === 'gulf' || $category->slug === 'club-football-updates' || $category->slug === 'club-football' || $category->slug === 'english-premier-league' || $category->slug === 'premier-league' || $category->slug === 'epl' || $category->slug === 'la-liga' || $category->slug === 'laliga' || $category->slug === 'serie-a' || $category->slug === 'bundesliga' || $category->slug === 'saudi-pro-league' || $category->slug === 'ligue-1' || $category->slug === 'champions-league' || $category->slug === 'uefa-champions-league' || $category->slug === 'football') {
                                                            continue;
                                                        }
                                                        if ($cat_count > 0)
                                                            echo ', ';
                                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                        $cat_count++;
                                                        if ($cat_count >= 2)
                                                            break;
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="post-time ms-3 flex-shrink-0">
                                                <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="post-image-wrapper">
                                        <a href="<?php the_permalink(); ?>" class="image-link">
                                            <?php if (has_post_thumbnail()): ?>
                                                <?php the_post_thumbnail('large', array('class' => 'img-fluid img-border-radius', 'alt' => get_the_title())); ?>
                                            <?php else: ?>
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>assets/images/new/hero.jpg"
                                                    alt="<?php the_title_attribute(); ?>" class="img-fluid img-border-radius">
                                            <?php endif; ?>
                                        </a>
                                        <?php
                                        $categories = get_filtered_categories();
                                        $main_category = null;
                                        if (!empty($categories)) {
                                            foreach ($categories as $category) {
                                                if ($category->slug !== 'editors-pick' && $category->slug !== 'featured' && $category->slug !== 'gulf' && $category->slug !== 'club-football-updates' && $category->slug !== 'club-football' && $category->slug !== 'english-premier-league' && $category->slug !== 'premier-league' && $category->slug !== 'epl' && $category->slug !== 'la-liga' && $category->slug !== 'laliga' && $category->slug !== 'serie-a' && $category->slug !== 'bundesliga' && $category->slug !== 'saudi-pro-league' && $category->slug !== 'ligue-1' && $category->slug !== 'champions-league' && $category->slug !== 'uefa-champions-league' && $category->slug !== 'football') {
                                                    $main_category = $category;
                                                    break;
                                                }
                                            }
                                        }

                                        if ($main_category):
                                            ?>
                                            <div class="post-cat-group badge-on-image">
                                                <a href="<?php echo esc_url(get_category_link($main_category->term_id)); ?>"
                                                    class="post-cat cat-btn bg-primary-color"><?php echo esc_html($main_category->name); ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                                wp_reset_postdata();
                            endif;
                        endif; ?>
                        <?php if (!empty($gulf_other_posts)): ?>
                            <?php foreach ($gulf_other_posts as $post_item):
                                $loop_post_instance = null;

                                if (is_object($post_item) && isset($post_item->ID)) {
                                    $loop_post_instance = (array) $post_item;
                                } elseif (is_array($post_item) && isset($post_item['id'])) {
                                    $loop_post_instance = get_post((int) $post_item['id']);
                                } elseif (is_array($post_item)) {
                                    $loop_post_instance = $post_item;
                                }

                                if (!is_array($loop_post_instance) || empty($loop_post_instance)) {
                                    continue;
                                }

                                setup_postdata($loop_post_instance);
                                ?>
                                <a href="<?php the_permalink(); ?>" class="block-link">
                                    <div class="media post-block small-block">
                                        <div class="post-image-wrapper">
                                            <?php if (has_post_thumbnail()): ?>
                                                <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                                            <?php else: ?>
                                                <img class="img-fluid"
                                                    src="<?php echo get_stylesheet_directory_uri(); ?>assets/images/new/hero.jpg"
                                                    alt="<?php the_title_attribute(); ?>">
                                            <?php endif; ?>
                                            <?php echo get_video_play_button(); ?>
                                        </div>
                                        <div class="media-body">
                                            <a href="<?php the_permalink(); ?>" class="block-link">
                                                <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                            </a>
                                            <div class="d-flex align-items-center flex-nowrap">
                                                <div class="post-cat-group flex-shrink-0">
                                                    <?php
                                                    $categories = get_filtered_categories();
                                                    if (!empty($categories)) {
                                                        $cat_count = 0;
                                                        foreach ($categories as $category) {
                                                            if ($category->slug === 'editors-pick' || $category->slug === 'featured' || $category->slug === 'gulf' || $category->slug === 'club-football-updates' || $category->slug === 'club-football' || $category->slug === 'english-premier-league' || $category->slug === 'premier-league' || $category->slug === 'epl' || $category->slug === 'la-liga' || $category->slug === 'laliga' || $category->slug === 'serie-a' || $category->slug === 'bundesliga' || $category->slug === 'saudi-pro-league' || $category->slug === 'ligue-1' || $category->slug === 'champions-league' || $category->slug === 'uefa-champions-league' || $category->slug === 'football') {
                                                                continue;
                                                            }
                                                            if ($cat_count > 0)
                                                                echo ', ';
                                                            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                            $cat_count++;
                                                            if ($cat_count >= 2)
                                                                break;
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <div class="post-time ms-3 flex-shrink-0">
                                                    <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>


                    </main>
                    <!-- End of .axil-content -->
                </div>
                <!-- End of .col-lg-8 -->
                <div class="col-lg-4">
                    <aside class="post-sidebar medium-section">


                        <div class="section-title m-b-xs-10">
                            <?php
                            $regional_slug = 'regional-football';
                            $regional_category = get_category_by_slug($regional_slug);

                            if (!$regional_category) {
                                ?>
                                <a href="#" class="d-block">
                                    <h2 class="axil-title">Regional Football</h2>
                                </a>
                                <p class="regional-empty-state">Add posts to the Regional Football category to populate this
                                    block.</p>
                                <?php
                            } else {
                                $regional_link = get_safe_category_link($regional_slug);
                                ?>
                                <a href="<?php echo esc_url($regional_link); ?>" class="d-block">
                                    <h2 class="axil-title">Regional Football</h2>
                                </a>
                            <?php } ?>
                        </div>

                        <?php if ($regional_category):
                            $uae_category = $regional_category;
                            $uae_posts = array();

                            $uae_posts = get_all_posts(array(
                                'category_name' => $regional_slug,
                                'posts_per_page' => 5,
                                'status' => 'published'
                            ));

                            $uae_featured = !empty($uae_posts) ? array_shift($uae_posts) : null;
                            $uae_other_posts = array_slice($uae_posts, 0, 4);
                            ?>
                            <?php if ($uae_featured):
                                setup_postdata($uae_featured); ?>
                                <div class="live-card">
                                    <div class="flex-1">
                                        <a href="<?php the_permalink(); ?>" class="title-link">
                                            <h3 class="featured-title"><?php the_title(); ?></h3>
                                        </a>
                                        <p class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <div class="post-cat-group flex-shrink-0">
                                                <?php
                                                $categories = get_filtered_categories();
                                                if (!empty($categories)) {
                                                    $cat_count = 0;
                                                    foreach ($categories as $category) {
                                                        if ($category->slug === 'editors-pick' || $category->slug === 'featured' || $category->slug === 'gulf' || $category->slug === 'uae' || $category->slug === 'regional-football') {
                                                            continue;
                                                        }
                                                        if ($cat_count > 0)
                                                            echo ', ';
                                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                        $cat_count++;
                                                        if ($cat_count >= 2)
                                                            break;
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="post-time ms-3 flex-shrink-0">
                                                <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="post-image-wrapper">
                                        <a href="<?php the_permalink(); ?>" class="image-link">
                                            <?php if (has_post_thumbnail()): ?>
                                                <?php the_post_thumbnail('large', array('class' => 'img-fluid img-border-radius', 'alt' => get_the_title())); ?>
                                            <?php else: ?>
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>assets/images/new/hero.jpg"
                                                    alt="<?php the_title_attribute(); ?>" class="img-fluid img-border-radius">
                                            <?php endif; ?>
                                        </a>
                                        <?php
                                        $categories = get_filtered_categories();
                                        $main_category = null;
                                        if (!empty($categories)) {
                                            foreach ($categories as $category) {
                                                if ($category->slug !== 'editors-pick' && $category->slug !== 'featured' && $category->slug !== 'gulf' && $category->slug !== 'uae' && $category->slug !== 'regional-football') {
                                                    $main_category = $category;
                                                    break;
                                                }
                                            }
                                        }

                                        if ($main_category):
                                            ?>
                                            <div class="post-cat-group badge-on-image">
                                                <a href="<?php echo esc_url(get_category_link($main_category->term_id)); ?>"
                                                    class="post-cat cat-btn bg-primary-color"><?php echo esc_html($main_category->name); ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    </a>
                                </div>
                                <?php wp_reset_postdata(); endif; ?>
                            <?php if (!empty($uae_other_posts)): ?>
                                <?php foreach ($uae_other_posts as $post_item):
                                    setup_postdata($post_item); ?>
                                    <a href="<?php the_permalink(); ?>" class="block-link">
                                        <div class="media post-block small-block">
                                            <div class="post-image-wrapper">
                                                <?php if (has_post_thumbnail()): ?>
                                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                                                <?php else: ?>
                                                    <img class="img-fluid"
                                                        src="<?php echo get_stylesheet_directory_uri(); ?>assets/images/new/hero.jpg"
                                                        alt="<?php the_title_attribute(); ?>">
                                                <?php endif; ?>
                                                <?php echo get_video_play_button(); ?>
                                            </div>
                                            <div class="media-body">
                                                <a href="<?php the_permalink(); ?>" class="block-link">
                                                    <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                                </a>
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    <div class="post-cat-group flex-shrink-0">
                                                        <?php
                                                        $categories = get_filtered_categories();
                                                        if (!empty($categories)) {
                                                            $cat_count = 0;
                                                            foreach ($categories as $category) {
                                                                if ($category->slug === 'editors-pick' || $category->slug === 'featured' || $category->slug === 'gulf' || $category->slug === 'uae' || $category->slug === 'regional-football') {
                                                                    continue;
                                                                }
                                                                if ($cat_count > 0)
                                                                    echo ', ';
                                                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                                $cat_count++;
                                                                if ($cat_count >= 2)
                                                                    break;
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="post-time ms-3 flex-shrink-0">
                                                        <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                                <?php wp_reset_postdata(); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </aside>
                    <!-- End of .post-sidebar -->
                </div>
                <!-- End of .col-lg-4 -->
                <div class="col-lg-4">
                    <main class="axil-content big-section">
                        <?php
                        $isl_slug = 'isl';
                        $isl_category = get_category_by_slug($isl_slug);

                        if ($isl_category) {
                            $isl_link = get_safe_category_link($isl_slug);
                            $isl_posts = get_all_posts(array(
                                'category_name' => $isl_slug,
                                'posts_per_page' => 6,
                                'status' => 'published'
                            ));

                            $isl_featured = !empty($isl_posts) ? array_shift($isl_posts) : null;
                            $isl_other_posts = array_slice($isl_posts, 0, 5);
                            ?>
                            <div class="section-title m-b-xs-10">
                                <a href="<?php echo esc_url($isl_link); ?>" class="d-block">
                                    <h2 class="axil-title">ISL Updates</h2>
                                </a>
                            </div>

                            <?php if ($isl_featured): ?>
                                <?php setup_postdata($isl_featured); ?>
                                <div class="live-card">
                                    <div class="flex-1">
                                        <a href="<?php the_permalink(); ?>" class="title-link">
                                            <h3 class="featured-title"><?php the_title(); ?></h3>
                                        </a>
                                        <p class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <div class="post-cat-group flex-shrink-0">
                                                <?php
                                                $categories = get_filtered_categories();
                                                if (!empty($categories)) {
                                                    $cat_count = 0;
                                                    foreach ($categories as $category) {
                                                        if ($category->slug === $isl_slug || $category->slug === 'featured' || $category->slug === 'editors-pick') {
                                                            continue;
                                                        }
                                                        if ($cat_count > 0) {
                                                            echo ', ';
                                                        }
                                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                        $cat_count++;
                                                        if ($cat_count >= 2) {
                                                            break;
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="post-time ms-3 flex-shrink-0">
                                                <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="post-image-wrapper">
                                        <a href="<?php the_permalink(); ?>" class="image-link">
                                            <?php if (has_post_thumbnail()): ?>
                                                <?php the_post_thumbnail('large', array('class' => 'img-fluid img-border-radius', 'alt' => get_the_title())); ?>
                                            <?php else: ?>
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>assets/images/new/hero.jpg"
                                                    alt="<?php the_title_attribute(); ?>" class="img-fluid img-border-radius">
                                            <?php endif; ?>
                                        </a>
                                        <?php
                                        $categories = get_filtered_categories();
                                        $main_category = null;
                                        if (!empty($categories)) {
                                            foreach ($categories as $category) {
                                                if ($category->slug !== $isl_slug && $category->slug !== 'featured' && $category->slug !== 'editors-pick') {
                                                    $main_category = $category;
                                                    break;
                                                }
                                            }
                                        }

                                        if ($main_category):
                                            ?>
                                            <div class="post-cat-group badge-on-image">
                                                <a href="<?php echo esc_url(get_category_link($main_category->term_id)); ?>"
                                                    class="post-cat cat-btn bg-primary-color"><?php echo esc_html($main_category->name); ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php wp_reset_postdata(); ?>
                            <?php endif; ?>
                            ?>

                            <?php if (!empty($isl_other_posts)): ?>
                                <?php foreach ($isl_other_posts as $post_item):
                                    setup_postdata($post_item); ?>
                                    <a href="<?php the_permalink(); ?>" class="block-link">
                                        <div class="media post-block small-block">
                                            <div class="post-image-wrapper">
                                                <?php if (has_post_thumbnail()): ?>
                                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                                                <?php else: ?>
                                                    <img class="img-fluid"
                                                        src="<?php echo get_stylesheet_directory_uri(); ?>assets/images/new/hero.jpg"
                                                        alt="<?php the_title_attribute(); ?>">
                                                <?php endif; ?>
                                                <?php echo get_video_play_button(); ?>
                                            </div>
                                            <div class="media-body">
                                                <a href="<?php the_permalink(); ?>" class="block-link">
                                                    <h4 class="axil-post-title small-card-title"><?php the_title(); ?></h4>
                                                </a>
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    <div class="post-cat-group flex-shrink-0">
                                                        <?php
                                                        $categories = get_filtered_categories();
                                                        if (!empty($categories)) {
                                                            $cat_count = 0;
                                                            foreach ($categories as $category) {
                                                                if ($category->slug === $isl_slug || $category->slug === 'featured' || $category->slug === 'editors-pick') {
                                                                    continue;
                                                                }
                                                                if ($cat_count > 0) {
                                                                    echo ', ';
                                                                }
                                                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat color-blue-three">' . esc_html($category->name) . '</a>';
                                                                $cat_count++;
                                                                if ($cat_count >= 2) {
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="post-time ms-3 flex-shrink-0">
                                                        <i class="far fa-clock clock-icon"></i><?php echo meks_time_ago(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                                <?php wp_reset_postdata(); ?>
                            <?php else: ?>
                                <p class="no-posts-message">No ISL updates available right now.</p>
                            <?php endif; ?>

                            <?php
                        } else {
                            ?>
                            <div class="section-title m-b-xs-10">
                                <h2 class="axil-title">ISL Updates</h2>
                            </div>
                            <p class="no-posts-message">Create or populate the 'isl' category to show these stories.</p>
                        <?php } ?>
                    </main>
                </div>
                <!-- End of .col-lg-4 -->
            </div>
            <!-- End of .row -->
        </div>
        <!-- End of .container -->
    </div>
    <!-- End of .random-posts -->
</section>
<!-- End of Entertainment and Sports Section -->

<?php include 'parts/shared/footer.php'; ?>
<?php include 'parts/shared/html-footer.php'; ?>