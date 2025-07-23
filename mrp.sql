-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2025 at 09:51 AM
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
-- Database: `mrp`
--

-- --------------------------------------------------------

--
-- Table structure for table `bom_headers`
--

CREATE TABLE `bom_headers` (
  `bom_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `version` varchar(20) NOT NULL DEFAULT '1.0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bom_headers`
--

INSERT INTO `bom_headers` (`bom_id`, `product_id`, `version`, `created_at`) VALUES
(1, 1, '1.0', '2025-04-27 16:28:01'),
(2, 2, '1', '2025-04-27 17:45:04'),
(6, 6, '1.0', '2025-04-28 15:02:19'),
(8, 8, '1.0', '2025-05-05 06:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `bom_items`
--

CREATE TABLE `bom_items` (
  `bom_item_id` int(11) NOT NULL,
  `bom_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `uom_id` int(2) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bom_items`
--

INSERT INTO `bom_items` (`bom_item_id`, `bom_id`, `material_id`, `quantity`, `uom_id`, `notes`) VALUES
(1, 2, 1, 1.00, 1, NULL),
(2, 2, 10, 1.00, 5, NULL),
(3, 1, 4, 1.00, 1, NULL),
(4, 1, 10, 1.00, 1, NULL),
(5, 1, 11, 1.00, 1, NULL),
(6, 6, 9, 1.00, 1, NULL),
(7, 6, 14, 1.00, 5, NULL),
(27, 8, 4, 1.00, 1, NULL),
(28, 8, 10, 1.00, 5, NULL),
(29, 8, 17, 1.00, 5, NULL),
(30, 8, 19, 1.00, 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `capacity_machine`
--

CREATE TABLE `capacity_machine` (
  `id` int(11) NOT NULL,
  `machine_model` varchar(100) NOT NULL,
  `speed` int(11) NOT NULL,
  `output_hour` float NOT NULL,
  `labels_per_meter` float NOT NULL,
  `theoritical_labels` float NOT NULL,
  `actual_labels` float NOT NULL,
  `utilization` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `material_id` int(10) NOT NULL,
  `uom_id` int(2) NOT NULL,
  `availability_length` decimal(10,2) NOT NULL,
  `last_order_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `material_id`, `uom_id`, `availability_length`, `last_order_date`) VALUES
(1, 1, 1, 55.00, '2024-12-11'),
(2, 2, 1, 5.00, '2024-12-21'),
(3, 3, 1, 45.00, '2024-12-05'),
(4, 4, 1, 200.00, '2024-12-08'),
(5, 5, 1, 35.00, '2024-01-09'),
(6, 6, 1, 30.00, '2024-11-09'),
(7, 7, 1, 4.00, '2024-12-15'),
(8, 8, 1, 25.00, '2024-06-06'),
(9, 9, 1, 2.00, '2024-11-11'),
(10, 10, 5, 400.00, '2025-04-27'),
(11, 11, 5, 400.00, '2025-04-27'),
(12, 12, 5, 40.00, '2025-04-27'),
(13, 13, 5, 50.00, '2025-04-27'),
(14, 14, 5, 45.00, '2025-04-20'),
(15, 15, 5, 78.00, '2025-04-21'),
(16, 16, 5, 10.00, '2025-04-22'),
(17, 17, 5, 92.00, '2025-04-23'),
(18, 18, 5, 33.00, '2025-04-24'),
(19, 19, 5, 67.00, '2025-04-25');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `actual` int(10) NOT NULL,
  `barcode` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `product_id`, `quantity`, `deadline`, `status`, `created_at`, `started_at`, `completed_at`, `actual`, `barcode`) VALUES
(1, 1, 333, '2025-04-30', 'Completed', '2025-04-29 10:25:04', NULL, '2025-05-03 08:58:37', 0, 0),
(2, 6, 1, '2025-04-30', 'Completed', '2025-04-29 10:36:11', '2025-05-03 09:10:32', '2025-05-03 09:11:28', 0, 0),
(13, 1, 1, '2025-05-04', 'Completed', '2025-05-03 12:24:08', NULL, '2025-05-03 12:24:14', 0, 0),
(14, 6, 1, '2025-05-03', 'Cancelled', '2025-05-03 12:48:57', NULL, '2025-05-03 12:49:34', 0, 0),
(16, 8, 4, '2025-05-05', 'Cancelled', '2025-05-05 12:22:42', NULL, NULL, 0, 0),
(17, 8, 6, '2025-05-05', 'Completed', '2025-05-05 12:23:05', '2025-05-05 12:23:59', '2025-05-05 12:24:02', 6, 0),
(18, 2, 2, '2025-05-05', 'Completed', '2025-05-05 12:30:32', '2025-05-05 12:30:48', '2025-05-05 12:30:54', 2, 0),
(19, 2, 1, '2025-05-05', 'Completed', '2025-05-05 12:31:21', '2025-05-05 12:31:36', '2025-05-05 12:32:03', 1, 0),
(20, 1, 2, '2025-05-05', 'Cancelled', '2025-05-05 12:46:57', NULL, NULL, 0, 0),
(21, 2, 3, '2025-05-05', 'Completed', '2025-05-05 12:47:21', '2025-05-05 12:48:09', '2025-05-05 12:48:16', 3, 0),
(22, 2, 3, '2025-07-23', 'Completed', '2025-07-23 06:15:42', '2025-07-23 06:15:59', '2025-07-23 06:16:05', 3, 0),
(23, 2, 2, '2025-07-23', 'Completed', '2025-07-23 06:16:40', '2025-07-23 06:17:41', '2025-07-23 06:17:44', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `machine`
--

CREATE TABLE `machine` (
  `machine_id` int(11) NOT NULL,
  `machine_name` varchar(50) NOT NULL,
  `speed_m_per_min` decimal(10,2) NOT NULL COMMENT 'Kecepatan dalam meter per menit',
  `output_m_per_hour` decimal(10,2) NOT NULL COMMENT 'Output dalam meter per jam',
  `labels_per_meter` int(11) NOT NULL COMMENT 'Estimasi label per meter',
  `theoretical_labels_per_hour` int(11) NOT NULL COMMENT 'Output label teoritis per jam',
  `actual_labels_per_hour` int(11) NOT NULL COMMENT 'Output label aktual per jam',
  `utilization` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel untuk menyimpan data performa mesin produksi label';

--
-- Dumping data for table `machine`
--

INSERT INTO `machine` (`machine_id`, `machine_name`, `speed_m_per_min`, `output_m_per_hour`, `labels_per_meter`, `theoretical_labels_per_hour`, `actual_labels_per_hour`, `utilization`) VALUES
(1, 'MA260', 30.00, 1800.00, 5, 9000, 8400, 0),
(2, 'EM280', 40.00, 2400.00, 4, 9600, 9100, 0),
(3, 'ECS340', 60.00, 3600.00, 4, 14400, 13700, 0),
(4, 'LM440', 80.00, 4800.00, 3, 14400, 13600, 0);

-- --------------------------------------------------------

--
-- Table structure for table `material`
--

CREATE TABLE `material` (
  `material_id` int(10) NOT NULL,
  `material_name` varchar(50) NOT NULL,
  `type` int(2) NOT NULL,
  `uom` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `material`
--

INSERT INTO `material` (`material_id`, `material_name`, `type`, `uom`) VALUES
(1, 'High Gloss Paper', 1, 1),
(2, 'Fascoat 2 FSC', 1, 1),
(3, 'MC Primecoat GP FSC', 1, 1),
(4, 'MC Elite FSC', 1, 1),
(5, 'rMC Primecoat FSC', 1, 1),
(6, 'Vellum Elite FSC', 1, 1),
(7, 'rDirect Thermal 300LD FSC', 1, 1),
(8, 'rPP Top White', 1, 1),
(9, 'Synthetic Paper', 1, 1),
(10, 'Black Ink', 2, 5),
(11, 'Cyan Ink', 2, 5),
(12, 'Magenta Ink', 2, 5),
(13, 'Yellow Ink', 2, 5),
(14, 'White Ink', 2, 5),
(15, 'Gold Ink', 2, 5),
(16, 'Silver Ink', 2, 5),
(17, 'Pantone 072C', 2, 5),
(18, 'Pantone 485C', 2, 5),
(19, 'Clear Varnish', 2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `material_type`
--

CREATE TABLE `material_type` (
  `type_id` int(2) NOT NULL,
  `type_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `material_type`
--

INSERT INTO `material_type` (`type_id`, `type_name`) VALUES
(1, 'material'),
(2, 'ink');

-- --------------------------------------------------------

--
-- Table structure for table `production_lines`
--

CREATE TABLE `production_lines` (
  `id` int(11) NOT NULL,
  `line_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_lines`
--

INSERT INTO `production_lines` (`id`, `line_name`) VALUES
(1, 'Line 1'),
(2, 'Line 2');

-- --------------------------------------------------------

--
-- Table structure for table `production_logs`
--

CREATE TABLE `production_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `employee` varchar(255) NOT NULL,
  `machine_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `line_id` int(11) NOT NULL,
  `production_date` datetime DEFAULT current_timestamp(),
  `job_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_logs`
--

INSERT INTO `production_logs` (`id`, `product_id`, `employee`, `machine_id`, `shift_id`, `line_id`, `production_date`, `job_id`) VALUES
(1, 1, '', 0, 0, 0, '2025-04-29 17:25:04', 1),
(2, 6, 'tes', 1, 1, 1, '2025-05-03 11:10:32', 2),
(13, 1, '-', 0, 0, 0, '2025-05-03 14:24:08', 13),
(14, 6, '-', 0, 0, 0, '2025-05-03 14:48:57', 14),
(16, 8, '-', 0, 0, 0, '2025-05-05 14:22:42', 16),
(17, 8, 'ipul', 1, 2, 2, '2025-05-05 14:23:59', 17),
(18, 2, 'test', 1, 1, 1, '2025-05-05 14:30:48', 18),
(19, 2, 'test', 1, 1, 1, '2025-05-05 14:31:36', 19),
(20, 1, '-', 0, 0, 0, '2025-05-05 14:46:57', 20),
(21, 2, 'test', 1, 2, 1, '2025-05-05 14:48:09', 21),
(22, 2, '213', 1, 1, 1, '2025-07-23 08:15:59', 22),
(23, 2, '333', 2, 2, 2, '2025-07-23 08:17:41', 23);

-- --------------------------------------------------------

--
-- Table structure for table `production_stock`
--

CREATE TABLE `production_stock` (
  `id` int(11) NOT NULL,
  `material_id` int(10) NOT NULL,
  `uom_id` int(2) NOT NULL,
  `availability_length` decimal(10,2) NOT NULL,
  `last_order_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_stock`
--

INSERT INTO `production_stock` (`id`, `material_id`, `uom_id`, `availability_length`, `last_order_date`) VALUES
(1, 1, 1, 49.00, '2024-12-11'),
(2, 2, 1, 0.00, '2024-12-21'),
(3, 3, 1, 45.00, '2024-12-05'),
(4, 4, 1, 99994.00, '2024-12-08'),
(5, 5, 1, 35.00, '2024-01-09'),
(6, 6, 1, 30.00, '2024-11-09'),
(7, 7, 1, 4.00, '2024-12-15'),
(8, 8, 1, 20.00, '2024-06-06'),
(9, 9, 1, 2.00, '2024-11-11'),
(10, 10, 5, 865978.00, '2025-04-27'),
(11, 11, 5, 8609995.00, '2025-04-27'),
(12, 12, 5, 35.00, '2025-04-27'),
(13, 13, 5, 50.00, '2025-04-27'),
(14, 14, 5, 45.00, '2025-04-20'),
(15, 15, 5, 78.00, '2025-04-21'),
(16, 16, 5, 10.00, '2025-04-22'),
(17, 17, 5, 86.00, '2025-04-23'),
(18, 18, 5, 33.00, '2025-04-24'),
(19, 19, 5, 61.00, '2025-04-25');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `created_at`) VALUES
(1, 'MORRIS 100ML', '2025-04-27 16:25:05'),
(2, 'test', '2025-04-27 17:45:04'),
(6, 'test2', '2025-04-28 15:02:19'),
(8, 'CUSSONS', '2025-05-05 06:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `product_colors`
--

CREATE TABLE `product_colors` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_name` varchar(50) NOT NULL,
  `color_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`id`, `product_id`, `color_name`, `color_code`) VALUES
(2, 1, 'Black Ink', '10'),
(3, 2, 'Black Ink', NULL),
(15, 6, 'White Ink', NULL),
(19, 8, 'Black Ink', NULL),
(20, 8, 'Pantone 072C', NULL),
(21, 8, 'Clear Varnish', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL,
  `shift_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `shift_name`) VALUES
(1, 'Shift 1'),
(2, 'Shift 2');

-- --------------------------------------------------------

--
-- Table structure for table `uom`
--

CREATE TABLE `uom` (
  `uom_id` int(2) NOT NULL,
  `uom_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uom`
--

INSERT INTO `uom` (`uom_id`, `uom_name`) VALUES
(1, 'roll'),
(2, 'cm'),
(3, 'm'),
(4, 'ml'),
(5, 'l'),
(6, 'kg'),
(7, 'g'),
(8, 'pack'),
(9, 'pcs'),
(10, 'box');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`name`, `email`, `password`) VALUES
('ilham', 'ilham@gmail.com', '$2y$10$aGslFAXQoOBRJzaZRp5rFeXKS/bAVyTXInTMk0cSy9z51YnVn95y2'),
('Mas Fuad', 'fuad@gmail.com', '$2y$10$qDJ8avIPqrANh2xC4y295.IWuhqL2K19TmsmZRow3Wl1jlr6vUcJa'),
('test', 'test@example.com', '$2y$10$Ywv78HLPXC1pUI809VGpCeg6n67uQ8mytXv1NEnyQrWRf3IMa.Ywa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bom_headers`
--
ALTER TABLE `bom_headers`
  ADD PRIMARY KEY (`bom_id`),
  ADD KEY `fk_bom_product` (`product_id`);

--
-- Indexes for table `bom_items`
--
ALTER TABLE `bom_items`
  ADD PRIMARY KEY (`bom_item_id`),
  ADD KEY `fk_bom_item_bom` (`bom_id`),
  ADD KEY `fk_bom_item_material` (`material_id`),
  ADD KEY `fk_bom_item_uom` (`uom_id`);

--
-- Indexes for table `capacity_machine`
--
ALTER TABLE `capacity_machine`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fk_machine` (`machine_model`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inventory_material` (`material_id`),
  ADD KEY `fk_inventory_uom` (`uom_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `machine`
--
ALTER TABLE `machine`
  ADD PRIMARY KEY (`machine_id`);

--
-- Indexes for table `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `fk_material_type` (`type`),
  ADD KEY `fk_material_uom` (`uom`);

--
-- Indexes for table `material_type`
--
ALTER TABLE `material_type`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `production_lines`
--
ALTER TABLE `production_lines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_logs`
--
ALTER TABLE `production_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `machine_id` (`machine_id`),
  ADD KEY `shift_id` (`shift_id`),
  ADD KEY `line_id` (`line_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `production_stock`
--
ALTER TABLE `production_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_color_product` (`product_id`);

--
-- Indexes for table `uom`
--
ALTER TABLE `uom`
  ADD PRIMARY KEY (`uom_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bom_headers`
--
ALTER TABLE `bom_headers`
  MODIFY `bom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bom_items`
--
ALTER TABLE `bom_items`
  MODIFY `bom_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `capacity_machine`
--
ALTER TABLE `capacity_machine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `machine`
--
ALTER TABLE `machine`
  MODIFY `machine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `material`
--
ALTER TABLE `material`
  MODIFY `material_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `material_type`
--
ALTER TABLE `material_type`
  MODIFY `type_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `production_logs`
--
ALTER TABLE `production_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `production_stock`
--
ALTER TABLE `production_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `uom`
--
ALTER TABLE `uom`
  MODIFY `uom_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bom_headers`
--
ALTER TABLE `bom_headers`
  ADD CONSTRAINT `fk_bom_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `bom_items`
--
ALTER TABLE `bom_items`
  ADD CONSTRAINT `fk_bom_item_bom` FOREIGN KEY (`bom_id`) REFERENCES `bom_headers` (`bom_id`),
  ADD CONSTRAINT `fk_bom_item_material` FOREIGN KEY (`material_id`) REFERENCES `material` (`material_id`),
  ADD CONSTRAINT `fk_bom_item_uom` FOREIGN KEY (`uom_id`) REFERENCES `uom` (`uom_id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_material` FOREIGN KEY (`material_id`) REFERENCES `material` (`material_id`),
  ADD CONSTRAINT `fk_inventory_uom` FOREIGN KEY (`uom_id`) REFERENCES `uom` (`uom_id`);

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `material`
--
ALTER TABLE `material`
  ADD CONSTRAINT `fk_material_type` FOREIGN KEY (`type`) REFERENCES `material_type` (`type_id`),
  ADD CONSTRAINT `fk_material_uom` FOREIGN KEY (`uom`) REFERENCES `uom` (`uom_id`);

--
-- Constraints for table `production_logs`
--
ALTER TABLE `production_logs`
  ADD CONSTRAINT `production_logs_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`);

--
-- Constraints for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD CONSTRAINT `fk_color_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
