# Dynamic Portfolio Website

A pure PHP portfolio website with Bootstrap CSS and an admin dashboard for managing content.

## Features

- **Frontend Pages**
  - Home page with hero section and featured work
  - About page with skills section
  - Portfolio page with filtering
  - Contact page with contact form
  - Responsive design with Bootstrap

- **Admin Dashboard**
  - Manage portfolio items
  - Manage services
  - Edit about section
  - View and manage contact messages
  - Manage social media links
  - Dashboard with statistics

- **Technology Stack**
  - Pure PHP (no frameworks)
  - MySQL database
  - Bootstrap 5 CSS framework
  - Font Awesome icons
  - Responsive design

## Installation

### 1. Database Setup

1. Create a new MySQL database named `portfolio_db`
2. Import the `database.sql` file:
   ```sql
   mysql -u root -p portfolio_db < database.sql
   ```

### 2. Configuration

1. Update `config.php` with your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'portfolio_db');
   define('SITE_URL', 'domain.com');
   ```

### 3. File Structure

```
portfolio/
├── config.php                 # Database configuration
├── database.sql              # Database schema
├── index.php                 # Home page
├── about.php                 # About page
├── portfolio.php             # Portfolio page
├── contact.php               # Contact page
├── login.php                 # Admin login
├── logout.php                # Admin logout
├── includes/
│   ├── header.php           # Header component
│   └── footer.php           # Footer component
├── admin/
│   ├── dashboard.php        # Admin dashboard
│   ├── portfolio.php        # Manage portfolio
│   ├── services.php         # Manage services
│   ├── about.php            # Manage about
│   ├── messages.php         # View messages
│   └── social.php           # Manage social links
├── assets/
│   ├── css/
│   │   ├── style.css        # Frontend styles
│   │   └── admin.css        # Admin styles
│   └── js/
│       └── script.js        # JavaScript
└── README.md
```

## Usage

### Frontend

1. Navigate to `domain.com/portfolio/`
2. Browse through Home, About, Portfolio, and Contact pages
3. Submit contact form to send messages

### Admin Dashboard

1. Login at `domain.com/login.php`
   - Username: `admin`
   - Password: `Admin.123`

2. From the dashboard, you can:
   - Add/Edit/Delete portfolio items
   - Add/Edit/Delete services
   - Edit about section
   - View contact messages
   - Manage social media links

## Default Admin Credentials

- **Username:** admin
- **Password:** admin123

**Important:** Change these credentials after first login!

## Customization

### Adding Images

1. Replace placeholder image URLs with your own
2. In admin panel, update image URLs for:
   - Portfolio items
   - About section
   - Services (use Font Awesome icons)

### Font Awesome Icons

The site uses Font Awesome 6.4.0. Available icon classes:
- `fa-palette` - Design
- `fa-code` - Development
- `fa-pencil-ruler` - UI/UX
- `fa-briefcase` - Portfolio
- `fa-cogs` - Services
- `fab fa-linkedin-in` - LinkedIn
- `fab fa-instagram` - Instagram
- `fab fa-github` - GitHub
- `fab fa-twitter` - Twitter

### Styling

Modify `assets/css/style.css` for frontend styling and `assets/css/admin.css` for admin panel styling.

## Database Tables

- **users** - Admin users
- **portfolio_items** - Portfolio projects
- **services** - Services offered
- **about** - About section content
- **contact_messages** - Contact form submissions
- **social_links** - Social media links

## Security Notes

1. Change default admin password immediately
2. Use prepared statements for production (current code uses basic escaping)
3. Add CSRF tokens for forms
4. Implement proper authentication middleware
5. Use HTTPS in production
6. Validate and sanitize all user inputs

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## License

This project is open source and available under the MIT License.

## Support

For issues or questions, please refer to the code comments or create an issue.
