    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><?php echo SITE_NAME; ?></h5>
                    <p>Creating beautiful and functional web experiences.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>/index.php" class="text-white-50">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about.php" class="text-white-50">About</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/portfolio.php" class="text-white-50">Portfolio</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php" class="text-white-50">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Follow Me</h5>
                    <div class="social-links">
                        <?php
                        $result = $conn->query("SELECT * FROM social_links");
                        while ($social = $result->fetch_assoc()):
                        ?>
                            <a href="<?php echo htmlspecialchars($social['url']); ?>" class="text-white-50 me-3" target="_blank">
                                <i class="<?php echo htmlspecialchars($social['icon']); ?>"></i>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <hr class="bg-white-50">
            <div class="text-center text-white-50">
                <p>&copy; <?php echo date('Y'); ?> <a href="https://censonotech.com.ng">Censono Tech</a> - <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>
</html>
