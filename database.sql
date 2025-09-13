-- Create database
CREATE DATABASE karen_country_club_assets;
USE karen_country_club_assets;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'staff') DEFAULT 'staff',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Assets table
CREATE TABLE assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id VARCHAR(20) UNIQUE,
    asset_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    purchase_date DATE NOT NULL,
    purchase_value DECIMAL(15,2) NOT NULL,
    description TEXT,
    status ENUM('Operational', 'Under Maintenance', 'Out of Service', 'Retired') DEFAULT 'Operational',
    location VARCHAR(100),
    estimated_lifespan INT COMMENT 'In years',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Maintenance table
CREATE TABLE maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    maintenance_type VARCHAR(50) NOT NULL,
    scheduled_date DATE NOT NULL,
    performed_date DATE,
    cost DECIMAL(10,2),
    description TEXT,
    status ENUM('Pending', 'Completed', 'Cancelled') DEFAULT 'Pending',
    technician VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample data
INSERT INTO assets (asset_name, category, purchase_date, purchase_value, description, status, location, estimated_lifespan) VALUES
('Tennis Court Roller', 'Sports Equipment', '2022-03-15', 185000, 'Heavy duty roller for maintaining tennis courts', 'Operational', 'Tennis Court Storage', 10),
('Golf Cart Fleet', 'Vehicles', '2021-11-08', 2450000, 'Set of 8 electric golf carts', 'Operational', 'Golf Course Garage', 7),
('Commercial Oven', 'Kitchen Equipment', '2023-01-20', 350000, 'Industrial oven for banquet kitchen', 'Operational', 'Main Kitchen', 15);