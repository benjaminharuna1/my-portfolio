<?php
require 'config.php';
require 'includes/image-helper.php';
require 'includes/email-config.php';
$page_title = 'Portfolio Detail';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$portfolio = $conn->query("SELECT * FROM portfolio_items WHERE id = $id AND status = 'published'")->fetch_assoc();

if (!$portfolio) {
    header('Location: ' . SITE_URL . '/portfolio.php');
    exit;
}

$images = $conn->query("SELECT * FROM portfolio_images WHERE portfolio_id = $id ORDER BY sort_order");

// Handle review submission
$review_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    $reviewer_name = $conn->real_escape_string($_POST['reviewer_name']);
    $reviewer_email = $conn->real_escape_string($_POST['reviewer_email']);
    $rating = intval($_POST['rating']);
    $review_text = $conn->real_escape_string($_POST['review_text']);
    
    if ($reviewer_name && $reviewer_email && $rating && $review_text) {
        $conn->query("INSERT INTO portfolio_ratings (portfolio_id, rating, review_text, reviewer_name, reviewer_email, is_approved) VALUES ($id, $rating, '$review_text', '$reviewer_name', '$reviewer_email', 0)");
        
        // Load email config
        EmailConfig::load($conn);
        
        // Send notification to admin
        if (EmailConfig::get('enable_notifications') && EmailConfig::get('admin_email')) {
            $admin_email_data = EmailTemplate::reviewNotificationAdmin($reviewer_name, $rating, $review_text, $portfolio['title']);
            EmailConfig::sendEmail(
                EmailConfig::get('admin_email'),
                $admin_email_data['subject'],
                $admin_email_data['body'],
                true
            );
        }
        
        // Send confirmation to reviewer
        $reviewer_email_data = EmailTemplate::reviewConfirmation($reviewer_name, $portfolio['title']);
        EmailConfig::sendEmail(
            $reviewer_email,
            $reviewer_email_data['subject'],
            $reviewer_email_data['body'],
            true
        );
        
        $review_message = '<div class="alert alert-success">Thank you for your review! It will be displayed after approval.</div>';
    } else {
        $review_message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    }
}
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
                            <?php $main_image = getImageWithFallback($portfolio['featured_image_url'], $portfolio['title'] ?? 'Portfolio Item', 800, 600); ?>
                            <img id="mainImage" src="<?php echo $main_image; ?>" alt="<?php echo getImageAlt($portfolio['title'], 'Portfolio Item'); ?>" class="img-fluid rounded" style="width: 100%; max-height: 600px; object-fit: cover; cursor: zoom-in;" ondblclick="openLightbox(0)">
                            <div class="image-counter">
                                <span id="currentImageNum">1</span> / <span id="totalImageNum"><?php echo ($images->num_rows + 1); ?></span>
                            </div>
                        </div>

                        <!-- Thumbnail Gallery (Shopify/AliExpress Style) -->
                        <?php if ($images && $images->num_rows > 0): ?>
                        <div class="thumbnail-gallery">
                            <?php if (!empty($portfolio['featured_image_url'])): ?>
                            <div class="thumbnail-item" onclick="changeImage('<?php echo getImageUrl($portfolio['featured_image_url']); ?>', 1)" ondblclick="openLightbox(0)">
                                <img src="<?php echo getImageUrl($portfolio['featured_image_url']); ?>" alt="Featured" class="thumbnail-img">
                            </div>
                            <?php endif; ?>
                            <?php
                            $images->data_seek(0);
                            $index = 2;
                            while ($img = $images->fetch_assoc()):
                                if (!empty($img['image_url'])):
                            ?>
                            <div class="thumbnail-item" onclick="changeImage('<?php echo getImageUrl($img['image_url']); ?>', <?php echo $index; ?>)" ondblclick="openLightbox(<?php echo $index - 1; ?>)">
                                <img src="<?php echo getImageUrl($img['image_url']); ?>" alt="<?php echo getImageAlt($img['alt_text'], 'Gallery Image'); ?>" class="thumbnail-img">
                            </div>
                            <?php 
                                endif;
                                $index++; 
                            endwhile; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="portfolio-info">
                        <h1 class="mb-3"><?php echo htmlspecialchars($portfolio['title'] ?? 'Portfolio Item'); ?></h1>
                        
                        <?php if (!empty($portfolio['category'])): ?>
                        <p class="text-muted mb-3">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($portfolio['category']); ?></span>
                        </p>
                        <?php endif; ?>

                        <?php if (!empty($portfolio['description'])): ?>
                        <p class="lead mb-4"><?php echo htmlspecialchars($portfolio['description']); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($portfolio['link'])): ?>
                        <a href="<?php echo htmlspecialchars($portfolio['link']); ?>" class="btn btn-primary btn-lg mb-4" target="_blank">
                            <i class="fas fa-external-link-alt"></i> View Project
                        </a>
                        <?php endif; ?>

                        <div class="portfolio-meta">
                            <?php if (!empty($portfolio['created_at'])): ?>
                            <p><strong>Created:</strong> <?php echo date('M d, Y', strtotime($portfolio['created_at'])); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($portfolio['updated_at'])): ?>
                            <p><strong>Last Updated:</strong> <?php echo date('M d, Y', strtotime($portfolio['updated_at'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Description -->
            <?php if (!empty($portfolio['body'])): ?>
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

            <!-- Review Section -->
            <div class="row mt-5">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Leave a Review</h5>
                        </div>
                        <div class="card-body">
                            <?php echo $review_message; ?>
                            
                            <!-- Privacy Notice -->
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="fas fa-shield-alt"></i> <strong>Your Privacy Matters</strong>
                                <p class="mb-0" style="margin-top: 8px; font-size: 0.9rem;">Your email address will never be shared with third parties or displayed publicly. Only your name will appear with your review. We respect your privacy.</p>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            
                            <form method="POST">
                                <input type="hidden" name="action" value="submit_review">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="reviewer_name" class="form-label">Your Name *</label>
                                        <input type="text" class="form-control" id="reviewer_name" name="reviewer_name" placeholder="Your name (will be visible)" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="reviewer_email" class="form-label">Your Email *</label>
                                        <input type="email" class="form-control" id="reviewer_email" name="reviewer_email" placeholder="Your email (kept private)" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="rating" class="form-label">Rating *</label>
                                    <select class="form-control" id="rating" name="rating" required>
                                        <option value="">Select a rating</option>
                                        <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                        <option value="4">⭐⭐⭐⭐ Good</option>
                                        <option value="3">⭐⭐⭐ Average</option>
                                        <option value="2">⭐⭐ Fair</option>
                                        <option value="1">⭐ Poor</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="review_text" class="form-label">Your Review *</label>
                                    <textarea class="form-control" id="review_text" name="review_text" rows="4" placeholder="Share your thoughts about this project..." required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Review
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Approved Reviews -->
                    <?php
                    $reviews = $conn->query("SELECT id, reviewer_name, rating, review_text, created_at FROM portfolio_ratings WHERE portfolio_id = $id AND is_approved = 1 ORDER BY created_at DESC");
                    if ($reviews && $reviews->num_rows > 0):
                    ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5>
                                <i class="fas fa-comments"></i> Reviews
                                <span class="badge bg-primary"><?php echo $reviews->num_rows; ?></span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php while ($review = $reviews->fetch_assoc()): ?>
                            <div class="review-item mb-4 pb-4 border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0">
                                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($review['reviewer_name']); ?>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="rating-stars">
                                        <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                            <i class="fas fa-star" style="color: #ffc107;"></i>
                                        <?php endfor; ?>
                                        <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                                            <i class="far fa-star" style="color: #ddd;"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="review-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section py-5">
        <div class="container">
            <div class="cta-content text-center">
                <h2 class="cta-title mb-3">Interested in This Project?</h2>
                <p class="cta-subtitle mb-4">Let's discuss how I can help bring your ideas to life. Contact me today to start your next project.</p>
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-light btn-lg cta-button">
                    <i class="fas fa-envelope"></i> Get In Touch Today
                </a>
            </div>
        </div>
    </section>

    <!-- Image Lightbox Modal -->
    <div id="imageLightbox" class="lightbox-modal">
        <div class="lightbox-container">
            <!-- Close Button -->
            <button class="lightbox-close" onclick="closeLightbox()" title="Close (Esc)">
                <i class="fas fa-times"></i>
            </button>

            <!-- Image Counter -->
            <div class="lightbox-counter" id="lightboxCounter">1 / 1</div>

            <!-- Main Image -->
            <div class="lightbox-image-wrapper">
                <img id="lightboxImage" src="" alt="Gallery Image" class="lightbox-image">
            </div>

            <!-- Navigation Arrows -->
            <button class="lightbox-nav lightbox-prev" onclick="prevImage()" title="Previous (←)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="lightbox-nav lightbox-next" onclick="nextImage()" title="Next (→)">
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Controls -->
            <div class="lightbox-controls">
                <button class="lightbox-btn" onclick="zoomOut()" title="Zoom Out (-)">
                    <i class="fas fa-minus"></i>
                </button>
                <button class="lightbox-btn" onclick="resetZoom()" title="Reset Zoom (0)">
                    <i class="fas fa-expand"></i>
                </button>
                <button class="lightbox-btn" onclick="zoomIn()" title="Zoom In (+)">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <!-- Info -->
            <div class="lightbox-info">
                <small>💡 Swipe to navigate • Arrow keys to move • +/- to zoom • 0 to reset • Esc to close</small>
            </div>
        </div>
    </div>

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

.image-counter {
    position: absolute;
    bottom: 15px;
    right: 15px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 0.9rem;
    font-weight: 600;
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

/* Lightbox Styles */
.lightbox-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.lightbox-container {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.lightbox-image-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

.lightbox-image {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    transition: transform 0.3s ease;
    cursor: grab;
}

.lightbox-image:active {
    cursor: grabbing;
}

.lightbox-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 28px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10000;
}

.lightbox-close:hover {
    background: rgba(255, 255, 255, 0.4);
    transform: scale(1.1);
}

.lightbox-counter {
    position: absolute;
    top: 20px;
    left: 20px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    font-size: 0.9rem;
    font-weight: 600;
    z-index: 10000;
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 24px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10000;
}

.lightbox-nav:hover {
    background: rgba(255, 255, 255, 0.4);
    transform: translateY(-50%) scale(1.1);
}

.lightbox-prev {
    left: 20px;
}

.lightbox-next {
    right: 20px;
}

.lightbox-controls {
    position: absolute;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    background: rgba(0, 0, 0, 0.5);
    padding: 15px;
    border-radius: 50px;
    z-index: 10000;
}

.lightbox-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 16px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.lightbox-btn:hover {
    background: rgba(255, 255, 255, 0.4);
    transform: scale(1.1);
}

.lightbox-info {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    color: rgba(255, 255, 255, 0.7);
    text-align: center;
    font-size: 0.85rem;
    z-index: 10000;
}

@media (max-width: 768px) {
    .lightbox-close,
    .lightbox-nav {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }

    .lightbox-prev {
        left: 10px;
    }

    .lightbox-next {
        right: 10px;
    }

    .lightbox-counter {
        top: 10px;
        left: 10px;
        font-size: 0.8rem;
    }

    .lightbox-controls {
        bottom: 60px;
        padding: 10px;
        gap: 8px;
    }

    .lightbox-btn {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }

    .lightbox-info {
        font-size: 0.75rem;
        bottom: 10px;
    }
}
</style>

<script>
// Gallery images array
const galleryImages = [
    '<?php echo getImageUrl($portfolio['featured_image_url']); ?>'
    <?php 
    $images->data_seek(0);
    while ($img = $images->fetch_assoc()):
        if (!empty($img['image_url'])):
    ?>
    , '<?php echo getImageUrl($img['image_url']); ?>'
    <?php 
        endif;
    endwhile; 
    ?>
];

let currentLightboxIndex = 0;
let lightboxZoom = 1;
let touchStartX = 0;
let touchEndX = 0;

function changeImage(imageUrl, imageNum) {
    const mainImage = document.getElementById('mainImage');
    mainImage.src = imageUrl;
    
    // Reset zoom when changing image
    mainImage.style.transform = 'scale(1)';
    
    // Update image counter
    const currentNumEl = document.getElementById('currentImageNum');
    if (currentNumEl) {
        currentNumEl.textContent = imageNum;
    }
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Find and mark the clicked thumbnail as active
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        const img = item.querySelector('.thumbnail-img');
        if (img && img.src === imageUrl) {
            item.classList.add('active');
        }
    });
}

function openLightbox(index) {
    currentLightboxIndex = index;
    lightboxZoom = 1;
    const lightbox = document.getElementById('imageLightbox');
    const lightboxImg = document.getElementById('lightboxImage');
    
    lightboxImg.src = galleryImages[index];
    lightbox.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    updateLightboxCounter();
}

function closeLightbox() {
    const lightbox = document.getElementById('imageLightbox');
    lightbox.style.display = 'none';
    document.body.style.overflow = 'auto';
    lightboxZoom = 1;
}

function updateLightboxCounter() {
    const counter = document.getElementById('lightboxCounter');
    if (counter) {
        counter.textContent = (currentLightboxIndex + 1) + ' / ' + galleryImages.length;
    }
}

function nextImage() {
    currentLightboxIndex = (currentLightboxIndex + 1) % galleryImages.length;
    lightboxZoom = 1;
    const lightboxImg = document.getElementById('lightboxImage');
    lightboxImg.src = galleryImages[currentLightboxIndex];
    lightboxImg.style.transform = 'scale(1)';
    updateLightboxCounter();
}

function prevImage() {
    currentLightboxIndex = (currentLightboxIndex - 1 + galleryImages.length) % galleryImages.length;
    lightboxZoom = 1;
    const lightboxImg = document.getElementById('lightboxImage');
    lightboxImg.src = galleryImages[currentLightboxIndex];
    lightboxImg.style.transform = 'scale(1)';
    updateLightboxCounter();
}

function zoomIn() {
    lightboxZoom = Math.min(lightboxZoom + 0.2, 3);
    const lightboxImg = document.getElementById('lightboxImage');
    lightboxImg.style.transform = 'scale(' + lightboxZoom + ')';
}

function zoomOut() {
    lightboxZoom = Math.max(lightboxZoom - 0.2, 1);
    const lightboxImg = document.getElementById('lightboxImage');
    lightboxImg.style.transform = 'scale(' + lightboxZoom + ')';
}

function resetZoom() {
    lightboxZoom = 1;
    const lightboxImg = document.getElementById('lightboxImage');
    lightboxImg.style.transform = 'scale(1)';
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const lightbox = document.getElementById('imageLightbox');
    if (lightbox.style.display === 'flex') {
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') prevImage();
        if (e.key === 'Escape') closeLightbox();
        if (e.key === '+' || e.key === '=') zoomIn();
        if (e.key === '-') zoomOut();
        if (e.key === '0') resetZoom();
    }
});

// Touch swipe support
document.addEventListener('touchstart', function(e) {
    const lightbox = document.getElementById('imageLightbox');
    if (lightbox.style.display === 'flex') {
        touchStartX = e.changedTouches[0].screenX;
    }
}, false);

document.addEventListener('touchend', function(e) {
    const lightbox = document.getElementById('imageLightbox');
    if (lightbox.style.display === 'flex') {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }
}, false);

function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            nextImage();
        } else {
            prevImage();
        }
    }
}

// Initialize first thumbnail as active
document.addEventListener('DOMContentLoaded', function() {
    const firstThumbnail = document.querySelector('.thumbnail-item');
    if (firstThumbnail) {
        firstThumbnail.classList.add('active');
    }
});
</script>
