-- Database for Digital Service Booking & Management System (DSBMS)

CREATE DATABASE IF NOT EXISTS dsbms_db;
USE dsbms_db;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Admin Table
CREATE TABLE IF NOT EXISTS admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Services Table
CREATE TABLE IF NOT EXISTS services (
    service_id INT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration_minutes INT NOT NULL COMMENT 'Duration in minutes',
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE CASCADE
);

-- 5. Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    booking_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
);

-- Insert Dummy Services
INSERT INTO services (service_id, service_name, description, price, duration_minutes, image_path) VALUES 
(101, 'PC & Laptop Repair', 'Diagnosis and repair of hardware and software issues.', 60.00, 120, 'laptop_repair.jpg'),
(102, 'Virus Removal & Protection', 'Complete system scan, virus removal, and antivirus installation.', 45.00, 60, 'virus_protection.jpg'),
(103, 'Data Recovery', 'Recover lost or deleted files from hard drives and USBs.', 100.00, 150, 'data_recovery.jpg'),
(104, 'Software Installation', 'Installation and configuration of operating systems and software.', 30.00, 45, 'software_install.jpg'),
(105, 'Wi-Fi & Network Setup', 'Home or office network configuration and troubleshooting.', 55.00, 90, 'network_setup.jpg');

-- Insert Dummy Users (Password: user123)
INSERT INTO users (full_name, email, password, phone) VALUES 
('John Doe', 'john@example.com', 'user123', '0123456789'),
('Alice Smith', 'alice@example.com', 'user123', '0987654321');

