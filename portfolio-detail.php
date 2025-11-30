<?php
require 'config.php';
$page_title = 'Portfolio Detail';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$portfolio = $conn->query("SELECT * FROM portfolio_items WHERE id = $id")->fetch_assoc();

if (!$portfolio) {
    header('Location: ' . SITE_URL . '/portfolio.php');
    exit;
}

$images = $conn->query("SELECT * FROM portfolio_images WHERE portfolio_id = $id ORDER BY sort_order");
?>
<?php include 'includes/header.php'; ?>

<main>
    <section class="portfolio-detail-section py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8">
                    <!-- Image Gallery -->
                    <div class="portfolio-gallery mb-5">
                        <div class="main-image-container mb-3">
                            <img id="mainImage" src="<?php echo htmlspecialchars($portfolio['featured_image_url']); ?>" alt="<?php echo htmlspecialchars($portfolio['title']); ?>" class="img-fluid rounded" style="width: 100%; max-height: 600px; object-fit: cover;">
                        </div>

                        <!-- Thumbnail Gallery (Shopify/AliExpress Style) -->
                        <?php if ($images->num_rows > 0): ?>
                        <div class="thumbnail-gallery">
                            <div class="thumbnail-item active" onclick="changeImage('<?php echo htmlspecialchars($portfolio['featured_image_url']); ?>')">
                                <img src="<?php echo htmlspecialchars($portfolio['featured_image_url']); ?>" alt="Featured" class="thumbnail-img">
                            </div>
                            <?php
                            $images->data_seek(0);
                            while ($img = $images->fetch_assoc()):
                            ?>
                            <div class="thumbnail-item" onclick="changeImage('<?php echo htmlspecialchars($img['image_url']); ?>')">
                                <img src="<?php echo htmlspecialchars($img['image_url']); ?>" alt="<?php echo htmlspecialchars($img['alt_text']); ?>" class="thumbnail-img">
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="portfolio-info">
                        <h1 class="mb-3"><?php echo htmlspecialchars($portfolio['title']); ?></h1>
                        
                        <?php if ($portfolio['category']): ?>
                        <p class="text-muted mb-3">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($portfolio['category']); ?></span>
                        </p>
                        <?php endif; ?>

                        <p class="lead mb-4"><?php echo htmlspecialchars($portfolio['description']); ?></p>

                        <?php if ($portfolio['link']): ?>
                        <a href="<?php echo htmlspecialchars($portfolio['link']); ?>" class="btn btn-primary btn-lg mb-4" target="_blank">
                            <i class="fas fa-external-link-alt"></i> View Project
                        </a>
                        <?php endif; ?>

                        <div class="portfolio-meta">
                            <p><strong>Created:</strong> <?php echo date('M d, Y', strtotime($portfolio['created_at'])); ?></p>
                            <p><strong>Last Updated:</strong> <?php echo date('M d, Y', strtotime($portfolio['updated_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Description -->
            <?php if ($portfolio['body']): ?>
            <div class="row mb-5">
                <div class="col-lg-8">
                    <div class="portfolio-body">
                        <h2 class="mb-4">Project Details</h2>
                        <div class="content">
                            <?php echo $portfolio['body']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Back to Portfolio -->
            <div class="row">
                <div class="col-lg-8">
                    <a href="<?php echo SITE_URL; ?>/portfolio.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to Portfolio
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<style>
.portfolio-gallery {
    background: white;
    border-radius: 10px;
    overflow: hidden;
}

.main-image-container {
    position: relative;
    background: #f5f5f5;
    border-radius: 10px;
    overflow: hidden;
}

.main-image-container img {
    transition: transform 0.3s ease;
    cursor: zoom-in;
}

.main-image-container img:hover {
    transform: scale(1.05);
}

.thumbnail-gallery {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 10px 0;
}

.thumbnail-item {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border: 2px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
}

.thumbnail-item:hover {
    border-color: #667eea;
    transform: scale(1.05);
}

.thumbnail-item.active {
    border-color: #667eea;
    box-shadow: 0 0 10px rgba(102, 126, 234, 0.3);
}

.thumbnail-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.portfolio-info {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
}

.portfolio-info h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 20px;
}

.portfolio-meta {
    border-top: 1px solid #ddd;
    padding-top: 20px;
    margin-top: 20px;
}

.portfolio-meta p {
    margin-bottom: 10px;
    color: #666;
}

.portfolio-body {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.portfolio-body h2 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: #2c3e50;
}

.portfolio-body .content {
    line-height: 1.8;
    color: #333;
}

.portfolio-body .content h3 {
    font-size: 1.3rem;
    font-weight: 600;
    margin-top: 30px;
    margin-bottom: 15px;
    color: #2c3e50;
}

.portfolio-body .content p {
    margin-bottom: 15px;
}

.portfolio-body .content ul,
.portfolio-body .content ol {
    margin-bottom: 15px;
    margin-left: 20px;
}

.portfolio-body .content li {
    margin-bottom: 8px;
}

.portfolio-body .content img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
    margin: 20px 0;
}

.portfolio-body .content blockquote {
    border-left: 4px solid #667eea;
    padding-left: 20px;
    margin: 20px 0;
    font-style: italic;
    color: #666;
}

.portfolio-body .content code {
    background: #f5f5f5;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}

.portfolio-body .content pre {
    background: #2c3e50;
    color: #ecf0f1;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
    margin: 20px 0;
}

.portfolio-body .content pre code {
    background: none;
    padding: 0;
    color: inherit;
}

@media (max-width: 768px) {
    .portfolio-info {
        margin-top: 30px;
    }

    .portfolio-info h1 {
        font-size: 1.5rem;
    }

    .thumbnail-item {
        width: 70px;
        height: 70px;
    }

    .main-image-container img {
        max-height: 400px;
    }
}
</style>

<script>
function changeImage(imageUrl) {
    const mainImage = document.getElementById('mainImage');
    mainImage.src = imageUrl;
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        item.classList.remove('active');
    });
    event.target.closest('.thumbnail-item').classList.add('active');
}

// Zoom functionality
document.getElementById('mainImage').addEventListener('click', function() {
    if (this.style.transform === 'scale(1.5)') {
        this.style.transform = 'scale(1)';
    } else {
        this.style.transform = 'scale(1.5)';
    }
});
</script>
