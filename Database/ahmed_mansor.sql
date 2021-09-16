-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2021 at 11:46 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ahmed_mansor`
--

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` bigint(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` varchar(8000) NOT NULL,
  `image1` varchar(500) NOT NULL,
  `price` float NOT NULL,
  `qty` int(4) NOT NULL,
  `total_purchase` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `image1`, `price`, `qty`, `total_purchase`, `status`) VALUES
(1, 'Cooking Oil', 'Test', '1134316606Cooking Oil.jpg', 2, 1, '9', 'Active'),
(8, 'Maize flour', 'Test', '263439214Maize flour.jpg', 2, 10, '2', 'Active'),
(11, 'Wheat Flour', 'test', '1051807463wheat flour.jpg', 2, 10, '', 'Active'),
(12, 'Salt', 'test', '1049660208salt.jpg', 0.4, 5, '2', 'Active'),
(22, 'Milk', 'Test', '356924563milk.jpg', 2, 5, '', 'Active'),
(23, 'Sugar', 'waa', '284093101sugar.webp', 1, 3, '', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `product_order`
--

CREATE TABLE `product_order` (
  `id` int(4) UNSIGNED NOT NULL,
  `shippingid` int(4) NOT NULL,
  `billingid` int(4) NOT NULL,
  `order_no` varchar(50) NOT NULL,
  `tracking_id` varchar(255) NOT NULL,
  `orderdate` datetime NOT NULL,
  `amount` varchar(50) NOT NULL,
  `sub_amount` varchar(25) NOT NULL,
  `total_charge_amount` varchar(255) NOT NULL,
  `total_discount` varchar(255) NOT NULL,
  `checkouttype` varchar(300) NOT NULL,
  `status` int(4) NOT NULL COMMENT '0 = ''Payment Pending'',1=''Ready to ship'',2=''Shipping Done'',3=''Order Complete''',
  `payment_type` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0-PayPal 1- CcAvenue 2-COD',
  `mailcounter` int(4) NOT NULL,
  `couponcode` varchar(50) NOT NULL,
  `paypal_transaction_id` varchar(500) NOT NULL,
  `ordercomments` varchar(1000) NOT NULL,
  `actualprice` double NOT NULL DEFAULT 0,
  `discount` double NOT NULL DEFAULT 0,
  `currency_code` varchar(10) NOT NULL,
  `qty` int(255) NOT NULL DEFAULT 0,
  `productid` varchar(255) NOT NULL,
  `o_created` int(11) NOT NULL,
  `o_modified` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_order`
--

INSERT INTO `product_order` (`id`, `shippingid`, `billingid`, `order_no`, `tracking_id`, `orderdate`, `amount`, `sub_amount`, `total_charge_amount`, `total_discount`, `checkouttype`, `status`, `payment_type`, `mailcounter`, `couponcode`, `paypal_transaction_id`, `ordercomments`, `actualprice`, `discount`, `currency_code`, `qty`, `productid`, `o_created`, `o_modified`) VALUES
(26, 35, 0, '', '', '2019-01-08 06:11:33', '840.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 4, '20', 0, 0),
(27, 35, 0, '', '', '2019-01-08 06:11:33', '600.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 2, '12', 0, 0),
(28, 36, 0, '', '', '2019-01-08 06:23:06', '600.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '18', 0, 0),
(29, 37, 0, '', '', '2019-01-08 06:24:04', '210.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '20', 0, 0),
(30, 38, 0, '', '', '2019-01-08 07:05:47', '1099.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '14', 0, 0),
(31, 39, 0, '', '', '2019-01-08 07:17:26', '600.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '18', 0, 0),
(32, 40, 0, '', '', '2019-01-08 07:19:59', '998.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '17', 0, 0),
(33, 41, 0, '', '', '2019-01-08 07:21:34', '600.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '18', 0, 0),
(34, 42, 0, '', '', '2019-01-09 09:13:38', '1250.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 5, '8', 0, 0),
(35, 43, 0, '', '', '2019-01-09 09:21:26', '1200.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 2, '18', 0, 0),
(36, 43, 0, '', '', '2019-01-09 09:21:26', '600.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 3, '13', 0, 0),
(37, 44, 0, '', '', '2019-01-09 09:59:53', '1200.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 2, '18', 0, 0),
(38, 44, 0, '', '', '2019-01-09 09:59:53', '800.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 4, '13', 0, 0),
(39, 45, 0, '', '', '2019-01-09 10:00:51', '1200.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 2, '18', 0, 0),
(40, 45, 0, '', '', '2019-01-09 10:00:51', '800.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 4, '13', 0, 0),
(41, 46, 0, '', '', '2019-01-09 10:01:42', '1200.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 2, '18', 0, 0),
(42, 46, 0, '', '', '2019-01-09 10:01:42', '800.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 4, '13', 0, 0),
(43, 47, 0, '', '', '2019-01-09 10:02:24', '1200.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 2, '18', 0, 0),
(44, 47, 0, '', '', '2019-01-09 10:02:24', '800.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 4, '13', 0, 0),
(45, 48, 0, '', '', '2019-01-09 10:03:38', '1200.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 2, '18', 0, 0),
(46, 48, 0, '', '', '2019-01-09 10:03:38', '800.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 4, '13', 0, 0),
(47, 49, 0, '', '', '2019-01-09 10:56:06', '600.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '18', 0, 0),
(48, 50, 0, '', '', '2019-01-09 11:47:02', '600.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '18', 0, 0),
(49, 51, 0, '', '', '2019-01-09 02:24:08', '630.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 3, '20', 0, 0),
(50, 51, 0, '', '', '2019-01-09 02:24:08', '500.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 2, '8', 0, 0),
(51, 52, 0, '', '', '2019-01-10 08:37:42', '400.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 2, '13', 0, 0),
(52, 52, 0, '', '', '2019-01-10 08:37:42', '699.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '1', 0, 0),
(53, 53, 0, '', '', '2019-01-14 06:48:29', '210.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '20', 0, 0),
(54, 54, 0, '', '', '2019-01-14 06:49:41', '210.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '20', 0, 0),
(55, 55, 0, '', '', '2019-01-14 06:51:06', '210.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '20', 0, 0),
(56, 56, 0, '', '', '2019-01-14 06:57:07', '210.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '20', 0, 0),
(57, 57, 0, '', '', '2019-01-14 07:00:05', '210.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '20', 0, 0),
(58, 58, 0, '', '', '2021-09-16 10:21:57', '4.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 4, '23', 0, 0),
(59, 60, 0, '', '', '2021-09-16 11:33:36', '4.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 2, '22', 0, 0),
(60, 61, 0, '', '', '2021-09-16 11:35:00', '4.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 2, '11', 0, 0),
(61, 61, 0, '', '', '2021-09-16 11:35:00', '2.00', '', '', '', '', 0, '0', 0, '', '', '', 0, 0, '', 1, '1', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `shippingdetails`
--

CREATE TABLE `shippingdetails` (
  `id` int(4) NOT NULL,
  `firstname` varchar(500) NOT NULL,
  `lastname` varchar(500) NOT NULL,
  `email` varchar(250) NOT NULL,
  `mobile` varchar(250) NOT NULL,
  `address` varchar(1000) NOT NULL,
  `city` varchar(250) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `total` int(50) NOT NULL,
  `pincode` varchar(50) NOT NULL,
  `country` varchar(250) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  `comments` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `shippingdetails`
--

INSERT INTO `shippingdetails` (`id`, `firstname`, `lastname`, `email`, `mobile`, `address`, `city`, `payment_type`, `total`, `pincode`, `country`, `created`, `modified`, `status`, `comments`) VALUES
(59, 'yusuf', 'abas', 'maazim@gmail.com', '7777', 'kpp', 'marka', 'Mobile Payments', 4, '', 'somali', '2021-09-16 11:31:36', '0000-00-00 00:00:00', 'Pending', ''),
(60, 'yusuf', 'abas', 'maazim@gmail.com', '7777', 'kpp', 'marka', 'Mobile Payments', 4, '', 'somali', '2021-09-16 11:33:36', '0000-00-00 00:00:00', 'Pending', ''),
(61, 'yusuf', 'abas', 'cusmaancabaas9999@gmail.com', '7777', 'kpp', 'marka', 'Cash On Delivery', 6, '', 'somali', '2021-09-16 11:35:00', '0000-00-00 00:00:00', 'Pending', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(20) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(60) NOT NULL,
  `userType` varchar(25) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(25) NOT NULL,
  `passreset` int(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` int(50) NOT NULL,
  `jobTitle` varchar(80) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `lastlogin` datetime NOT NULL,
  `status` varchar(10) NOT NULL,
  `registerDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `firstName`, `lastName`, `userType`, `username`, `password`, `passreset`, `email`, `contact`, `jobTitle`, `gender`, `lastlogin`, `status`, `registerDate`) VALUES
(1, 'Yusuf', 'Abas Ali', 'Administrator', 'admin', 'admin', 852715, 'maazim13@gmail.com', 615567673, 'System Adminstrator', 'Male', '2021-09-16 11:35:13', 'Active', '0000-00-00'),
(4, 'Qasim', 'Ismaacil', 'User', 'qaasim', 'qaasim', 0, 'qaasim@hotmail.com', 8695715, 'Data Enter', 'Male', '2017-08-10 09:23:47', 'Active', '2017-07-30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_order`
--
ALTER TABLE `product_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shippingdetails`
--
ALTER TABLE `shippingdetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `product_order`
--
ALTER TABLE `product_order`
  MODIFY `id` int(4) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `shippingdetails`
--
ALTER TABLE `shippingdetails`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
