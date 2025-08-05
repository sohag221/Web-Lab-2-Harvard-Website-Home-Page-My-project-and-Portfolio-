-- SQL script to create the lab5 database and tables
-- Run this script in your MySQL server to set up the database

-- Create database
CREATE DATABASE IF NOT EXISTS lab5;
USE lab5;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create bio_data table
CREATE TABLE IF NOT EXISTS bio_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    occupation VARCHAR(255),
    education ENUM('high_school', 'bachelor', 'master', 'phd', 'other'),
    bio TEXT,
    profile_picture VARCHAR(255),
    newsletter BOOLEAN DEFAULT FALSE,
    terms BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE
);

-- Create an index on user_email for better performance
CREATE INDEX idx_user_email ON bio_data(user_email);

-- Show the created tables
SHOW TABLES;

-- Display table structures
DESCRIBE users;
DESCRIBE bio_data;
