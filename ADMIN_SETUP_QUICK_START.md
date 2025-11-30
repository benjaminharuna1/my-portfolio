# Admin Profile & Settings - Quick Start

## ⚡ 5-Minute Setup

### Step 1: Update Database

Run these SQL queries:

```sql
-- Add columns to users table
ALTER TABLE users ADD COLUMN first_name VARCHAR(100);
ALTER TABLE users ADD COLUMN last_name VARCHAR(100);
ALTER TABLE users ADD COLUMN phone VARCHAR(20);
ALTER TABLE users ADD COLUMN bio TEXT;
ALTER TABLE users ADD COLUMN avatar_url VARCHAR(255);
ALTER TABLE users ADD COLUMN avatar_filename VARCHAR(255);
ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Create website_settings table
CREATE TABLE website_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    logo_url VARCHAR(255),
    logo_filename VARCHAR(255),
    favicon_url VARCHAR(255),
    favicon_filename VARCHAR(255),
    website_name VARCHAR(200),
    website_description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO website_settings (website_name, website_description) VALUES 
('My Portfolio', 'A professional portfolio website showcasing my work and skills');
```

### Step 2: Update Files

Replace these files:
- `admin/dashboard.php` (add profile/settings links)
- `includes/header.php` (add logo/favicon display)
- `database.sql` (updated schema)

### Step 3: Add New Files

Add these files:
- `admin/profile.php` (profile management)
- `admin/settings.php` (website settings)

### Step 4: Test

1. Login to admin
2. Click **My Profile**
3. Update your information
4. Click **Website Settings**
5. Upload logo and favicon
6. Verify they display on frontend

---

## 🎯 Quick Tasks

### Update Your Profile

1. Admin → **My Profile**
2. Fill in your details
3. Upload profile picture
4. Click **Save Profile**

### Change Password

1. Admin → **My Profile**
2. Scroll to **Change Password**
3. Enter current password
4. Enter new password (min 6 chars)
5. Click **Update Password**

### Upload Logo

1. Admin → **Website Settings**
2. Click **Upload Logo**
3. Select image (200x100px recommended)
4. Click **Save Settings**
5. Logo appears in navbar

### Upload Favicon

1. Admin → **Website Settings**
2. Click **Upload Favicon**
3. Select image (64x64px recommended)
4. Click **Save Settings**
5. Favicon appears in browser tab

---

## 📸 Image Specifications

### Logo
- Size: 200x100px or square
- Format: PNG (recommended)
- Max: 5MB

### Favicon
- Size: 64x64px or 32x32px
- Format: PNG or ICO
- Max: 5MB

### Profile Picture
- Size: 300x300px
- Format: JPG or PNG
- Max: 5MB

---

## 🔑 Password Requirements

- Minimum 6 characters
- Must match confirmation
- Current password required
- Bcrypt hashed

---

## 📍 New Pages

**Admin Profile:** `/admin/profile.php`
- Edit personal info
- Upload avatar
- Change password
- View account details

**Website Settings:** `/admin/settings.php`
- Upload logo
- Upload favicon
- Edit website name
- Edit description

---

## ✅ Verification

After setup, verify:
- [ ] Profile page loads
- [ ] Settings page loads
- [ ] Can update profile
- [ ] Can change password
- [ ] Logo displays in navbar
- [ ] Favicon displays in tab
- [ ] Mobile responsive

---

## 🆘 Troubleshooting

**Logo not showing?**
- Clear browser cache (Ctrl+Shift+Delete)
- Hard refresh (Ctrl+F5)
- Check file format (PNG, JPG, GIF, WebP)

**Favicon not showing?**
- Clear browser cache
- Hard refresh (Ctrl+F5)
- Try PNG or ICO format

**Password change fails?**
- Verify current password
- Check password length (min 6)
- Ensure passwords match

---

## 📚 Full Documentation

See **ADMIN_PROFILE_SETTINGS_GUIDE.md** for complete details.

---

**You're all set!** 🎉
