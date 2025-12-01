<?php
/**
 * Admin Sidebar Navigation
 * Unified sidebar for all admin pages
 */

// Get current page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="col-md-2 d-md-block bg-dark sidebar">
    <div class="position-sticky pt-3">
        <h5 class="text-white px-3 mb-4">
            <i class="fas fa-cog"></i> Admin Panel
        </h5>
        
        <!-- Sidebar Toggle Button (Mobile) -->
        <button class="btn btn-dark d-md-none w-100 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
            <i class="fas fa-bars"></i> Menu
        </button>

        <ul class="nav flex-column" id="sidebarMenu">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            </li>

            <!-- Portfolio -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'portfolio.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>/admin/portfolio.php">
                    <i class="fas fa-briefcase"></i> Portfolio
                </a>
            </li>

            <!-- Services -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'services.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>/admin/services.php">
                    <i class="fas fa-cogs"></i> Services
                </a>
            </li>

            <!-- About -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'about.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>/admin/about.php">
                    <i class="fas fa-user"></i> About
                </a>
            </li>

            <!-- Testimonials -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'testimonials.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>/admin/testimonials.php">
                    <i class="fas fa-star"></i> Testimonials
                </a>
            </li>

            <!-- Messages -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'messages.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>/admin/messages.php">
                    <i class="fas fa-envelope"></i> Messages
                </a>
            </li>

            <!-- Social Links -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'social.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>/admin/social.php">
                    <i class="fas fa-share-alt"></i> Social Links
                </a>
            </li>

            <!-- Custom Icons -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'icons.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>/admin/icons.php">
                    <i class="fas fa-icons"></i> Custom Icons
                </a>
            </li>

            <!-- Divider -->
            <li class="nav-item mt-3 mb-3">
                <hr class="bg-secondary">
            </li>

            <!-- Profile -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>/admin/profile.php">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
            </li>

            <!-- Settings -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'settings.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>/admin/settings.php">
                    <i class="fas fa-sliders-h"></i> Settings
                </a>
            </li>

            <!-- Logs -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'logs.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>/admin/logs.php">
                    <i class="fas fa-file-alt"></i> System Logs
                </a>
            </li>

            <!-- Divider -->
            <li class="nav-item mt-3 mb-3">
                <hr class="bg-secondary">
            </li>

            <!-- Logout -->
            <li class="nav-item">
                <a class="nav-link text-danger" href="<?php echo SITE_URL; ?>/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
.sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    max-height: 100vh;
    overflow-y: auto;
}

.sidebar .nav-link {
    color: #c2c7d0;
    padding: 0.75rem 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.sidebar .nav-link:hover {
    color: #fff;
    background-color: rgba(255, 255, 255, 0.1);
    padding-left: 1.5rem;
}

.sidebar .nav-link.active {
    color: #fff;
    background-color: #667eea;
    border-left: 3px solid #fff;
    padding-left: calc(1rem - 3px);
}

.sidebar .nav-link i {
    margin-right: 0.5rem;
    width: 20px;
    text-align: center;
}

.sidebar hr {
    margin: 0.5rem 0;
    opacity: 0.3;
}

@media (max-width: 768px) {
    .sidebar {
        position: relative;
        padding: 0;
        max-height: none;
    }

    .sidebar .nav-link {
        padding: 0.5rem 1rem;
    }

    #sidebarMenu {
        display: none;
    }

    #sidebarMenu.show {
        display: flex;
    }
}
</style>
