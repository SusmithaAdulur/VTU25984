-- Migration: Create users table for authentication
-- Run this migration to add user authentication to your database

USE event_sync_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Example schema documentation for reference:
-- id - Auto-incrementing primary key
-- full_name - User's full name
-- email - User's email (unique constraint)
-- password - Hashed password using password_hash()
-- created_at - Account creation timestamp
-- updated_at - Last update timestamp
