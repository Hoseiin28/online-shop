-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2025 at 10:58 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `onlinestore`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `CartID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`CartID`, `UserID`, `ProductID`, `Quantity`) VALUES
(5, 1, 64, 2);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `OrderDetailID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`OrderDetailID`, `OrderID`, `ProductID`, `Quantity`, `Price`) VALUES
(2, 1, 69, 1, '700000'),
(3, 2, 75, 2, '150000');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `TotalPrice` decimal(10,0) NOT NULL,
  `Status` enum('Pending','Processing','Completed','Cancelled') DEFAULT 'Pending',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `UserID`, `TotalPrice`, `Status`, `CreatedAt`) VALUES
(1, 1, '950000', 'Processing', '2025-05-22 08:11:19'),
(2, 1, '300000', 'Completed', '2025-05-22 08:54:46');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ProductID` int(11) NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Price` decimal(10,0) NOT NULL,
  `Stock` int(11) NOT NULL,
  `ImageURL` varchar(255) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ProductID`, `ProductName`, `Description`, `Price`, `Stock`, `ImageURL`, `CreatedAt`) VALUES
(62, 'گوشی موبایل مدل A1', 'گوشی موبایل با صفحه نمایش 6.5 اینچ و دوربین 48 مگاپیکسل', '4500000', 10, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(63, 'لپ‌تاپ مدل B2', 'لپ‌تاپ با پردازنده Intel Core i5 و حافظه 8 گیگابایت', '12000000', 5, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(64, 'هدفون بی‌سیم C3', 'هدفون بلوتوثی با کیفیت صدای عالی و باتری طولانی', '1500000', 14, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(65, 'ساعت هوشمند D4', 'ساعت هوشمند با قابلیت اندازه‌گیری ضربان قلب و گام‌شمار', '3000000', 8, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(66, 'دوربین دیجیتال E5', 'دوربین دیجیتال با رزولوشن 20 مگاپیکسل و زوم اپتیکال 10 برابر', '7000000', 6, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(67, 'تبلت F6', 'تبلت با صفحه نمایش 10 اینچ و حافظه داخلی 64 گیگابایت', '6500000', 7, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(68, 'کیبورد مکانیکی G7', 'کیبورد مکانیکی با نور پس زمینه RGB و کلیدهای ضد سایش', '1800000', 20, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(69, 'ماوس بی‌سیم H8', 'ماوس بی‌سیم با دقت بالا و طراحی ارگونومیک', '700000', 23, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(70, 'اسپیکر بلوتوثی I9', 'اسپیکر قابل حمل با صدای قدرتمند و ضد آب', '2200000', 12, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(71, 'پرینتر J10', 'پرینتر لیزری با سرعت چاپ 20 صفحه در دقیقه', '4500000', 4, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(72, 'کارت گرافیک K11', 'کارت گرافیک با حافظه 4 گیگابایت و پشتیبانی از DirectX 12', '9000000', 3, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(73, 'رم کامپیوتر L12', 'رم DDR4 با ظرفیت 16 گیگابایت و فرکانس 3200 مگاهرتز', '3500000', 10, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(74, 'هارد اکسترنال M13', 'هارد اکسترنال با ظرفیت 1 ترابایت و اتصال USB 3.0', '2500000', 14, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(75, 'کابل شارژ N14', 'کابل شارژ سریع با طول 1.5 متر و مقاوم در برابر گره خوردن', '150000', 46, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(76, 'پاوربانک O15', 'پاوربانک با ظرفیت 10000 میلی‌آمپر و دو خروجی USB', '900000', 16, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(77, 'مانیتور P16', 'مانیتور 24 اینچ با رزولوشن Full HD و پنل IPS', '5500000', 6, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(78, 'کیس کامپیوتر Q17', 'کیس کامپیوتر با طراحی مدرن و تهویه مناسب', '2500000', 8, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(79, 'مادربورد R18', 'مادربورد با چیپست Intel و پشتیبانی از پردازنده‌های نسل دهم', '4500000', 5, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(80, 'سیستم خنک‌کننده S19', 'سیستم خنک‌کننده آبی برای کامپیوترهای گیمینگ', '3200000', 7, 'uploads/default.jpeg', '2025-05-22 08:04:49'),
(81, 'کارت صدا T20', 'کارت صدای حرفه‌ای با پشتیبانی از صدای 7.1 کاناله', '1500000', 10, 'uploads/default.jpeg', '2025-05-22 08:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('Admin','User') DEFAULT 'User',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FullName`, `Email`, `Password`, `Role`, `CreatedAt`) VALUES
(1, 'Hoseiin', 'Hoseiin@gmail.com', '$2y$10$zQ0b3KDEcelR.OdwIPE34.SrUzCnQ/Ex2RmLXitC.tsbOj58K6DuW', 'Admin', '2025-05-22 07:14:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`CartID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`OrderDetailID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ProductID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `CartID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orderdetails`
--
ALTER TABLE `orderdetails`
  MODIFY `OrderDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `products` (`ProductID`);

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`),
  ADD CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `products` (`ProductID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
