# Image Upload Troubleshooting Guide

## Issue: Images Not Showing After Upload

### Root Cause
The image URL was being generated incorrectly, causing images to not display even though they were uploaded successfully.

### What Was Fixed

**Before (Broken):**
```php
'url' => '/' . $filedestination  // Results in: /uploads/img_123.jpg
```

**After (Fixed):**
```php
$url = SITE_URL . '/' . str_replace('\\', '/', $filedestination);
// Results in: http://localhost/my-portfolio/uploads/img_123.jpg
```

## How to Verify the Fix

### Check if Images Display

1. Go to Admin → Portfolio
2. Add a new portfolio item
3. Upload a featured image
4. Click "Add"
5. Go to Portfolio page
6. Verify image displays

### Check Image URLs

1. Right-click on an image
2. Select "Inspect" or "View Image"
3. Check the URL in the address bar
4. Should be: `http://localhost/my-portfolio/uploads/img_xxxxx.jpg`
5. NOT: `/uploads/img_xxxxx.jpg`

### Check Browser Console

1. Open Developer Tools (F12)
2. Go to Console tab
3. Look for any 404 errors
4. If you see 404, the URL is wrong

## Troubleshooting Steps

### Step 1: Verify Uploads Directory

```bash
# Check if uploads directory exists
ls -la uploads/

# If not, create it
mkdir uploads

# Set permissions
chmod 755 uploads
```

### Step 2: Check File Permissions

```bash
# Make uploads directory writable
chmod 755 uploads

# Check permissions
ls -la uploads/
# Should show: drwxr-xr-x
```

### Step 3: Verify SITE_URL in .env

```env
# Check your .env file
SITE_URL=http://localhost/my-portfolio

# Should match your actual site URL
# Examples:
# Local: http://localhost/my-portfolio
# Production: https://myportfolio.com
```

### Step 4: Test Upload

1. Go to Admin → Portfolio
2. Add a new item
3. Upload an image
4. Check the error message if it fails
5. Check System Logs for details

### Step 5: Check System Logs

1. Go to Admin → System Logs
2. Look for upload-related errors
3. Check the error message
4. Fix the issue based on the error

## Common Issues and Solutions

### Issue: "File too large"
**Solution:** 
- Check file size (max 5MB)
- Compress image before uploading
- Or increase MAX_UPLOAD_SIZE in .env

### Issue: "Invalid file type"
**Solution:**
- Use supported formats: JPG, PNG, GIF, WebP
- Don't upload: PDF, DOC, ZIP, etc.

### Issue: "Permission denied"
**Solution:**
```bash
chmod 755 uploads
chmod 644 uploads/*
```

### Issue: "Failed to move uploaded file"
**Solution:**
- Check uploads directory exists
- Check directory permissions
- Check disk space
- Check PHP temp directory

### Issue: Image uploads but doesn't display
**Solution:**
- Check SITE_URL in .env
- Verify image file exists in /uploads
- Check browser console for 404 errors
- Clear browser cache (Ctrl+Shift+Delete)

## How Images Are Stored

```
project/
├── uploads/
│   ├── img_6756a1b2c3d4e.jpg
│   ├── img_7867b2c3d4e5f.png
│   └── ...
├── admin/
│   └── portfolio.php
└── ...
```

## How Images Are Displayed

### In Database
```
featured_image_url: http://localhost/my-portfolio/uploads/img_123.jpg
featured_image_filename: img_123.jpg
```

### In HTML
```html
<img src="http://localhost/my-portfolio/uploads/img_123.jpg" alt="Portfolio">
```

### In Code
```php
// Upload returns
$upload['url'] = 'http://localhost/my-portfolio/uploads/img_123.jpg';
$upload['filename'] = 'img_123.jpg';

// Stored in database
$featured_image_url = $upload['url'];
$featured_image_filename = $upload['filename'];

// Displayed in HTML
<img src="<?php echo htmlspecialchars($featured_image_url); ?>">
```

## Testing Image Upload

### Manual Test

```php
<?php
require 'config.php';

// Check SITE_URL
echo "SITE_URL: " . SITE_URL . "\n";

// Check uploads directory
echo "Uploads dir exists: " . (is_dir('uploads') ? 'Yes' : 'No') . "\n";
echo "Uploads dir writable: " . (is_writable('uploads') ? 'Yes' : 'No') . "\n";

// Test file path
$test_file = 'uploads/test.txt';
file_put_contents($test_file, 'test');
echo "Can write files: " . (file_exists($test_file) ? 'Yes' : 'No') . "\n";
unlink($test_file);
?>
```

## Browser Cache Issue

If images still don't show after fix:

1. **Clear Browser Cache**
   - Chrome: Ctrl+Shift+Delete
   - Firefox: Ctrl+Shift+Delete
   - Safari: Cmd+Shift+Delete

2. **Hard Refresh**
   - Chrome: Ctrl+F5
   - Firefox: Ctrl+Shift+R
   - Safari: Cmd+Shift+R

3. **Incognito/Private Mode**
   - Open in private/incognito window
   - Test if images display

## Verify Fix is Applied

### Check upload.php

```php
// Should have this code:
$url = SITE_URL . '/' . str_replace('\\', '/', $filedestination);

return [
    'success' => true,
    'filename' => $newfilename,
    'path' => $filedestination,
    'url' => $url  // Full URL with SITE_URL
];
```

### Check Database

```sql
-- Query to check image URLs
SELECT featured_image_url FROM portfolio_items LIMIT 1;

-- Should show full URL like:
-- http://localhost/my-portfolio/uploads/img_123.jpg
-- NOT just: /uploads/img_123.jpg
```

## Re-upload Images

If images were uploaded before the fix:

1. Go to Admin → Portfolio
2. Edit each portfolio item
3. Re-upload the featured image
4. Update gallery images
5. Verify images display

## Performance Tips

- Compress images before uploading
- Use appropriate image formats (JPG for photos, PNG for graphics)
- Keep file sizes under 1MB when possible
- Use WebP format for better compression

## Security Notes

- Only JPG, PNG, GIF, WebP allowed
- Max file size: 5MB
- Files stored outside web root (in /uploads)
- Filenames randomized (img_xxxxx.jpg)
- Original filenames not preserved

## Getting Help

If images still don't display:

1. Check System Logs (Admin → System Logs)
2. Check browser console (F12 → Console)
3. Verify SITE_URL in .env
4. Verify uploads directory exists and is writable
5. Try uploading a small test image
6. Check file permissions

## Summary

The image upload issue has been fixed by:
- ✅ Using full SITE_URL in image URLs
- ✅ Proper path handling for Windows/Linux
- ✅ Better error messages
- ✅ Detailed logging

Images should now display correctly on all servers!
