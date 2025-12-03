<?php
require 'config.php';
require 'includes/image-helper.php';
require 'includes/icon-helper.php';
$page_title = 'Portfolio';
$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($category) {
    $category = $conn->real_escape_string($category);
    // Get the category name from slug
    $cat_result = $conn->query("SELECT name FROM categories WHERE slug = '$category'");
    if ($cat_result && $cat_result->num_rows > 0) {
        $cat_row = $cat_result->fetch_assoc();
        $cat_name = $conn->real_escape_string($cat_row['name']);
        $result = $conn->query("SELECT * FROM portfolio_items WHERE category = '$cat_name' AND status = 'published' ORDER BY id DESC");
    } else {
        $result = $conn->query("SELECT * FROM portfolio_items WHERE status = 'published' ORDER BY id DESC");
    }
} else {
    $result = $conn->query("SELECT * FROM portfolio_items WHERE status = 'published' ORDER BY id DESC");
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
                <?php
                $categories = $conn->query("SELECT id, name, slug, icon FROM categories ORDER BY name");
                while ($cat = $categories->fetch_assoc()):
                    $cat_name = htmlspecialchars($cat['name']);
                    $cat_slug = htmlspecialchars($cat['slug']);
                ?>
                <a href="<?php echo SITE_URL; ?>/portfolio.php?category=<?php echo urlencode($cat_slug); ?>" class="btn btn-outline-primary me-2">
                    <?php if (!empty($cat['icon'])): ?>
                        <?php echo icon($cat['icon'], '', 'fa-folder'); ?>
                    <?php else: ?>
                        <i class="fas fa-folder"></i>
                    <?php endif; ?>
                    <?php echo $cat_name; ?>
                </a>
                <?php endwhile; ?>
            </div>

            <!-- Portfolio Grid -->
            <div class="row">
                <?php
                if ($result->num_rows > 0):
                    while ($item = $result->fetch_assoc()):
                ?>
                    <div class="col-md-4 mb-4">
                        <a href="<?php echo SITE_URL; ?>/portfolio-detail.php?id=<?php echo $item['id']; ?>" class="portfolio-card-link">
                            <div class="portfolio-card">
                                <?php 
                                $image_url = getImageWithFallback($item['featured_image_url'], $item['title'] ?? 'Portfolio', 400, 300);
                                ?>
                                <img src="<?php echo $image_url; ?>" 
                                     alt="<?php echo getImageAlt($item['title'], 'Portfolio Item'); ?>" class="img-fluid rounded portfolio-card-image">
                                <div class="portfolio-overlay">
                                    <h5><?php echo htmlspecialchars($item['title'] ?? 'Untitled'); ?></h5>
                                    <p><?php echo htmlspecialchars(substr($item['description'] ?? '', 0, 100)); ?>...</p>
                                    <span class="btn btn-sm btn-light">View Details</span>
                                </div>
                            </div>
                        </a>
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
