-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 30, 2025 at 10:54 AM
-- Server version: 9.1.0
-- PHP Version: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portfolio`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text,
  `avatar_url` varchar(255) DEFAULT NULL,
  `avatar_filename` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `first_name`, `last_name`, `phone`, `bio`, `avatar_url`, `avatar_filename`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$a1ZlQ3AAVlETr2XWM9MrsOxhlOelw9Vxqg0Kcj08T7a9Rnr4MjHB.', 'admin@portfolio.com', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-30 10:43:53', '2025-11-30 10:43:53');

-- --------------------------------------------------------

--
-- Table structure for table `about`
--

DROP TABLE IF EXISTS `about`;
CREATE TABLE IF NOT EXISTS `about` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_filename` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `about`
--

INSERT INTO `about` (`id`, `title`, `subtitle`, `description`, `image_url`, `image_filename`, `email`, `phone`, `location`, `updated_at`) VALUES
(1, 'About Me', 'Web Developer & Designer', 'I am a passionate web developer and designer with expertise in creating beautiful and functional websites.', 'http://localhost/my-portfolio/uploads/img_692c1ebd24ff56.89028188.jpg', NULL, 'contact@portfolio.com', '+1 (555) 123-4567', 'New York, USA', '2025-11-30 10:44:19');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `created_at`, `is_read`) VALUES
(1, 'John Smith', 'john.smith@company.com', 'Hi! I\'m impressed with your portfolio. I\'m looking for a developer to build a custom e-commerce platform for my business. Would you be available for a consultation call next week?', '2025-11-28 14:30:00', 1),
(2, 'Sarah Johnson', 'sarah.j@designstudio.com', 'Your brand identity work is outstanding! We\'re rebranding our design studio and would love to discuss your services. Can you send me your rates and availability?', '2025-11-27 09:15:00', 1),
(3, 'Michael Chen', 'michael.chen@startup.io', 'We need help redesigning our website. Our current site is outdated and not converting well. I saw your corporate website redesign project - that\'s exactly what we need. Let\'s talk!', '2025-11-26 16:45:00', 0),
(4, 'Emma Wilson', 'emma.w@marketing.co', 'Great work on the SaaS dashboard! We\'re building a similar product and would like to hire you as a consultant. Are you open to contract work?', '2025-11-25 11:20:00', 0),
(5, 'David Rodriguez', 'david@techventures.com', 'Your mobile app UI design is beautiful. We\'re launching a fitness app and need someone to design the interface. Can we schedule a call to discuss the project scope and timeline?', '2025-11-24 13:00:00', 1),
(6, 'Lisa Anderson', 'lisa.anderson@agency.com', 'I\'m a project manager at a digital agency. We\'re looking for freelance designers and developers. Your portfolio is impressive. Would you be interested in working with us on client projects?', '2025-11-23 10:30:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_items`
--

DROP TABLE IF EXISTS `portfolio_items`;
CREATE TABLE IF NOT EXISTS `portfolio_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `body` longtext COMMENT 'Rich text content from QuillJS editor',
  `featured_image_url` varchar(255) DEFAULT NULL,
  `featured_image_filename` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'published',
  `is_featured` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `is_featured` (`is_featured`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `portfolio_items`
--

INSERT INTO `portfolio_items` (`id`, `title`, `description`, `body`, `featured_image_url`, `featured_image_filename`, `category`, `link`, `status`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 'Modern E-Commerce Platform', 'A fully responsive e-commerce platform built with React and Node.js featuring real-time inventory management and secure payment processing.', '<h3>Project Overview</h3><p>This e-commerce platform was designed to provide a seamless shopping experience across all devices. The project involved creating a robust backend API, implementing a modern frontend interface, and integrating multiple payment gateways.</p><h3>Technologies Used</h3><ul><li>React.js for frontend</li><li>Node.js and Express for backend</li><li>MongoDB for database</li><li>Stripe API for payments</li><li>Redux for state management</li></ul><h3>Key Features</h3><ul><li>Real-time inventory tracking</li><li>Secure user authentication</li><li>Multiple payment options</li><li>Order tracking system</li><li>Admin dashboard</li></ul>', 'https://via.placeholder.com/600x400?text=E-Commerce+Platform', NULL, 'Web Development', 'https://example.com/ecommerce', 'published', 1, '2025-11-20 10:00:00', '2025-11-30 10:00:00'),
(2, 'Brand Identity Design', 'Complete brand identity package including logo design, color palette, typography guidelines, and brand guidelines document for a tech startup.', '<h3>Project Overview</h3><p>Created a comprehensive brand identity for a growing tech startup. The project included extensive research, mood boarding, and multiple design iterations to arrive at a cohesive visual identity.</p><h3>Deliverables</h3><ul><li>Logo design (multiple variations)</li><li>Color palette with hex codes</li><li>Typography guidelines</li><li>Brand guidelines document</li><li>Social media templates</li><li>Business card design</li></ul><h3>Design Process</h3><p>The design process involved understanding the company\'s vision, target audience analysis, competitive research, and iterative design refinement based on client feedback.</p>', 'https://via.placeholder.com/600x400?text=Brand+Identity', NULL, 'Graphic Design', 'https://example.com/branding', 'published', 1, '2025-11-18 10:00:00', '2025-11-30 10:00:00'),
(3, 'Corporate Website Redesign', 'Complete redesign of a corporate website with improved UX/UI, faster load times, and better mobile responsiveness. Increased conversion rates by 45%.', '<h3>Project Overview</h3><p>The client\'s existing website was outdated and not performing well on mobile devices. We conducted a complete redesign focusing on user experience, modern design trends, and conversion optimization.</p><h3>Results</h3><ul><li>45% increase in conversion rates</li><li>60% improvement in page load time</li><li>95% mobile responsiveness score</li><li>Improved SEO rankings</li></ul><h3>Technologies</h3><ul><li>HTML5 and CSS3</li><li>JavaScript for interactivity</li><li>Responsive design framework</li><li>Performance optimization</li></ul>', 'https://via.placeholder.com/600x400?text=Corporate+Website', NULL, 'Web Design', 'https://example.com/corporate', 'published', 0, '2025-11-15 10:00:00', '2025-11-30 10:00:00'),
(4, 'Mobile App UI/UX Design', 'User interface and experience design for a fitness tracking mobile application with intuitive navigation and engaging visual design.', '<h3>Project Overview</h3><p>Designed a complete user interface for a fitness tracking application. The design focused on making complex fitness data easy to understand and motivating users to achieve their goals.</p><h3>Design Features</h3><ul><li>Intuitive navigation structure</li><li>Data visualization for fitness metrics</li><li>Motivational design elements</li><li>Accessibility considerations</li><li>Dark and light mode support</li></ul><h3>Tools Used</h3><ul><li>Figma for design</li><li>Adobe XD for prototyping</li><li>User testing and feedback</li></ul>', 'https://via.placeholder.com/600x400?text=Mobile+App+UI', NULL, 'Graphic Design', 'https://example.com/fitness-app', 'published', 0, '2025-11-12 10:00:00', '2025-11-30 10:00:00'),
(5, 'SaaS Dashboard Development', 'Built a comprehensive SaaS dashboard with real-time analytics, user management, and customizable reporting features for enterprise clients.', '<h3>Project Overview</h3><p>Developed a full-featured SaaS dashboard that allows enterprise clients to manage their operations, view analytics, and generate custom reports in real-time.</p><h3>Key Features</h3><ul><li>Real-time data visualization</li><li>User role management</li><li>Custom report generation</li><li>API integration</li><li>Data export functionality</li><li>Advanced filtering options</li></ul><h3>Technical Stack</h3><ul><li>Vue.js for frontend</li><li>Python Flask for backend</li><li>PostgreSQL database</li><li>Chart.js for visualizations</li></ul>', 'https://via.placeholder.com/600x400?text=SaaS+Dashboard', NULL, 'Web Development', 'https://example.com/saas', 'published', 1, '2025-11-10 10:00:00', '2025-11-30 10:00:00'),
(6, 'Marketing Collateral Design', 'Designed comprehensive marketing materials including brochures, flyers, social media graphics, and email templates for a B2B company.', '<h3>Project Overview</h3><p>Created a complete set of marketing collateral to support a B2B company\'s marketing campaigns across multiple channels.</p><h3>Deliverables</h3><ul><li>Tri-fold brochures</li><li>Product flyers</li><li>Social media graphics (Instagram, LinkedIn, Facebook)</li><li>Email newsletter templates</li><li>Presentation templates</li><li>Infographics</li></ul><h3>Design Approach</h3><p>All materials were designed to maintain brand consistency while being optimized for their specific use cases and platforms.</p>', 'https://via.placeholder.com/600x400?text=Marketing+Collateral', NULL, 'Graphic Design', 'https://example.com/marketing', 'published', 0, '2025-11-08 10:00:00', '2025-11-30 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_images`
--

DROP TABLE IF EXISTS `portfolio_images`;
CREATE TABLE IF NOT EXISTS `portfolio_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `portfolio_id` int NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `image_filename` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL COMMENT 'Accessibility alt text for the image',
  `sort_order` int DEFAULT '0' COMMENT 'Order of images in gallery',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `portfolio_id` (`portfolio_id`),
  KEY `sort_order` (`sort_order`),
  CONSTRAINT `fk_portfolio_images_portfolio_id` FOREIGN KEY (`portfolio_id`) REFERENCES `portfolio_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_ratings`
--

DROP TABLE IF EXISTS `portfolio_ratings`;
CREATE TABLE IF NOT EXISTS `portfolio_ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `portfolio_id` int NOT NULL,
  `rating` int NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `review_text` text,
  `reviewer_name` varchar(100) DEFAULT NULL,
  `reviewer_email` varchar(100) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `portfolio_id` (`portfolio_id`),
  KEY `is_approved` (`is_approved`),
  CONSTRAINT `fk_portfolio_ratings_portfolio_id` FOREIGN KEY (`portfolio_id`) REFERENCES `portfolio_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `portfolio_ratings`
--

INSERT INTO `portfolio_ratings` (`id`, `portfolio_id`, `rating`, `review_text`, `reviewer_name`, `reviewer_email`, `is_approved`) VALUES
(1, 1, 5, 'Exceptional work on our e-commerce platform! The developer delivered exactly what we needed on time and within budget. The platform is fast, secure, and our customers love the user experience. Highly recommended!', 'Alex Thompson', 'alex@ecommerce.com', 1),
(2, 1, 5, 'Outstanding technical expertise and attention to detail. The e-commerce solution has increased our sales by 35% in just three months. The support and maintenance have been excellent.', 'Jennifer Lee', 'jennifer@retail.com', 1),
(3, 2, 5, 'The brand identity design is absolutely stunning! Our new logo and brand guidelines have completely transformed how our company is perceived. The designer really understood our vision and delivered beyond expectations.', 'Marcus Johnson', 'marcus@startup.io', 1),
(4, 2, 4, 'Great design work and professional approach. The brand identity package was comprehensive and well-documented. Would definitely work together again on future projects.', 'Patricia Davis', 'patricia@creative.co', 1),
(5, 3, 5, 'Incredible website redesign! Our conversion rates improved dramatically, and the site loads so much faster now. The mobile experience is perfect. This investment paid for itself within weeks.', 'Robert Wilson', 'robert@corporate.com', 1),
(6, 3, 5, 'Professional, responsive, and results-driven. The team understood our business goals and delivered a website that not only looks great but performs exceptionally well. Highly satisfied!', 'Catherine Brown', 'catherine@business.com', 1),
(7, 4, 5, 'The mobile app UI design is beautiful and intuitive. Our users have given us fantastic feedback about the interface. The design really makes our fitness app stand out in the market.', 'Kevin Martinez', 'kevin@fitness.app', 1),
(8, 4, 4, 'Excellent design work with great attention to user experience. The app interface is clean and easy to navigate. Very happy with the final product.', 'Nicole Anderson', 'nicole@health.io', 1),
(9, 5, 5, 'The SaaS dashboard is a game-changer for our business! It\'s intuitive, powerful, and has significantly improved our operational efficiency. The real-time analytics are exactly what we needed.', 'Thomas Garcia', 'thomas@saas.com', 1),
(10, 5, 5, 'Outstanding development work! The dashboard is feature-rich, performs beautifully, and the code quality is excellent. Our team was impressed with the technical implementation.', 'Amanda White', 'amanda@enterprise.com', 1),
(11, 6, 4, 'Great marketing collateral design! All the materials are cohesive, professional, and have helped us maintain brand consistency across all channels. The designer was easy to work with.', 'Steven Taylor', 'steven@marketing.co', 1),
(12, 6, 5, 'Fantastic design work on our marketing materials! The brochures and social media graphics have significantly improved our marketing campaigns. Highly professional and creative.', 'Rachel Green', 'rachel@agency.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `description` text,
  `color` varchar(7) DEFAULT '#667eea',
  `icon` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `color`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'Web Design', 'web-design', 'Beautiful and responsive web designs', '#667eea', 'fa-palette', '2025-11-30 10:43:54', '2025-11-30 10:43:54'),
(2, 'Web Development', 'web-development', 'Robust and scalable web applications', '#00d4ff', 'fa-code', '2025-11-30 10:43:54', '2025-11-30 10:43:54'),
(3, 'Mobile App', 'mobile-app', 'iOS and Android mobile applications', '#ff6b6b', 'fa-mobile-alt', '2025-11-30 10:43:54', '2025-11-30 10:43:54'),
(4, 'UI/UX Design', 'ui-ux-design', 'Intuitive user interfaces and experiences', '#ffd93d', 'fa-pencil-ruler', '2025-11-30 10:43:54', '2025-11-30 10:43:54');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

DROP TABLE IF EXISTS `skills`;
CREATE TABLE IF NOT EXISTS `skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `proficiency` int NOT NULL CHECK (`proficiency` >= 0 AND `proficiency` <= 100),
  `category` varchar(100) DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sort_order` (`sort_order`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `name`, `proficiency`, `category`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Web Design', 90, 'Design', 1, '2025-11-30 10:43:54', '2025-11-30 10:43:54'),
(2, 'Web Development', 85, 'Development', 2, '2025-11-30 10:43:54', '2025-11-30 10:43:54'),
(3, 'UI/UX Design', 88, 'Design', 3, '2025-11-30 10:43:54', '2025-11-30 10:43:54'),
(4, 'PHP & MySQL', 92, 'Backend', 4, '2025-11-30 10:43:54', '2025-11-30 10:43:54');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `tech_icons` text,
  `status` enum('draft','published') DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `icon`, `tech_icons`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Web Design', 'Creating beautiful and responsive web designs', 'fa-palette', 'fab fa-figma, fab fa-adobe, fab fa-sketch', 'published', '2025-11-30 10:43:54', '2025-11-30 10:43:54'),
(2, 'Web Development', 'Building robust and scalable web applications', 'fa-code', 'fab fa-php, fab fa-laravel, fab fa-js, fab fa-react', 'published', '2025-11-30 10:43:54', '2025-11-30 10:43:54'),
(3, 'UI/UX Design', 'Designing intuitive user interfaces and experiences', 'fa-pencil-ruler', 'fab fa-figma, fab fa-adobe, fab fa-invision', 'published', '2025-11-30 10:43:54', '2025-11-30 10:43:54');

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

DROP TABLE IF EXISTS `social_links`;
CREATE TABLE IF NOT EXISTS `social_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `social_links`
--

INSERT INTO `social_links` (`id`, `platform`, `url`, `icon`, `created_at`) VALUES
(1, 'LinkedIn', 'https://linkedin.com', 'fab fa-linkedin-in', '2025-11-30 10:43:54'),
(2, 'Instagram', 'https://instagram.com', 'fab fa-instagram', '2025-11-30 10:43:54'),
(3, 'GitHub', 'https://github.com', 'fab fa-github', '2025-11-30 10:43:54'),
(4, 'Twitter', 'https://twitter.com', 'fab fa-twitter', '2025-11-30 10:43:54');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_name` varchar(100) NOT NULL,
  `client_title` varchar(100) DEFAULT NULL,
  `client_company` varchar(100) DEFAULT NULL,
  `client_image_url` varchar(255) DEFAULT NULL,
  `client_image_filename` varchar(255) DEFAULT NULL,
  `testimonial_text` text NOT NULL,
  `rating` int DEFAULT '5',
  `is_featured` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `client_name`, `client_title`, `client_company`, `client_image_url`, `client_image_filename`, `testimonial_text`, `rating`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Sarah Johnson', 'Marketing Director', 'Tech Innovations Inc', NULL, NULL, 'Exceptional work! The website redesign exceeded our expectations. Highly professional and responsive to feedback.', 5, 1, 1, '2025-11-30 10:43:54', '2025-11-30 10:43:54'),
(2, 'Michael Chen', 'CEO', 'Digital Solutions Ltd', NULL, NULL, 'Outstanding developer. Delivered the project on time and with excellent attention to detail. Highly recommended!', 5, 1, 1, '2025-11-30 10:43:54', '2025-11-30 10:43:54'),
(3, 'Emma Williams', 'Product Manager', 'Creative Agency Co', NULL, NULL, 'Great communication and technical expertise. The final product was exactly what we envisioned.', 5, 0, 1, '2025-11-30 10:43:54', '2025-11-30 10:43:54');

-- --------------------------------------------------------

--
-- Table structure for table `website_settings`
--

DROP TABLE IF EXISTS `website_settings`;
CREATE TABLE IF NOT EXISTS `website_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logo_url` varchar(255) DEFAULT NULL,
  `logo_filename` varchar(255) DEFAULT NULL,
  `favicon_url` varchar(255) DEFAULT NULL,
  `favicon_filename` varchar(255) DEFAULT NULL,
  `website_name` varchar(200) DEFAULT NULL,
  `website_description` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `website_settings`
--

INSERT INTO `website_settings` (`id`, `logo_url`, `logo_filename`, `favicon_url`, `favicon_filename`, `website_name`, `website_description`, `updated_at`) VALUES
(1, NULL, NULL, NULL, NULL, 'My Portfolio', 'A professional portfolio website showcasing my work and skills', '2025-11-30 10:43:54');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
