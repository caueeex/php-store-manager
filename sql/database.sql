-- Database: mini_erp
-- Description: Sistema de gerenciamento de loja com controle de produtos, estoque, pedidos e cupons

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for `coupons`
-- --------------------------------------------------------

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_value` decimal(10,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `max_uses` int(11) DEFAULT NULL,
  `use_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_code` (`code`),
  KEY `idx_dates` (`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `products`
-- --------------------------------------------------------

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `variations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variations`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `stock`
-- --------------------------------------------------------

CREATE TABLE `stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `orders`
-- --------------------------------------------------------

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_address` text NOT NULL,
  `customer_zipcode` varchar(10) NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `shipping` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `coupon_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `coupon_id` (`coupon_id`),
  KEY `idx_customer_email` (`customer_email`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `order_items`
-- --------------------------------------------------------

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Data for table `coupons`
-- --------------------------------------------------------

INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_order_value`, `start_date`, `end_date`, `max_uses`, `use_count`, `created_at`) VALUES
(1, 'DESCONTO10', 'percentage', 10.00, 100.00, '2025-05-13', '2025-06-12', 100, 0, '2025-05-13 22:27:32'),
(2, 'FREEGRATIS', 'fixed', 20.00, 200.00, '2025-05-13', '2025-07-12', NULL, 0, '2025-05-13 22:27:32'),
(3, 'PRIMEIRACOMPRA', 'percentage', 15.00, NULL, '2025-05-13', '2025-08-11', 1, 0, '2025-05-13 22:27:32');

-- --------------------------------------------------------
-- Data for table `products`
-- --------------------------------------------------------

INSERT INTO `products` (`id`, `name`, `price`, `description`, `variations`, `created_at`, `updated_at`) VALUES
(1, 'Camiseta Básica Branca', 29.90, 'Camiseta 100% algodão', '{\"name\": [\"Tamanho\"], \"options\": [\"P,M,G,GG\"]}', '2025-05-13 22:27:32', '2025-05-13 22:27:32'),
(2, 'Calça Jeans Slim', 89.90, 'Calça jeans masculina slim fit', '{\"name\": [\"Tamanho\", \"Cor\"], \"options\": [\"38,40,42,44\", \"Azul,Preto\"]}', '2025-05-13 22:27:32', '2025-05-13 22:27:32'),
(3, 'Tênis Esportivo', 129.90, 'Tênis para corrida', '{\"name\": [\"Numeração\"], \"options\": [\"36,37,38,39,40,41,42\"]}', '2025-05-13 22:27:32', '2025-05-13 22:27:32'),
(4, 'Iphone 15', 15000.00, 'celular', 'null', '2025-05-13 22:38:21', '2025-05-13 22:38:21');

-- --------------------------------------------------------
-- Data for table `stock`
-- --------------------------------------------------------

INSERT INTO `stock` (`id`, `product_id`, `quantity`, `updated_at`) VALUES
(1, 1, 50, '2025-05-13 22:27:32'),
(2, 2, 30, '2025-05-13 22:27:32'),
(3, 3, 25, '2025-05-13 22:27:32'),
(4, 4, 5, '2025-05-14 03:47:02');

-- --------------------------------------------------------
-- Data for table `orders`
-- --------------------------------------------------------

INSERT INTO `orders` (`id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `customer_zipcode`, `notes`, `subtotal`, `discount`, `shipping`, `total`, `status`, `coupon_id`, `created_at`, `updated_at`) VALUES
(5, 'Maria Benedita Santos de Jesus', 'soterocaue2@gmail.com', '(12) 99711-6023', 'RUA DR ADELIO DA SILVA 90\r\nCASA', '12050720', NULL, 15000.00, 0.00, 0.00, 15000.00, 'pending', NULL, '2025-05-13 23:14:39', '2025-05-13 23:14:39'),
(6, 'Maria Benedita Santos de Jesus', 'simdeia.9@hotmail.com', '(12) 99711-6023', 'RUA DR ADELIO DA SILVA 90\r\nCASA', '12050720', NULL, 15000.00, 1500.00, 0.00, 13500.00, 'pending', 1, '2025-05-14 03:37:02', '2025-05-14 03:37:02'),
(7, 'Maria Benedita Santos de Jesus', 'soterocaue2@gmail.com', '(12) 99711-6023', 'RUA DR ADELIO DA SILVA 90\r\nCASA', '12050720', NULL, 15000.00, 1500.00, 0.00, 13500.00, 'pending', 1, '2025-05-14 03:37:11', '2025-05-14 03:37:11'),
(8, 'Maria Benedita Santos de Jesus', 'soterocaue2@gmail.com', '(12) 99711-6023', 'RUA DR ADELIO DA SILVA 90\r\nCASA', '12050720', NULL, 15000.00, 1500.00, 0.00, 13500.00, 'pending', 1, '2025-05-14 03:38:07', '2025-05-14 03:38:07'),
(9, 'Maria Benedita Santos de Jesus', 'soterocaue2@gmail.com', '(12) 99711-6023', 'RUA DR ADELIO DA SILVA 90\r\nCASA', '12050720', NULL, 15000.00, 1500.00, 0.00, 13500.00, 'pending', 1, '2025-05-14 03:39:08', '2025-05-14 03:39:08'),
(10, 'Maria Benedita Santos de Jesus', 'soterocaue2@gmail.com', '(12) 99711-6023', 'RUA DR ADELIO DA SILVA 90\r\nCASA', '12050720', NULL, 15000.00, 0.00, 0.00, 15000.00, 'pending', NULL, '2025-05-14 03:39:44', '2025-05-14 03:39:44'),
(11, 'Maria Benedita Santos de Jesus', 'soterocaue2@gmail.com', '(12) 99711-6023', 'RUA DR ADELIO DA SILVA 90\r\nCASA', '12050720', NULL, 15000.00, 0.00, 0.00, 15000.00, 'pending', NULL, '2025-05-14 03:40:41', '2025-05-14 03:40:41'),
(12, 'Maria Benedita Santos de Jesus', 'soterocaue2@gmail.com', '(12) 99711-6023', 'RUA DR ADELIO DA SILVA 90\r\nCASA', '12050720', NULL, 15000.00, 0.00, 0.00, 15000.00, 'pending', NULL, '2025-05-14 03:42:36', '2025-05-14 03:42:36'),
(13, 'Maria Benedita Santos de Jesus', 'soterocaue2@gmail.com', '(12) 99711-6023', 'RUA DR ADELIO DA SILVA 90\r\nCASA', '12050720', NULL, 15000.00, 1500.00, 0.00, 13500.00, 'pending', 1, '2025-05-14 03:47:02', '2025-05-14 03:47:02');

-- --------------------------------------------------------
-- Data for table `order_items`
-- --------------------------------------------------------

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 8, 4, 1, 15000.00),
(2, 9, 4, 1, 15000.00),
(3, 10, 4, 1, 15000.00),
(4, 11, 4, 1, 15000.00),
(5, 12, 4, 1, 15000.00),
(6, 13, 4, 1, 15000.00);

COMMIT;
