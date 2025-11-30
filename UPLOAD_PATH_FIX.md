# Upload Path Configuration Fix

## Problem

Previously, the upload system was generating incorrect image URLs like:
```
http://localhost/my-portfolio/../uploads/img_692bcf573c19f6.67980818.jpg
```

This was because:
1. Upload folder was relative to the calling script location
2. Admin pages were in `/admin/` subdirectory
3. Relative path `../uploads` was being appended to SITE_URL

## Solution

The upload system has been updated to:
1. Use absolute file paths for storing files
2. Generate clean URLs using `SITE_URL/uploads/filename`
3. Work correctly regardless of where the upload is called from

## How It Works Now

### File Storage
- Files are stored in the root `/uploads` directory
- Absolute path: `/var/www/html/my-portfolio/uploads/img_xxx.jpg`
- Works from any subdirectory (admin, public pages, etc.)

### URL Generation
- Clean URL: `http://localhost/my-portfolio/uploads/img_xxx.jpg`
- Works with any SITE_URL configuration
- Automatically works on production: `https://domain.com/uploads/img_xxx.jpg`

## Configuration

### .env File
```env
SITE_URL=http://localhost/my-portfolio
```

### Upload Behavior

**Local Development:**
```
SITE_URL = http://localhost/my-portfolio
Image URL = http://localhost/my-portfolio/uploads/img_xxx.jpg
```

**Production:**
```
SITE_URL = https://myportfolio.com
Image URL = https://myportfolio.com/uploads/img_xxx.jpg
```

**cPanel/Shared Hosting:**
```
SITE_URL = https://domain.com
Image URL = https://domain.com/uploads/img_xxx.jpg
```

## Code Changes

### includes/upload.php

**Before:**
```php
$url = SITE_URL . '/' . str_replace('\\', '/', $filedestination);
// Result: http://localhost/my-portfolio/../uploads/img_xxx.jpg
```

**After:**
```php
$url = SITE_URL . '/uploads/' . $newfilename;
// Result: http://localhost/my-portfolio/uploads/img_xxx.jpg
```

### Admin Files Updated

All admin files now call upload functions without relative paths:

**Before:**
```php
$upload = uploadImage($_FILES['image'], '../uploads');
deleteImage($filename, '../uploads');
```

**After:**
```php
$upload = uploadImage($_FILES['image']);
deleteImage($filename);
```

### Files Modified

- ✅ `includes/upload.php` - Core upload logic
- ✅ `admin/portfolio.php` - Portfolio uploads
- ✅ `admin/about.php` - About section uploads
- ✅ `admin/profile.php` - Profile picture uploads
- ✅ `admin/settings.php` - Logo and favicon uploads
- ✅ `admin/testimonials.php` - Testimonial image uploads

## Directory Structure

```
project_root/
├── .env
├── config.php
├── admin/
│   ├── portfolio.php
│   ├── about.php
│   ├── profile.php
│   ├── settings.php
│   └── testimonials.php
├── includes/
│   ├── upload.php
│   └── ...
├── uploads/                    # Root-level uploads directory
│   ├── img_xxx.jpg
│   ├── img_yyy.png
│   └── ...
└── ...
```

## Upload Function Signature

### uploadImage()

```php
function uploadImage($file, $folder = null)
```

**Parameters:**
- `$file` - $_FILES array element
- `$folder` - (Optional) Custom folder path. Defaults to root `/uploads`

**Returns:**
```php
[
    'success' => true/false,
    'filename' => 'img_xxx.jpg',
    'path' => '/absolute/path/to/uploads/img_xxx.jpg',
    'url' => 'http://localhost/my-portfolio/uploads/img_xxx.jpg'
]
```

**Usage:**
```php
// Uses default root uploads folder
$upload = uploadImage($_FILES['image']);

// Or specify custom folder
$upload = uploadImage($_FILES['image'], '/custom/path');
```

### deleteImage()

```php
function deleteImage($filename, $folder = null)
```

**Parameters:**
- `$filename` - Filename to delete
- `$folder` - (Optional) Custom folder path. Defaults to root `/uploads`

**Usage:**
```php
// Uses default root uploads folder
deleteImage('img_xxx.jpg');

// Or specify custom folder
deleteImage('img_xxx.jpg', '/custom/path');
```

## Features

✅ **Absolute Paths** - Files stored with absolute paths
✅ **Clean URLs** - No relative path traversal in URLs
✅ **Flexible** - Works from any subdirectory
✅ **Portable** - Works on any hosting environment
✅ **Scalable** - Easy to change upload directory
✅ **Secure** - Proper path handling prevents directory traversal

## Environment Variables

The upload system uses these environment variables:

```env
# Maximum file size (bytes)
MAX_UPLOAD_SIZE=5242880

# Allowed file types (comma-separated)
ALLOWED_UPLOAD_TYPES=jpg,jpeg,png,gif,webp
```

## Testing

### Test Upload

1. Go to admin panel
2. Upload an image to portfolio
3. Check the image URL in the database
4. Should be: `http://localhost/my-portfolio/uploads/img_xxx.jpg`
5. NOT: `http://localhost/my-portfolio/../uploads/img_xxx.jpg`

### Test on Different Environments

**Local:**
```
SITE_URL=http://localhost/my-portfolio
Image URL=http://localhost/my-portfolio/uploads/img_xxx.jpg ✅
```

**Production:**
```
SITE_URL=https://myportfolio.com
Image URL=https://myportfolio.com/uploads/img_xxx.jpg ✅
```

**Subdomain:**
```
SITE_URL=https://portfolio.mycompany.com
Image URL=https://portfolio.mycompany.com/uploads/img_xxx.jpg ✅
```

## Troubleshooting

### Images Not Uploading

**Check:**
1. `/uploads` directory exists and is writable
2. `MAX_UPLOAD_SIZE` is large enough
3. File type is in `ALLOWED_UPLOAD_TYPES`
4. PHP `upload_max_filesize` is configured

**Fix permissions:**
```bash
chmod 755 uploads
chmod 644 uploads/*
```

### Wrong Image URLs

**Check:**
1. `SITE_URL` is correct in `.env`
2. Images are stored in `/uploads` directory
3. Database has correct image URLs

**Verify:**
```bash
# Check stored images
ls -la uploads/

# Check database
mysql> SELECT image_url FROM portfolio_items LIMIT 1;
```

### Images Not Displaying

**Check:**
1. Image URL is correct
2. Image file exists in `/uploads`
3. File permissions are readable (644)
4. Browser cache is cleared

## Migration from Old System

If you have existing images with old paths:

**Old path format:**
```
http://localhost/my-portfolio/../uploads/img_xxx.jpg
```

**New path format:**
```
http://localhost/my-portfolio/uploads/img_xxx.jpg
```

**To migrate:**
1. Update database URLs (remove `../`)
2. Or re-upload images with new system

## Performance

✅ **No Performance Impact** - Same file operations
✅ **Faster URL Generation** - Direct concatenation
✅ **Better Caching** - Clean URLs cache better

## Security

✅ **Path Traversal Prevention** - Absolute paths prevent `../` attacks
✅ **File Type Validation** - Only allowed types uploaded
✅ **Size Validation** - Enforced file size limits
✅ **Unique Filenames** - Prevents overwrites

## Summary

The upload system now:
- ✅ Stores files in root `/uploads` directory
- ✅ Generates cleajpg`
/img_xxx../uploadsio/.olost/my-portfocalhtp://lhtead of `g` inst_xxx.jpds/imguploaortfolio/alhost/my-ploc//`http:Ls like correct URupload with s now t:** Imageul

**Residationvalcurity and  seintains Maion
- ✅ configuratto SITE_URLpts ally ada ✅ Automaticonment
-envirting on any hosctly corre
- ✅ Works tive pathsithout relan URLs w