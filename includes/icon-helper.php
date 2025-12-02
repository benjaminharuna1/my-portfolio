<?php
/**
 * Icon Helper Functions
 * Provides utilities for displaying custom SVG icons and Font Awesome icons
 * 
 * Icon Prefix System:
 * - ci-* = Custom Icon (SVG from database)
 * - fa-* = Font Awesome Icon
 */

/**
 * Display an icon with automatic ci- prefix detection
 * @param string $name Icon name (with ci- or fa- prefix)
 * @param string $class CSS classes to apply
 * @param string $fallback Fallback Font Awesome icon if custom not found
 * @return string HTML for the icon
 */
function icon($name, $class = '', $fallback = '') {
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
        
        // Add class to SVG if provided
        if (!empty($class)) {
            $svg_content = preg_replace('/<svg /', '<svg class="' . htmlspecialchars($class) . '" ', $svg_content, 1);
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
        // Already has prefix
        $fa_class = $name;
    } elseif (strpos($name, 'fa-') === 0) {
        // Has fa- but no prefix, add fas
        $fa_class = 'fas ' . $name;
    } else {
        // No prefix, assume solid
        $fa_class = 'fas fa-' . $name;
    }
    
    // Add custom class if provided
    if (!empty($class)) {
        $fa_class .= ' ' . $class;
    }
    
    return '<i class="' . htmlspecialchars($fa_class) . '"></i>';
}

/**
 * Legacy function - use icon() instead
 * Display a custom icon with fallback to Font Awesome
 * @param string $name Icon name
 * @param array $options Display options (deprecated)
 * @param string $fallback Font Awesome icon class
 * @return string HTML for the icon
 */
function displayIcon($name, $options = [], $fallback = '') {
    // Convert options array to class string
    $class = isset($options['class']) ? $options['class'] : '';
    
    // Add color as inline style if provided
    $style = '';
    if (isset($options['color'])) {
        $style = 'style="color: ' . htmlspecialchars($options['color']) . ';"';
    }
    
    // Get the icon
    $icon_html = icon($name, $class, $fallback);
    
    // Add style if needed
    if (!empty($style) && strpos($icon_html, '<i ') === 0) {
        $icon_html = str_replace('<i ', '<i ' . $style . ' ', $icon_html);
    }
    
    return $icon_html;
}

/**
 * Get all custom icons
 * @param string $category Optional category filter
 * @return array Array of icons
 */
function getAllCustomIcons($category = null) {
    global $conn;
    
    // Check if custom_icons table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'custom_icons'");
    if (!$table_check || $table_check->num_rows === 0) {
        return [];
    }
    
    $query = "SELECT * FROM custom_icons";
    
    if ($category) {
        $query .= " WHERE category = '" . $conn->real_escape_string($category) . "'";
    }
    
    $query .= " ORDER BY name ASC";
    
    $result = $conn->query($query);
    $icons = [];
    
    if ($result) {
        while ($icon = $result->fetch_assoc()) {
            $icons[] = $icon;
        }
    }
    
    return $icons;
}

/**
 * Get icon categories
 * @return array Array of unique categories
 */
function getIconCategories() {
    global $conn;
    
    // Check if custom_icons table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'custom_icons'");
    if (!$table_check || $table_check->num_rows === 0) {
        return [];
    }
    
    $result = $conn->query("SELECT DISTINCT category FROM custom_icons WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
    $categories = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }
    }
    
    return $categories;
}

/**
 * Check if a custom icon exists
 * @param string $name Icon name or slug
 * @return bool
 */
function customIconExists($name) {
    global $conn;
    
    // Only check if name starts with 'ci-' (custom icon prefix)
    if (strpos($name, 'ci-') !== 0) {
        return false;
    }
    
    // Check if custom_icons table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'custom_icons'");
    if (!$table_check || $table_check->num_rows === 0) {
        return false;
    }
    
    // Remove 'ci-' prefix to get the actual icon name
    $icon_name = substr($name, 3);
    
    $query = $conn->query("SELECT id FROM custom_icons WHERE name = '" . $conn->real_escape_string($icon_name) . "' OR slug = '" . $conn->real_escape_string($icon_name) . "' LIMIT 1");
    
    return $query && $query->num_rows > 0;
}
?>
