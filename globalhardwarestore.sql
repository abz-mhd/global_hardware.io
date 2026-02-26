-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2026 at 06:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `globalhardwarestore`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `created_at`) VALUES
(2, 'admin', '$2y$10$DZmPPd/vjPz16tNBCp.duei3zDX.qCbra4q0a0nPkK1xlmpHKx5aa', '2026-01-10 12:55:20');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `email`, `password`, `phone`, `address`, `status`, `created_at`) VALUES
(1, 'abzin', 'ab@gmail.com', '$2y$10$gUaVAUdt/0jCwDI/gM2FYe6YUEno4kZPOxEdlqD8K8FX1hVrKXhSW', '0775655656', 'Main Street Pottuvil 01', 'Active', '2026-01-10 13:07:10'),
(2, 'john', 'john20@gmail.com', '$2y$10$YECEoLukaMCw6/455yINWeTHZ/jCfc6TwXd/mR4WvIpymeCl6MdZy', '0753445856', 'Post Office Road Kandy', 'Active', '2026-01-12 06:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Processing','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `total_amount`, `status`) VALUES
(1, 1, '2026-01-10 13:12:39', 100.00, 'Processing'),
(2, 1, '2026-01-10 16:08:13', 1000.00, 'Pending'),
(3, 1, '2026-01-12 04:44:23', 1000.00, 'Pending'),
(4, 1, '2026-01-12 05:09:33', 1000.00, 'Pending'),
(5, 1, '2026-01-12 05:23:02', 1000.00, 'Pending'),
(6, 1, '2026-01-12 05:57:45', 1500.00, 'Pending'),
(7, 1, '2026-01-12 07:10:17', 1000.00, 'Pending'),
(8, 1, '2026-01-12 08:48:00', 600.00, 'Pending'),
(9, 1, '2026-01-12 10:10:59', 600.00, 'Processing'),
(10, 1, '2026-01-12 11:57:20', 9500.00, 'Pending'),
(11, 1, '2026-01-12 12:16:27', 40100.00, 'Processing'),
(12, 1, '2026-01-12 12:22:21', 2800.00, 'Processing'),
(13, 1, '2026-01-12 13:05:15', 3000.00, 'Processing'),
(14, 1, '2026-01-12 16:03:31', 3000.00, 'Pending'),
(15, 1, '2026-01-12 16:16:28', 5000.00, 'Processing'),
(16, 2, '2026-02-02 13:28:45', 3000.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(2, 2, 10, 1, 1000.00),
(3, 3, 22, 1, 1000.00),
(4, 4, 22, 1, 1000.00),
(5, 5, 22, 1, 1000.00),
(6, 6, 15, 1, 1500.00),
(7, 7, 22, 1, 1000.00),
(8, 8, 21, 1, 600.00),
(9, 9, 21, 1, 600.00),
(10, 10, 20, 1, 1500.00),
(11, 10, 19, 1, 8000.00),
(12, 11, 14, 2, 15000.00),
(13, 11, 19, 1, 8000.00),
(14, 11, 21, 1, 600.00),
(15, 11, 20, 1, 1500.00),
(16, 12, 13, 1, 2800.00),
(17, 13, 18, 1, 3000.00),
(18, 14, 18, 1, 3000.00),
(19, 15, 5, 1, 5000.00),
(20, 16, 9, 1, 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('Available','Out of Stock') DEFAULT 'Available',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `category`, `price`, `stock`, `description`, `image`, `status`, `created_at`) VALUES
(3, 'Hammer', 'Tools', 1200.00, 150, 'A strong and durable hammer for all your DIY needs.', 'product_696223cf53b43_1768039375.jpeg', 'Available', '2026-01-10 15:32:55'),
(4, 'Wrench Set', 'Tools', 3500.00, 150, 'A complete set for precision work and various applications.', 'product_6962290094528_1768040704.webp', 'Available', '2026-01-10 15:55:04'),
(5, 'Drill Machine', 'Tools', 5000.00, 149, 'A powerful drill for easy and efficient drilling of materials.', 'product_6962295491f14_1768040788.jpg', 'Available', '2026-01-10 15:56:28'),
(6, 'Screwdriver Set', 'Tools', 800.00, 150, 'A set of screwdrivers with multiple sizes for different tasks.', 'product_696229a8799c7_1768040872.jpg', 'Available', '2026-01-10 15:57:52'),
(7, 'Wall Paint', 'Paints', 1500.00, 150, 'Durable and vibrant colors for interior walls.', 'product_69622a9fc2d55_1768041119.webp', 'Available', '2026-01-10 16:01:59'),
(8, 'Glossy Paint', 'Paints', 2000.00, 150, 'High-quality glossy finish for furniture and decor.', 'product_69622af9ebf00_1768041209.webp', 'Available', '2026-01-10 16:03:29'),
(9, 'Exterior Paint', 'Paints', 3000.00, 149, 'Weather-resistant paint for outdoor surfaces.', 'product_69622b5dbbbdf_1768041309.jpg', 'Available', '2026-01-10 16:05:09'),
(10, 'Primer Paint', 'Paints', 1000.00, 149, 'Primer for smooth application of topcoat paints.', 'product_69622b8b6c2b4_1768041355.jpg', 'Available', '2026-01-10 16:05:55'),
(11, 'Electric Drill', 'Electronics', 4500.00, 150, 'High-powered drill for efficient work on various materials.', 'product_69622d71e5bf8_1768041841.jpg', 'Available', '2026-01-10 16:14:01'),
(12, 'Electric Saw', 'Electronics', 7000.00, 150, 'A fast and precise saw for cutting wood, metal, and plastic.', 'product_69622db00e11b_1768041904.jpg', 'Available', '2026-01-10 16:15:04'),
(13, 'Soldering Kit', 'Electronics', 2800.00, 149, 'Perfect for small electronics repairs and projects.', 'product_69622defe816a_1768041967.webp', 'Available', '2026-01-10 16:16:07'),
(14, 'Power Tool', 'Electronics', 15000.00, 148, 'Comprehensive power tools for every job, from drilling to sawing.', 'product_69622e3158e9b_1768042033.jpg', 'Available', '2026-01-10 16:17:13'),
(15, 'Plumbing Wrench', 'Plumbing', 1500.00, 149, 'Heavy-duty plumbing wrench for tightening and loosening pipes.', 'product_69622eb31777c_1768042163.jpg', 'Available', '2026-01-10 16:19:23'),
(16, 'Pipe Cutter', 'Plumbing', 2200.00, 150, 'Sharp and efficient pipe cutter for easy cutting of pipes.', 'product_69622f0bba1ed_1768042251.jpg', 'Available', '2026-01-10 16:20:51'),
(17, 'Plumbing Tape', 'Plumbing', 500.00, 150, 'Sealant tape for preventing leaks in plumbing joints.', 'product_69622f40e19df_1768042304.webp', 'Available', '2026-01-10 16:21:44'),
(18, 'Plumber\'s Snake', 'Plumbing', 3000.00, 148, 'Clears clogged drains and pipes with ease.', 'product_69622f83825f4_1768042371.jpg', 'Available', '2026-01-10 16:22:51'),
(19, 'Lawn Mower', 'Garden Tools', 8000.00, 148, 'Efficient and easy-to-use lawn mower for maintaining your yard', 'product_69622fe2d1ba7_1768042466.jpg', 'Available', '2026-01-10 16:24:26'),
(20, 'Pruning Shears', 'Garden Tools', 1500.00, 148, 'High-quality shears for trimming and shaping your plants.', 'product_6962300cab3ca_1768042508.jpg', 'Available', '2026-01-10 16:25:08'),
(21, 'Garden Trowel I', 'Garden Tools', 600.00, 147, 'Ideal for digging, planting, and moving small amounts of soil.', 'product_696230440765f_1768042564.jpg', 'Available', '2026-01-10 16:26:04'),
(22, 'Garden Rake', 'Garden Tools', 1000.00, 146, 'Rake leaves and debris easily with this sturdy garden rake.', 'product_6962306e2b53c_1768042606.webp', 'Available', '2026-01-10 16:26:46');

-- --------------------------------------------------------

--
-- Table structure for table `product_supplier`
--

CREATE TABLE `product_supplier` (
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_supplier`
--

INSERT INTO `product_supplier` (`product_id`, `supplier_id`) VALUES
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 3),
(12, 3),
(13, 3),
(14, 3),
(15, 4),
(16, 4),
(17, 4),
(18, 4),
(19, 1),
(20, 1),
(21, 1),
(22, 1);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(30) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `name`, `contact`, `email`, `address`, `created_at`) VALUES
(1, 'abzin', '0775655656', 'ab@gmail.com', 'nbhbkb', '2026-01-10 13:11:52'),
(2, 'Husain', '0725564582', 'husain@2001', '123 Street Kalmunai', '2026-01-10 16:00:57'),
(3, 'akmal', '0703586752', 'akmal2@gmail.com', '345,Main Street Colombo', '2026-01-10 16:11:47'),
(4, 'sabeeh', '0753565689', 'sabeeh20@gmail.com', 'old school street', '2026-01-10 16:13:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_supplier`
--
ALTER TABLE `product_supplier`
  ADD PRIMARY KEY (`product_id`,`supplier_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_supplier`
--
ALTER TABLE `product_supplier`
  ADD CONSTRAINT `product_supplier_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_supplier_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
