-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 06, 2025 at 06:00 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `starling_coffee`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('super_admin','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `name`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@starlingcoffee.com', '$2y$12$Zzu201X4ZJ9ctK1xvL1brOjwY/rjUokSye5TyVjgb.yIte69JlzCW', 'Administrator', 'super_admin', '2025-11-19 00:42:07', '2025-11-19 00:56:04');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `menu_item_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `menu_item_id`, `quantity`, `created_at`, `updated_at`) VALUES
(13, 6, 1, 1, '2025-12-03 08:08:46', '2025-12-03 08:08:46'),
(14, 6, 2, 1, '2025-12-03 08:08:54', '2025-12-03 08:08:54'),
(15, 6, 3, 1, '2025-12-03 08:09:00', '2025-12-03 08:09:00'),
(16, 6, 6, 1, '2025-12-03 08:09:05', '2025-12-03 08:09:05'),
(17, 6, 13, 1, '2025-12-03 08:09:12', '2025-12-03 08:09:12');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','read','replied') COLLATE utf8mb4_unicode_ci DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` enum('minuman','makanan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `image`, `category`, `status`, `price`, `created_at`, `updated_at`) VALUES
(1, 'Caffè Latte', 'Espresso kaya rasa dengan susu steam dan lapisan busa tipis.', 'images/menu-latte.webp', 'minuman', 'active', '35000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(2, 'Caramel Macchiato', 'Espresso dengan sirup vanila, susu steam, dan saus karamel.', 'images/menu-macchiato.webp', 'minuman', 'active', '40000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(3, 'Chocolate Chip Frappuccino', 'Kopi, susu, saus moka, dan butiran cokelat diblender dengan es.', 'images/choc-frap.webp', 'minuman', 'active', '45000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(4, 'Iced Green Tea Latte', 'Teh hijau matcha yang lembut dicampur dengan susu dan es.', 'images/menu-green-tea.webp', 'minuman', 'active', '38000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(5, 'Cold Brew', 'Kopi yang diseduh lambat dalam air dingin selama 20 jam.', 'images/menu-cold-brew.webp', 'minuman', 'active', '32000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(6, 'Signature Chocolate', 'Minuman cokelat premium yang lembut, disajikan panas atau dingin.', 'images/menu-chocolate.webp', 'minuman', 'active', '36000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(7, 'Asian Dolce Latte', 'Espresso dengan saus dolce spesial untuk rasa yang khas.', 'images/menu-dolce-latte.webp', 'minuman', 'active', '42000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(8, 'Iced Shaken Lemon Tea', 'Teh hitam dengan perasan lemon segar yang dikocok dengan es.', 'images/menu-lemon-tea.webp', 'minuman', 'active', '28000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(9, 'Butter Croissant', 'Pastry klasik dari Perancis dengan tekstur lembut dan rasa mentega.', 'images/food-croissant.webp', 'makanan', 'active', '25000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(10, 'Almond Croissant', 'Croissant dengan isian pasta almon manis dan taburan almon renyah.', 'images/food-almond-croissant.webp', 'makanan', 'active', '30000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(11, 'Tuna Puff Pastry', 'Pastry gurih renyah dengan isian tuna dan bumbu spesial.', 'images/food-tuna-puff.webp', 'makanan', 'active', '28000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(12, 'Blueberry Muffin', 'Muffin yang lembut dan padat dengan buah bluberi asli.', 'images/food-muffin.webp', 'makanan', 'active', '22000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(13, 'New York Cheesecake', 'Kue keju klasik dengan tekstur padat, lembut, dan kaya rasa.', 'images/food-cheesecake.webp', 'makanan', 'active', '45000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16'),
(14, 'Red Velvet Cake', 'Kue Red Velvet berlapis dengan krim keju yang mewah.', 'images/food-red-velvet.webp', 'makanan', 'active', '50000.00', '2025-11-14 02:47:16', '2025-11-14 02:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `delivery_type` enum('delivery','pickup') COLLATE utf8mb4_unicode_ci DEFAULT 'pickup',
  `shipping_address` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `status`, `delivery_type`, `shipping_address`, `notes`, `created_at`, `updated_at`) VALUES
(10, 4, 'ORD-20251129-55F9942A', '66000.00', 'pending', 'pickup', 'Ambil di tempat (Starling Coffee)', '', '2025-11-29 15:46:39', '2025-11-29 15:46:39'),
(11, 4, 'ORD-20251129-D4C5412C', '35000.00', 'completed', 'pickup', 'Ambil di tempat (Starling Coffee)', '', '2025-11-29 16:20:28', '2025-11-29 16:22:31'),
(12, 4, 'ORD-20251129-1348CDB8', '32000.00', 'pending', 'pickup', 'Ambil di tempat (Starling Coffee)', '', '2025-11-29 16:37:08', '2025-11-29 16:37:08'),
(13, 6, 'ORD-20251203-FF75D4E1', '35000.00', 'pending', 'pickup', 'Ambil di tempat (Starling Coffee)', '', '2025-12-03 08:08:23', '2025-12-03 08:08:23'),
(14, 3, 'ORD-20251205-DDDAA505', '35000.00', 'completed', 'pickup', 'Ambil di tempat (Starling Coffee)', '', '2025-12-05 11:11:25', '2025-12-05 11:29:20');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `menu_item_id` int DEFAULT NULL,
  `menu_item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_item_price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `menu_item_name`, `menu_item_price`, `quantity`, `subtotal`, `created_at`) VALUES
(6, 10, 10, 'Almond Croissant', '30000.00', 1, '30000.00', '2025-11-29 15:46:39'),
(7, 10, 6, 'Signature Chocolate', '36000.00', 1, '36000.00', '2025-11-29 15:46:39'),
(8, 11, 1, 'Caffè Latte', '35000.00', 1, '35000.00', '2025-11-29 16:20:28'),
(9, 12, 5, 'Cold Brew', '32000.00', 1, '32000.00', '2025-11-29 16:37:08'),
(10, 13, 1, 'Caffè Latte', '35000.00', 1, '35000.00', '2025-12-03 08:08:23'),
(11, 14, 1, 'Caffè Latte', '35000.00', 1, '35000.00', '2025-12-05 11:11:25');

-- --------------------------------------------------------

--
-- Table structure for table `rewards`
--

CREATE TABLE `rewards` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `points_required` int NOT NULL,
  `reward_type` enum('discount','free_item','cashback') COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_percent` decimal(5,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `free_item_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cashback_amount` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `stock` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rewards`
--

INSERT INTO `rewards` (`id`, `name`, `description`, `points_required`, `reward_type`, `discount_percent`, `discount_amount`, `free_item_name`, `cashback_amount`, `image`, `status`, `stock`, `created_at`, `updated_at`) VALUES
(1, 'Diskon 10%', 'Dapatkan diskon 10% untuk pembelian berikutnya', 100, 'discount', '10.00', NULL, NULL, NULL, NULL, 'active', NULL, '2025-11-26 13:34:57', '2025-11-26 13:34:57'),
(2, 'Diskon 20%', 'Dapatkan diskon 20% untuk pembelian berikutnya', 200, 'discount', '20.00', NULL, NULL, NULL, NULL, 'active', NULL, '2025-11-26 13:34:57', '2025-11-26 13:34:57'),
(3, 'Free Coffee', 'Gratis 1 cup coffee pilihan Anda', 300, 'free_item', NULL, NULL, NULL, NULL, NULL, 'active', NULL, '2025-11-26 13:34:57', '2025-11-26 13:34:57'),
(4, 'Diskon 15%', 'Dapatkan diskon 15% untuk pembelian berikutnya', 150, 'discount', '15.00', NULL, NULL, NULL, NULL, 'active', NULL, '2025-11-26 13:34:57', '2025-11-26 13:34:57'),
(5, 'Cashback Rp 10.000', 'Dapatkan cashback Rp 10.000', 250, 'cashback', NULL, NULL, NULL, NULL, NULL, 'active', NULL, '2025-11-26 13:34:57', '2025-11-26 13:34:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(3, 'user', 'user123@gmail.com', '$2y$10$mrtEqH1EP11UbDUVa.p.2u5txmzUtwRWlPBwxyPk3recCJpuG3qjG', '', '', '2025-11-29 15:43:13', '2025-11-29 15:43:13'),
(4, 'hello', 'hello@gmail.com', '$2y$10$8MeDrRNcUuxVG3BcWRfMU.yFLtAh.2gJEbsPgDZYQsNgC4Zk1.L4y', '', '', '2025-11-29 15:45:54', '2025-11-29 15:45:54'),
(5, 'user1', 'user1@gmail.com', '$2y$10$.fCxdHkY.tW6FEVqb7mWu.rc0nf.DUZEp6y3duDxSw1vaKH10lEBG', '', '', '2025-11-29 15:56:31', '2025-11-29 15:56:31'),
(6, 'cesyaa', 'cesyaaulia12@gmail.com', '$2y$10$RSkZI1A.TTK3Tp.XiNP9QOOemqmwzwUvdNst.DNOv.L2rji2mi1Fe', '', '', '2025-12-03 08:07:56', '2025-12-03 08:07:56');

-- --------------------------------------------------------

--
-- Table structure for table `user_points`
--

CREATE TABLE `user_points` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `points` int NOT NULL DEFAULT '0',
  `earned_from_order_id` int DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_points`
--

INSERT INTO `user_points` (`id`, `user_id`, `points`, `earned_from_order_id`, `description`, `created_at`) VALUES
(7, 4, 66, 10, 'Poin dari pesanan #ORD-20251129-55F9942A', '2025-11-29 15:46:39'),
(8, 4, 35, 11, 'Poin dari pesanan #ORD-20251129-D4C5412C', '2025-11-29 16:20:28'),
(9, 4, 32, 12, 'Poin dari pesanan #ORD-20251129-1348CDB8', '2025-11-29 16:37:08'),
(10, 6, 35, 13, 'Poin dari pesanan #ORD-20251203-FF75D4E1', '2025-12-03 08:08:23'),
(11, 3, 35, 14, 'Poin dari pesanan #ORD-20251205-DDDAA505', '2025-12-05 11:11:25');

-- --------------------------------------------------------

--
-- Table structure for table `user_rewards`
--

CREATE TABLE `user_rewards` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `reward_id` int NOT NULL,
  `points_used` int NOT NULL,
  `status` enum('pending','used','expired') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`menu_item_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_points`
--
ALTER TABLE `user_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `earned_from_order_id` (`earned_from_order_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `user_rewards`
--
ALTER TABLE `user_rewards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `reward_id` (`reward_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_points`
--
ALTER TABLE `user_points`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_rewards`
--
ALTER TABLE `user_rewards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_points`
--
ALTER TABLE `user_points`
  ADD CONSTRAINT `user_points_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_points_ibfk_2` FOREIGN KEY (`earned_from_order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_rewards`
--
ALTER TABLE `user_rewards`
  ADD CONSTRAINT `user_rewards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_rewards_ibfk_2` FOREIGN KEY (`reward_id`) REFERENCES `rewards` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
