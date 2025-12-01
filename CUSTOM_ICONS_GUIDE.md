# Custom SVG Icons System

A flexible system for uploading and managing custom SVG icons throughout your portfolio website.

## Features

- Upload custom SVG icons with automatic sanitization
- Organize icons by category
- Set default colors and sizes
- Easy integration with Font Awesome fallback
- Customizable colors and sizes on display
- Responsive and accessible

## Admin Interface

Access the custom icons manager at: `/admin/icons.php`

### Adding Icons

1. Click "Add New Icon"
2. Enter icon name (e.g., "Download", "Upload", "Settings")
3. Optionally set category for organization
4. Choose default size (16-256px)
5. Select default color
6. Upload SVG file
7. Save

### Editing Icons

1. Click the edit button on any icon
2. Modify name, category, size, or color
3. Upload a new SVG file to replace it
4. Save changes

### Deleting Icons

Click the delete button on any icon card. The SVG file will be automatically removed.

## Using Icons in Code

### Basic Usage

```php
<?php
require 'includes/icon-helper.php';

// Display a custom icon
echo getCustomIcon('download');

// Display with custom color
echo getCustomIcon('download', ['color' => '#FF5733']);

// Display with custom size
echo getCustomIcon('download', ['size' => 32]);

// Display with both color and size
echo getCustomIcon('download', [
    'color' => '#FF5733',
    'size' => 48,
    'title' => 'Download file'
]);
?>
```

### With Font Awesome Fallback

```php
<?php
// Display custom icon or fallback to Font Awesome
echo displayIcon('download', ['color' => '#667eea'], 'fa-download');

// If custom icon "download" doesn't exist, it will show Font Awesome icon instead
?>
```

### Get All Icons

```php
<?php
$all_icons = getAllCustomIcons();

// Get icons by category
$social_icons = getAllCustomIcons('Social');

// Get available categories
$categories = getIconCategories();
?>
```

### Check if Icon Exists

```php
<?php
if (customIconExists('download')) {
    echo getCustomIcon('download');
}
?>
```

## Icon Options

When displaying icons, you can pass these options:

| Option | Type | Description |
|--------|------|-------------|
| `color` | string | Hex color code (e.g., '#FF5733') |
| `size` | int | Size in pixels (e.g., 32) |
| `class` | string | CSS class to add to SVG |
| `title` | string | Tooltip text |
| `alt` | string | Accessibility label |

## SVG Requirements

- Valid SVG format
- Script tags are automatically removed for security
- Dangerous attributes are stripped
- Recommended: Keep SVG simple and scalable
- Use `fill` or `stroke` attributes for colors

## Example SVG

```xml
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
  <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
</svg>
```

## Integration Examples

### In Services Section

```php
<?php
require 'includes/icon-helper.php';

$services = [
    ['title' => 'Web Design', 'icon' => 'web-design'],
    ['title' => 'Development', 'icon' => 'development'],
    ['title' => 'Consulting', 'icon' => 'consulting']
];

foreach ($services as $service) {
    echo '<div class="service">';
    echo displayIcon($service['icon'], ['size' => 48, 'color' => '#667eea'], 'fa-cogs');
    echo '<h3>' . $service['title'] . '</h3>';
    echo '</div>';
}
?>
```

### In Categories

```php
<?php
require 'includes/icon-helper.php';

$categories = $conn->query("SELECT * FROM categories");
while ($cat = $categories->fetch_assoc()) {
    echo '<div class="category">';
    echo displayIcon($cat['icon'], ['size' => 32, 'color' => $cat['color']], 'fa-folder');
    echo '<h4>' . $cat['name'] . '</h4>';
    echo '</div>';
}
?>
```

### In Navigation

```php
<?php
require 'includes/icon-helper.php';

$nav_items = [
    ['label' => 'Home', 'icon' => 'home'],
    ['label' => 'Portfolio', 'icon' => 'portfolio'],
    ['label' => 'About', 'icon' => 'about'],
    ['label' => 'Contact', 'icon' => 'contact']
];

foreach ($nav_items as $item) {
    echo '<a href="#">';
    echo displayIcon($item['icon'], ['size' => 20], 'fa-link');
    echo ' ' . $item['label'];
    echo '</a>';
}
?>
```

## Security

- SVG files are sanitized to remove script tags
- Dangerous attributes are stripped
- Files are stored outside web root when possible
- Only SVG files are accepted

## Performance

- Icons are cached in the database
- SVG content is stored directly (no external requests)
- Minimal file size impact
- Fast rendering

## Troubleshooting

### Icon not displaying
- Check if icon name matches exactly (case-sensitive)
- Verify SVG file was uploaded successfully
- Check browser console for errors

### Color not changing
- Ensure SVG uses `fill` or `stroke` attributes
- Some SVGs with inline styles may not respond to color changes
- Edit SVG to use fill/stroke attributes instead of inline styles

### Icon looks distorted
- Check SVG viewBox attribute
- Ensure SVG is properly formatted
- Try uploading a different SVG file

## Best Practices

1. Use consistent naming conventions (lowercase, hyphens)
2. Organize icons into categories
3. Keep SVG files simple and optimized
4. Test icons at different sizes
5. Always provide Font Awesome fallback for critical icons
6. Use descriptive titles for accessibility
