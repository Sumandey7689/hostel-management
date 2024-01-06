-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2024 at 07:36 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shanti_hostel`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_addresses`
--

CREATE TABLE `tbl_addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_config`
--

CREATE TABLE `tbl_config` (
  `id` int(11) NOT NULL,
  `late_fine` int(10) NOT NULL,
  `reset_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_config`
--

INSERT INTO `tbl_config` (`id`, `late_fine`, `reset_date`) VALUES
(1, 50, '2024-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_expenses`
--

CREATE TABLE `tbl_expenses` (
  `expenses_id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `item` varchar(40) NOT NULL,
  `amount` int(10) NOT NULL,
  `expenses_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_master_user`
--

CREATE TABLE `tbl_master_user` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(50) NOT NULL,
  `status` int(1) NOT NULL,
  `acess` int(1) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_master_user`
--

INSERT INTO `tbl_master_user` (`id`, `name`, `username`, `password`, `status`, `acess`, `date`) VALUES
(1, 'admin', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 1, '2024-01-06 06:36:05');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_payments`
--

CREATE TABLE `tbl_payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_due_date` date DEFAULT NULL,
  `grace_period` int(11) DEFAULT 5,
  `total_payment_amount` int(10) DEFAULT NULL,
  `additional_comments` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_payments_history`
--

CREATE TABLE `tbl_payments_history` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `total_payment_amount` int(10) DEFAULT NULL,
  `payment_month` varchar(20) DEFAULT NULL,
  `payment_color` varchar(20) DEFAULT NULL,
  `additional_comments` varchar(30) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_payments_months`
--

CREATE TABLE `tbl_payments_months` (
  `month_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `january` int(1) DEFAULT 0,
  `february` int(1) DEFAULT 0,
  `march` int(1) DEFAULT 0,
  `april` int(1) DEFAULT 0,
  `may` int(1) DEFAULT 0,
  `june` int(1) DEFAULT 0,
  `july` int(1) DEFAULT 0,
  `august` int(1) DEFAULT 0,
  `september` int(1) DEFAULT 0,
  `october` int(1) DEFAULT 0,
  `november` int(1) DEFAULT 0,
  `december` int(1) DEFAULT 0,
  `active` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rooms_data`
--

CREATE TABLE `tbl_rooms_data` (
  `room_id` int(11) NOT NULL,
  `room_type` varchar(30) NOT NULL,
  `room_category` varchar(30) NOT NULL,
  `room_number` int(10) NOT NULL,
  `room_capacity` int(10) NOT NULL,
  `room_filled` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_room_tracking`
--

CREATE TABLE `tbl_room_tracking` (
  `tracking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_type` varchar(30) NOT NULL,
  `room_category` varchar(30) NOT NULL,
  `room_number` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `number` varchar(10) NOT NULL,
  `location_type` varchar(10) NOT NULL,
  `subject` varchar(30) NOT NULL,
  `year` varchar(5) NOT NULL,
  `semester` varchar(5) NOT NULL,
  `organizationname` varchar(60) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users_room`
--

CREATE TABLE `tbl_users_room` (
  `user_room_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) NOT NULL,
  `room_type` varchar(30) NOT NULL,
  `room_category` varchar(30) NOT NULL,
  `room_number` int(10) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_addresses`
--
ALTER TABLE `tbl_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_config`
--
ALTER TABLE `tbl_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_expenses`
--
ALTER TABLE `tbl_expenses`
  ADD PRIMARY KEY (`expenses_id`);

--
-- Indexes for table `tbl_master_user`
--
ALTER TABLE `tbl_master_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_payments`
--
ALTER TABLE `tbl_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_payments_history`
--
ALTER TABLE `tbl_payments_history`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_payments_months`
--
ALTER TABLE `tbl_payments_months`
  ADD PRIMARY KEY (`month_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_rooms_data`
--
ALTER TABLE `tbl_rooms_data`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `tbl_room_tracking`
--
ALTER TABLE `tbl_room_tracking`
  ADD PRIMARY KEY (`tracking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `tbl_users_room`
--
ALTER TABLE `tbl_users_room`
  ADD PRIMARY KEY (`user_room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_addresses`
--
ALTER TABLE `tbl_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_config`
--
ALTER TABLE `tbl_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_expenses`
--
ALTER TABLE `tbl_expenses`
  MODIFY `expenses_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_master_user`
--
ALTER TABLE `tbl_master_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_payments`
--
ALTER TABLE `tbl_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_payments_history`
--
ALTER TABLE `tbl_payments_history`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_payments_months`
--
ALTER TABLE `tbl_payments_months`
  MODIFY `month_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_rooms_data`
--
ALTER TABLE `tbl_rooms_data`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_room_tracking`
--
ALTER TABLE `tbl_room_tracking`
  MODIFY `tracking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_users_room`
--
ALTER TABLE `tbl_users_room`
  MODIFY `user_room_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_addresses`
--
ALTER TABLE `tbl_addresses`
  ADD CONSTRAINT `tbl_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`);

--
-- Constraints for table `tbl_payments`
--
ALTER TABLE `tbl_payments`
  ADD CONSTRAINT `tbl_payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`);

--
-- Constraints for table `tbl_payments_history`
--
ALTER TABLE `tbl_payments_history`
  ADD CONSTRAINT `tbl_payments_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`);

--
-- Constraints for table `tbl_payments_months`
--
ALTER TABLE `tbl_payments_months`
  ADD CONSTRAINT `tbl_payments_months_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`);

--
-- Constraints for table `tbl_room_tracking`
--
ALTER TABLE `tbl_room_tracking`
  ADD CONSTRAINT `tbl_room_tracking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`);

--
-- Constraints for table `tbl_users_room`
--
ALTER TABLE `tbl_users_room`
  ADD CONSTRAINT `tbl_users_room_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
