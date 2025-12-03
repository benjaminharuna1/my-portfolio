<?php
/**
 * Icon Helper Functions
 * Provides utilities for displaying custom SVG icons and Font Awesome icons
 * 
 * Unified class system: all icons now get the 'icon' class for consistent styling
 */

/**
 * Display an icon with automatic ci- prefix detection
 * @param string $name Icon name (with ci- or fa- prefix)
 * @param string $class Additional CSS classes to apply
 * @param string $fallback Fallback Font Awesome icon if custom not found
 * @return string HTML for the icon
 */
function icon($name, $class = '', $fallback = '') {
    // Merge 'icon' base class with any custom classes
    $class = trim('icon ' . $class);
    
    // If it's a custom SVG icon (ci- prefix)
    if (strpos($name, 'ci-') === 0) {
        $svg = getCustomIconSVG($name, $class);
        if ($svg) {
            return $svg;
        }
        // If custom icon not found, use fallback
        if ($fallback) {
            return fontAwesomeIcon($fallback, $class);
        }
        return '';
    }
    
    // Otherwise use Font Awesome
    return fontAwesomeIcon($name, $class);
}

/**
 * Get custom SVG icon from database
 * @param string $name Icon name with ci- prefix
 * @param string $class CSS classes to apply
 * @return string SVG HTML or empty string
 */
function getCustomIconSVG($name, $class = '') {
    global $conn;
    
    // Check if custom_icons table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'custom_icons'");
    if (!$table_check || $table_check->num_rows === 0) {
        return '';
    }
    
    // Remove 'ci-' prefix to get the actual icon name
    $icon_name = substr($name, 3);
    $icon_name = $conn->real_escape_string($icon_name);
    
    // Try to find icon by name or slug
    $query = $conn->query("SELECT svg_content FROM custom_icons WHERE name = '$icon_name' OR slug = '$icon_name' LIMIT 1");
    
    if ($query && $query->num_rows > 0) {
        $icon = $query->fetch_assoc();
        $svg_content = $icon['svg_content'];
        
        if (empty($svg_content)) {
            return '';
        }
        
        // Ensure SVG uses currentColor for consistency
        if (!preg_match('/fill=/i', $svg_content)) {
            $svg_content = preg_replace('/<svg /i', '<svg class="' . htmlspecialchars($class) . '" fill="currentColor" ', $svg_content, 1);
        } else {
            $svg_content = preg_replace('/<svg /i', '<svg class="' . htmlspecialchars($class) . '" ', $svg_content, 1);
        }
        
        return $svg_content;
    }
    
    return '';
}

/**
 * Display Font Awesome icon
 * @param string $name Icon name (with or without fa- prefix)
 * @param string $class CSS classes to apply
 * @return string HTML for the icon
 */
function fontAwesomeIcon($name, $class = '') {
    // Build Font Awesome class
    $fa_class = '';
    
    if (strpos($name, 'fab ') === 0 || strpos($name, 'fas ') === 0 || strpos($name, 'far ') === 0) {
        $fa_class = $name;
    } elseif (strpos($name, 'fa-') === 0) {
        $fa_class = 'fas ' . $name;
    } else {
        $fa_class = 'fas fa-' . $name;
    }
    
    if (!empty($class)) {
        $fa_class .= ' ' . $class;
    }
    
    return '<i class="' . htmlspecialchars($fa_class) . '"></i>';
}

/**
 * Legacy function - use icon() instead
 */
function displayIcon($name, $options = [], $fallback = '') {
    $class = isset($options['class']) ? $options['class'] : '';
    $style = isset($options['color']) ? 'style="color: ' . htmlspecialchars($options['color']) . ';"' : '';
    
    $icon_html = icon($name, $class, $fallback);
    
    if (!empty($style) && strpos($icon_html, '<i ') === 0) {
        $icon_html = str_replace('<i ', '<i ' . $style . ' ', $icon_html);
    }
    
    return $icon_html;
}
?>
