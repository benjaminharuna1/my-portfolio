# Upload Path Fix - Documentation

## Problem Fixed

**Before:**
```
http://localhost/my-portfolio/../uploads/img_692bcf573c19f6.67980818.jpg
```

**After:**
```
http://localhost/my-portfolio/uploads/img_692bcf573c19f6.67980818.jpg
```

## What Changed

### 1. Upload Function (includes/upload.php)

**URL Generation:**
```php
// OLD: Concatenated relative path
$url = SITE_URL . '/' . str_replace('\\', '/', $filedestination);

// NEW: Direct uploads folder
$url = SITE_URL . '/uploads/' . $newfilename;
```

### 2. File Storage

**Uses absolute paths:**
```php
$folder = dirname(__DIR__) . '/uploads';  // /var/www/html/my-portfolio/uploads
```

### 3. Admin Files Updated

All admin files now call upload functions without relative paths:

```php
// OLD
uploadImage($_FILES['image'], '../uploads');
deleteImage($filename, '../uploads');

// NEW
uploadImage($_FILES['image']);
deleteImage($filename);
```

## Files Modified

- ✅ includes/upload.php
- ✅ admin/portfolio.php
- ✅ admin/about.php
- ✅ admin/profile.php
- ✅ admin/settings.php
- ✅ admin/testimonials.php

## How It Works

### File Storage
```
/var/www/html/my-portfolio/
├── uploads/
│   ├── img_xxx.jpg
│   ├── img_yyy.png
│   └── ...
```

### URL Generation
```
SITE_URL = http://localhost/my-portfolio
Image URL = http://localhost/my-portfolio/uploads/img_xxx.jpg
```

## Environment Compatibility

### Local Development
```env
SITE_URL=http://localhost/my-portfolio
# Result: http://localhost/my-portfolio/uploads/img_xxx.jpg ✅
```

### Production
```env
SITE_URL=https://myportfolio.com
# Result: https://myportfolio.com/uploads/img_xxx.jpg ✅
```

### cPanel/Shared Hosting
```env
SITE_URL=https://domain.com
# Result: https://domain.com/uploads/img_xxx.jpg ✅
```

## Upload Function Usage

### uploadImage()
```php
// Uses root uploads folder
$upload = uploadImage($_FILES['image']);

// Returns
[
    'success' => true,
    'filename' => 'img_xxx.jpg',
    'path' => '/absolute/path/uploads/img_xxx.jpg',
    'url' => 'http://localhost/my-portfolio/uploads/img_xxx.jpg'
]
```

### deleteImage()
```php
// Uses root uploads folder
deleteImage('img_xxx.jpg');
```

## Configuration

### .env
```env
SITE_URL=http://localhost/my-portfolio
MAX_UPLOAD_SIZE=5242880
ALLOWED_UPLOAD_TYPES=jpg,jpeg,png,gif,webp
```

## Testing

1. Upload an image via admin panel
2. Check database for image URL
3. Should be: `http://localhost/my-portfolio/uploads/img_xxx.jpg`
4. NOT: `http://localhost/my-portfolio/../uploads/img_xxx.jpg`

## Troubleshooting

### Images Not Uploading
```bash
# Check directory exists and is writable
chmod 755 uploads
chmod 644 uploads/*
```

### Wrong URLs
- Verify SITE_URL in .env
- Check images exist in /uploads
- Clear browser cache

## Benefits

✅ Clean URLs without relative paths
✅ Works on any hosting environment
✅ Automatically adapts to SITE_URL
✅ Better security (no path traversal)
✅ Easier to maintain and debug
