<?php
// Admin page head helper - includes favicon and common meta tags
require_once __DIR__ . '/image-helper.php';
$settings = $conn->query("SELECT favicon_url FROM website_settings LIMIT 1")->fetch_assoc();
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
<?php if ($settings && $settings['favicon_url']): ?>
<link rel="icon" href="<?php echo getImageUrl($settings['favicon_url']); ?>" type="image/x-icon">
<?php endif; ?>
