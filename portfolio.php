<?php
require 'config.php';
require 'includes/image-helper.php';
require 'includes/icon-helper.php';
$page_title = 'Portfolio';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 12;
$offset = ($page - 1) * $items_per_page;

// Build base query
$where_clause = "status = 'published'";
if ($category) {
    $category = $conn->real_escape_string($category);
    // Get the category name from slug
    $cat_result = $conn->query("SELECT name FROM categories WHERE slug = '$category'");
    if ($cat_result && $cat_result->num_rows > 0) {
        $cat_row = $cat_result->fetch_assoc();
        $cat_name = $conn->real_escape_string($cat_row['name']);
        $where_clause .= " AND category = '$cat_name'";
    }
}

// Get total count
$count_result = $conn->query("SELECT COUNT(*) as total FROM portfolio_items WHERE $where_clause");
$count_row = $count_result->fetch_assoc();
$total_items = $count_row['total'];
$total_pages = ceil($total_items / $items_per_page);

// Ensure page is valid
if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;
$offset = ($page - 1) * $items_per_page;

// Get paginated results
$result = $conn->query("SELECT * FROM portfolio_items WHERE $where_clause ORDER BY id DESC LIMIT $items_per_page OFFSET $offset");
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
                                     alt="<?php echo getImageAlt($item['title'], 'Portfolio Item'); ?>" class="img-fluid rounded portfolio-card-image" loading="lazy">
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

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Portfolio pagination" class="mt-5">
                <ul class="pagination justify-content-center">
                    <!-- Previous Page -->
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo SITE_URL; ?>/portfolio.php<?php echo $category ? '?category=' . urlencode($category) . '&page=' : '?page='; ?>1" aria-label="First">
                                <span aria-hidden="true">&laquo;</span> First
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo SITE_URL; ?>/portfolio.php<?php echo $category ? '?category=' . urlencode($category) . '&page=' : '?page='; ?><?php echo $page - 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">&lsaquo;</span> Previous
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    if ($start_page > 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif;
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                        $active = ($i === $page) ? 'active' : '';
                    ?>
                        <li class="page-item <?php echo $active; ?>">
                            <a class="page-link" href="<?php echo SITE_URL; ?>/portfolio.php<?php echo $category ? '?category=' . urlencode($category) . '&page=' : '?page='; ?><?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor;
                    
                    if ($end_page < $total_pages): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>

                    <!-- Next Page -->
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo SITE_URL; ?>/portfolio.php<?php echo $category ? '?category=' . urlencode($category) . '&page=' : '?page='; ?><?php echo $page + 1; ?>" aria-label="Next">
                                Next <span aria-hidden="true">&rsaquo;</span>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo SITE_URL; ?>/portfolio.php<?php echo $category ? '?category=' . urlencode($category) . '&page=' : '?page='; ?><?php echo $total_pages; ?>" aria-label="Last">
                                Last <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
