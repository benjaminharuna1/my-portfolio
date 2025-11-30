# Image Upload Guide

## Overview

The portfolio website now supports direct image uploads from the admin dashboard. No need to manually upload images to your server or use external URLs.

## Features

✅ **Direct Image Upload** - Upload images directly from admin panel
✅ **Automatic File Management** - Images stored in `/uploads` folder
✅ **Image Validation** - File type and size checking
✅ **Fallback Support** - Still supports external image URLs
✅ **Image Preview** - See current image before updating
✅ **Auto Cleanup** - Old images deleted when replaced

## Supported Formats

- JPG / JPEG
- PNG
- GIF
- WebP

**Maximum File Size:** 5MB

## How to Upload Images

### Portfolio Items

1. Go to **Admin Dashboard** → **Portfolio**
2. Click **Edit** on an existing item or add a new one
3. In the form, find **"Upload Image"** section
4. Click to select an image file from your computer
5. Or use **"Image URL"** field for external images
6. Click **Add** or **Update**

### About Section

1. Go to **Admin Dashboard** → **About**
2. Find **"Upload Image"** section
3. Select your profile/about image
4. Click **Update**

## File Structure

```
portfolio/
├── uploads/                    ← Images stored here
│   ├── img_abc123.jpg
│   ├── img_def456.png
│   └── ...
├── includes/
│   └── upload.php             ← Upload handler
└── ...
```

## Technical Details

### Upload Handler (`includes/upload.php`)

The upload handler provides:

- **uploadImage()** - Handles file upload and validation
- **deleteImage()** - Removes old images when replaced
- Automatic filename generation (prevents conflicts)
- Error handling and validation

### Database Changes

New columns added:
- `portfolio_items.image_filename` - Stores uploaded filename
- `about.image_filename` - Stores uploaded filename
- `services.tech_icons` - Stores technology icons

## Troubleshooting

### Upload Not Working

**Check:**
1. `/uploads` folder exists and is writable
2. File size is under 5MB
3. File format is supported (JPG, PNG, GIF, WebP)
4. Server has write permissions

### Create Uploads Folder

If folder doesn't exist, create it:

```bash
mkdir uploads
chmod 755 uploads
```

### File Permissions (Linux/Mac)

```bash
chmod 755 uploads
chmod 644 uploads/*
```

## Security

✅ File type validation
✅ File size limits (5MB max)
✅ Unique filename generation
✅ Stored outside web root (optional)

## Best Practices

1. **Optimize Images** - Compress before uploading
   - Use tools like TinyPNG, ImageOptim
   - Recommended: 1200x800px for portfolio items

2. **Use Consistent Sizes**
   - Portfolio items: 1200x800px
   - About image: 500x600px
   - Maintains consistent look

3. **Backup Regularly**
   - Backup `/uploads` folder
   - Backup database

4. **Clean Up**
   - Delete unused images
   - Keep folder organized

## Image Recommendations

### Portfolio Items
- **Size:** 1200x800px (3:2 ratio)
- **Format:** JPG (smaller file size)
- **Quality:** 80-85% compression

### About Section
- **Size:** 500x600px (5:6 ratio)
- **Format:** PNG (for transparency) or JPG
- **Quality:** High quality (90%+)

### Service Icons
- Use Font Awesome icons (no image upload needed)
- Add tech icons via comma-separated Font Awesome classes

## Fallback to URLs

If you prefer not to upload:

1. Host images on external service (Imgur, Cloudinary, etc.)
2. Use full URL in **"Image URL"** field
3. Leave **"Upload Image"** empty

Example: `https://example.com/images/portfolio-1.jpg`

## Batch Upload

Currently, single file upload is supported. For batch uploads:

1. Upload images one by one
2. Or use FTP/SFTP to upload to `/uploads` folder
3. Then reference in admin panel

## API Reference

### uploadImage()

```php
$result = uploadImage($_FILES['image'], 'uploads');

if ($result['success']) {
    $filename = $result['filename'];  // img_abc123.jpg
    $path = $result['path'];          // uploads/img_abc123.jpg
    $url = $result['url'];            // /uploads/img_abc123.jpg
} else {
    $error = $result['message'];
}
```

### deleteImage()

```php
deleteImage('img_abc123.jpg', 'uploads');
```

## Support

For issues:
1. Check file permissions
2. Verify file format
3. Check file size
4. Review error messages in admin panel

---

**Happy uploading!** 🎉
