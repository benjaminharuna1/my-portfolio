<?php
require 'config.php';
require 'includes/image-helper.php';
require 'includes/email-config.php';
$page_title = 'Reviews';

// Handle review submission
$review_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    $reviewer_name = $conn->real_escape_string($_POST['reviewer_name']);
    $reviewer_email = $conn->real_escape_string($_POST['reviewer_email']);
    $portfolio_id = intval($_POST['portfolio_id']);
    $rating = intval($_POST['rating']);
    $review_text = $conn->real_escape_string($_POST['review_text']);
    
    if ($reviewer_name && $reviewer_email && $portfolio_id && $rating && $review_text) {
        $conn->query("INSERT INTO portfolio_ratings (portfolio_id, rating, review_text, reviewer_name, reviewer_email, is_approved) VALUES ($portfolio_id, $rating, '$review_text', '$reviewer_name', '$reviewer_email', 0)");
        
        // Load email config
        EmailConfig::load($conn);
        
        // Get portfolio title
        $portfolio = $conn->query("SELECT title FROM portfolio_items WHERE id = $portfolio_id")->fetch_assoc();
        
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
        
        $review_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <strong>Thank you!</strong> Your review has been submitted and will appear after approval.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        $review_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <strong>Error!</strong> Please fill in all fields.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
}

// Get all approved reviews
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$total_reviews = $conn->query("SELECT COUNT(*) as count FROM portfolio_ratings WHERE is_approved = 1")->fetch_assoc()['count'];
$total_pages = ceil($total_reviews / $limit);

$reviews = $conn->query("SELECT pr.*, pi.title as portfolio_title FROM portfolio_ratings pr 
                        JOIN portfolio_items pi ON pr.portfolio_id = pi.id 
                        WHERE pr.is_approved = 1 
                        ORDER BY pr.created_at DESC 
                        LIMIT $limit OFFSET $offset");

// Get portfolio items for the form
$portfolios = $conn->query("SELECT id, title FROM portfolio_items WHERE status = 'published' ORDER BY title");
?>
<?php include 'includes/header.php'; ?>

<main>
    <section class="reviews-section py-5">
        <div class="container">
            <h1 class="text-center mb-5">
                <i class="fas fa-star"></i> Client Reviews
            </h1>
            
            <!-- Review Form -->
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-pen-fancy"></i> Share Your Review
                            </h5>
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

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="portfolio_id" class="form-label">Project *</label>
                                        <select class="form-control" id="portfolio_id" name="portfolio_id" required>
                                            <option value="">Select a project...</option>
                                            <?php 
                                            $portfolios->data_seek(0);
                                            while ($portfolio = $portfolios->fetch_assoc()): 
                                            ?>
                                            <option value="<?php echo $portfolio['id']; ?>">
                                                <?php echo htmlspecialchars($portfolio['title']); ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
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
                                </div>

                                <div class="mb-3">
                                    <label for="review_text" class="form-label">Your Review *</label>
                                    <textarea class="form-control" id="review_text" name="review_text" rows="4" placeholder="Share your thoughts about this project..." required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-paper-plane"></i> Submit Review
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4">
                        <i class="fas fa-comments"></i> All Reviews
                        <span class="badge bg-primary"><?php echo $total_reviews; ?></span>
                    </h2>
                </div>
            </div>

            <?php if ($reviews->num_rows > 0): ?>
            <div class="row">
                <?php while ($review = $reviews->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 review-card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title mb-1">
                                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($review['reviewer_name']); ?>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-project-diagram"></i> <?php echo htmlspecialchars($review['portfolio_title']); ?>
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
                            <p class="card-text review-text"><?php echo nl2br(htmlspecialchars(substr($review['review_text'], 0, 150))); ?><?php echo strlen($review['review_text']) > 150 ? '...' : ''; ?></p>
                            <small class="text-muted d-block mt-3">
                                <i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=1">First</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $total_pages; ?>">Last</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
            <?php else: ?>
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p>No reviews yet. Be the first to share your experience!</p>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<style>
.review-card {
    transition: all 0.3s ease;
    border: none;
    border-top: 4px solid #667eea;
}

.review-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2) !important;
}

.review-text {
    color: #555;
    line-height: 1.6;
    font-size: 0.95rem;
}

.rating-stars {
    display: flex;
    gap: 3px;
}
</style>
