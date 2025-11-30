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
 * Get placeholder image URL
 */
function getPlaceholderImage($text = 'Image', $width = 400, $height = 300) {
    return 'https://via.placeholder.com/' . $width . 'x' . $height . '?text=' . urlencode($text);
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
