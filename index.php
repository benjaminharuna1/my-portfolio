<?php
require 'config.php';
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
                            <a href="<?php echo htmlspecialchars($social['url']); ?>" class="me-3" target="_blank">
                                <i class="<?php echo htmlspecialchars($social['icon']); ?>"></i>
                            </a>
                        <?php endwhile; ?>
                    </div>
                    <h1 class="display-4 fw-bold mb-3">I'm Web Developer</h1>
                    <p class="lead mb-4">Creating beautiful and functional web experiences with modern technologies.</p>
                    <a href="<?php echo SITE_URL; ?>/about.php" class="btn btn-primary btn-lg">Learn More</a>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="https://via.placeholder.com/500x600?text=Portfolio+Image" alt="Hero" class="img-fluid rounded">
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
                $result = $conn->query("SELECT * FROM services");
                while ($service = $result->fetch_assoc()):
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm service-card">
                            <div class="card-body text-center">
                                <i class="fas <?php echo htmlspecialchars($service['icon']); ?> fa-3x mb-3 text-primary"></i>
                                <h5 class="card-title"><?php echo htmlspecialchars($service['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                                <?php if ($service['tech_icons']): ?>
                                    <div class="tech-icons mt-3">
                                        <?php
                                        $icons = array_map('trim', explode(',', $service['tech_icons']));
                                        foreach ($icons as $icon):
                                            if ($icon):
                                        ?>
                                            <i class="<?php echo htmlspecialchars($icon); ?> tech-icon"></i>
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
                $result = $conn->query("SELECT * FROM portfolio_items LIMIT 3");
                while ($item = $result->fetch_assoc()):
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="portfolio-card">
                            <img src="<?php echo htmlspecialchars($item['image_url'] ?: 'https://via.placeholder.com/400x300?text=' . urlencode($item['title'])); ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>" class="img-fluid rounded">
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
</main>

<?php include 'includes/footer.php'; ?>
