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
