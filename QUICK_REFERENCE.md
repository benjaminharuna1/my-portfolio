# Quick Reference Guide

## Admin Dashboard Access

| Feature | URL | Purpose |
|---------|-----|---------|
| Custom Icons | `/admin/icons.php` | Upload and manage SVG icons |
| Email Settings | `/admin/settings.php` | Configure SMTP and email |
| Test Email | `/admin/test-email.php` | Verify email configuration |
| Messages | `/admin/messages.php` | View contact messages |
| Portfolio | `/admin/portfolio.php` | Manage portfolio items and reviews |

---

## Email Configuration Quick Setup

### Gmail
```
SMTP Host: smtp.gmail.com
SMTP Port: 587
Username: your-email@gmail.com
Password: [App Password from Google Account]
From Email: your-email@gmail.com
Admin Email: your-email@gmail.com
```

### Outlook
```
SMTP Host: smtp-mail.outlook.com
SMTP Port: 587
Username: your-email@outlook.com
Password: Your Outlook password
From Email: your-email@outlook.com
Admin Email: your-email@outlook.com
```

---

## Using Custom Icons in Code

### Basic Display
```php
<?php require 'includes/icon-helper.php'; ?>
<?php echo getCustomIcon('icon-name'); ?>
```

### With Customization
```php
<?php echo getCustomIcon('icon-name', [
    'color' => '#667eea',
    'size' => 48,
    'title' => 'Icon Title'
]); ?>
```

### With Fallback
```php
<?php echo displayIcon('icon-name', ['size' => 32], 'fa-star'); ?>
```

---

## Email Flows

### Contact Form
```
User submits message
    ↓
User receives: Confirmation email
Admin receives: Notification email
    ↓
Message stored in database
```

### Review Submission
```
User submits review
    ↓
Reviewer receives: Thank you email
Admin receives: Notification email
    ↓
Review stored (pending approval)
```

---

## Common Tasks

### Add a Custom Icon
1. Go to `/admin/icons.php`
2. Click "Add New Icon"
3. Fill in name, category, color, size
4. Upload SVG file
5. Click "Add Icon"

### Test Email Configuration
1. Go to `/admin/test-email.php`
2. Enter your email address
3. Click "Send Test" for contact or review email
4. Check inbox for test email

### Configure Email
1. Go to `/admin/settings.php`
2. Scroll to "Email Configuration"
3. Enter SMTP details
4. Click "Save Email Settings"
5. Go to `/admin/test-email.php` to verify

### Display Icon on Website
```php
<?php
require 'includes/icon-helper.php';
echo displayIcon('download', ['size' => 32, 'color' => '#667eea'], 'fa-download');
?>
```

---

## Troubleshooting Checklist

### Emails Not Sending
- [ ] SMTP Host configured
- [ ] SMTP Port correct (587 or 465)
- [ ] Username and password correct
- [ ] From Email configured
- [ ] Admin Email configured
- [ ] Notifications enabled
- [ ] Test email sent successfully

### Icons Not Displaying
- [ ] Icon name matches exactly
- [ ] SVG file uploaded successfully
- [ ] Icon appears in admin grid
- [ ] `icon-helper.php` included in code
- [ ] Icon name used in `getCustomIcon()` call

### Emails Going to Spam
- [ ] Using professional email provider
- [ ] From email is professional address
- [ ] Email templates don't have spam triggers
- [ ] SPF/DKIM records configured (if available)

---

## File Locations

| File | Location | Purpose |
|------|----------|---------|
| Icon Manager | `/admin/icons.php` | Manage custom icons |
| Email Config | `/admin/settings.php` | Configure SMTP |
| Test Email | `/admin/test-email.php` | Test email system |
| Icon Helper | `/includes/icon-helper.php` | Icon functions |
| Email Config | `/includes/email-config.php` | Email system |
| Contact Form | `/contact.php` | Contact page |
| Reviews | `/reviews.php` | Reviews page |

---

## Database Tables

### custom_icons
- Stores SVG icon data
- Auto-created on first use
- Columns: id, name, slug, svg_content, category, color, size

### email_settings
- Stores SMTP configuration
- Auto-created on first use
- Columns: id, smtp_host, smtp_port, username, password, from_email, admin_email

---

## Email Templates

Located in `/templates/email/`:

| Template | Sent To | Purpose |
|----------|---------|---------|
| contact-confirmation.html | User | Confirm message received |
| contact-notification-admin.html | Admin | Notify of new message |
| review-confirmation.html | Reviewer | Thank for review |
| review-notification-admin.html | Admin | Notify of new review |

---

## Icon Helper Functions

```php
// Get icon by name
getCustomIcon($name, $options = [])

// Display with fallback
displayIcon($name, $options = [], $fallback = '')

// Get all icons
getAllCustomIcons($category = null)

// Get categories
getIconCategories()

// Check if exists
customIconExists($name)
```

---

## Email Config Functions

```php
// Load configuration
EmailConfig::load($conn)

// Get config value
EmailConfig::get($key, $default = null)

// Send email
EmailConfig::sendEmail($to, $subject, $body, $isHtml = true)

// Get template
EmailTemplate::contactConfirmation($name)
EmailTemplate::reviewConfirmation($name, $title)
```

---

## Useful Links

- [Gmail App Passwords](https://myaccount.google.com/apppasswords)
- [SendGrid SMTP](https://sendgrid.com/docs/for-developers/sending-email/smtp/)
- [Mailgun SMTP](https://documentation.mailgun.com/en/latest/quickstart-sending.html)
- [SVG Optimization](https://jakearchibald.github.io/svgomg/)

---

## Support

For detailed information, see:
- `CUSTOM_ICONS_GUIDE.md` - Icon system documentation
- `EMAIL_SYSTEM_GUIDE.md` - Email setup guide
- `IMPLEMENTATION_SUMMARY.md` - Complete implementation details
