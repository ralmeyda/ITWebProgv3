-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2025 at 05:49 PM
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
-- Database: `cycride`
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
(1, 'Bike Frame', '', '2025-10-26 12:47:41'),
(4, 'Equipment', '', '2025-10-26 12:47:41'),
(5, 'Mountain Bike', '', '2025-10-26 12:47:41'),
(6, 'Road Bike', '', '2025-10-26 12:47:41');

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
  `notified` tinyint(1) NOT NULL DEFAULT 0,
  `paid` tinyint(1) NOT NULL DEFAULT 0,
  `picked_up` tinyint(1) NOT NULL DEFAULT 0,
  `paid_at` datetime DEFAULT NULL,
  `picked_up_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `status`, `source`, `created_at`, `notified`, `paid`, `picked_up`, `paid_at`, `picked_up_at`) VALUES
(9, 3, 7410.00, 'accepted', 'shop', '2025-12-06 15:36:11', 1, 0, 0, NULL, NULL),
(10, 3, 3705.00, 'accepted', 'shop', '2025-12-06 15:41:23', 1, 0, 0, NULL, NULL),
(11, 5, 7710.00, 'accepted', 'shop', '2025-12-06 15:49:15', 0, 0, 0, NULL, NULL),
(12, 3, 14343.00, 'declined', 'shop', '2025-12-06 16:15:55', 1, 0, 0, NULL, NULL),
(13, 3, 742108.00, 'accepted', 'shop', '2025-12-06 16:41:53', 1, 0, 0, NULL, NULL),
(14, 3, 512700.00, 'accepted', 'shop', '2025-12-06 16:46:32', 1, 0, 0, NULL, NULL);

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
(15, 9, 63, 6, 1235.00),
(16, 10, 63, 3, 1235.00),
(17, 11, 63, 6, 35.00),
(18, 11, 64, 6, 1250.00),
(19, 12, 63, 1, 35.00),
(20, 12, 64, 5, 359.00),
(21, 12, 65, 1, 12513.00),
(22, 13, 75, 1, 175000.00),
(23, 13, 76, 1, 185000.00),
(24, 13, 77, 1, 189758.00),
(25, 13, 78, 1, 192350.00),
(26, 14, 78, 1, 192350.00),
(27, 14, 72, 1, 5000.00),
(28, 14, 66, 1, 315350.00);

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
(60, 1, 'TEST', 'TEST', 250.00, 5, 'uploads/1765034985_bframe1.png', 0, '2025-12-06 15:29:45', '2025-12-06 15:32:40'),
(61, 4, 'TEST', 'TEST', 150.00, 5, 'uploads/1765035038_gl1.png', 0, '2025-12-06 15:30:38', '2025-12-06 15:32:41'),
(62, 6, 'TEST', 'qwer', 170.00, 5, 'uploads/1765035063_helm4.png', 0, '2025-12-06 15:31:03', '2025-12-06 15:32:36'),
(63, 4, 'TEST', 'TESTING', 35.00, 9, 'uploads/1765035277_bframe1.png', 0, '2025-12-06 15:34:37', '2025-12-06 16:20:32'),
(64, 4, 'TEST', 'TEST', 359.00, 29, 'uploads/1765036124_helm3.png', 0, '2025-12-06 15:48:44', '2025-12-06 16:20:28'),
(65, 1, 'test', 'TSET', 12513.00, 15, 'uploads/1765036623_bframe8.png', 0, '2025-12-06 15:57:03', '2025-12-06 16:20:30'),
(66, 6, '2024 S-Works Tarmac SL8', 'Weight: 1.37kg (3 lb, .3 oz)\r\nFork: S-Works FACT 12r Carbon, 12x100mm thru-axle, flat-mount disc\r\nFrame: S-Works Tarmac SL8 FACT 12r Carbon, Rider First Engineered™, Win Tunnel Engineered, Clean Routing, Threaded BB, 12x142mm thru-axle, flat-mount disc. 685g frame.', 315350.00, 4, 'uploads/1765038337_product2.jpg', 1, '2025-12-06 16:25:37', '2025-12-06 16:46:52'),
(67, 6, 'Specialized Aethos S-Works Di2 Gloss', 'Frameset\r\nFrame\r\n\r\nS-Works Aethos FACT 12r Carbon, Rider First Engineered™, Threaded BB, Electronic cable routing only, 12x142mm thru-axle, flat-mount disc\r\n\r\nSuspension\r\nFork\r\n\r\nS-Works FACT Carbon, 12x100mm thru-axle, flat-mount disc\r\n\r\nBrakes\r\nFront Brake\r\n\r\nShimano Dura-Ace 9200, Hydraulic Disc\r\n\r\nRear Brake\r\n\r\nShimano Dura-Ace 9200, Hydraulic Disc\r\n\r\nDrivetrain\r\nShift Levers\r\n\r\nShimano Dura-Ace R9270, hydraulic disc\r\n\r\nFront Derailleur\r\n\r\nShimano Dura-Ace R9250, braze-on\r\n\r\nRear Derailleur\r\n\r\nShimano Dura-Ace R9250, 12-speed\r\n\r\nCassette\r\n\r\nShimano Dura-Ace, 12-speed, 11-30t\r\n\r\nChain\r\n\r\nShimano XTR M9100, 12-speed w/ quick link\r\n\r\nCrankset\r\n\r\nShimano Dura-Ace R9200, HollowTech II, 12-speed with 4iiii Precision Pro dual-sided powermeter\r\n\r\nChainrings\r\n\r\n52/36T\r\n\r\nBottom Bracket\r\n\r\nShimano Dura-Ace, BB-R9100\r\n\r\nWheels & Tires\r\nFront Tire\r\n\r\nS-works Turbo 2BR, 700x28mm\r\n\r\nFront Wheel\r\n\r\nRoval Alpinist CLX, Tubeless, 21mm internal width carbon rim, 33mm depth, Win Tunnel Engineered, Roval AFD hub, 21h, DT Swiss Aerolite spokes\r\n\r\nRear Tire\r\n\r\nS-works Turbo 2BR, 700x28mm\r\n\r\nRear Wheel\r\n\r\nRoval Alpinist CLX, Tubeless, 21mm internal width carbon rim, 33mm depth, Win Tunnel Engineered, Roval AFD hub, 24h, DT Swiss Aerolite spokes\r\n\r\nInner Tubes\r\n\r\n700x20-28, 48mm Presta Valve\r\n\r\nCockpit\r\nStem\r\n\r\nS-Works SL, alloy, titanium bolts, 6-degree rise\r\n\r\nHandlebars\r\n\r\nS-Works Short & Shallow, 123mm Drop, 75mm Reach w/Di2 Hole\r\n\r\nTape\r\n\r\nSupacaz Super Sticky Kush\r\n\r\nSaddle\r\n\r\nBody Geometry S-Works Power, carbon fiber rails, carbon fiber base\r\n\r\nSeatPost\r\n\r\nRoval Alpinist Carbon Seatpost\r\n\r\nSeat Binder\r\n\r\nSpecialized Alloy, 30.0mm, titanium bolt\r\n\r\nWeight\r\nWeight\r\n\r\n6.34kg (13 lb, 15.6 oz)\r\n\r\nWeight Size\r\n\r\n56', 350125.00, 6, 'uploads/1765038902_product3.jpg', 1, '2025-12-06 16:35:02', '2025-12-06 16:35:02'),
(68, 6, 'Specialized S-Works Tarmac SL7 Frameset', '', 350734.00, 8, 'uploads/1765038995_product4.jpg', 1, '2025-12-06 16:36:35', '2025-12-06 16:36:35'),
(69, 4, 'ROCKBROS Lightweight Bicycle Helmet', '', 2353.00, 10, 'uploads/1765039037_helm6.png', 1, '2025-12-06 16:37:17', '2025-12-06 16:37:17'),
(70, 4, 'Limar Maloja Road Helmet', '', 3750.00, 12, 'uploads/1765039060_helm5.png', 1, '2025-12-06 16:37:40', '2025-12-06 16:37:40'),
(71, 4, 'SPYDER Carve S3 Bike Helmet', '', 7000.00, 6, 'uploads/1765039081_helm4.png', 1, '2025-12-06 16:38:01', '2025-12-06 16:38:01'),
(72, 4, 'Meigu Bicycle Helmet Ultralight', '', 5000.00, 9, 'uploads/1765039103_helm3.png', 1, '2025-12-06 16:38:23', '2025-12-06 16:46:52'),
(73, 4, 'Giro Fixture MIPS Mtb Helmet', '', 4305.00, 8, 'uploads/1765039126_helm2.png', 1, '2025-12-06 16:38:46', '2025-12-06 16:38:46'),
(74, 4, 'ASIEVIE Bike Helmet', '', 5300.00, 8, 'uploads/1765039149_helm1.png', 1, '2025-12-06 16:39:09', '2025-12-06 16:39:09'),
(75, 1, 'Cervelo S5 Disc Road Frameset 2020', '', 175000.00, 6, 'uploads/1765039196_bframe1.png', 1, '2025-12-06 16:39:56', '2025-12-06 16:42:16'),
(76, 1, 'Cervelo S5 2025 Road Frame', '', 185000.00, 5, 'uploads/1765039218_bframe2.png', 1, '2025-12-06 16:40:18', '2025-12-06 16:42:16'),
(77, 1, 'TREK PROCALIBER 2023 FRAME', '', 189758.00, 7, 'uploads/1765039251_bframe3.png', 1, '2025-12-06 16:40:51', '2025-12-06 16:42:16'),
(78, 1, 'TREK EMONDA SLR DISC FRAMESET 2021', '', 192350.00, 4, 'uploads/1765039275_bframe4.png', 1, '2025-12-06 16:41:15', '2025-12-06 16:46:52');

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
(1, 'admin', 'admin@cycride.com', '$2y$10$LfZoTVAn99I/r./DmAqQj.JjTMJuWmen0O2GzVlYXLXH3maawSJYW', 'Admin', 'User', '09123456789', NULL, 'admin', '2025-10-26 12:52:03', '2025-12-06 16:46:41'),
(3, 'almd', 'asdfasokdf@gmail.com', '$2y$10$MYZ8arvrGA0q2KX5Og6GfeTuR3ImSQUC4j2V0bGOxxek.oSi4xppK', 'almd', 'almd', '09619392234', 'B6 L17 Jardin De Madrid Habay 1', 'customer', '2025-11-09 10:36:11', '2025-12-06 16:47:05'),
(4, 'almd3', 'almdsda@gmail.com', '$2y$10$G0.JXabXjF4P6uS8oKUyT.hfN.A9gSnDD828ae4FKK6zCwrglBV1O', 'craa', 'almd', '09349293812', NULL, 'customer', '2025-11-16 14:17:24', '2025-11-16 14:17:34'),
(5, 'almd2', 'afasdasd@gmail.com', '$2y$10$zHYQzRXkCibG3UT9h0NGHO00NLt9/AKYGT3Hk4sx.o7xO/KHBmXGC', 'Clarence Ronyll', 'Almeyda', '09610131147', 'B6 L17 Jardin De Madrid Habay 1 Bacoor Cavite', 'customer', '2025-12-06 15:47:19', '2025-12-06 15:49:06');

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
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
