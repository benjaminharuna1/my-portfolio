<?php
require 'config.php';
$page_title = 'Portfolio';
$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($category) {
    $category = $conn->real_escape_string($category);
    $result = $conn->query("SELECT * FROM portfolio_items WHERE category = '$category'");
} else {
    $result = $conn->query("SELECT * FROM portfolio_items");
}
?>
<?php include 'includes/header.php'; ?>

<main>
    <section class="portfolio-section py-5">
        <div class="container">
            <h1 class="text-center mb-5">Portfolio</h1>
            
            <!-- Filter Buttons -->
            <div class="text-center mb-5">
                <a href="<?php echo SITE_URL; ?>/portfolio.php" class="btn btn-outline-primary me-2">All</a>
                <a href="<?php echo SITE_URL; ?>/portfolio.php?category=Web Design" class="btn btn-outline-primary me-2">Web Design</a>
                <a href="<?php echo SITE_URL; ?>/portfolio.php?category=Web Development" class="btn btn-outline-primary me-2">Web Development</a>
                <a href="<?php echo SITE_URL; ?>/portfolio.php?category=UI/UX" class="btn btn-outline-primary">UI/UX</a>
            </div>

            <!-- Portfolio Grid -->
            <div class="row">
                <?php
                if ($result->num_rows > 0):
                    while ($item = $result->fetch_assoc()):
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="portfolio-card">
                            <img src="<?php echo htmlspecialchars($item['image_url'] ?: 'https://via.placeholder.com/400x300?text=' . urlencode($item['title'])); ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>" class="img-fluid rounded">
                            <div class="portfolio-overlay">
                                <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                                <p><?php echo htmlspecialchars($item['description']); ?></p>
                                <?php if ($item['link']): ?>
                                    <a href="<?php echo htmlspecialchars($item['link']); ?>" class="btn btn-sm btn-light" target="_blank">View Project</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php
                    endwhile;
                else:
                ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No portfolio items found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
