-- create_database_and_table.sql
CREATE DATABASE IF NOT EXISTS ums;
USE ums;

-- Create the users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(50)
);

-- Insert data into the users table
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('john_doe', 'john@example.com', '$2a$12$xEMB956OdZa1koH5Dv9e1e4eiw8A66Jy7DWL53Fuwcy6pHZ/Bailq', 'admin'); --- admin123
