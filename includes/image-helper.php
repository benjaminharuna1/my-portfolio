<?php
/**
 * Image Helper Functions
 * Ensures consistent image URL handling across public and admin pages
 */

/**
 * Get a properly formatted image URL
 * Handles both uploaded images and external URLs
 */
function getImageUrl($image_url) {
    if (empty($image_url)) {
        return '';
    }
    
    // If it's already a full URL (starts with http:// or https://), return as-is
    if (strpos($image_url, 'http://') === 0 || strpos($image_url, 'https://') === 0) {
        return htmlspecialchars($image_url);
    }
    
    // If it's a relative path starting with /uploads/, prepend SITE_URL
    if (strpos($image_url, '/uploads/') === 0) {
        return htmlspecialchars(SITE_URL . $image_url);
    }
    
    // If it's just a filename, assume it's in uploads folder
    if (strpos($image_url, '/') === false) {
        return htmlspecialchars(SITE_URL . '/uploads/' . $image_url);
    }
    
    // Default: prepend SITE_URL
    return htmlspecialchars(SITE_URL . '/' . ltrim($image_url, '/'));
}

/**
 * Get image alt text with fallback
 */
function getImageAlt($alt_text, $fallback = 'Image') {
    return htmlspecialchars(!empty($alt_text) ? $alt_text : $fallback);
}

/**
 * Validate if an image URL is accessible
 */
function isImageAccessible($image_url) {
    if (empty($image_url)) {
        return false;
    }
    
    // Extract filename from URL
    $filename = basename($image_url);
    $file_path = dirname(__DIR__) . '/uploads/' . $filename;
    
    return file_exists($file_path);
}

/**
 * Get custom SVG placeholder image
 */
function getPlaceholderImage($text = 'Image', $width = 400, $height = 300) {
    // Create a custom SVG placeholder
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">';
    $svg .= '<defs>';
    $svg .= '<linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">';
    $svg .= '<stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />';
    $svg .= '<stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />';
    $svg .= '</linearGradient>';
    $svg .= '</defs>';
    $svg .= '<rect width="' . $width . '" height="' . $height . '" fill="url(#grad)"/>';
    $svg .= '<g>';
    $svg .= '<circle cx="' . ($width / 2) . '" cy="' . ($height / 2 - 30) . '" r="40" fill="rgba(255,255,255,0.3)"/>';
    $svg .= '<path d="M ' . ($width / 2 - 50) . ' ' . ($height / 2 + 20) . ' L ' . ($width / 2) . ' ' . ($height / 2 - 20) . ' L ' . ($width / 2 + 50) . ' ' . ($height / 2 + 20) . ' Z" fill="rgba(255,255,255,0.3)"/>';
    $svg .= '</g>';
    $svg .= '<text x="50%" y="50%" font-family="Arial, sans-serif" font-size="18" fill="rgba(255,255,255,0.7)" text-anchor="middle" dominant-baseline="middle">';
    $svg .= htmlspecialchars($text);
    $svg .= '</text>';
    $svg .= '</svg>';
    
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

/**
 * Get image with fallback to placeholder
 */
function getImageWithFallback($image_url, $fallback_text = 'Portfolio Item', $width = 400, $height = 300) {
    if (!empty($image_url)) {
        return getImageUrl($image_url);
    }
    return getPlaceholderImage($fallback_text, $width, $height);
}
?>
