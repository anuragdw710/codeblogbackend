-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2023 at 07:11 PM
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
-- Database: `codeblog`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` varchar(250) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `vote` int(11) NOT NULL DEFAULT 0,
  `depth_reply` int(11) NOT NULL DEFAULT 0,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `parent_id`, `page_id`, `user_id`, `content`, `timestamp`, `vote`, `depth_reply`, `is_delete`) VALUES
(69, 0, 1, 3, 'testing direct comment 1', '2023-12-23 17:00:39', 0, 1, 0),
(70, 0, 1, 3, 'Testing direct comment 2', '2023-12-23 17:01:00', 0, 1, 0),
(71, 69, 1, 3, 'Testing reply 1', '2023-12-23 17:01:17', 0, 2, 0),
(72, 71, 1, 3, 'Testing reply2', '2023-12-23 17:01:35', 0, 3, 0),
(73, 72, 1, 3, 'Testing reply 3', '2023-12-23 17:01:49', 0, 4, 0),
(74, 73, 1, 3, 'Message Deleted', '2023-12-23 22:25:06', 0, 5, 0),
(76, 0, 1, 4, 'Other user comment 1', '2023-12-23 17:05:59', 0, 1, 0),
(77, 0, 1, 8, 'Message Deleted', '2023-12-23 22:06:35', 0, 1, 0),
(78, 0, 1, 3, 'Message Deleted', '2023-12-23 22:14:07', 0, 1, 0),
(79, 0, 1, 3, 'Message Deleted', '2023-12-23 22:14:44', 0, 1, 0),
(80, 0, 1, 3, 'Message Deleted', '2023-12-23 22:16:36', 0, 1, 0),
(81, 0, 1, 3, 'edit 2', '2023-12-23 22:46:39', 0, 1, 0),
(82, 0, 1, 3, 'chenage comment s', '2023-12-23 22:51:01', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `moderators`
--

CREATE TABLE `moderators` (
  `moderator_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `page_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`page_id`, `title`, `content`) VALUES
(1, 'Demystifying Data Structures: A Journey into the Heart of Efficient Algorithms', 'Dive into the fascinating world of data structures and algorithms as we unravel their importance in computer science. '),
(2, 'Optimizing Code: A Practical Guide to Data Structures and Algorithm Efficiency', 'Unlock the secrets of writing efficient code by delving into the intricacies of data structures and algorithms. '),
(3, 'Cracking the Code Interview: Mastering Data Structures and Algorithms for Success', 'Prepare for coding interviews like a pro by mastering the core concepts of data structures and algorithms. '),
(4, 'The Art of Problem Solving: A Comprehensive Guide to Data Structures and Algorithms', 'Unleash your problem-solving potential with this comprehensive guide to data structures and algorithms');

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `reply_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` varchar(250) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `replies`
--

INSERT INTO `replies` (`reply_id`, `comment_id`, `user_id`, `content`, `timestamp`, `is_deleted`) VALUES
(1, 1, 201, 'Thank you!', '2023-01-01 06:45:00', 0),
(2, 1, 202, 'I agree!', '2023-01-02 10:15:00', 0),
(3, 2, 203, 'Tell me more!', '2023-01-03 05:30:00', 0),
(4, 3, 204, 'We appreciate your interest!', '2023-01-04 03:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `email`, `password`) VALUES
(3, 'anu', 'anuragdw0710@gmail.com', '123'),
(4, 'aa', 'a@hmail.com', '123'),
(8, 'hanuman', 'anuragd710@gmail.com', '123');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `vote_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`vote_id`, `comment_id`, `user_id`, `vote_type`) VALUES
(150, 69, 4, 1),
(181, 69, 8, 0),
(184, 70, 8, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `moderators`
--
ALTER TABLE `moderators`
  ADD PRIMARY KEY (`moderator_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`reply_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`) USING BTREE;

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`vote_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
