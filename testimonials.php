<?php
require 'config.php';
$page_title = 'Testimonials';

// Get active testimonials
$testimonials = $conn->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY is_featured DESC, created_at DESC");
?>
<?php include 'includes/header.php'; ?>

<main>
    <section class="testimonials-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="mb-3">Client Testimonials</h1>
                <p class="lead text-muted">What my clients say about working with me</p>
            </div>

            <!-- Testimonials Grid -->
            <div class="row">
                <?php
                if ($testimonials->num_rows > 0):
                    while ($testimonial = $testimonials->fetch_assoc()):
                ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="testimonial-card">
                            <!-- Rating -->
                            <div class="rating mb-3">
                                <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                                <?php for ($i = $testimonial['rating']; $i < 5; $i++): ?>
                                    <i class="far fa-star"></i>
                                <?php endfor; ?>
                            </div>

                            <!-- Testimonial Text -->
                            <p class="testimonial-text mb-4">
                                "<?php echo htmlspecialchars($testimonial['testimonial_text']); ?>"
                            </p>

                            <!-- Client Info -->
                            <div class="client-info d-flex align-items-center">
                                <?php if (!empty($testimonial['client_image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($testimonial['client_image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($testimonial['client_name']); ?>" 
                                         class="client-avatar me-3">
                                <?php else: ?>
                                    <div class="client-avatar me-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($testimonial['client_name']); ?></h6>
                                    <?php if (!empty($testimonial['client_title'])): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($testimonial['client_title']); ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($testimonial['client_company'])): ?>
                                        <small class="text-muted d-block"><?php echo htmlspecialchars($testimonial['client_company']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    endwhile;
                else:
                ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No testimonials available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<style>
.testimonials-section {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 600px;
}

.testimonial-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.rating {
    color: #ffc107;
    font-size: 1.1rem;
    letter-spacing: 2px;
}

.rating i {
    margin-right: 3px;
}

.testimonial-text {
    font-size: 1rem;
    line-height: 1.8;
    color: #555;
    font-style: italic;
    flex-grow: 1;
}

.client-info {
    border-top: 1px solid #eee;
    padding-top: 20px;
    margin-top: auto;
}

.client-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    background: #667eea;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.client-info h6 {
    font-weight: 600;
    color: #2c3e50;
}

@media (max-width: 768px) {
    .testimonial-card {
        padding: 20px;
    }

    .testimonial-text {
        font-size: 0.95rem;
    }
}
</style>
