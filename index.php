<?php
require 'config.php';
require 'includes/image-helper.php';
require 'includes/icon-helper.php';
$page_title = 'Home';
?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- Hero Section -->
    <section class="hero-section bg-gradient py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="social-icons mb-4">
                        <?php
                        $result = $conn->query("SELECT * FROM social_links LIMIT 4");
                        while ($social = $result->fetch_assoc()):
                        ?>
                            <a href="<?php echo htmlspecialchars($social['url']); ?>" class="social-icon-link" target="_blank" title="<?php echo htmlspecialchars($social['platform']); ?>">
                                <?php echo icon($social['icon'], 'social-icon', 'fa-link'); ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                    <?php
                    $about = $conn->query("SELECT greeting, subtitle FROM about LIMIT 1")->fetch_assoc();
                    $greeting = $about && !empty($about['greeting']) ? htmlspecialchars($about['greeting']) : "Hello, welcome to my portfolio";
                    $subtitle = $about && !empty($about['subtitle']) ? htmlspecialchars($about['subtitle']) : "Web Developer";
                    ?>
                    <p class="text-muted mb-2"><?php echo $greeting; ?></p>
                    <h1 class="display-4 fw-bold mb-3">I'm a <span><?php echo $subtitle; ?></span></h1>
                    <p class="lead mb-4">Creating beautiful and functional web experiences with modern technologies.</p>
                    <div class="button-group">
                        <a href="<?php echo SITE_URL; ?>/about.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-arrow-right"></i> Learn More
                        </a>
                        <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-calendar-check"></i> Book a Service
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <?php 
                        $about = $conn->query("SELECT image_url FROM about LIMIT 1")->fetch_assoc();
                        $hero_image = getImageWithFallback($about['image_url'] ?? '', 'Hero Image', 500, 600);
                        ?>
                        <img src="<?php echo $hero_image; ?>" alt="Hero" class="img-fluid rounded hero-img" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Services</h2>
            <div class="row">
                <?php
                $result = $conn->query("SELECT * FROM services WHERE status = 'published' ORDER BY id DESC");
                while ($service = $result->fetch_assoc()):
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm service-card">
                            <div class="card-body text-center">
                                <div class="service-icon mb-3" style="font-size: 3rem; color: #667eea;">
                                    <?php echo icon($service['icon'], 'text-primary', 'fa-' . strtolower(str_replace(' ', '-', $service['title']))); ?>
                                </div>
                                <h5 class="card-title"><?php echo htmlspecialchars($service['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                                <?php if ($service['tech_icons']): ?>
                                    <div class="tech-icons mt-3">
                                        <?php
                                        $icons = array_map('trim', explode(',', $service['tech_icons']));
                                        foreach ($icons as $icon_name):
                                            if ($icon_name):
                                        ?>
                                            <span class="tech-icon">
                                                <?php echo icon($icon_name, 'tech-icon-svg'); ?>
                                            </span>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Featured Portfolio -->
    <section class="portfolio-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Featured Work</h2>
            <div class="row">
                <?php
                $result = $conn->query("SELECT * FROM portfolio_items WHERE status = 'published' AND is_featured = 1 ORDER BY id DESC LIMIT 6");
                while ($item = $result->fetch_assoc()):
                    $image_url = getImageWithFallback($item['featured_image_url'], $item['title'], 400, 300);
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="portfolio-card">
                            <img src="<?php echo $image_url; ?>" 
                                 alt="<?php echo getImageAlt($item['title'], 'Portfolio Item'); ?>" class="img-fluid rounded" loading="lazy">
                            <div class="portfolio-overlay">
                                <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                                <p><?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?>...</p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?php echo SITE_URL; ?>/portfolio.php" class="btn btn-outline-primary">View All Portfolio</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">
                <i class="fas fa-quote-left"></i> What Clients Say
            </h2>
            
            <?php
            $testimonials = $conn->query("SELECT * FROM portfolio_ratings WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 6");
            if ($testimonials->num_rows > 0):
            ?>
            <!-- Testimonials Carousel -->
            <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $index = 0;
                    while ($testimonial = $testimonials->fetch_assoc()):
                        $active = $index === 0 ? 'active' : '';
                    ?>
                    <div class="carousel-item <?php echo $active; ?>">
                        <div class="testimonial-card mx-auto">
                            <div class="testimonial-header mb-3">
                                <div class="rating-stars">
                                    <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                        <i class="fas fa-star" style="color: #ffc107;"></i>
                                    <?php endfor; ?>
                                    <?php for ($i = $testimonial['rating']; $i < 5; $i++): ?>
                                        <i class="far fa-star" style="color: #ddd;"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="testimonial-text"><?php echo nl2br(htmlspecialchars($testimonial['review_text'])); ?></p>
                            <div class="testimonial-footer mt-4 pt-3 border-top">
                                <p class="mb-1">
                                    <strong><?php echo htmlspecialchars($testimonial['reviewer_name']); ?></strong>
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($testimonial['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php 
                        $index++;
                    endwhile; 
                    ?>
                </div>

                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                    <i class="fas fa-chevron-left"></i>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                    <i class="fas fa-chevron-right"></i>
                    <span class="visually-hidden">Next</span>
                </button>

                <!-- Carousel Indicators -->
                <div class="carousel-indicators mt-4">
                    <?php for ($i = 0; $i < $testimonials->num_rows; $i++): ?>
                    <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="<?php echo $i; ?>" <?php echo $i === 0 ? 'class="active"' : ''; ?> aria-label="Slide <?php echo $i + 1; ?>"></button>
                    <?php endfor; ?>
                </div>
            </div>
            <?php
            else:
            ?>
            <div class="text-center py-5">
                <p class="text-muted">No testimonials yet.</p>
            </div>
            <?php endif; ?>
            
            <div class="text-center mt-5">
                <a href="<?php echo SITE_URL; ?>/reviews.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-star"></i> View All Reviews & Leave Your Own
                </a>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section py-5">
        <div class="container">
            <div class="cta-content text-center">
                <h2 class="cta-title mb-3">Ready to Start Your Next Project?</h2>
                <p class="cta-subtitle mb-4">Let's collaborate and bring your ideas to life with cutting-edge web solutions.</p>
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-light btn-lg cta-button">
                    <i class="fas fa-envelope"></i> Get In Touch Today
                </a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
