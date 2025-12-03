<?php include 'parts/shared/html-header.php'; ?>
<?php include 'parts/shared/header.php'; ?>

<section class="banner section-gap">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="page-title">LiveBlogs</h1>
                <p class="page-description">Stay updated with our latest live coverage and real-time updates.</p>
            </div>
        </div>
    </div>
</section>

<div class="liveblog-archive-wrapper section-gap">
    <div class="container">
        <div class="row">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <article class="liveblog-card">
                            <div class="card-image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                                    </a>
                                <?php else : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/new/hero.jpg" alt="<?php the_title(); ?>" class="img-fluid">
                                    </a>
                                <?php endif; ?>
                                <div class="live-indicator">
                                    <i class="fas fa-circle pulse"></i> LIVE
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="post-categories">
                                    <?php
                                    $categories = get_filtered_categories();
                                    if (!empty($categories)) {
                                        foreach (array_slice($categories, 0, 3) as $category) {
                                            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="post-cat">' . esc_html($category->name) . '</a>';
                                        }
                                    }
                                    ?>
                                </div>
                                <h3 class="card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="card-meta">
                                    <span class="post-date">
                                        <i class="far fa-calendar-alt"></i>
                                        <?php echo get_the_date(); ?>
                                    </span>
                                    <span class="post-author">
                                        <i class="far fa-user"></i>
                                        <?php the_author(); ?>
                                    </span>
                                </div>
                                <div class="card-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                    Read Live Updates <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
                
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
                    <div class="no-posts">
                        <h3>No LiveBlogs Found</h3>
                        <p>No live blogs have been published yet. Check back soon for live updates!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.liveblog-archive-wrapper {
    background: #f8f9fa;
}

.page-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 1rem;
}

.page-description {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 2rem;
}

.liveblog-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.liveblog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.card-image {
    position: relative;
    overflow: hidden;
}

.card-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.liveblog-card:hover .card-image img {
    transform: scale(1.05);
}

.live-indicator {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #dc3545;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
}

.live-indicator i {
    margin-right: 5px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.card-content {
    padding: 20px;
}

.post-categories {
    margin-bottom: 10px;
}

.post-cat {
    display: inline-block;
    background: #007bff;
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    margin-right: 5px;
    text-decoration: none;
    transition: background 0.3s ease;
}

.post-cat:hover {
    background: #0056b3;
    color: white;
    text-decoration: none;
}

.card-title {
    font-size: 1.2rem;
    margin-bottom: 10px;
    line-height: 1.4;
}

.card-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.card-title a:hover {
    color: #007bff;
}

.card-meta {
    margin-bottom: 15px;
    font-size: 0.9rem;
    color: #666;
}

.card-meta span {
    margin-right: 15px;
}

.card-meta i {
    margin-right: 5px;
}

.card-excerpt {
    color: #666;
    line-height: 1.6;
    margin-bottom: 15px;
}

.read-more-btn {
    display: inline-block;
    background: #007bff;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.3s ease;
}

.read-more-btn:hover {
    background: #0056b3;
    color: white;
    text-decoration: none;
}

.read-more-btn i {
    margin-left: 5px;
    transition: transform 0.3s ease;
}

.read-more-btn:hover i {
    transform: translateX(3px);
}

.pagination-wrapper {
    text-align: center;
    margin-top: 40px;
}

.no-posts {
    text-align: center;
    padding: 60px 20px;
}

.no-posts h3 {
    color: #333;
    margin-bottom: 15px;
}

.no-posts p {
    color: #666;
    font-size: 1.1rem;
}
</style>

<?php include 'parts/shared/footer.php'; ?>
<?php include 'parts/shared/html-footer.php'; ?> 