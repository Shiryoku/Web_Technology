-- Database: gym_db

CREATE DATABASE IF NOT EXISTS gym_db;
USE gym_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'trainer', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Trainer Profiles Table
CREATE TABLE IF NOT EXISTS trainers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    specialization VARCHAR(100),
    bio TEXT,
    image_url VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Programmes Table
CREATE TABLE IF NOT EXISTS programmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    trainer_id INT,
    duration VARCHAR(50), -- e.g. "4 Weeks"
    difficulty ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner',
    image_url VARCHAR(255),
    price DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE SET NULL
);

-- Seed Data
-- Password is 'password123' (hashed)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('john_trainer', 'john@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'trainer'),
('sarah_trainer', 'sarah@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'trainer'),
('jane_user', 'jane@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

INSERT INTO trainers (user_id, specialization, bio, image_url) VALUES
(2, 'Strength & Conditioning', 'Certified strength coach with 10 years experience.', 'https://img.freepik.com/free-photo/young-fitness-man-studio_7502-5008.jpg'),
(3, 'Yoga & Flexibility', 'Helping you find balance and flexibility.', 'https://img.freepik.com/free-photo/young-woman-doing-yoga-exercise-isolated-black-background_1303-24236.jpg');

INSERT INTO programmes (title, description, trainer_id, duration, difficulty, image_url, price) VALUES
('Muscle Builder 101', 'A comprehensive guide to building muscle mass.', 1, '8 Weeks', 'Intermediate', 'https://img.freepik.com/free-photo/heavy-weight-exercise_1098-1428.jpg', 49.99),
('Yoga for Beginners', 'Start your yoga journey with basic poses.', 2, '4 Weeks', 'Beginner', 'https://img.freepik.com/free-photo/woman-practicing-yoga-mat-home_1303-20703.jpg', 29.99),
('HIIT Burn', 'High Intensity Interval Training for fat loss.', 1, '6 Weeks', 'Advanced', 'https://img.freepik.com/free-photo/young-sports-man-training-gym_1303-20574.jpg', 39.99);
