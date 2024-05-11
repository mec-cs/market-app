-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 18, 2024 at 10:06 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS=1;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Table structure for table 'auth'
CREATE TABLE IF NOT EXISTS auth_table (
  email VARCHAR(100) NOT NULL,
  passwd VARCHAR(100) NOT NULL,
  usrtoken VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (email)
);

-- Table structure for table 'user'
CREATE TABLE IF NOT EXISTS user_table (
  email VARCHAR(100) NOT NULL,
  u_name VARCHAR(100) NOT NULL,
  PRIMARY KEY (email),
  CONSTRAINT user_auth_fk FOREIGN KEY (email) REFERENCES auth_table(email) ON DELETE CASCADE
);

-- Table structure for table 'address'
CREATE TABLE IF NOT EXISTS address_table (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NOT NULL,
  city VARCHAR(25) NOT NULL,
  district VARCHAR(25),
  addr VARCHAR(100) NOT NULL,
  FOREIGN KEY (email) REFERENCES user_table(email) ON DELETE CASCADE
);

-- Table structure for table 'role'
CREATE TABLE IF NOT EXISTS role_table (
  email VARCHAR(100) NOT NULL,
  u_role CHAR(1) NOT NULL,
  PRIMARY KEY (email),
  FOREIGN KEY (email) REFERENCES auth_table(email) ON DELETE CASCADE
);

-- INSERT DATA
-- Insert data into the 'auth' table
INSERT INTO auth_table (email, passwd, usrtoken) VALUES
('ali@gmail.com', '$2a$10$e5fur6yolFMdD2fICktZBeMGVtwNjtajsoSnGYRiqDfvLj3aJbsG2', NULL);

-- Insert data into the 'user' table
INSERT INTO user_table (email, u_name) VALUES
('ali@gmail.com', 'Ali Gül');

-- Insert data into the 'address' table
INSERT INTO address_table (email, city, district, addr) VALUES
('ali@gmail.com', 'Ankara', 'Çankaya', 'Üniversiteler Mahallesi No:33');

-- Insert data into the 'role' table
INSERT INTO role_table (email, u_role) VALUES
('ali@gmail.com', 'C');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
