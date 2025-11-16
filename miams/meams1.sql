-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2025 at 02:49 AM
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
-- Database: `meams1`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `employee_id` varchar(10) DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('PRESENT','LATE','ABSENT') DEFAULT NULL,
  `time_marked` time DEFAULT NULL,
  `actual_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `employee_id`, `schedule_id`, `date`, `status`, `time_marked`, `actual_time`) VALUES
(3, 'EMP002', 14, '2025-11-12', 'ABSENT', '11:49:45', NULL),
(4, 'EMP002', 10, '2025-11-12', 'PRESENT', '13:00:51', NULL),
(5, 'EMP001', 15, '2025-11-12', 'PRESENT', '15:00:28', NULL),
(6, 'EMP001', 8, '2025-11-14', 'PRESENT', '10:00:15', NULL),
(7, 'EMP002', 14, '2025-11-14', 'ABSENT', '11:52:26', NULL),
(8, 'EMP003', 17, '2025-11-15', 'LATE', '10:23:08', NULL),
(9, 'EMP005', 19, '2025-11-15', 'LATE', '15:35:28', NULL),
(10, 'EMP003', 20, '2025-11-15', 'PRESENT', '17:01:50', NULL),
(11, 'EMP004', 21, '2025-11-15', 'LATE', '17:24:44', NULL),
(12, 'EMP005', 22, '2025-11-15', 'LATE', '19:19:28', NULL),
(13, 'EMP006', 24, '2025-11-16', 'LATE', '09:19:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` varchar(10) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `name`, `email`, `department`) VALUES
('EMP001', 'Clydle Yvonne D. Sardana', 'sclydleyvonne@gmail.com', 'IT'),
('EMP002', 'domingo mequiso', 'migz_domerz@gmail.com', 'IE'),
('EMP003', 'Sonia Soňaz', 'sonia_2025@gmail.com', 'Custom'),
('EMP004', 'Vanessa Septimo', 'yu.jecong@gmail.com', 'engineering'),
('EMP005', 'Kate Jillian Yntig', 'k_jilyntig@gmail.com', 'Education'),
('EMP006', 'Vanessa Calimbo', 'vanz8_calimbo@gmail.com', 'Engineering');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `building` varchar(100) NOT NULL,
  `floor` int(11) NOT NULL,
  `availability` varchar(20) DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `room_name`, `capacity`, `room_type`, `building`, `floor`, `availability`) VALUES
(2, 'M001', 'ITLAB', 50, 'laboratory', 'M-Building', 2, 'Available'),
(3, 'M002', 'IELAB', 50, 'laboratory', 'M-Building', 1, 'Available'),
(4, 'S200', 'S201', 50, 'classroom', 'S-Building', 2, 'Available'),
(5, 'S202', 'S203', 50, 'classroom', 'S-Building', 2, 'Available'),
(6, 'M004', 'COMLAB', 45, 'laboratory', 'M-Building', 1, 'Available'),
(7, 'M005', 'M-301', 50, 'classroom', 'M-Building', 2, 'Available'),
(8, 'M300', 'M-302', 50, 'classroom', 'M-Building', 2, 'Available'),
(9, 'S203', 'S-204', 50, 'classroom', 'S-Building', 2, 'Available'),
(10, 'M006', 'M-305', 60, 'classroom', 'M-Building', 3, 'Available'),
(11, 'M004', 'M-304', 50, 'classroom', 'M-Building', 3, 'Available'),
(12, 'M007', 'M-306', 50, 'classroom', 'M-Building', 3, 'Available'),
(14, 'M008', 'M-308', 50, 'classroom', 'M-Building', 2, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `schedule_id` int(11) NOT NULL,
  `employee_id` varchar(10) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `day` varchar(20) DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  `time_end` time DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `day_of_week` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`schedule_id`, `employee_id`, `subject_id`, `start_time`, `end_time`, `day`, `time_start`, `time_end`, `room_id`, `day_of_week`) VALUES
(8, 'EMP001', 6, '10:00:00', '11:00:00', 'MWF', NULL, NULL, NULL, NULL),
(10, 'EMP002', 7, '13:00:00', '14:30:00', 'TTH', NULL, NULL, NULL, NULL),
(11, 'EMP001', 9, '14:30:00', '16:00:00', 'TTH', NULL, NULL, NULL, NULL),
(14, 'EMP002', 8, '11:00:00', '00:00:00', 'MWF', NULL, NULL, NULL, NULL),
(15, 'EMP001', 10, '15:00:00', '16:00:00', 'MWF', NULL, NULL, NULL, NULL),
(16, 'EMP002', 11, '16:00:00', '17:00:00', 'MWF', NULL, NULL, NULL, NULL),
(17, 'EMP003', 12, '10:00:00', '13:00:00', 'Sat', NULL, NULL, NULL, NULL),
(18, 'EMP004', 13, '13:00:00', '15:00:00', 'MWF', NULL, NULL, NULL, NULL),
(19, 'EMP005', 8, '15:00:00', '19:00:00', 'Sat', NULL, NULL, NULL, NULL),
(20, 'EMP003', 15, '17:00:00', '20:00:00', 'Sat', NULL, NULL, NULL, NULL),
(21, 'EMP004', 16, '17:00:00', '20:00:00', 'Sat', NULL, NULL, NULL, NULL),
(22, 'EMP005', 17, '19:00:00', '22:00:00', 'Sat', NULL, NULL, NULL, NULL),
(24, 'EMP006', 22, '09:00:00', '10:00:00', 'Sun', NULL, NULL, NULL, NULL),
(25, 'EMP006', 23, '10:00:00', '11:00:00', 'Sun', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `room_name` varchar(100) DEFAULT NULL,
  `room` varchar(100) DEFAULT NULL,
  `class_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`, `room_name`, `room`, `class_date`) VALUES
(6, 'intro. to computing', NULL, 'ITLAB', NULL),
(7, 'Advanced Math', NULL, 'IELAB', NULL),
(8, 'Mathematics in the Modern World', NULL, 'IELAB', NULL),
(9, 'Human Computer Interaction', NULL, 'ITLAB', NULL),
(10, 'Multimedia', NULL, 'ITLAB', NULL),
(11, 'Probability and Statistics', NULL, 'S201', NULL),
(12, 'Customs Warehousing', NULL, 'S203', NULL),
(13, 'Living in the IT Era', NULL, 'COMLAB', NULL),
(14, 'Mathematics in the Modern World', NULL, 'M-301', NULL),
(15, 'Customs Operations and Cargo Handling', NULL, 'M-302', NULL),
(16, 'Purposive Communications', NULL, 'S-204', NULL),
(17, 'Number Theory', NULL, 'M-304', NULL),
(22, 'Information System', NULL, 'M-308', NULL),
(23, 'System Engineering', NULL, 'IELAB', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(6, 'admin', 'librarylightwood@gmail.com', '$2y$10$qf7e76yQaP8I9Er4dd5GCehg5PMSpKeonokg6TSMtTLOUYKjzDEzG'),
(7, 'admin', 'carolinesardania_13@yahoo.com', '$2y$10$JxNSoZT0XsTvUy6yWMF7gOf0t5LXt2YmLz7X8gXfPyrLFrKsUHtwW'),
(10, 'school checker ', 'jossey_88@gmail.com', '$2y$10$XHKngkr7CD4mQbZscvWti.PGyJd9pFWXVlbMhVipvxfmE6LPIuHz.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`schedule_id`);

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `schedule_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
