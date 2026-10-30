-- phpMyAdmin SQL Dump
-- Database: `nabta_store`
-- Ready for import via phpMyAdmin (data in English)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `nabta_store` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nabta_store`;

DROP TABLE IF EXISTS `wishlist`, `reviews`, `reports_ai`, `product_images`, `cart_items`, `cart`, `collections_details`, `contact_us`, `discount_codes`, `product`, `collections`, `categories`, `users`;

-- --------------------------------------------------------
-- Table structure for table `cart`
-- --------------------------------------------------------

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `discount_code_id` int(11) DEFAULT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_address_at_order` varchar(255) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(30) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cart` (`cart_id`, `user_id`, `discount_code_id`, `delivery_fee`, `delivery_address_at_order`, `payment_method`, `created_at`, `total_price`, `status`) VALUES
(1, 1, 1, 3.00, 'Amman', 'cash_on_delivery', '2026-10-01 10:00:00', 14.25, 'completed'),
(2, 2, 2, 3.00, 'Madaba', 'cash_on_delivery', '2026-10-02 11:00:00', 10.60, 'completed'),
(3, 3, 3, 3.00, 'Irbid', 'credit_card', '2026-10-03 12:00:00', 7.25, 'completed'),
(4, 4, 4, 3.00, 'Zarqa', 'cash_on_delivery', '2026-10-04 13:00:00', 15.88, 'completed'),
(5, 5, 5, 3.00, 'Salt', 'credit_card', '2026-10-05 14:00:00', 10.92, 'completed'),
(6, 6, 6, 3.00, 'Amman', 'cash_on_delivery', '2026-10-06 15:00:00', 6.56, 'completed'),
(7, 7, 7, 3.00, 'Aqaba', 'credit_card', '2026-10-07 16:00:00', 11.01, 'completed'),
(8, 8, 8, 3.00, 'Jerash', 'cash_on_delivery', '2026-10-08 17:00:00', 8.44, 'completed'),
(9, 9, 9, 3.00, 'Karak', 'credit_card', '2026-10-09 18:00:00', 6.18, 'completed'),
(10, 10, 10, 3.00, 'Madaba', 'cash_on_delivery', '2026-10-10 19:00:00', 8.62, 'completed');

-- --------------------------------------------------------
-- Table structure for table `cart_items`
-- --------------------------------------------------------

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `pro_id` int(11) DEFAULT NULL,
  `collection_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `pro_id`, `collection_id`, `quantity`, `unit_price_at_purchase`) VALUES
(1, 1, 1, NULL, 1, 12.50),
(2, 2, 2, NULL, 1, 8.00),
(3, 3, 3, NULL, 1, 5.00),
(4, 4, 4, NULL, 1, 14.00),
(5, 5, 5, NULL, 1, 9.00),
(6, 6, NULL, 6, 1, 3.83),
(7, 7, NULL, 7, 1, 10.01),
(8, 8, NULL, 8, 1, 5.79),
(9, 9, NULL, 9, 1, 3.49),
(10, 10, NULL, 10, 1, 6.31);

-- --------------------------------------------------------
-- Table structure for table `categories`
-- --------------------------------------------------------

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`cat_id`, `name`, `description`) VALUES
(1, 'Indoor Plants', 'Plants suitable for home and office'),
(2, 'Outdoor Plants', 'Plants suitable for gardens and balconies'),
(3, 'Cacti', 'Easy-care cactus plants'),
(4, 'Pots & Vases', 'Pots and containers for growing plants'),
(5, 'Potting Soil', 'Soil types for home gardening'),
(6, 'Fertilizers', 'Fertilizers to improve plant growth'),
(7, 'Gardening Tools', 'Tools and supplies for gardening'),
(8, 'Seeds', 'A variety of plant and flower seeds'),
(9, 'Plant Treatments', 'Products for plant care and treatment'),
(10, 'Plant Decor', 'Plant-related accessories and decorations');

-- --------------------------------------------------------
-- Table structure for table `collections`
-- --------------------------------------------------------

CREATE TABLE `collections` (
  `collection_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `discount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `collections` (`collection_id`, `name`, `description`, `discount`, `image`, `status`) VALUES
(1, 'Monstera Collection', 'Monstera with its essential supplies', 10.00, 'images/collection-1.jpg', 'active'),
(2, 'Peace Lily Collection', 'Peace lily with its supplies', 12.00, 'images/collection-2.jpg', 'active'),
(3, 'Cactus Collection', 'Cactus with simple care tools', 8.00, 'images/collection-3.jpg', 'active'),
(4, 'Lavender Collection', 'Lavender with gardening tools', 10.00, 'images/collection-4.jpg', 'active'),
(5, 'Soil Collection', 'Soil and fertilizer for home gardening', 15.00, 'images/collection-5.jpg', 'active'),
(6, 'Organic Care Collection', 'Organic fertilizer and care tools', 10.00, 'images/collection-6.jpg', 'active'),
(7, 'Clay Pot Collection', 'Pot with a decorative plant', 9.00, 'images/collection-7.jpg', 'active'),
(8, 'Pruning Tools Collection', 'Essential tools for plant care', 11.00, 'images/collection-8.jpg', 'active'),
(9, 'Seeds Collection', 'Seeds and gardening supplies', 7.00, 'images/collection-9.jpg', 'active'),
(10, 'Plant Protection Collection', 'Care and protection products', 13.00, 'images/collection-10.jpg', 'active');

-- --------------------------------------------------------
-- Table structure for table `collections_details`
-- --------------------------------------------------------

CREATE TABLE `collections_details` (
  `id` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `collections_details` (`id`, `collection_id`, `pro_id`, `quantity`) VALUES
(1, 1, 1, 1),
(2, 2, 2, 1),
(3, 3, 3, 1),
(4, 4, 4, 1),
(5, 5, 5, 1),
(6, 6, 6, 1),
(7, 7, 7, 1),
(8, 8, 8, 1),
(9, 9, 9, 1),
(10, 10, 10, 1),
(11, 1, 5, 1),
(12, 1, 7, 1),
(13, 2, 5, 1),
(14, 2, 6, 1),
(15, 3, 6, 1),
(16, 3, 9, 1),
(17, 4, 5, 1),
(18, 4, 9, 1),
(19, 5, 6, 1),
(20, 5, 8, 1),
(21, 6, 5, 1),
(22, 6, 10, 1),
(23, 7, 3, 1),
(24, 7, 1, 1),
(25, 8, 6, 1),
(26, 8, 7, 1),
(27, 9, 3, 1),
(28, 9, 5, 1),
(29, 10, 6, 1),
(30, 10, 8, 1);

-- --------------------------------------------------------
-- Table structure for table `contact_us`
-- --------------------------------------------------------

CREATE TABLE `contact_us` (
  `contact_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `contact_us` (`contact_id`, `user_id`, `message`, `status`) VALUES
(1, 1, 'When will a new product be available?', 'answered'),
(2, 2, 'I need tips for caring for indoor plants', 'new'),
(3, 3, 'Are other sizes of this product available?', 'new'),
(4, 4, 'I would like to ask about the delivery service', 'answered'),
(5, 5, 'Can I change my order address?', 'in_progress'),
(6, 6, 'I need help choosing the right soil', 'new'),
(7, 7, 'Are the products suitable for outdoor gardens?', 'answered'),
(8, 8, 'I want to know how to use the fertilizer', 'in_progress'),
(9, 9, 'I received the product and want to send feedback', 'new'),
(10, 10, 'Is there a discount on collections?', 'answered');

-- --------------------------------------------------------
-- Table structure for table `discount_codes`
-- --------------------------------------------------------

CREATE TABLE `discount_codes` (
  `discount_code_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `discount_codes` (`discount_code_id`, `code`, `discount_percentage`, `expires_at`, `is_active`) VALUES
(1, 'NABTA10', 10.00, '2027-12-31 23:59:59', 1),
(2, 'GREEN5', 5.00, '2027-11-30 23:59:59', 1),
(3, 'PLANT15', 15.00, '2027-10-31 23:59:59', 1),
(4, 'GARDEN8', 8.00, '2027-09-30 23:59:59', 1),
(5, 'LEAF12', 12.00, '2027-08-31 23:59:59', 1),
(6, 'BLOOM7', 7.00, '2027-07-31 23:59:59', 1),
(7, 'NATURE20', 20.00, '2027-06-30 23:59:59', 1),
(8, 'SEED6', 6.00, '2027-05-31 23:59:59', 1),
(9, 'FLOWER9', 9.00, '2027-04-30 23:59:59', 1),
(10, 'FRESH11', 11.00, '2027-03-31 23:59:59', 1);

-- --------------------------------------------------------
-- Table structure for table `product`
-- --------------------------------------------------------

CREATE TABLE `product` (
  `pro_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `count_selling` int(11) NOT NULL DEFAULT 0,
  `status` varchar(30) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product` (`pro_id`, `cat_id`, `name`, `description`, `price`, `quantity`, `count_selling`, `status`) VALUES
(1, 1, 'Monstera', 'Houseplant with large leaves', 12.50, 25, 14, 'active'),
(2, 1, 'Peace Lily', 'Flowering indoor plant', 8.00, 30, 11, 'active'),
(3, 3, 'Mini Cactus', 'Small cactus suitable for the office', 5.00, 40, 18, 'active'),
(4, 2, 'Lavender', 'Aromatic plant suitable for balconies', 14.00, 15, 7, 'active'),
(5, 5, 'All-Purpose Potting Soil', 'Bag of soil for home gardening', 9.00, 50, 25, 'active'),
(6, 6, 'Organic Fertilizer', 'Organic fertilizer for houseplants', 4.25, 35, 16, 'active'),
(7, 4, 'Clay Pot', 'Medium-sized clay pot', 11.00, 20, 8, 'active'),
(8, 7, 'Pruning Shears', 'Shears for trimming branches and leaves', 6.50, 18, 9, 'active'),
(9, 8, 'Sunflower Seeds', 'Pack of seeds for home planting', 3.75, 45, 20, 'active'),
(10, 9, 'Plant Protection Spray', 'Spray to help protect plants', 7.25, 22, 6, 'active');

-- --------------------------------------------------------
-- Table structure for table `product_images`
-- --------------------------------------------------------

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_images` (`image_id`, `pro_id`, `image_url`, `is_primary`) VALUES
(1, 1, 'images/products/monstera.jpg', 1),
(2, 2, 'images/products/peace-lily.jpg', 1),
(3, 3, 'images/products/cactus.jpg', 1),
(4, 4, 'images/products/lavender.jpg', 1),
(5, 5, 'images/products/soil.jpg', 1),
(6, 6, 'images/products/organic-fertilizer.jpg', 1),
(7, 7, 'images/products/clay-pot.jpg', 1),
(8, 8, 'images/products/pruning-shears.jpg', 1),
(9, 9, 'images/products/sunflower-seeds.jpg', 1),
(10, 10, 'images/products/plant-spray.jpg', 1);

-- --------------------------------------------------------
-- Table structure for table `reports_ai`
-- --------------------------------------------------------

CREATE TABLE `reports_ai` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `request_prompt` text DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `report_content` text DEFAULT NULL,
  `generated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reports_ai` (`report_id`, `user_id`, `title`, `request_prompt`, `date_from`, `date_to`, `report_content`, `generated_at`) VALUES
(1, 1, 'Sales Report', 'Analyze store sales', '2026-10-01', '2026-10-10', 'Sample sales report', '2026-10-11 09:00:00'),
(2, 1, 'Best-Selling Products', 'Rank products by sales', '2026-09-01', '2026-09-30', 'Sample best-selling products report', '2026-10-11 09:10:00'),
(3, 1, 'Inventory Status', 'Analyze available product quantities', '2026-10-01', '2026-10-10', 'Sample inventory report', '2026-10-11 09:20:00'),
(4, 1, 'Collections Performance', 'Compare collection sales', '2026-09-01', '2026-09-30', 'Sample collections performance report', '2026-10-11 09:30:00'),
(5, 1, 'Customer Reviews', 'Analyze product reviews', '2026-09-01', '2026-09-30', 'Sample customer reviews report', '2026-10-11 09:40:00'),
(6, 1, 'Discount Code Usage', 'Analyze discount code usage', '2026-10-01', '2026-10-10', 'Sample discount codes report', '2026-10-11 09:50:00'),
(7, 1, 'Completed Orders', 'Show a summary of completed orders', '2026-10-01', '2026-10-10', 'Sample completed orders report', '2026-10-11 10:00:00'),
(8, 1, 'Incoming Messages', 'Summarize customer messages', '2026-10-01', '2026-10-10', 'Sample customer messages report', '2026-10-11 10:10:00'),
(9, 1, 'Categories Performance', 'Compare category performance', '2026-09-01', '2026-09-30', 'Sample categories report', '2026-10-11 10:20:00'),
(10, 1, 'Monthly Store Summary', 'Create a monthly store summary', '2026-09-01', '2026-09-30', 'Sample comprehensive store report', '2026-10-11 10:30:00');

-- --------------------------------------------------------
-- Table structure for table `reviews`
-- --------------------------------------------------------

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pro_id` int(11) DEFAULT NULL,
  `collection_id` int(11) DEFAULT NULL,
  `cart_item_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reviews` (`review_id`, `user_id`, `pro_id`, `collection_id`, `cart_item_id`, `rating`, `comment`) VALUES
(1, 1, 1, NULL, 1, 5, 'Excellent product, arrived in good condition'),
(2, 2, 2, NULL, 2, 4, 'Beautiful plant and easy to care for'),
(3, 3, 3, NULL, 3, 5, 'Perfect size for the office'),
(4, 4, 4, NULL, 4, 4, 'Lovely scent'),
(5, 5, 5, NULL, 5, 5, 'Good soil for plants'),
(6, 6, NULL, 6, 6, 4, 'Useful collection at a good price'),
(7, 7, NULL, 7, 7, 5, 'Beautiful pot with good quality'),
(8, 8, NULL, 8, 8, 4, 'Practical set of tools'),
(9, 9, NULL, 9, 9, 5, 'The seeds arrived quickly'),
(10, 10, NULL, 10, 10, 4, 'Good plant care products');

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `google_sub` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'customer',
  `status` varchar(30) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `google_sub`, `phone`, `address`, `role`, `status`) VALUES
(1, 'Mohammad Ahmad', 'mohammad@example.com', '$2y$12$SISp9TNfNhELW9VRm2Qta.QqPZpGg3/5mCkkvjSpmjbMnSoGGtr3u', NULL, '0791111111', 'Amman', 'admin', 'active'),
(2, 'Sara Khaled', 'sara@example.com', '$2y$12$J2nSugFb2f/LKAM//yFB3.0GSQXLbiYWhVapUY/OD1SXUsWhqxt4i', NULL, '0792222222', 'Madaba', 'customer', 'active'),
(3, 'Layan Omar', 'layan@example.com', '$2y$12$J2nSugFb2f/LKAM//yFB3.0GSQXLbiYWhVapUY/OD1SXUsWhqxt4i', NULL, '0793333333', 'Irbid', 'customer', 'active'),
(4, 'Omar Yousef', 'omar@example.com', '$2y$12$J2nSugFb2f/LKAM//yFB3.0GSQXLbiYWhVapUY/OD1SXUsWhqxt4i', NULL, '0794444444', 'Zarqa', 'customer', 'active'),
(5, 'Noor Ali', 'noor@example.com', '$2y$12$J2nSugFb2f/LKAM//yFB3.0GSQXLbiYWhVapUY/OD1SXUsWhqxt4i', NULL, '0795555555', 'Salt', 'customer', 'active'),
(6, 'Yasmin Samir', 'yasmin@example.com', '$2y$12$J2nSugFb2f/LKAM//yFB3.0GSQXLbiYWhVapUY/OD1SXUsWhqxt4i', NULL, '0796666666', 'Amman', 'customer', 'active'),
(7, 'Kareem Hasan', 'kareem@example.com', '$2y$12$J2nSugFb2f/LKAM//yFB3.0GSQXLbiYWhVapUY/OD1SXUsWhqxt4i', NULL, '0797777777', 'Aqaba', 'customer', 'active'),
(8, 'Dana Mahmoud', 'dana@example.com', '$2y$12$J2nSugFb2f/LKAM//yFB3.0GSQXLbiYWhVapUY/OD1SXUsWhqxt4i', NULL, '0798888888', 'Jerash', 'customer', 'active'),
(9, 'Rami Nasser', 'rami@example.com', '$2y$12$J2nSugFb2f/LKAM//yFB3.0GSQXLbiYWhVapUY/OD1SXUsWhqxt4i', NULL, '0799999999', 'Karak', 'customer', 'active'),
(10, 'Tala Fadi', 'tala@example.com', '$2y$12$J2nSugFb2f/LKAM//yFB3.0GSQXLbiYWhVapUY/OD1SXUsWhqxt4i', NULL, '0781010101', 'Madaba', 'customer', 'active');

-- --------------------------------------------------------
-- Table structure for table `wishlist`
-- --------------------------------------------------------

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pro_id` int(11) DEFAULT NULL,
  `collection_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `pro_id`, `collection_id`) VALUES
(1, 1, 1, NULL),
(2, 2, 2, NULL),
(3, 3, 3, NULL),
(4, 4, 4, NULL),
(5, 5, 5, NULL),
(6, 6, NULL, 6),
(7, 7, NULL, 7),
(8, 8, NULL, 8),
(9, 9, NULL, 9),
(10, 10, NULL, 10);

-- --------------------------------------------------------
-- Indexes
-- --------------------------------------------------------

ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `discount_code_id` (`discount_code_id`);

ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `pro_id` (`pro_id`),
  ADD KEY `collection_id` (`collection_id`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

ALTER TABLE `collections`
  ADD PRIMARY KEY (`collection_id`);

ALTER TABLE `collections_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `collection_id` (`collection_id`,`pro_id`),
  ADD KEY `pro_id` (`pro_id`);

ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `discount_codes`
  ADD PRIMARY KEY (`discount_code_id`),
  ADD UNIQUE KEY `code` (`code`);

ALTER TABLE `product`
  ADD PRIMARY KEY (`pro_id`),
  ADD KEY `cat_id` (`cat_id`);

ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `pro_id` (`pro_id`);

ALTER TABLE `reports_ai`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pro_id` (`pro_id`),
  ADD KEY `collection_id` (`collection_id`),
  ADD KEY `cart_item_id` (`cart_item_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uq_users_google_sub` (`google_sub`);

ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pro_id` (`pro_id`),
  ADD KEY `collection_id` (`collection_id`);

-- --------------------------------------------------------
-- AUTO_INCREMENT
-- --------------------------------------------------------

ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `collections`
  MODIFY `collection_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `collections_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
ALTER TABLE `contact_us`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `discount_codes`
  MODIFY `discount_code_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `product`
  MODIFY `pro_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `reports_ai`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

-- --------------------------------------------------------
-- Foreign key constraints
-- --------------------------------------------------------

ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`discount_code_id`);

ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`pro_id`) REFERENCES `product` (`pro_id`),
  ADD CONSTRAINT `cart_items_ibfk_3` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`collection_id`);

ALTER TABLE `collections_details`
  ADD CONSTRAINT `collections_details_ibfk_1` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`collection_id`),
  ADD CONSTRAINT `collections_details_ibfk_2` FOREIGN KEY (`pro_id`) REFERENCES `product` (`pro_id`);

ALTER TABLE `contact_us`
  ADD CONSTRAINT `contact_us_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`cat_id`);

ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`pro_id`) REFERENCES `product` (`pro_id`);

ALTER TABLE `reports_ai`
  ADD CONSTRAINT `reports_ai_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`pro_id`) REFERENCES `product` (`pro_id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`collection_id`),
  ADD CONSTRAINT `reviews_ibfk_4` FOREIGN KEY (`cart_item_id`) REFERENCES `cart_items` (`cart_item_id`);

ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`pro_id`) REFERENCES `product` (`pro_id`),
  ADD CONSTRAINT `wishlist_ibfk_3` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`collection_id`);

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
