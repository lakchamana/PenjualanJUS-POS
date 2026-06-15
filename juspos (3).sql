-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2026 at 02:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `juspos`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `code`, `name`, `created_at`) VALUES
(4, 'BEVERAGE', 'Beverages', '2026-01-11 07:19:44'),
(5, 'FOOD', 'Food', '2026-01-11 07:19:44'),
(6, 'OTHERS', 'Others', '2026-01-11 07:19:44');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `code`, `name`, `phone`, `created_at`) VALUES
(1, 'M001', 'Andi', '081234000001', '2026-01-10 08:28:35'),
(2, 'M002', 'Sari', '081234000002', '2026-01-10 08:28:35'),
(3, 'M7737', 'Muhammad Lakchamana', '089643092842', '2026-01-10 18:13:33'),
(4, 'MFAJRI50', 'Fajri', '08888', '2026-01-16 18:18:38');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `available` tinyint(1) DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `category_id`, `code`, `name`, `price`, `stock`, `available`, `description`, `created_at`) VALUES
(1, 4, 'BEV-001', 'Jus alpukat', 15000.00, 7, 1, 'Jus Alpukat Segar - 300ml', '2026-01-10 08:28:35'),
(2, 4, 'BEV-002', 'Jus mangga', 14000.00, 26, 1, 'Jus Mangga Manis - 300ml', '2026-01-10 08:28:35'),
(3, 4, 'BEV-003', 'Jus jeruk', 12000.00, 19, 1, 'Jus Jeruk Segar - 300ml', '2026-01-10 08:28:35'),
(4, 4, 'BEV-004', 'Smoothie berry', 20000.00, 0, 0, 'Smoothie mix berry - 350ml', '2026-01-10 08:28:35'),
(5, 6, 'OTH-005', 'Topping boba', 3000.00, 100, 1, 'Tambahkan boba', '2026-01-10 08:28:35'),
(6, 4, 'BEV-006', 'Jus mangga (regular)', 14000.00, 26, 1, 'Jus mangga segar 300ml', '2026-01-11 07:19:44'),
(7, 4, 'BEV-007', 'Jus alpukat (regular)', 15000.00, 7, 1, 'Jus alpukat creamy', '2026-01-11 07:19:45'),
(8, 4, 'BEV-008', 'Jus stroberi', 16000.00, 35, 1, 'Jus stroberi segar', '2026-01-11 07:19:45'),
(9, 4, 'BEV-009', 'Es teh manis', 8000.00, 70, 1, 'Es teh manis gula aren', '2026-01-11 07:19:45'),
(10, 4, 'BEV-010', 'Lemon tea', 12000.00, 60, 1, 'Lemon tea dingin', '2026-01-11 07:19:45'),
(11, 5, 'FOOD-011', 'Roti bakar coklat', 12000.00, 30, 1, 'Roti bakar dengan isian coklat', '2026-01-11 07:19:45'),
(12, 5, 'FOOD-012', 'Sandwich ayam', 20000.00, 25, 1, 'Sandwich ayam panggang, sayur segar', '2026-01-11 07:19:45'),
(13, 5, 'FOOD-013', 'Pisang goreng', 10000.00, 40, 1, 'Pisang goreng kriuk', '2026-01-11 07:19:45'),
(14, 5, 'FOOD-014', 'Smoothie berry', 20000.00, 0, 0, 'Smoothie berry (sold out if stock 0)', '2026-01-11 07:19:45'),
(15, 6, 'OTH-015', 'Air mineral 600ml', 7000.00, 171, 1, 'Air mineral kemasan', '2026-01-11 07:19:45'),
(16, 6, 'OTH-016', 'Toping madu 30ml', 5000.00, 145, 1, 'Toping madu untuk topping jus', '2026-01-11 07:19:45'),
(17, 6, 'OTH-017', 'Snack kacang', 12000.00, 60, 1, 'Snack kacang panggang', '2026-01-11 07:19:45'),
(20, 4, 'M81D693', 'Macarena Dua', 10000.00, 99, 1, 'Macarena Kedua', '2026-01-14 12:39:44');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `visit_type` enum('DINE','TAKEAWAY') NOT NULL DEFAULT 'DINE',
  `status` enum('PENDING','FINISHED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  `member_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT 0.00,
  `discount` decimal(12,2) DEFAULT 0.00,
  `total` decimal(12,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `order_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `visit_type`, `status`, `member_id`, `user_id`, `subtotal`, `discount`, `total`, `created_at`, `order_no`) VALUES
(1, 'DINE', 'PENDING', NULL, NULL, 186000.00, 0.00, 186000.00, '2026-01-10 08:51:30', 'SRPM20260110000001'),
(2, 'DINE', 'PENDING', NULL, NULL, 200000.00, 0.00, 200000.00, '2026-01-10 08:52:35', 'SRPM20260110000002'),
(3, 'TAKEAWAY', 'PENDING', NULL, NULL, 69000.00, 0.00, 76600.00, '2026-01-10 16:56:20', 'SRPM20260110000003'),
(4, 'TAKEAWAY', 'PENDING', NULL, NULL, 186000.00, 18600.00, 185800.00, '2026-01-10 16:57:04', 'SRPM20260110000004'),
(5, 'DINE', 'PENDING', NULL, NULL, 200000.00, 0.00, 222000.00, '2026-01-10 17:16:28', 'SRPM20260110000005'),
(6, 'DINE', 'PENDING', NULL, NULL, 242000.00, 0.00, 268600.00, '2026-01-10 17:16:53', 'SRPM20260110000006'),
(7, 'DINE', 'PENDING', 3, NULL, 140000.00, 0.00, 155400.00, '2026-01-10 21:20:16', 'SRPM20260110000007'),
(13, 'DINE', 'FINISHED', NULL, NULL, 80000.00, 0.00, 88800.00, '2026-01-11 14:35:21', 'SRPM202601115035'),
(14, 'DINE', 'FINISHED', NULL, NULL, 80000.00, 0.00, 88800.00, '2026-01-11 14:36:00', 'SRPM202601119661'),
(15, 'DINE', 'FINISHED', NULL, NULL, 80000.00, 0.00, 88800.00, '2026-01-11 14:51:50', 'SRPM202601115150'),
(16, 'DINE', 'FINISHED', NULL, NULL, 150000.00, 0.00, 166500.00, '2026-01-11 14:57:36', 'SRPM202601116250'),
(17, 'DINE', 'FINISHED', 2, 2, 150000.00, 0.00, 166500.00, '2026-01-11 15:13:00', 'SRPM202601117439'),
(18, 'DINE', 'FINISHED', 3, 2, 70000.00, 7000.00, 69900.00, '2026-01-11 15:15:10', 'SRPM202601115819'),
(19, 'TAKEAWAY', 'FINISHED', NULL, 4, 91000.00, 22750.00, 75800.00, '2026-01-11 22:34:07', 'SRPM202601111289'),
(20, 'TAKEAWAY', 'FINISHED', NULL, 4, 42000.00, 0.00, 46600.00, '2026-01-13 14:46:59', 'SRPM202601139320'),
(21, 'DINE', 'FINISHED', 3, 2, 80000.00, 20000.00, 66600.00, '2026-01-13 15:02:53', 'SRPM202601135466'),
(22, 'DINE', 'FINISHED', NULL, 2, 25000.00, 0.00, 27800.00, '2026-01-13 15:32:31', 'SRPM202601135475');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `qty`, `price`, `created_at`) VALUES
(1, 1, 1, 10, 15000.00, '2026-01-10 08:51:30'),
(2, 1, 3, 3, 12000.00, '2026-01-10 08:51:30'),
(3, 2, 1, 10, 15000.00, '2026-01-10 08:52:35'),
(4, 2, 3, 3, 12000.00, '2026-01-10 08:52:35'),
(5, 2, 2, 1, 14000.00, '2026-01-10 08:52:35'),
(6, 3, 1, 3, 15000.00, '2026-01-10 16:56:20'),
(7, 3, 3, 2, 12000.00, '2026-01-10 16:56:20'),
(8, 4, 1, 10, 15000.00, '2026-01-10 16:57:04'),
(9, 4, 3, 3, 12000.00, '2026-01-10 16:57:04'),
(10, 5, 4, 10, 20000.00, '2026-01-10 17:16:28'),
(11, 6, 4, 10, 20000.00, '2026-01-10 17:16:53'),
(12, 6, 2, 3, 14000.00, '2026-01-10 17:16:53'),
(13, 7, 2, 10, 14000.00, '2026-01-10 21:20:16'),
(18, 13, 9, 10, 8000.00, '2026-01-11 14:35:22'),
(19, 14, 9, 10, 8000.00, '2026-01-11 14:36:00'),
(20, 15, 9, 10, 8000.00, '2026-01-11 14:51:50'),
(21, 16, 1, 10, 15000.00, '2026-01-11 14:57:36'),
(22, 17, 7, 10, 15000.00, '2026-01-11 15:13:00'),
(23, 18, 15, 10, 7000.00, '2026-01-11 15:15:10'),
(24, 19, 15, 13, 7000.00, '2026-01-11 22:34:07'),
(25, 20, 15, 6, 7000.00, '2026-01-13 14:46:59'),
(26, 21, 8, 5, 16000.00, '2026-01-13 15:02:53'),
(27, 22, 16, 5, 5000.00, '2026-01-13 15:32:31');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `method` enum('CASH','CARD','VOUCHER') DEFAULT 'CASH',
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `method`, `amount`, `meta`, `created_at`) VALUES
(5, 13, 'CASH', 88800.00, '{\"paid_amount\":88800}', '2026-01-11 14:35:22'),
(6, 14, 'CASH', 88800.00, '{\"paid_amount\":88800}', '2026-01-11 14:36:00'),
(7, 15, 'CASH', 88800.00, '{\"paid_amount\":null}', '2026-01-11 14:51:50'),
(8, 16, 'CASH', 166500.00, '{\"paid_amount\":null}', '2026-01-11 14:57:36'),
(9, 17, 'CASH', 166500.00, '{\"paid_amount\":null}', '2026-01-11 15:13:00'),
(10, 17, 'CASH', 166500.00, NULL, '2026-01-11 15:13:00'),
(11, 18, 'CARD', 69900.00, '{\"paid_amount\":null}', '2026-01-11 15:15:10'),
(12, 19, 'CARD', 75800.00, '{\"paid_amount\":null}', '2026-01-11 22:34:07'),
(13, 20, 'CARD', 46600.00, '{\"paid_amount\":null}', '2026-01-13 14:46:59'),
(14, 21, 'CARD', 66600.00, '{\"paid_amount\":null}', '2026-01-13 15:02:53'),
(15, 22, 'CARD', 27800.00, '{\"paid_amount\":27800}', '2026-01-13 15:32:31');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('BUNDLE','PERCENT','AMOUNT') NOT NULL DEFAULT 'PERCENT',
  `value` decimal(12,2) DEFAULT 0.00,
  `active` tinyint(1) DEFAULT 1,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `code`, `type`, `value`, `active`, `starts_at`, `ends_at`, `created_at`) VALUES
(1, 'WELCOME10', 'PERCENT', 15.00, 1, NULL, NULL, '2026-01-10 08:28:35'),
(2, 'SURPRISE2025', 'PERCENT', 25.00, 1, NULL, NULL, '2026-01-11 22:33:09');

-- --------------------------------------------------------

--
-- Table structure for table `promo_applied`
--

CREATE TABLE `promo_applied` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `promo_id` int(10) UNSIGNED NOT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo_applied`
--

INSERT INTO `promo_applied` (`id`, `order_id`, `promo_id`, `meta`, `created_at`) VALUES
(1, 4, 1, '{\"code\":\"WELCOME10\"}', '2026-01-10 16:57:04');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `file_path` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `order_id`, `file_path`, `created_at`) VALUES
(1, 1, 'receipts/receipt_1.html', '2026-01-10 08:51:30'),
(2, 2, 'receipts/receipt_2.html', '2026-01-10 08:52:35'),
(3, 3, 'receipts/receipt_3.html', '2026-01-10 16:56:20'),
(4, 4, 'receipts/receipt_4.html', '2026-01-10 16:57:04'),
(5, 5, 'receipts/receipt_5.html', '2026-01-10 17:16:28'),
(6, 6, 'receipts/receipt_6.html', '2026-01-10 17:16:53'),
(7, 7, 'receipts/receipt_7.html', '2026-01-10 21:20:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `auth_uuid` varchar(36) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(254) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `role` enum('KASIR','LEADER','ADMIN') DEFAULT 'KASIR',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `auth_uuid`, `username`, `email`, `password_hash`, `name`, `phone`, `role`, `created_at`) VALUES
(1, NULL, 'kasir01', 'laksa@tokojus.com', '$2y$10$n3FazKbA473Urh.FwyYulO7nNjrHuupr6zwUiBkKh6AeDW1SKprNG', 'Kasir Demo', NULL, 'KASIR', '2026-01-10 18:22:40'),
(2, NULL, 'leader01', 'hapis@tokojus.com', '$2y$10$6BhnZB6u9/lPGNgUO4Oj4ugy.Zw0uEjeNsDlU0U.Gl9JyMm79aJgG', 'Leader Demo', NULL, 'LEADER', '2026-01-10 18:22:40'),
(3, NULL, 'admin', 'admin@tokojus.com', '$2y$10$AeKc3tA682PyEZHGUMlf..gytun8rhmJao9m0JYm78qgQqhrL2oji', 'Admin', NULL, 'ADMIN', '2026-01-10 18:22:41'),
(4, NULL, 'lakchamana', 'lakchamana@gmail.com', '$2y$10$3LZjvl33OK0l9kDLE5XKJeMLe99oI/COR8FMPHi.gttHbLY8g3fyG', 'Muhammad Lakchamana', NULL, 'KASIR', '2026-01-10 20:19:33'),
(7, NULL, 'amatlaksamana4', 'amatlaksamana4@gmail.com', '$2y$10$eF9qqyEr2inRNnS4Na91nOo.T3lh.PCMFO2vcvzQoETQ3TTG1F.9e', 'Muhammad Lakchamana', NULL, '', '2026-05-25 09:01:50');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_daily_sales`
-- (See below for the actual view)
--
CREATE TABLE `v_daily_sales` (
`day` date
,`orders_count` bigint(21)
,`revenue` decimal(34,2)
,`items_sold` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Structure for view `v_daily_sales`
--
DROP TABLE IF EXISTS `v_daily_sales`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_daily_sales`  AS SELECT cast(`o`.`created_at` as date) AS `day`, count(distinct `o`.`id`) AS `orders_count`, coalesce(sum(`o`.`total`),0) AS `revenue`, coalesce(sum(`oi`.`qty`),0) AS `items_sold` FROM (`orders` `o` left join `order_items` `oi` on(`oi`.`order_id` = `o`.`id`)) WHERE `o`.`status` = 'FINISHED' GROUP BY cast(`o`.`created_at` as date) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_menus_category` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_orders_order_no` (`order_no`),
  ADD KEY `fk_orders_member` (`member_id`),
  ADD KEY `fk_orders_user` (`user_id`),
  ADD KEY `idx_orders_created_at` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_oi_menu` (`menu_id`),
  ADD KEY `idx_order_items_order_id` (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payments_order_id` (`order_id`),
  ADD KEY `idx_payments_method` (`method`),
  ADD KEY `idx_payments_created_at` (`created_at`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `promo_applied`
--
ALTER TABLE `promo_applied`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_promo_order` (`order_id`),
  ADD KEY `fk_promo_promo` (`promo_id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `idx_receipts_order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `promo_applied`
--
ALTER TABLE `promo_applied`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `fk_menus_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_menu` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`),
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_pay_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promo_applied`
--
ALTER TABLE `promo_applied`
  ADD CONSTRAINT `fk_promo_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_promo_promo` FOREIGN KEY (`promo_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `fk_receipts_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
