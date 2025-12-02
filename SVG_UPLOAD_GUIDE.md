# SVG Upload & Content Extraction Guide

## Overview

This guide explains how SVG files are uploaded, processed, and saved in the database so they can be displayed with proper styling.

## The Complete SVG Upload Process

### Step 1: Upload SVG File

1. Go to `/admin/icons.php`
2. Click "Add New Icon"
3. Fill in:
   - **Icon Name:** e.g., "photoshop"
   - **Category:** e.g., "Design"
   - **Color:** Default color
   - **Size:** Default size
   - **SVG File:** Upload your .svg file
4. Click "Add Icon"

### Step 2: File Processing

When you upload an SVG file, the system:

1. **Validates** the file
   - Checks file extension is `.svg`
   - Verifies it's a valid SVG file

2. **Reads** the SVG content
   - Reads the entire SVG markup from the file
   - Converts to UTF-8 encoding if needed

3. **Cleans** the SVG
   - Removes XML declaration (`<?xml ... ?>`)
   - Removes DOCTYPE declaration
   - Removes `<script>` tags (security)
   - Removes event handlers (`onclick`, `onload`, etc.)
   - Removes embedded `<style>` tags (so CSS can style it)

4. **Minifies** the SVG
   - Removes excessive whitespace
   - Removes newlines
   - Reduces file size

5. **Saves** to database
   - Stores cleaned SVG markup in `svg_content` column
   - Stores filename in `svg_filename` column
   - Stores metadata (name, category, color, size)

### Step 3: Database Storage

The SVG content is stored in the `custom_icons` table:

```sql
id          | name        | svg_content
1           | photoshop   | <svg class="..." viewBox="0 0 24 24">...</svg>
2           | figma       | <svg class="..." viewBox="0 0 24 24">...</svg>
```

## How SVG Content is Used

### Displaying the Icon

When you use `icon('ci-photoshop')`:

1. System detects `ci-` prefix
2. Looks up "photoshop" in database
3. Retrieves `svg_content` from database
4. Returns inline SVG markup
5. Browser renders the SVG

### Example Output

```html
<!-- Input -->
<?php echo icon('ci-photoshop', 'w-8 h-8 text-primary'); ?>

<!-- Output -->
<svg class="w-8 h-8 text-primary" viewBox="0 0 24 24">
  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
</svg>
```

## Styling SVG Icons

### CSS Styling

Add to your stylesheet:

```css
/* SVG icons */
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

.text-primary { fill: #007bff; }
.text-danger { fill: #dc3545; }
.text-success { fill: #28a745; }
```

### Using CSS Classes

```php
<?php
// Icon with size and color
echo icon('ci-photoshop', 'w-8 h-8 text-primary');

// Icon with custom class
echo icon('ci-photoshop', 'my-custom-icon');
?>
```

## Verification

### Check if SVG Content is Saved

1. Go to `/admin/verify-svg-content.php`
2. View all custom icons in database
3. Check "SVG Content Length" column
   - If it shows a number (e.g., "245 chars"), SVG content is saved ✅
   - If it shows "EMPTY", SVG content was not saved ❌

### View SVG Content

1. Go to `/admin/verify-svg-content.php`
2. Click "View" button on any icon
3. See the SVG preview and code

## Troubleshooting

### SVG Not Displaying

**Problem:** Icon uploaded but not showing

**Check:**
1. Go to `/admin/verify-svg-content.php`
2. Look for your icon in the list
3. Check "SVG Content Length" column
   - If "EMPTY": SVG content wasn't saved
   - If number: SVG content is saved

**Solutions:**
1. Try uploading the SVG again
2. Check file size (should be reasonable)
3. Check browser console for errors
4. Verify icon name is correct

### SVG Content Empty

**Problem:** SVG uploaded but `svg_content` column is empty

**Causes:**
1. SVG file is corrupted
2. SVG file is too large
3. SVG file has invalid encoding
4. Database column is too small

**Solutions:**
1. Try a different SVG file
2. Optimize SVG (remove unnecessary elements)
3. Check file encoding (should be UTF-8)
4. Check database column size (should be LONGTEXT)

### SVG Not Styled Correctly

**Problem:** SVG displays but styling doesn't work

**Solutions:**
1. Check CSS is loaded
2. Verify color variables are set
3. Check SVG doesn't have inline styles
4. Clear browser cache
5. Check browser console for errors

## SVG Cleaning Process

### What Gets Removed

| Item | Reason |
|------|--------|
| XML declaration | Not needed in HTML |
| DOCTYPE | Not needed in HTML |
| `<script>` tags | Security risk |
| Event handlers | Security risk |
| Embedded `<style>` | Prevents CSS styling |
| Whitespace | Reduces file size |

### What Gets Kept

| Item | Reason |
|------|--------|
| SVG markup | Core content |
| Attributes | Needed for rendering |
| Paths/shapes | Visual content |
| viewBox | Scaling support |
| Classes | For CSS styling |

## Database Schema

### custom_icons Table

```sql
CREATE TABLE custom_icons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE,
    slug VARCHAR(100) UNIQUE,
    svg_content LONGTEXT,           -- SVG markup stored here
    svg_filename VARCHAR(255),      -- File reference
    category VARCHAR(100),
    color VARCHAR(7),
    size VARCHAR(20),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Important Columns

- **svg_content** - The actual SVG markup (LONGTEXT)
- **svg_filename** - Reference to uploaded file
- **name** - Icon name (used with `ci-` prefix)

## Best Practices

1. **Use optimized SVGs** - Remove unnecessary elements
2. **Keep file sizes small** - Minify before uploading
3. **Use simple colors** - Avoid complex gradients
4. **Test display** - Check at different sizes
5. **Verify content** - Use verification page to confirm

## Example SVG File

### Before Upload

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
  <style>
    .icon-path { fill: #000; }
  </style>
  <path class="icon-path" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
</svg>
```

### After Upload (Cleaned)

```xml
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"> <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/> </svg>
```

## Summary

| Step | What Happens |
|------|--------------|
| Upload | File validated and read |
| Clean | Dangerous content removed |
| Minify | Whitespace removed |
| Save | SVG content stored in database |
| Display | SVG retrieved and rendered inline |
| Style | CSS applies colors and sizes |

The SVG upload system is now complete and ready to use! 🎉
