# Simplified Icon System Guide

## Overview

The icon system now uses a clean, professional approach with inline SVG loading and automatic Font Awesome fallback.

## Icon Prefix System

- **`ci-`** = Custom Icon (SVG from database)
- **`fa-`** = Font Awesome Icon (built-in library)

## How to Use

### Basic Usage

```php
<?php
// Custom icon
echo icon('ci-photoshop');

// Font Awesome icon
echo icon('fa-palette');

// With CSS class
echo icon('ci-photoshop', 'w-6 h-6 text-primary');

// With fallback
echo icon('ci-photoshop', '', 'fa-image');
?>
```

### In Services

1. Go to `/admin/services.php`
2. Edit or create a service
3. Enter icon name: `ci-photoshop` or `fa-palette`
4. Or click "Browse Icons" to select

### In Portfolio Categories

1. Go to `/admin/portfolio.php`
2. Category filter buttons automatically display icons
3. Use `ci-` prefix for custom icons

## How It Works

### Icon Resolution

When you call `icon('ci-photoshop')`:

1. **Check if it's a custom icon** (starts with `ci-`)
   - Look for SVG in database
   - If found, return inline SVG
   - If not found, use fallback

2. **Check if it's Font Awesome** (starts with `fa-`)
   - Return Font Awesome icon

3. **Use fallback** (if provided)
   - Return fallback icon

### Example Flow

```php
// This will:
// 1. Look for 'photoshop' in custom_icons table
// 2. If found, display SVG inline
// 3. If not found, display Font Awesome 'image' icon
echo icon('ci-photoshop', '', 'fa-image');
```

## Custom Icons

### Upload Custom Icon

1. Go to `/admin/icons.php`
2. Click "Add New Icon"
3. Upload SVG file
4. Click "Add Icon"

### Use Custom Icon

```php
<?php
// Simple
echo icon('ci-photoshop');

// With class
echo icon('ci-photoshop', 'w-8 h-8 text-blue-500');

// With fallback
echo icon('ci-photoshop', 'icon-large', 'fa-image');
?>
```

## Font Awesome Icons

### Use Font Awesome

```php
<?php
// Simple
echo icon('fa-palette');

// With class
echo icon('fa-palette', 'text-primary');

// Brand icons
echo icon('fab fa-php', 'text-danger');
echo icon('fab fa-js', 'text-warning');
?>
```

### Popular Font Awesome Icons

| Icon | Code |
|------|------|
| Palette | `fa-palette` |
| Code | `fa-code` |
| Pencil Ruler | `fa-pencil-ruler` |
| Mobile | `fa-mobile-alt` |
| Laptop | `fa-laptop` |
| Database | `fa-database` |
| Server | `fa-server` |
| Cloud | `fa-cloud` |
| Shield | `fa-shield-alt` |
| Lock | `fa-lock` |
| Cog | `fa-cog` |
| Tools | `fa-tools` |
| Wrench | `fa-wrench` |
| Paint Brush | `fa-paint-brush` |
| Star | `fa-star` |
| Heart | `fa-heart` |
| Check | `fa-check` |
| Download | `fa-download` |
| Upload | `fa-upload` |

## CSS Styling

### Default SVG Styling

Add to your CSS:

```css
/* Make SVG icons responsive */
svg {
    width: 1em;
    height: 1em;
    vertical-align: middle;
    display: inline-block;
}

/* Utility classes */
.w-4 { width: 16px; }
.w-6 { width: 24px; }
.w-8 { width: 32px; }

.h-4 { height: 16px; }
.h-6 { height: 24px; }
.h-8 { height: 32px; }

.text-primary { fill: #007bff; }
.text-danger { fill: #dc3545; }
.text-success { fill: #28a745; }
.text-warning { fill: #ffc107; }
```

### Styling Custom Icons

```php
<?php
// Icon with Bootstrap classes
echo icon('ci-photoshop', 'text-primary');

// Icon with custom CSS
echo icon('ci-photoshop', 'my-custom-class');
?>
```

## Function Reference

### icon($name, $class = '', $fallback = '')

Display an icon with automatic prefix detection.

**Parameters:**
- `$name` (string) - Icon name with prefix (ci-* or fa-*)
- `$class` (string) - CSS classes to apply
- `$fallback` (string) - Fallback Font Awesome icon

**Returns:** HTML string

**Examples:**
```php
icon('ci-photoshop');
icon('fa-palette', 'text-primary');
icon('ci-unknown', '', 'fa-question');
```

### fontAwesomeIcon($name, $class = '')

Display a Font Awesome icon.

**Parameters:**
- `$name` (string) - Icon name (with or without fa- prefix)
- `$class` (string) - CSS classes to apply

**Returns:** HTML string

**Examples:**
```php
fontAwesomeIcon('fa-palette');
fontAwesomeIcon('palette', 'text-primary');
```

### getCustomIconSVG($name, $class = '')

Get custom SVG icon from database.

**Parameters:**
- `$name` (string) - Icon name with ci- prefix
- `$class` (string) - CSS classes to apply

**Returns:** SVG HTML or empty string

**Examples:**
```php
getCustomIconSVG('ci-photoshop', 'w-8 h-8');
```

## Troubleshooting

### Icon Not Showing

**Problem:** Icon name entered but not displaying

**Solutions:**
1. Check prefix is correct (`ci-` or `fa-`)
2. For custom icons, verify it was uploaded
3. Check icon name matches exactly
4. Use "Browse Icons" to select

### Wrong Icon Displaying

**Problem:** Icon showing but it's the wrong one

**Solutions:**
1. Verify icon name is correct
2. Check if custom icon exists in `/admin/icons.php`
3. Try Font Awesome icon instead
4. Check if fallback is being used

### Custom Icon Not Found

**Problem:** Custom icon uploaded but not displaying

**Solutions:**
1. Verify SVG content was saved to database
2. Check icon name in database
3. Make sure you're using `ci-` prefix
4. Try uploading icon again

## Best Practices

1. **Use `ci-` for custom icons** - Makes it clear you're using custom SVG
2. **Use `fa-` for Font Awesome** - Standard Font Awesome prefix
3. **Always provide fallback** - In case custom icon doesn't exist
4. **Keep icon names simple** - Use lowercase, hyphens for spaces
5. **Test display** - Check how icons look with your CSS classes

## Migration from Old System

If you were using the old system:

**Old way:**
```php
displayIcon('photoshop', ['size' => 48, 'color' => '#667eea']);
```

**New way:**
```php
icon('ci-photoshop', 'w-12 h-12 text-primary');
```

## Summary

| Feature | Custom Icon | Font Awesome |
|---------|-------------|--------------|
| Prefix | `ci-` | `fa-` |
| Source | Database | Library |
| Format | SVG | Icon Font |
| Styling | CSS classes | CSS classes |
| Example | `ci-photoshop` | `fa-palette` |

This simplified system is:
- ✔ Clean and professional
- ✔ Easy to use
- ✔ Reliable across browsers
- ✔ Supports both custom and Font Awesome icons
- ✔ No complicated CSS hacks
- ✔ Inline SVG for best rendering
