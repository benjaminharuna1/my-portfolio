<?php
/**
 * Icon Helper Functions
 * Provides utilities for displaying custom SVG icons and Font Awesome icons
 */

/**
 * Get a custom icon by name or slug
 * @param string $name Icon name or slug
 * @param array $options Display options (color, size, class, etc.)
 * @return string HTML for the icon
 */
function getCustomIcon($name, $options = []) {
    global $conn;
    
    $defaults = [
        'color' => null,
        'size' => null,
        'class' => '',
        'title' => '',
        'alt' => ''
    ];
    
    $options = array_merge($defaults, $options);
    
    // Try to find icon by name or slug
    $query = $conn->query("SELECT * FROM custom_icons WHERE name = '" . $conn->real_escape_string($name) . "' OR slug = '" . $conn->real_escape_string($name) . "' LIMIT 1");
    
    if ($query && $query->num_rows > 0) {
        $icon = $query->fetch_assoc();
        $svg_content = $icon['svg_content'];
        
        // Apply color if specified
        if ($options['color']) {
            $svg_content = preg_replace('/fill="[^"]*"/', 'fill="' . htmlspecialchars($options['color']) . '"', $svg_content);
            $svg_content = preg_replace('/stroke="[^"]*"/', 'stroke="' . htmlspecialchars($options['color']) . '"', $svg_content);
        }
        
        // Apply size if specified
        if ($options['size']) {
            $size_px = intval($options['size']) . 'px';
            $svg_content = preg_replace('/<svg/', '<svg style="width: ' . $size_px . '; height: ' . $size_px . ';"', $svg_content);
        }
        
        // Add class if specified
        if ($options['class']) {
            $svg_content = preg_replace('/<svg/', '<svg class="' . htmlspecialchars($options['class']) . '"', $svg_content);
        }
        
        // Add title for accessibility
        if ($options['title']) {
            $svg_content = preg_replace('/<svg/', '<svg title="' . htmlspecialchars($options['title']) . '"', $svg_content);
        }
        
        // Add alt text (as aria-label)
        if ($options['alt']) {
            $svg_content = preg_replace('/<svg/', '<svg aria-label="' . htmlspecialchars($options['alt']) . '"', $svg_content);
        }
        
        return $svg_content;
    }
    
    return '';
}

/**
 * Display a custom icon with fallback to Font Awesome
 * @param string $name Icon name
 * @param array $options Display options
 * @param string $fallback Font Awesome icon class (e.g., 'fa-star')
 * @return string HTML for the icon
 */
function displayIcon($name, $options = [], $fallback = '') {
    $custom_icon = getCustomIcon($name, $options);
    
    if ($custom_icon) {
        return $custom_icon;
    }
    
    // Fallback to Font Awesome
    if ($fallback) {
        $class = 'fas ' . $fallback;
        if (isset($options['class'])) {
            $class .= ' ' . $options['class'];
        }
        
        $html = '<i class="' . htmlspecialchars($class) . '"';
        
        if (isset($options['title'])) {
            $html .= ' title="' . htmlspecialchars($options['title']) . '"';
        }
        
        if (isset($options['color'])) {
            $html .= ' style="color: ' . htmlspecialchars($options['color']) . ';"';
        }
        
        $html .= '></i>';
        
        return $html;
    }
    
    return '';
}

/**
 * Get all custom icons
 * @param string $category Optional category filter
 * @return array Array of icons
 */
function getAllCustomIcons($category = null) {
    global $conn;
    
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
    
    $query = $conn->query("SELECT id FROM custom_icons WHERE name = '" . $conn->real_escape_string($name) . "' OR slug = '" . $conn->real_escape_string($name) . "' LIMIT 1");
    
    return $query && $query->num_rows > 0;
}
?>
