-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 15, 2024 at 06:36 PM
-- Server version: 8.0.34
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `market-php-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `address_table`
--

DROP TABLE IF EXISTS `address_table`;
CREATE TABLE IF NOT EXISTS `address_table` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `city` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `district` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `addr` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `address_table_fk` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Dumping data for table `address_table`
--

INSERT INTO `address_table` (`id`, `email`, `city`, `district`, `addr`) VALUES
(1, 'ali@gmail.com', 'Kocaeli', 'Gebze', 'Asım Sokak no:70/54'),
(2, 'kizilay@gmail.com', 'Ankara', 'Kızılay', 'Meşrutiyet Caddesi no:19/08'),
(5, 'batuuzun81@gmail.com', 'Ankara', 'Kızılay', 'Göztepe Mahallesi, Afacan Sokak No:61/55'),
(6, 'mehmetenes@gmail.com', 'Ankara', 'Bahçelievler', 'Sorar Sokak no:14/7'),
(7, 'hakansibi@gmail.com', 'Ankara', 'Kızılay', 'Vadi Sokak no:78/3');

-- --------------------------------------------------------

--
-- Table structure for table `auth_table`
--

DROP TABLE IF EXISTS `auth_table`;
CREATE TABLE IF NOT EXISTS `auth_table` (
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `usrtoken` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Dumping data for table `auth_table`
--

INSERT INTO `auth_table` (`email`, `password`, `usrtoken`) VALUES
('ali@gmail.com', '$2a$10$dnztcVRKs52MDO/PI/HAkOyjJNVOTKCjaAlE1WOx/u4BH4p452b9W', NULL),
('batuuzun81@gmail.com', '$2a$10$oek6tXLRJQHOhX8IvF4qcuXsqbCJ4OGBBwce6KtiHwopDllIVOvWK', NULL),
('hakansibi@gmail.com', '$2a$10$JE7KG/7G8QQ2lYF4UiuyQuNn8MF6rosCYeNVu8zgs/5MYicENu8uW', NULL),
('kizilay@gmail.com', '$2a$10$4QeQjW1m4Zlx0mmKTKunBeNLeLSaywwZIa7Pt3jllTf2u3zgPJyxu', NULL),
('mehmetenes@gmail.com', '$2a$10$4QeQjW1m4Zlx0mmKTKunBeNLeLSaywwZIa7Pt3jllTf2u3zgPJyxu', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_table`
--

DROP TABLE IF EXISTS `company_table`;
CREATE TABLE IF NOT EXISTS `company_table` (
  `c_id` int NOT NULL AUTO_INCREMENT,
  `c_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `c_address_table` int DEFAULT NULL,
  `number_of_products` int DEFAULT NULL,
  `c_image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  PRIMARY KEY (`c_id`),
  KEY `c_address_table` (`c_address_table`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Dumping data for table `company_table`
--

INSERT INTO `company_table` (`c_id`, `c_name`, `c_address_table`, `number_of_products`, `c_image`) VALUES
(1, 'Kent Gıda', 5, 1, 'kentgida.png'),
(2, 'Ülker', 6, 1, 'ulker_logo_png.png'),
(3, 'Eti', 7, 2, 'eti.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `product_table`
--

DROP TABLE IF EXISTS `product_table`;
CREATE TABLE IF NOT EXISTS `product_table` (
  `p_id` int NOT NULL AUTO_INCREMENT,
  `p_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `p_stock` int DEFAULT NULL,
  `p_expire` date DEFAULT NULL,
  `c_id` int DEFAULT NULL,
  `p_image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci DEFAULT NULL,
  `p_price` decimal(10,2) DEFAULT NULL,
  `p_altprice` decimal(10,2) DEFAULT NULL,
  `p_discounted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`p_id`),
  KEY `c_id` (`c_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Dumping data for table `product_table`
--

INSERT INTO `product_table` (`p_id`, `p_name`, `p_stock`, `p_expire`, `c_id`, `p_image`, `p_price`, `p_altprice`, `p_discounted`) VALUES
(1, 'Falım Naneli', 50, '2024-08-15', 1, 'falım_nane.jpg', 1.50, 1.00, 0),
(2, 'Ülker Çikolatalı Gofret', 100, '2024-06-30', 2, 'ülker_gofret.jpg', 5.99, 4.99, 0),
(3, 'Eti Sütlü Çikolata', 50, '2024-08-15', 3, 'eti_sütlü_çikolata.jpg', 8.50, 5.99, 0),
(4, 'Eti Cin', 75, '2024-07-20', 3, 'eti_cin.jpg', 3.75, 2.99, 0);

-- --------------------------------------------------------

--
-- Table structure for table `role_table`
--

DROP TABLE IF EXISTS `role_table`;
CREATE TABLE IF NOT EXISTS `role_table` (
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `role` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Dumping data for table `role_table`
--

INSERT INTO `role_table` (`email`, `role`) VALUES
('ali@gmail.com', 'C'),
('batuuzun81@gmail.com', 'M'),
('hakansibi@gmail.com', 'M'),
('kizilay@gmail.com', 'C'),
('mehmetenes@gmail.com', 'M');

-- --------------------------------------------------------

--
-- Table structure for table `user_table`
--

DROP TABLE IF EXISTS `user_table`;
CREATE TABLE IF NOT EXISTS `user_table` (
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Dumping data for table `user_table`
--

INSERT INTO `user_table` (`email`, `name`) VALUES
('ali@gmail.com', 'Ali Gül'),
('batuuzun81@gmail.com', 'Kent Gıda'),
('hakansibi@gmail.com', 'Eti'),
('kizilay@gmail.com', 'Kızıl Ay'),
('mehmetenes@gmail.com', 'Ülker');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address_table`
--
ALTER TABLE `address_table`
  ADD CONSTRAINT `address_table_fk` FOREIGN KEY (`email`) REFERENCES `user_table` (`email`) ON DELETE CASCADE;

--
-- Constraints for table `company_table`
--
ALTER TABLE `company_table`
  ADD CONSTRAINT `company_table_ibfk_1` FOREIGN KEY (`c_address_table`) REFERENCES `address_table` (`id`);

--
-- Constraints for table `product_table`
--
ALTER TABLE `product_table`
  ADD CONSTRAINT `product_table_ibfk_1` FOREIGN KEY (`c_id`) REFERENCES `company_table` (`c_id`);

--
-- Constraints for table `user_table`
--
ALTER TABLE `user_table`
  ADD CONSTRAINT `user_auth_table_fk` FOREIGN KEY (`email`) REFERENCES `auth_table` (`email`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
