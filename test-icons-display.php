<?php
require 'config.php';
require 'includes/icon-helper.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Icon Display Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Icon Display Test</h1>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <h3>Font Awesome Icons</h3>
                <p>
                    <strong>fa-palette:</strong> 
                    <?php echo displayIcon('fa-palette', ['size' => 32, 'color' => '#667eea'], 'fa-palette'); ?>
                </p>
                <p>
                    <strong>fa-code:</strong> 
                    <?php echo displayIcon('fa-code', ['size' => 32, 'color' => '#667eea'], 'fa-code'); ?>
                </p>
                <p>
                    <strong>fab fa-php:</strong> 
                    <?php echo displayIcon('fab fa-php', ['size' => 32], 'fab fa-php'); ?>
                </p>
            </div>
            
            <div class="col-md-6">
                <h3>Services from Database</h3>
                <?php
                $services = $conn->query("SELECT * FROM services LIMIT 3");
                while ($service = $services->fetch_assoc()):
                ?>
                <div class="mb-3">
                    <strong><?php echo htmlspecialchars($service['title']); ?></strong><br>
                    Icon name: <code><?php echo htmlspecialchars($service['icon']); ?></code><br>
                    Display: 
                    <?php echo displayIcon($service['icon'], ['size' => 32, 'color' => '#667eea'], 'fa-' . strtolower(str_replace(' ', '-', $service['title']))); ?>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <h3>Tech Icons Test</h3>
                <?php
                $service = $conn->query("SELECT tech_icons FROM services LIMIT 1")->fetch_assoc();
                if ($service && $service['tech_icons']):
                    $icons = array_map('trim', explode(',', $service['tech_icons']));
                    foreach ($icons as $icon):
                        if ($icon):
                            $icon_class = $icon;
                            if (strpos($icon, 'fa-') === 0 && strpos($icon, 'fab ') !== 0 && strpos($icon, 'fas ') !== 0) {
                                $icon_class = 'fas ' . $icon;
                            }
                ?>
                    <span style="font-size: 2rem; margin-right: 10px;">
                        <i class="<?php echo htmlspecialchars($icon_class); ?>"></i>
                    </span>
                <?php 
                        endif;
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
</body>
</html>
