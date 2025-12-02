# Email System Guide

Your portfolio website has a complete email notification system that automatically sends emails to both users and admins when messages are submitted or reviews are left.

## Overview

The email system handles two main scenarios:

1. **Contact Form Submissions** - Users submit contact messages
2. **Review Submissions** - Users leave reviews on portfolio projects

## Email Flow

### Contact Form Emails

When a user submits a contact message:

1. **User receives:** Contact confirmation email
   - Confirms their message was received
   - Provides contact information
   - Includes social media links
   - Template: `contact-confirmation.html`

2. **Admin receives:** Contact notification email
   - Full message content
   - Sender's name and email
   - Link to admin messages dashboard
   - Template: `contact-notification-admin.html`

### Review Submission Emails

When a user submits a review:

1. **Reviewer receives:** Review thank you email
   - Thanks them for their review
   - Mentions the project they reviewed
   - Includes social media links
   - Template: `review-confirmation.html`

2. **Admin receives:** Review notification email
   - Review content and rating
   - Reviewer's name
   - Project title
   - Link to admin portfolio dashboard
   - Template: `review-notification-admin.html`

## Configuration

### Setting Up Email

1. Go to **Admin Dashboard > Settings**
2. Scroll to **Email Configuration** section
3. Fill in the following fields:

#### SMTP Settings

- **SMTP Host:** Your email provider's SMTP server
- **SMTP Port:** Usually 587 (TLS) or 465 (SSL)
- **SMTP Username:** Your email address or username
- **SMTP Password:** Your email password or app password

#### Email Details

- **From Email Address:** The email address emails will be sent from
- **From Name:** The name that appears as the sender
- **Admin Email Address:** Where admin notifications are sent
- **Enable Email Notifications:** Toggle to enable/disable all notifications

### Popular Email Providers

#### Gmail
- **SMTP Host:** smtp.gmail.com
- **SMTP Port:** 587
- **Username:** your-email@gmail.com
- **Password:** Use an [App Password](https://support.google.com/accounts/answer/185833), not your regular password

#### Outlook/Hotmail
- **SMTP Host:** smtp-mail.outlook.com
- **SMTP Port:** 587
- **Username:** your-email@outlook.com
- **Password:** Your Outlook password

#### SendGrid
- **SMTP Host:** smtp.sendgrid.net
- **SMTP Port:** 587
- **Username:** apikey
- **Password:** Your SendGrid API key

#### Mailgun
- **SMTP Host:** smtp.mailgun.org
- **SMTP Port:** 587
- **Username:** postmaster@your-domain.com
- **Password:** Your Mailgun password

## Testing Email

### Using the Test Email Tool

1. Go to **Admin Dashboard > Test Email**
2. Enter your email address
3. Click "Send Test" for either:
   - Contact Confirmation Email
   - Review Confirmation Email
4. Check your inbox (and spam folder) for the test email

### Checking Email Logs

1. Go to **Admin Dashboard > System Logs**
2. Look for entries with "Email sent to:" or "Email" in the message
3. Check for any error messages

## Email Templates

Email templates are located in `/templates/email/` directory:

- `contact-confirmation.html` - Sent to users who submit contact forms
- `contact-notification-admin.html` - Sent to admin for new contact messages
- `review-confirmation.html` - Sent to users who submit reviews
- `review-notification-admin.html` - Sent to admin for new reviews

### Customizing Templates

You can edit email templates to match your branding:

1. Open the template file in a text editor
2. Modify the HTML and styling
3. Use template variables like:
   - `{{SENDER_NAME}}` - User's name
   - `{{SENDER_EMAIL}}` - User's email
   - `{{MESSAGE}}` - Message content
   - `{{SITE_NAME}}` - Your website name
   - `{{CURRENT_YEAR}}` - Current year

## Troubleshooting

### Emails Not Sending

**Problem:** No emails are being sent

**Solutions:**
1. Check if email is enabled in Settings > Email Configuration
2. Verify SMTP settings are correct
3. Check if "Enable Email Notifications" is checked
4. Use Test Email tool to verify configuration
5. Check System Logs for error messages

### Emails Going to Spam

**Problem:** Emails are being marked as spam

**Solutions:**
1. Use a reputable email provider (Gmail, Outlook, SendGrid)
2. Set up SPF and DKIM records for your domain
3. Use a professional "From" email address
4. Avoid spam trigger words in email templates
5. Test with different email providers

### SMTP Authentication Failed

**Problem:** "SMTP authentication failed" error

**Solutions:**
1. Verify username and password are correct
2. For Gmail, use an App Password instead of your regular password
3. Check if SMTP port is correct (587 for TLS, 465 for SSL)
4. Ensure your email provider allows SMTP connections
5. Check if your IP is whitelisted (if required by provider)

### Connection Timeout

**Problem:** "Connection timeout" error

**Solutions:**
1. Verify SMTP host is correct
2. Check if port is correct (587 or 465)
3. Ensure your server can connect to external SMTP servers
4. Check firewall settings
5. Try a different email provider

## Email Variables

### Contact Emails

**contact-confirmation.html:**
- `{{SENDER_NAME}}` - User's name
- `{{CONTACT_EMAIL}}` - Your contact email
- `{{SOCIAL_LINKS}}` - Social media links
- `{{SITE_NAME}}` - Website name
- `{{CURRENT_YEAR}}` - Current year

**contact-notification-admin.html:**
- `{{SENDER_NAME}}` - User's name
- `{{SENDER_EMAIL}}` - User's email
- `{{MESSAGE}}` - Message content
- `{{ADMIN_LINK}}` - Link to admin messages
- `{{SITE_NAME}}` - Website name
- `{{CURRENT_YEAR}}` - Current year

### Review Emails

**review-confirmation.html:**
- `{{REVIEWER_NAME}}` - Reviewer's name
- `{{PORTFOLIO_TITLE}}` - Project title
- `{{SOCIAL_LINKS}}` - Social media links
- `{{SITE_NAME}}` - Website name
- `{{CURRENT_YEAR}}` - Current year

**review-notification-admin.html:**
- `{{PORTFOLIO_TITLE}}` - Project title
- `{{REVIEWER_NAME}}` - Reviewer's name
- `{{RATING_STARS}}` - Star rating display
- `{{RATING_TEXT}}` - Rating text (e.g., "Excellent (5/5)")
- `{{REVIEW_TEXT}}` - Review content
- `{{ADMIN_LINK}}` - Link to admin portfolio
- `{{SITE_NAME}}` - Website name
- `{{CURRENT_YEAR}}` - Current year

## Best Practices

1. **Use a professional email address** - Use your domain email, not a personal account
2. **Test before going live** - Use the Test Email tool to verify everything works
3. **Monitor logs** - Check System Logs regularly for email errors
4. **Keep credentials secure** - Don't share SMTP passwords
5. **Use app passwords** - For Gmail and other providers, use app-specific passwords
6. **Customize templates** - Make emails match your brand
7. **Enable notifications** - Keep email notifications enabled to stay informed
8. **Check spam folder** - Occasionally check spam to ensure emails aren't being filtered

## Security

- SMTP passwords are stored securely in the database
- Email content is sanitized to prevent injection attacks
- Sensitive information (like admin email) is not exposed to users
- All email sending is logged for audit purposes

## Support

If you encounter issues with the email system:

1. Check the System Logs for error messages
2. Use the Test Email tool to verify configuration
3. Review this guide for common solutions
4. Contact your email provider's support if SMTP issues persist
