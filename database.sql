-- Portfolio Database Schema

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS portfolio_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255),
    category VARCHAR(100),
    link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS about (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    subtitle VARCHAR(255),
    description TEXT NOT NULL,
    image_url VARCHAR(255),
    email VARCHAR(100),
    phone VARCHAR(20),
    location VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS social_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, email) VALUES 
('admin', '$2y$10$YIjlrDxR8.8Yd8Yd8Yd8eYIjlrDxR8.8Yd8Yd8Yd8eYIjlrDxR8.8', 'admin@portfolio.com');

-- Insert sample data
INSERT INTO services (title, description, icon) VALUES 
('Web Design', 'Creating beautiful and responsive web designs', 'fa-palette'),
('Web Development', 'Building robust and scalable web applications', 'fa-code'),
('UI/UX Design', 'Designing intuitive user interfaces and experiences', 'fa-pencil-ruler');

INSERT INTO about (title, subtitle, description, email, phone, location) VALUES 
('About Me', 'Web Developer & Designer', 'I am a passionate web developer and designer with expertise in creating beautiful and functional websites.', 'contact@portfolio.com', '+1 (555) 123-4567', 'New York, USA');

INSERT INTO social_links (platform, url, icon) VALUES 
('LinkedIn', 'https://linkedin.com', 'fab fa-linkedin-in'),
('Instagram', 'https://instagram.com', 'fab fa-instagram'),
('GitHub', 'https://github.com', 'fab fa-github'),
('Twitter', 'https://twitter.com', 'fab fa-twitter');
