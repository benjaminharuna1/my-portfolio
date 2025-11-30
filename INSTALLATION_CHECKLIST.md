# Installation Checklist

## Pre-Installation Requirements

- [ ] PHP 7.4+ installed
- [ ] MySQL 5.7+ installed
- [ ] Web server (Apache/Nginx) running
- [ ] phpMyAdmin or MySQL CLI access
- [ ] Text editor or IDE

## Installation Steps

### 1. Database Setup
- [ ] Create new MySQL database: `portfolio_db`
- [ ] Import `database.sql` file
- [ ] Verify tables created (6 tables total)
- [ ] Check default admin user exists

### 2. File Configuration
- [ ] Open `config.php`
- [ ] Update `DB_HOST` (usually 'localhost')
- [ ] Update `DB_USER` (usually 'root')
- [ ] Update `DB_PASS` (your MySQL password)
- [ ] Update `SITE_URL` (your local/production URL)
- [ ] Update `SITE_NAME` (your portfolio name)
- [ ] Save `config.php`

### 3. File Placement
- [ ] Place all files in web root directory
- [ ] Verify folder structure matches README
- [ ] Check file permissions (readable)
- [ ] Ensure `admin/` folder exists
- [ ] Ensure `assets/` folder exists
- [ ] Ensure `includes/` folder exists

### 4. Initial Access
- [ ] Navigate to `http://localhost/portfolio/`
- [ ] Verify home page loads
- [ ] Check navigation menu works
- [ ] Verify Bootstrap styling applied
- [ ] Check Font Awesome icons display

### 5. Admin Login
- [ ] Go to `http://localhost/portfolio/login.php`
- [ ] Login with username: `admin`
- [ ] Login with password: `admin123`
- [ ] Verify dashboard loads
- [ ] Check all menu items accessible

### 6. Content Management
- [ ] Add portfolio items via admin
- [ ] Add services via admin
- [ ] Edit about section
- [ ] Add social media links
- [ ] Test contact form

### 7. Frontend Testing
- [ ] Test home page
- [ ] Test about page
- [ ] Test portfolio page with filters
- [ ] Test contact form submission
- [ ] Verify messages appear in admin

### 8. Responsive Testing
- [ ] Test on desktop (1920px+)
- [ ] Test on tablet (768px)
- [ ] Test on mobile (375px)
- [ ] Verify navigation responsive
- [ ] Check images scale properly

### 9. Security Setup
- [ ] Change admin password
- [ ] Update database credentials
- [ ] Review security headers in .htaccess
- [ ] Test SQL injection prevention
- [ ] Test XSS protection

### 10. Production Deployment
- [ ] Update SITE_URL to production domain
- [ ] Enable HTTPS
- [ ] Set up SSL certificate
- [ ] Configure email for contact form
- [ ] Set up database backups
- [ ] Configure error logging
- [ ] Test all functionality on production

## File Checklist

### Root Files
- [ ] config.php
- [ ] database.sql
- [ ] index.php
- [ ] about.php
- [ ] portfolio.php
- [ ] contact.php
- [ ] login.php
- [ ] logout.php
- [ ] .htaccess
- [ ] README.md
- [ ] SETUP.md
- [ ] PROJECT_SUMMARY.md

### Admin Files
- [ ] admin/dashboard.php
- [ ] admin/portfolio.php
- [ ] admin/services.php
- [ ] admin/about.php
- [ ] admin/messages.php
- [ ] admin/social.php

### Include Files
- [ ] includes/header.php
- [ ] includes/footer.php

### Asset Files
- [ ] assets/css/style.css
- [ ] assets/css/admin.css
- [ ] assets/js/script.js

## Database Verification

### Tables Created
- [ ] users (admin authentication)
- [ ] portfolio_items (portfolio projects)
- [ ] services (services offered)
- [ ] about (about section)
- [ ] contact_messages (contact form)
- [ ] social_links (social media)

### Sample Data
- [ ] Admin user created
- [ ] 3 sample services added
- [ ] About section populated
- [ ] 4 social links added

## Functionality Testing

### Frontend Pages
- [ ] Home page loads correctly
- [ ] About page displays content
- [ ] Portfolio page shows items
- [ ] Contact form submits
- [ ] Navigation works on all pages
- [ ] Footer displays correctly

### Admin Dashboard
- [ ] Dashboard shows statistics
- [ ] Portfolio management works
- [ ] Services management works
- [ ] About editing works
- [ ] Message viewing works
- [ ] Social links management works

### Forms
- [ ] Contact form validates
- [ ] Contact form submits
- [ ] Admin forms save data
- [ ] Edit forms load data
- [ ] Delete functions work

## Performance Checklist

- [ ] Page load time < 2 seconds
- [ ] Images optimized
- [ ] CSS minified (optional)
- [ ] JavaScript minified (optional)
- [ ] Database queries optimized
- [ ] No console errors

## Security Checklist

- [ ] Admin password changed
- [ ] Database credentials secure
- [ ] No sensitive data in code
- [ ] Input validation working
- [ ] SQL injection prevented
- [ ] XSS protection enabled
- [ ] HTTPS enabled (production)
- [ ] Security headers set

## Backup Checklist

- [ ] Database backed up
- [ ] Files backed up
- [ ] Backup schedule set
- [ ] Restore procedure tested

## Post-Installation

- [ ] Document any customizations
- [ ] Create admin user guide
- [ ] Set up monitoring
- [ ] Configure analytics
- [ ] Test email notifications
- [ ] Create content guidelines

## Troubleshooting

If issues occur:
1. Check `config.php` database settings
2. Verify database tables exist
3. Check file permissions
4. Review error logs
5. Test database connection
6. Verify PHP version compatibility

## Support Resources

- README.md - Full documentation
- SETUP.md - Quick setup guide
- Code comments - Implementation details
- Database schema - Table structure

---

**Installation Complete!** Your portfolio website is ready to use.

Next: Customize content via admin dashboard and deploy to production.
