-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2021 at 08:48 PM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `soes`
--

-- --------------------------------------------------------

--
-- Table structure for table `class_soes`
--

CREATE TABLE `class_soes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `class_code` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `class_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `class_created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `class_soes`
--

INSERT INTO `class_soes` (`class_id`, `class_name`, `class_code`, `class_status`, `class_created_on`) VALUES
(1, '8th', 'd04f6e0cc31249a41bd1572c6ab6331c', 'Enable', '2021-06-29 16:28:57'),
(2, 'physics', 'a092bc7a70520b29b5e1220c855455e4', 'Enable', '2021-06-30 06:33:29'),
(3, 'abc1', '09346a0f0102034c5ab489a300b16ab1', 'Enable', '2021-06-30 23:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `exam_soes`
--

CREATE TABLE `exam_soes` (
  `exam_id` int(11) NOT NULL,
  `exam_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `exam_class_id` int(11) NOT NULL,
  `exam_duration` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `exam_status` enum('Pending','Created','Started','Completed') COLLATE utf8_unicode_ci NOT NULL,
  `exam_created_on` datetime NOT NULL,
  `exam_code` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `exam_result_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exam_soes`
--

INSERT INTO `exam_soes` (`exam_id`, `exam_title`, `exam_class_id`, `exam_duration`, `exam_status`, `exam_created_on`, `exam_code`, `exam_result_datetime`) VALUES
(1, 'Net101', 1, '5', 'Completed', '2021-06-29 16:29:30', '1f8fb730ae386beb491b84e9b4c1ec19', '0000-00-00 00:00:00'),
(2, 'physics', 2, '60', 'Started', '2021-06-30 06:34:43', '6d6fc1a3996ae496a493ab6fc701f99e', '0000-00-00 00:00:00'),
(3, 'abc', 3, '5', 'Started', '2021-06-30 23:52:47', '1f07407b9644ef18969adc8ece410d24', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `exam_subject_question_answer`
--

CREATE TABLE `exam_subject_question_answer` (
  `answer_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_subject_question_id` int(11) NOT NULL,
  `student_answer_option` enum('0','1','2','3','4') COLLATE utf8_unicode_ci NOT NULL,
  `marks` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exam_subject_question_answer`
--

INSERT INTO `exam_subject_question_answer` (`answer_id`, `student_id`, `exam_subject_question_id`, `student_answer_option`, `marks`) VALUES
(1, 1, 1, '1', '2');

-- --------------------------------------------------------

--
-- Table structure for table `exam_subject_question_soes`
--

CREATE TABLE `exam_subject_question_soes` (
  `exam_subject_question_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `exam_subject_id` int(11) NOT NULL,
  `exam_subject_question_title` text COLLATE utf8_unicode_ci NOT NULL,
  `exam_subject_question_answer` enum('1','2','3','4') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exam_subject_question_soes`
--

INSERT INTO `exam_subject_question_soes` (`exam_subject_question_id`, `exam_id`, `exam_subject_id`, `exam_subject_question_title`, `exam_subject_question_answer`) VALUES
(1, 1, 1, 'what is networking?', '2'),
(2, 3, 6, 'what is networking?', '1');

-- --------------------------------------------------------

--
-- Table structure for table `question_option_soes`
--

CREATE TABLE `question_option_soes` (
  `question_option_id` int(11) NOT NULL,
  `exam_subject_question_id` int(11) NOT NULL,
  `question_option_number` int(1) NOT NULL,
  `question_option_title` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `question_option_soes`
--

INSERT INTO `question_option_soes` (`question_option_id`, `exam_subject_question_id`, `question_option_number`, `question_option_title`) VALUES
(1, 1, 4, 'this is a test'),
(2, 2, 1, 'asdasd'),
(3, 2, 2, 'sadasd'),
(4, 2, 3, 'sasd'),
(5, 2, 4, 'sadasd');

-- --------------------------------------------------------

--
-- Table structure for table `student_soes`
--

CREATE TABLE `student_soes` (
  `student_id` int(11) NOT NULL,
  `student_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `student_address` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `student_email_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `student_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `student_gender` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `student_dob` date NOT NULL,
  `student_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `student_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `student_email_verification_code` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `student_email_verified` enum('No','Yes') COLLATE utf8_unicode_ci NOT NULL,
  `student_added_by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `student_added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `student_soes`
--

INSERT INTO `student_soes` (`student_id`, `student_name`, `student_address`, `student_email_id`, `student_password`, `student_gender`, `student_dob`, `student_image`, `student_status`, `student_email_verification_code`, `student_email_verified`, `student_added_by`, `student_added_on`) VALUES
(1, 'pratik saha', 'india', 'macjustinbieber@gmail.com', '12345', 'Male', '1997-12-15', '../images/43901260.jpg', 'Enable', '45c0c14249ed870dfec9b7874b94a6b6', 'Yes', 'Master', '2021-06-29 15:33:21');

-- --------------------------------------------------------

--
-- Table structure for table `student_to_class_soes`
--

CREATE TABLE `student_to_class_soes` (
  `student_to_class_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_roll_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `student_to_class_soes`
--

INSERT INTO `student_to_class_soes` (`student_to_class_id`, `class_id`, `student_id`, `student_roll_no`, `added_on`) VALUES
(1, 1, 1, '420', '2021-06-29 20:45:13');

-- --------------------------------------------------------

--
-- Table structure for table `subject_soes`
--

CREATE TABLE `subject_soes` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `subject_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `subject_created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `subject_soes`
--

INSERT INTO `subject_soes` (`subject_id`, `subject_name`, `subject_status`, `subject_created_on`) VALUES
(1, 'Networking', 'Enable', '2021-06-29 16:28:29'),
(2, 'Humanity', 'Enable', '2021-06-29 20:47:55'),
(3, 'physics', 'Enable', '2021-06-30 06:33:51');

-- --------------------------------------------------------

--
-- Table structure for table `subject_to_class_soes`
--

CREATE TABLE `subject_to_class_soes` (
  `subject_to_class_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `subject_to_class_soes`
--

INSERT INTO `subject_to_class_soes` (`subject_to_class_id`, `class_id`, `subject_id`, `added_on`) VALUES
(1, 1, 1, '2021-06-29 16:29:07'),
(2, 1, 2, '2021-06-29 20:49:11'),
(3, 2, 3, '2021-06-30 06:36:32'),
(4, 3, 2, '2021-06-30 23:52:27');

-- --------------------------------------------------------

--
-- Table structure for table `subject_wise_exam_detail`
--

CREATE TABLE `subject_wise_exam_detail` (
  `exam_subject_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `subject_total_question` int(5) NOT NULL,
  `marks_per_right_answer` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `marks_per_wrong_answer` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `subject_exam_datetime` datetime NOT NULL,
  `subject_exam_status` enum('Pending','Started','Completed') COLLATE utf8_unicode_ci NOT NULL,
  `subject_exam_code` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `subject_wise_exam_detail`
--

INSERT INTO `subject_wise_exam_detail` (`exam_subject_id`, `exam_id`, `subject_id`, `subject_total_question`, `marks_per_right_answer`, `marks_per_wrong_answer`, `subject_exam_datetime`, `subject_exam_status`, `subject_exam_code`) VALUES
(1, 1, 1, 5, '1', '1', '2021-06-30 07:00:00', 'Completed', ''),
(2, 7, 0, 0, '', '', '0000-00-00 00:00:00', 'Pending', ''),
(3, 2, 0, 0, '', '', '0000-00-00 00:00:00', 'Pending', ''),
(4, 2, 3, 5, '1', '1', '2021-06-30 23:55:00', 'Pending', '4333a1d21df8075a4d1ccb7ca31df067'),
(5, 2, 3, 5, '1', '1', '2021-06-30 23:55:00', 'Pending', '2170e8727fde276e6f07cfb4a4f7d48e'),
(6, 3, 2, 5, '1', '1', '2021-06-30 23:55:00', 'Pending', 'cee8c61440ce9151f131eb46697caa25');

-- --------------------------------------------------------

--
-- Table structure for table `user_soes`
--

CREATE TABLE `user_soes` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_contact_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_profile` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `user_type` enum('Master','User') COLLATE utf8_unicode_ci NOT NULL,
  `user_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `user_created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_soes`
--

INSERT INTO `user_soes` (`user_id`, `user_name`, `user_contact_no`, `user_email`, `user_password`, `user_profile`, `user_type`, `user_status`, `user_created_on`) VALUES
(1, 'ADMIN', '1', 'admin@mail.com', '12345', '../images/1083605241.jpg', 'Master', 'Enable', '2021-06-29 10:09:06'),
(2, 'Psarkar', '2', 'psarkar@mail.com', '123456', '', 'User', 'Enable', '2021-06-29 10:10:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `class_soes`
--
ALTER TABLE `class_soes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `exam_soes`
--
ALTER TABLE `exam_soes`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `exam_subject_question_answer`
--
ALTER TABLE `exam_subject_question_answer`
  ADD PRIMARY KEY (`answer_id`);

--
-- Indexes for table `exam_subject_question_soes`
--
ALTER TABLE `exam_subject_question_soes`
  ADD PRIMARY KEY (`exam_subject_question_id`);

--
-- Indexes for table `question_option_soes`
--
ALTER TABLE `question_option_soes`
  ADD PRIMARY KEY (`question_option_id`);

--
-- Indexes for table `student_soes`
--
ALTER TABLE `student_soes`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `student_to_class_soes`
--
ALTER TABLE `student_to_class_soes`
  ADD PRIMARY KEY (`student_to_class_id`);

--
-- Indexes for table `subject_soes`
--
ALTER TABLE `subject_soes`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `subject_to_class_soes`
--
ALTER TABLE `subject_to_class_soes`
  ADD PRIMARY KEY (`subject_to_class_id`);

--
-- Indexes for table `subject_wise_exam_detail`
--
ALTER TABLE `subject_wise_exam_detail`
  ADD PRIMARY KEY (`exam_subject_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `class_soes`
--
ALTER TABLE `class_soes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exam_soes`
--
ALTER TABLE `exam_soes`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exam_subject_question_answer`
--
ALTER TABLE `exam_subject_question_answer`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_subject_question_soes`
--
ALTER TABLE `exam_subject_question_soes`
  MODIFY `exam_subject_question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `question_option_soes`
--
ALTER TABLE `question_option_soes`
  MODIFY `question_option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_soes`
--
ALTER TABLE `student_soes`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_to_class_soes`
--
ALTER TABLE `student_to_class_soes`
  MODIFY `student_to_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subject_soes`
--
ALTER TABLE `subject_soes`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subject_to_class_soes`
--
ALTER TABLE `subject_to_class_soes`
  MODIFY `subject_to_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subject_wise_exam_detail`
--
ALTER TABLE `subject_wise_exam_detail`
  MODIFY `exam_subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
