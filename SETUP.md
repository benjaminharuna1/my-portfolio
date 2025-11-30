# Quick Setup Guide

## Step 1: Database Setup

1. Open phpMyAdmin or MySQL command line
2. Create a new database:
   ```sql
   CREATE DATABASE portfolio_db;
   ```

3. Import the schema:
   ```sql
   USE portfolio_db;
   -- Copy and paste the contents of database.sql
   ```

## Step 2: Configure the Site

1. Open `config.php`
2. Update these values:
   ```php
   define('DB_HOST', 'localhost');      // Your database host
   define('DB_USER', 'root');           // Your database user
   define('DB_PASS', '');               // Your database password
   define('DB_NAME', 'portfolio_db');   // Database name
   define('SITE_URL', 'http://localhost/portfolio'); // Your site URL
   define('SITE_NAME', 'My Portfolio'); // Your site name
   define('ADMIN_EMAIL', 'admin@portfolio.com'); // Admin email
   ```

## Step 3: Place Files

1. Extract all files to your web server directory (e.g., `htdocs` for XAMPP)
2. Ensure the folder structure matches the README

## Step 4: Access the Site

1. **Frontend:** `http://localhost/portfolio/`
2. **Admin Login:** `http://localhost/portfolio/login.php`
   - Username: `admin`
   - Password: `admin123`

## Step 5: Customize Content

1. Login to admin dashboard
2. Update About section with your information
3. Add your services
4. Add portfolio items
5. Update social media links
6. Replace placeholder images with your own

## Troubleshooting

### Database Connection Error
- Check database credentials in `config.php`
- Ensure MySQL server is running
- Verify database exists

### Images Not Showing
- Use full URLs (http://...) for image URLs
- Or upload images to your server and reference them

### Admin Login Not Working
- Clear browser cookies
- Check database has users table
- Verify admin user exists in database

### 404 Errors
- Check file paths in config.php
- Ensure SITE_URL matches your actual URL
- Verify all files are in correct directories

## Next Steps

1. Change admin password
2. Add your portfolio items
3. Update about section
4. Add social media links
5. Customize colors in CSS files
6. Deploy to production server

## File Permissions

Ensure these directories are writable (if using file uploads):
- `uploads/` (create if needed)

## Production Deployment

Before going live:
1. Change all default passwords
2. Update database credentials
3. Set `SITE_URL` to your domain
4. Enable HTTPS
5. Add security headers
6. Backup database regularly
7. Monitor error logs

## Support

Refer to README.md for more information about features and customization.
