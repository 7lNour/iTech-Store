-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2026 at 02:45 PM
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
-- Database: `itech_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`) VALUES
(1, 'noor', 'noor1234*'),
(2, 'Taghreed', 'Taghreed1234*'),
(3, 'Aishah', 'Aishah12345@'),
(4, 'Buthaynah ', 'Buthaynah1#');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `image_file` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `admin_id`, `product_name`, `description`, `price`, `quantity`, `category`, `image_file`) VALUES
(1, 1, 'iPhone 17', 'Storage: 256GB\r\n\r\nColor: Blue\r\n\r\nDisplay: 6.1 inch OLED\r\n\r\nCamera: 48MP\r\n\r\nBattery: 24 hours\r\n\r\nProcessor: A18 Chip', 3799.00, 20, 'iphone', 'iphone17.jpg'),
(2, 1, 'iPhone 17 Pro', 'Storage: 512GB\r\n\r\nColor: Black Titanium\r\n\r\nDisplay: 6.7 inch OLED\r\n\r\nCamera: 64MP\r\n\r\nBattery: 28 hours\r\n\r\nProcessor: A18 Pro', 5199.00, 20, 'iphone', 'iphone17pro.jpg'),
(4, 1, 'iPhone Air', 'Storage: 256GB\r\n\r\nColor: Silver\r\n\r\nDisplay: 6.5 inch OLED\r\n\r\nCamera: 50MP\r\n\r\nBattery: 25 hours\r\n\r\nProcessor: A18 Chip', 4299.00, 20, 'iphone', 'iphoneair.jpg'),
(5, 1, 'iPad A16', 'Storage: 128GB\r\n\r\nColor: Blue\r\n\r\nDisplay: 10.9 inch\r\n\r\nChip: A16 Bionic\r\n\r\nBattery: 10 hours\r\n\r\nPencil Support: Yes', 1999.00, 20, 'ipad', 'ipadA16.jpg'),
(6, 1, 'iPad Mini', 'Storage: 256GB\r\n\r\nColor: Space Gray\r\n\r\nDisplay: 8.3 inch\r\n\r\nChip: A17\r\n\r\nBattery: 10 hours\r\n\r\nPencil Support: Yes', 2299.00, 20, 'ipad', 'ipadmini.jpg'),
(7, 1, 'iPad Pro', 'Storage: 512GB\r\n\r\nColor: Silver\r\n\r\nDisplay: 12.9 inch Retina\r\n\r\nChip: M4\r\n\r\nBattery: 12 hours\r\n\r\nPencil Support: Yes', 4199.00, 20, 'ipad', 'ipadpro.jpg'),
(8, 1, 'MacBook Air', 'Chip: M3\r\n\r\nRAM: 8GB\r\n\r\nStorage: 256GB SSD\r\n\r\nDisplay: 13 inch\r\n\r\nBattery: 18 hours\r\n\r\nWeight: 1.2 kg', 4999.00, 20, 'macbook', 'macbookAir.jpg'),
(9, 1, 'MacBook Pro', 'Chip: M4 Pro\r\n\r\nRAM: 16GB\r\n\r\nStorage: 512GB SSD\r\n\r\nDisplay: 14 inch Retina\r\n\r\nBattery: 20 hours\r\n\r\nWeight: 1.6 kg', 7999.00, 20, 'macbook', 'macbooPro.jpg'),
(10, 1, 'AirPods Pro', 'Noise Cancellation: Yes\r\n\r\nBattery: 6 hours\r\n\r\nCase Battery: 30 hours\r\n\r\nCharging: Wireless\r\n\r\nConnectivity: Bluetooth 5.3\r\n\r\nWater Resistance: IPX4', 999.00, 20, 'airpods', 'airpodsPro.jpg'),
(11, 1, 'Apple Pencil Pro', 'Compatibility: iPad Pro / iPad Air\r\n\r\nCharging: Magnetic\r\n\r\nFeature: Precision Drawing\r\n\r\nPressure Sensitivity: Yes\r\n\r\nLatency: Low\r\n\r\nWeight: Light', 549.00, 20, 'pencil', 'pencilpro.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_admin` (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
