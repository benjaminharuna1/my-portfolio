<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <?php
    // Get website settings for favicon
    $settings = $conn->query("SELECT favicon_url FROM website_settings LIMIT 1")->fetch_assoc();
    if ($settings && $settings['favicon_url']):
    ?>
    <link rel="icon" href="<?php echo htmlspecialchars($settings['favicon_url']); ?>" type="image/x-icon">
    <?php endif; ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);">
        <div class="container">
            <?php
            $settings = $conn->query("SELECT logo_url FROM website_settings LIMIT 1")->fetch_assoc();
            if ($settings && $settings['logo_url']):
            ?>
            <a class="navbar-brand fw-bold" href="<?php echo SITE_URL; ?>" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                <img src="<?php echo htmlspecialchars($settings['logo_url']); ?>" alt="<?php echo SITE_NAME; ?>" style="height: 40px; margin-right: 10px;">
                <?php echo SITE_NAME; ?>
            </a>
            <?php else: ?>
            <a class="navbar-brand fw-bold" href="<?php echo SITE_URL; ?>" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                <i class="fas fa-code" style="color: #667eea;"></i> <?php echo SITE_NAME; ?>
            </a>
            <?php endif; ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/index.php" style="transition: all 0.3s ease;">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/about.php" style="transition: all 0.3s ease;">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/portfolio.php" style="transition: all 0.3s ease;">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/contact.php" style="transition: all 0.3s ease;">Contact</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/dashboard.php" style="transition: all 0.3s ease;">Admin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/logout.php" style="transition: all 0.3s ease;">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary btn-sm text-white ms-2" href="<?php echo SITE_URL; ?>/login.php" style="transition: all 0.3s ease;">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html>
