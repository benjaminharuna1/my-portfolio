# Admin Profile & Website Settings Guide

## Overview

Your portfolio website now includes:
- ✅ **Admin Profile Management** - Edit personal information and password
- ✅ **Website Settings** - Manage logo and favicon
- ✅ **Dynamic Branding** - Logo and favicon display across the site

---

## Admin Profile Management

### Accessing Your Profile

1. Go to **Admin Dashboard**
2. Click **My Profile** in the sidebar
3. Or navigate to `/admin/profile.php`

### Profile Information

Edit your personal details:

**Fields Available:**
- **First Name** - Your first name
- **Last Name** - Your last name
- **Email Address** - Contact email (required)
- **Phone Number** - Contact phone
- **Bio** - About you (displayed on website)
- **Profile Picture** - Avatar/profile image

### Updating Profile

1. Fill in your information
2. Upload profile picture or provide URL
3. Click **Save Profile**
4. Confirmation message appears

### Profile Picture

**Upload Options:**
- Direct upload (Max 5MB)
- External URL

**Recommended Size:**
- 300x300px (square)
- Format: JPG or PNG

### Changing Password

1. Scroll to **Change Password** section
2. Enter current password
3. Enter new password (min 6 characters)
4. Confirm new password
5. Click **Update Password**

**Password Requirements:**
- Minimum 6 characters
- Must match confirmation
- Current password required for verification

---

## Website Settings

### Accessing Settings

1. Go to **Admin Dashboard**
2. Click **Website Settings** in the sidebar
3. Or navigate to `/admin/settings.php`

### Website Information

**Fields:**
- **Website Name** - Your portfolio name
- **Website Description** - SEO description

### Logo Management

**Upload Logo:**
1. Click **Upload Logo**
2. Select image file
3. Or provide URL
4. Click **Save Settings**

**Logo Specifications:**
- Max 5MB
- Recommended: 200x100px or square
- Format: PNG, JPG, GIF, WebP
- Displays in navbar

**Logo Display:**
- Appears in website header/navbar
- Replaces default icon
- Responsive sizing

### Favicon Management

**Upload Favicon:**
1. Click **Upload Favicon**
2. Select image file
3. Or provide URL
4. Click **Save Settings**

**Favicon Specifications:**
- Max 5MB
- Recommended: 64x64px or 32x32px
- Format: PNG or ICO
- Displays in browser tab

**Favicon Display:**
- Browser tab icon
- Bookmarks
- Address bar
- Browser history

---

## How It Works

### Logo Display

**Location:** Website header/navbar
**Size:** Automatically scaled to fit
**Fallback:** Default icon if not set

```html
<!-- Logo in navbar -->
<img src="logo.png" alt="Website Name" style="height: 40px;">
```

### Favicon Display

**Location:** Browser tab
**Size:** 64x64px or 32x32px
**Fallback:** None (browser default)

```html
<!-- Favicon in head -->
<link rel="icon" href="favicon.png" type="image/x-icon">
```

---

## Best Practices

### Logo Design

- **Keep it simple** - Recognizable at small sizes
- **Use high contrast** - Visible on dark navbar
- **Square format** - Works best
- **Transparent background** - PNG recommended
- **High resolution** - 2x size for retina displays

### Favicon Design

- **Simple design** - Recognizable at 16x16px
- **High contrast** - Visible on any background
- **Square format** - 64x64px or 32x32px
- **Solid colors** - Avoid gradients
- **PNG or ICO** - Best formats

### Website Information

- **Name:** Keep it concise (under 50 chars)
- **Description:** 150-160 characters for SEO
- **Keywords:** Include in description

---

## Troubleshooting

### Logo Not Showing

**Check:**
1. File uploaded successfully
2. File format supported (PNG, JPG, GIF, WebP)
3. File size under 5MB
4. Browser cache cleared
5. /uploads folder has write permissions

**Solution:**
- Clear browser cache (Ctrl+Shift+Delete)
- Try uploading again
- Check file permissions

### Favicon Not Showing

**Check:**
1. File uploaded successfully
2. File format is PNG or ICO
3. File size under 5MB
4. Browser cache cleared
5. Correct file path

**Solution:**
- Clear browser cache
- Hard refresh (Ctrl+F5)
- Try different browser
- Check file format

### Changes Not Appearing

**Reasons:**
- Browser cache
- CDN cache
- Server cache

**Solutions:**
1. Clear browser cache
2. Hard refresh (Ctrl+F5)
3. Try incognito/private mode
4. Wait 5-10 minutes for CDN
5. Check file permissions

---

## Database Structure

### users Table (Updated)

```sql
id              INT PRIMARY KEY
username        VARCHAR(50) UNIQUE
password        VARCHAR(255)
email           VARCHAR(100)
first_name      VARCHAR(100)
last_name       VARCHAR(100)
phone           VARCHAR(20)
bio             TEXT
avatar_url      VARCHAR(255)
avatar_filename VARCHAR(255)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### website_settings Table (New)

```sql
id                  INT PRIMARY KEY
logo_url            VARCHAR(255)
logo_filename       VARCHAR(255)
favicon_url         VARCHAR(255)
favicon_filename    VARCHAR(255)
website_name        VARCHAR(200)
website_description TEXT
updated_at          TIMESTAMP
```

---

## File Structure

```
admin/
├── profile.php          ← Admin profile management
├── settings.php         ← Website settings
└── dashboard.php        ← Updated with new links

includes/
└── header.php           ← Updated with logo/favicon

database.sql            ← Updated schema
```

---

## Security

### Profile Security

- ✅ Password hashing (bcrypt)
- ✅ Current password verification
- ✅ Session-based access
- ✅ Input validation

### File Upload Security

- ✅ File type validation
- ✅ File size limits (5MB)
- ✅ Unique filename generation
- ✅ Dedicated storage folder

---

## Features Summary

### Admin Profile

| Feature | Details |
|---------|---------|
| Edit Profile | First name, last name, email, phone, bio |
| Profile Picture | Upload or URL |
| Change Password | Secure password update |
| Account Info | View account details |
| Member Since | Account creation date |
| Last Updated | Last modification date |

### Website Settings

| Feature | Details |
|---------|---------|
| Website Name | Portfolio name |
| Description | SEO description |
| Logo | Upload or URL |
| Favicon | Upload or URL |
| Preview | Live preview of logo/favicon |

---

## Tips & Tricks

### Logo Tips

- Use your brand colors
- Keep it professional
- Test at different sizes
- Use PNG for transparency
- Consider dark navbar background

### Favicon Tips

- Make it distinctive
- Use brand colors
- Keep it simple
- Test in multiple browsers
- Use 64x64px for best quality

### Profile Tips

- Keep bio concise (2-3 sentences)
- Use professional photo
- Update regularly
- Include contact info
- Add social links

---

## Common Issues

### Issue: Logo appears stretched

**Solution:**
- Use square format
- Adjust CSS if needed
- Try different image size

### Issue: Favicon not updating

**Solution:**
- Clear browser cache
- Hard refresh (Ctrl+F5)
- Try different browser
- Wait for CDN cache clear

### Issue: Password change fails

**Solution:**
- Verify current password
- Check password length (min 6)
- Ensure passwords match
- Try again

---

## Support

For help:
1. Check troubleshooting section
2. Review code comments
3. Check browser console
4. Verify file permissions
5. Check database connection

---

## Version Info

- **Feature:** Admin Profile & Website Settings v1.0
- **Release Date:** November 30, 2025
- **Status:** Production Ready
- **Database:** Updated schema included

---

**Manage your profile and website branding easily!** 🎨
