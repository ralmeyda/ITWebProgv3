-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2025 at 03:23 PM
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
-- Database: `fishda`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'Fish', '', '2025-10-26 12:47:41');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','accepted','declined','completed') NOT NULL DEFAULT 'pending',
  `source` varchar(50) NOT NULL DEFAULT 'shop',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `status`, `source`, `created_at`, `notified`) VALUES
(1, 3, 700.00, 'accepted', 'shop', '2025-11-16 14:13:19', 1),
(2, 3, 2050.00, 'accepted', 'shop', '2025-11-16 14:16:11', 0),
(3, 4, 850.00, 'accepted', 'shop', '2025-11-16 14:17:40', 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 46, 1, 350.00),
(2, 1, 45, 1, 350.00),
(3, 2, 46, 2, 350.00),
(4, 2, 45, 3, 350.00),
(5, 2, 47, 2, 150.00),
(6, 3, 46, 1, 350.00),
(7, 3, 47, 1, 150.00),
(8, 3, 45, 1, 350.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `description`, `price`, `stock_quantity`, `image_url`, `is_active`, `created_at`, `updated_at`) VALUES
(38, 1, 'Tilapia', 'qwerqet', 150.00, 5, 'admin/uploads/product_6915a2d19d0df_1763025617.jpeg', 0, '2025-11-13 09:20:17', '2025-11-13 09:24:35'),
(39, 1, 'Tilapia', 'Fresh Tilapia', 250.00, 5, 'admin/uploads/product_6915a3e4cf2b4_1763025892.jpeg', 0, '2025-11-13 09:24:52', '2025-11-13 09:49:07'),
(40, 1, 'Bangus', 'qwerrt', 135.00, 5, 'admin/uploads/product_6915a9a235a91_1763027362.jpeg', 0, '2025-11-13 09:49:22', '2025-11-16 07:14:43'),
(41, 1, 'Tilapia', 'TEST', 230.00, 5, 'admin/uploads/product_691979f624162_1763277302.jfif', 0, '2025-11-16 07:15:02', '2025-11-16 07:17:41'),
(42, 1, 'Tilapia', 'qegegqeg', 250.00, 15, 'admin/uploads/product_69197aa8862f0_1763277480.png', 0, '2025-11-16 07:18:00', '2025-11-16 08:06:03'),
(43, 1, 'Bangus', 'ETQTQETQET', 280.00, 2, 'admin/uploads/product_69197abcd6101_1763277500.png', 0, '2025-11-16 07:18:20', '2025-11-16 08:06:01'),
(44, 1, 'random', 'asdoakwdo', 15000.00, 3, 'admin/uploads/product_69197b561abec_1763277654.png', 0, '2025-11-16 07:20:54', '2025-11-16 07:21:12'),
(45, 1, 'Tilapia', 'gqegqegq', 350.00, 0, 'admin/uploads/product_69198607cfbb3_1763280391.png', 1, '2025-11-16 08:06:31', '2025-11-16 14:17:40'),
(46, 1, 'Bangus', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus auctor ullamcorper hendrerit. Nunc mollis erat lacus, sed pharetra leo commodo eget.', 350.00, 1, 'admin/uploads/product_69198944e21c3_1763281220.png', 1, '2025-11-16 08:20:20', '2025-11-16 14:17:40'),
(47, 1, 'Tilapia v2', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus auctor ullamcorper hendrerit. Nunc mollis erat lacus, sed pharetra leo commodo eget.', 150.00, 2, 'admin/uploads/product_69198959cb4a8_1763281241.png', 1, '2025-11-16 08:20:41', '2025-11-16 14:17:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `user_type` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone`, `address`, `user_type`, `created_at`, `last_login`) VALUES
(1, 'admin', 'admin@cycride.com', '$2y$10$LfZoTVAn99I/r./DmAqQj.JjTMJuWmen0O2GzVlYXLXH3maawSJYW', 'Admin', 'User', '09123456789', NULL, 'admin', '2025-10-26 12:52:03', '2025-11-16 14:17:45'),
(3, 'almd', 'asdfasokdf@gmail.com', '$2y$10$MYZ8arvrGA0q2KX5Og6GfeTuR3ImSQUC4j2V0bGOxxek.oSi4xppK', 'almd', 'almd', '09619392234', NULL, 'customer', '2025-11-09 10:36:11', '2025-11-16 14:15:04'),
(4, 'almd3', 'almdsda@gmail.com', '$2y$10$G0.JXabXjF4P6uS8oKUyT.hfN.A9gSnDD828ae4FKK6zCwrglBV1O', 'craa', 'almd', '09349293812', NULL, 'customer', '2025-11-16 14:17:24', '2025-11-16 14:17:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `fk_items_order` (`order_id`),
  ADD KEY `fk_items_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
