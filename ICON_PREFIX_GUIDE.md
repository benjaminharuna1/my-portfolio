# Icon Prefix System Guide

## Overview

The portfolio now uses a clear prefix system to distinguish between custom SVG icons and Font Awesome icons:

- **`ci-`** = Custom Icon (your uploaded SVG icons)
- **`fa-`** = Font Awesome Icon (built-in Font Awesome library)

This makes it easy for the system to know which icon library to use.

## Custom Icons (ci- prefix)

### What are Custom Icons?

Custom icons are SVG files you upload to the system. They're stored in the database and can be customized with colors and sizes.

### How to Use Custom Icons

1. **Upload Icon**
   - Go to `/admin/icons.php`
   - Click "Add New Icon"
   - Upload your SVG file
   - Click "Add Icon"

2. **Use in Services**
   - Go to `/admin/services.php`
   - Edit or create a service
   - In "Service Icon" field, enter: `ci-photoshop`
   - Or click "Browse Icons" to select (auto-adds `ci-` prefix)
   - Save service

3. **Use in Code**
   ```php
   <?php
   echo displayIcon('ci-photoshop', [
       'color' => '#667eea',
       'size' => 48
   ]);
   ?>
   ```

### Custom Icon Examples

- `ci-photoshop` - Custom Photoshop icon
- `ci-figma` - Custom Figma icon
- `ci-sketch` - Custom Sketch icon
- `ci-xd` - Custom Adobe XD icon

## Font Awesome Icons (fa- prefix)

### What are Font Awesome Icons?

Font Awesome is a popular icon library with thousands of pre-made icons. No upload needed - just use the icon name.

### How to Use Font Awesome Icons

1. **In Services**
   - Go to `/admin/services.php`
   - Edit or create a service
   - In "Service Icon" field, enter: `fa-palette`
   - Or click "Browse Icons" tab to select
   - Save service

2. **In Code**
   ```php
   <?php
   echo displayIcon('fa-palette', [
       'color' => '#667eea',
       'size' => 32
   ]);
   ?>
   ```

### Font Awesome Icon Examples

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

### Brand Icons (fab prefix)

Font Awesome also has brand icons for companies:

```php
<?php
echo displayIcon('fab fa-php', ['size' => 32]);
echo displayIcon('fab fa-js', ['size' => 32]);
echo displayIcon('fab fa-react', ['size' => 32]);
echo displayIcon('fab fa-laravel', ['size' => 32]);
?>
```

## How the System Works

### Icon Resolution Order

When you use `displayIcon()`:

1. **Check if it's a custom icon** (starts with `ci-`)
   - If yes, look for custom SVG icon in database
   - If found, display it
   - If not found, fall back to Font Awesome

2. **Check if it's a Font Awesome icon** (starts with `fa-` or `fab `)
   - If yes, display Font Awesome icon
   - If not found, return empty

3. **Use fallback** (if provided)
   - Display the fallback icon

### Examples

```php
<?php
// Custom icon - will display SVG if exists, else Font Awesome fallback
echo displayIcon('ci-photoshop', ['size' => 48], 'fa-image');

// Font Awesome icon - will display Font Awesome icon
echo displayIcon('fa-palette', ['size' => 48]);

// Font Awesome brand icon
echo displayIcon('fab fa-php', ['size' => 48]);

// With fallback
echo displayIcon('ci-unknown', ['size' => 48], 'fa-question');
?>
```

## Best Practices

1. **Use `ci-` for custom icons** - Makes it clear you're using a custom SVG
2. **Use `fa-` for Font Awesome** - Standard Font Awesome prefix
3. **Always provide fallback** - In case custom icon doesn't exist
4. **Keep icon names simple** - Use lowercase, hyphens for spaces
5. **Test display** - Check how icons look at different sizes

## Troubleshooting

### Icon Not Showing

**Problem:** Icon name entered but not displaying

**Solutions:**
1. Check prefix is correct (`ci-` or `fa-`)
2. For custom icons, verify it was uploaded successfully
3. Check icon name matches exactly (case-sensitive)
4. Use "Browse Icons" to select from available icons

### Wrong Icon Displaying

**Problem:** Icon showing but it's the wrong one

**Solutions:**
1. Verify icon name is correct
2. Check if custom icon exists in `/admin/icons.php`
3. Try using Font Awesome icon instead
4. Check if fallback is being used

### Custom Icon Not Found

**Problem:** Custom icon uploaded but not displaying

**Solutions:**
1. Verify SVG content was saved to database
2. Check icon name in database matches what you're using
3. Make sure you're using `ci-` prefix
4. Try uploading icon again

## Migration from Old System

If you were using icons without prefixes:

**Old way:**
```php
echo displayIcon('photoshop');  // Ambiguous
```

**New way:**
```php
echo displayIcon('ci-photoshop');  // Clear it's custom
echo displayIcon('fa-palette');    // Clear it's Font Awesome
```

## Summary

| Prefix | Type | Source | Example |
|--------|------|--------|---------|
| `ci-` | Custom SVG | Database | `ci-photoshop` |
| `fa-` | Font Awesome | Library | `fa-palette` |
| `fab ` | Font Awesome Brand | Library | `fab fa-php` |
| `fas ` | Font Awesome Solid | Library | `fas fa-star` |
| `far ` | Font Awesome Regular | Library | `far fa-star` |

This system makes it easy to understand which icon library is being used and provides clear fallback behavior!
