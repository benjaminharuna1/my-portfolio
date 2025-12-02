# Using Custom Icons in Services and Categories

## Overview

Your portfolio now supports custom SVG icons throughout the site. You can use custom icons you've uploaded or fall back to Font Awesome icons.

## Where Custom Icons Are Used

1. **Services Section** - Main page services display
2. **Portfolio Categories** - Category filter buttons
3. **Any custom page** - Using the icon helper functions

## How to Add Custom Icons to Services

### Step 1: Upload Your Icon

1. Go to **Admin Dashboard > Custom Icons**
2. Click "Add New Icon"
3. Enter the icon name (e.g., "photoshop")
4. Upload your SVG file
5. Click "Add Icon"

### Step 2: Use Icon in Service

1. Go to **Admin Dashboard > Manage Services**
2. Click "Edit" on a service or "Add New Service"
3. In the "Service Icon" field, enter your icon name with `ci-` prefix (e.g., `ci-photoshop`)
4. Or click "Browse Icons" to see available icons and select one (it will auto-add the `ci-` prefix)
5. Click "Update Service" or "Add Service"

### Step 3: Verify Display

The icon will now display on your website's services section with the custom SVG.

## Icon Name Examples

### Custom Icons (use `ci-` prefix)
- `ci-photoshop` - Custom Photoshop icon
- `ci-figma` - Custom Figma icon
- `ci-sketch` - Custom Sketch icon

### Font Awesome Icons (use `fa-` prefix)
- `fa-palette` - Font Awesome palette icon
- `fa-code` - Font Awesome code icon
- `fab fa-php` - Font Awesome PHP icon

## Using the Icon Picker

When editing a service:

1. Click the "Browse Icons" button next to the icon field
2. A modal will open with two tabs:
   - **Custom Icons** - Your uploaded SVG icons
   - **Font Awesome** - Popular Font Awesome icons
3. Click on any icon to select it
4. The icon name will be filled in automatically
5. Save the service

## Fallback Behavior

If an icon name doesn't exist:
- The system will try to find a custom icon with that name
- If not found, it will try Font Awesome
- If Font Awesome icon doesn't exist, a question mark icon appears

## Icon Display Options

When displaying icons in code, you can customize:

```php
<?php
// Custom icon with ci- prefix
echo displayIcon('ci-photoshop', [
    'color' => '#667eea',      // Custom color
    'size' => 48,              // Size in pixels
    'title' => 'Photoshop',    // Tooltip text
    'class' => 'my-class'      // CSS class
], 'fa-image');                // Fallback Font Awesome icon

// Font Awesome icon
echo displayIcon('fa-palette', [
    'color' => '#667eea',
    'size' => 32
]);
?>
```

## Common Font Awesome Icons

Popular Font Awesome icons you can use:

| Icon | Class |
|------|-------|
| Palette | fa-palette |
| Code | fa-code |
| Pencil Ruler | fa-pencil-ruler |
| Mobile | fa-mobile-alt |
| Laptop | fa-laptop |
| Database | fa-database |
| Server | fa-server |
| Cloud | fa-cloud |
| Shield | fa-shield-alt |
| Lock | fa-lock |
| Cog | fa-cog |
| Tools | fa-tools |
| Wrench | fa-wrench |
| Paint Brush | fa-paint-brush |
| Star | fa-star |
| Heart | fa-heart |
| Check | fa-check |
| Download | fa-download |
| Upload | fa-upload |

## Troubleshooting

### Icon Not Showing

**Problem:** Icon name entered but not displaying

**Solutions:**
1. Check the icon name matches exactly (case-sensitive)
2. Verify the icon was uploaded successfully in Custom Icons
3. Check if it's a Font Awesome icon (starts with "fa-")
4. Use the "Browse Icons" button to select from available icons

### Icon Looks Wrong

**Problem:** Icon displaying but looks distorted or wrong color

**Solutions:**
1. Check the SVG file is properly formatted
2. Try uploading a different SVG file
3. Use Font Awesome icon instead
4. Check if color is being applied correctly

### Can't Find Icon in Picker

**Problem:** Icon not appearing in the icon picker modal

**Solutions:**
1. Make sure icon was uploaded to Custom Icons
2. Refresh the page
3. Check icon name in Custom Icons admin page
4. Try searching in Font Awesome tab

## Best Practices

1. **Use consistent naming** - Use lowercase, hyphens for spaces (e.g., "my-icon")
2. **Upload optimized SVGs** - Keep file sizes small
3. **Test display** - Check how icons look at different sizes
4. **Provide fallback** - Always specify a Font Awesome fallback
5. **Use meaningful names** - Name icons based on what they represent

## Examples

### Service with Custom Icon
```
Service: Web Design
Icon: photoshop
Display: Custom Photoshop SVG icon
```

### Service with Font Awesome Icon
```
Service: Web Development
Icon: fa-code
Display: Font Awesome code icon
```

### Category with Custom Icon
```
Category: Graphic Design
Icon: figma
Display: Custom Figma SVG icon
```

## Advanced Usage

### In PHP Code

```php
<?php
require 'includes/icon-helper.php';

// Display custom icon
echo getCustomIcon('photoshop', ['size' => 48, 'color' => '#667eea']);

// Display with fallback
echo displayIcon('photoshop', ['size' => 48], 'fa-image');

// Get all icons in category
$design_icons = getAllCustomIcons('Design');
foreach ($design_icons as $icon) {
    echo $icon['name'];
}
?>
```

### In HTML

```html
<div class="service-icon">
    <?php echo displayIcon('photoshop', ['size' => 64, 'color' => '#667eea'], 'fa-image'); ?>
</div>
```

## Icon Management

### View All Icons
- Go to **Admin Dashboard > Custom Icons**
- See all uploaded icons in grid view
- Edit or delete icons as needed

### Edit Icon
1. Go to **Admin Dashboard > Custom Icons**
2. Click edit button on icon card
3. Modify name, category, color, or size
4. Upload new SVG if needed
5. Click "Update Icon"

### Delete Icon
1. Go to **Admin Dashboard > Custom Icons**
2. Click delete button on icon card
3. Confirm deletion
4. Icon is removed from system

## Support

For more information:
- See `CUSTOM_ICONS_GUIDE.md` for complete icon system documentation
- See `QUICK_REFERENCE.md` for quick lookup
- Check `admin/test-icon-upload.php` for upload debugging
