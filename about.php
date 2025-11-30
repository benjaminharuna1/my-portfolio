<?php
require 'config.php';
$page_title = 'About';
$about = $conn->query("SELECT * FROM about LIMIT 1")->fetch_assoc();
?>
<?php include 'includes/header.php'; ?>

<main>
    <section class="about-section py-5">
        <div class="container">
            <h1 class="text-center mb-5">About Me</h1>
            
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="<?php echo htmlspecialchars($about['image_url'] ?: 'https://via.placeholder.com/500x600?text=About+Image'); ?>" 
                         alt="About" class="img-fluid rounded">
                </div>
                <div class="col-lg-6">
                    <h2><?php echo htmlspecialchars($about['subtitle']); ?></h2>
                    <hr>
                    <p class="lead"><?php echo nl2br(htmlspecialchars($about['description'])); ?></p>
                    
                    <h5 class="mt-4">Let's talk with me.</h5>
                    <h6><a href="mailto:<?php echo htmlspecialchars($about['email']); ?>"><?php echo htmlspecialchars($about['email']); ?></a></h6>
                    
                    <div class="mt-4">
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($about['phone']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($about['location']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section class="skills-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Skills</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="skill-item mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Web Design</span>
                            <span>90%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 90%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="skill-item mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Web Development</span>
                            <span>85%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="skill-item mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>UI/UX Design</span>
                            <span>88%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 88%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="skill-item mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>PHP & MySQL</span>
                            <span>92%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 92%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
