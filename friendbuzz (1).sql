-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2016 at 08:30 AM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `friendbuzz`
--

-- --------------------------------------------------------

--
-- Table structure for table `blockedusers`
--

CREATE TABLE IF NOT EXISTS `blockedusers` (
  `id` int(11) NOT NULL,
  `blocker` varchar(16) NOT NULL,
  `blockee` varchar(16) NOT NULL,
  `blockdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE IF NOT EXISTS `conversations` (
  `conversation_id` int(8) NOT NULL,
  `conversation_name` varchar(128) NOT NULL,
  `date_created` datetime NOT NULL,
  `user_created` varchar(18) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`conversation_id`, `conversation_name`, `date_created`, `user_created`) VALUES
(1, 'Project', '2016-05-15 06:00:28', 'mayank');

-- --------------------------------------------------------

--
-- Table structure for table `conversations_member`
--

CREATE TABLE IF NOT EXISTS `conversations_member` (
  `conversation_id` int(8) NOT NULL,
  `user_id` int(8) NOT NULL,
  `conversation_last_view` datetime NOT NULL,
  `conversation_deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `conversations_member`
--

INSERT INTO `conversations_member` (`conversation_id`, `user_id`, `conversation_last_view`, `conversation_deleted`) VALUES
(1, 1, '2016-05-15 06:00:44', '0'),
(1, 3, '0000-00-00 00:00:00', '0'),
(1, 4, '0000-00-00 00:00:00', '0'),
(1, 5, '0000-00-00 00:00:00', '0'),
(1, 10, '0000-00-00 00:00:00', '0');

-- --------------------------------------------------------

--
-- Table structure for table `conversations_messages`
--

CREATE TABLE IF NOT EXISTS `conversations_messages` (
  `message_id` int(10) NOT NULL,
  `conversation_id` int(8) NOT NULL,
  `user_id` int(8) NOT NULL,
  `message_date` datetime NOT NULL,
  `message_text` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `conversations_messages`
--

INSERT INTO `conversations_messages` (`message_id`, `conversation_id`, `user_id`, `message_date`, `message_text`) VALUES
(1, 1, 1, '2016-05-15 06:00:39', 'cdsdcds');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `id` int(11) NOT NULL,
  `user1` varchar(16) NOT NULL,
  `user2` varchar(16) NOT NULL,
  `datemade` datetime NOT NULL,
  `accepted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `user1`, `user2`, `datemade`, `accepted`) VALUES
(1, 'mayank', 'manay', '2016-03-25 09:02:46', '1'),
(2, 'mayank', 'mahendra', '2016-03-25 09:03:04', '1'),
(3, 'mayank', 'manisha', '2016-03-25 09:03:18', '1'),
(4, 'mayank', 'satvik', '2016-03-25 09:03:51', '1'),
(5, 'mayank', 'shivanshu', '2016-03-25 09:04:05', '1'),
(6, 'mayank', 'pradeep', '2016-03-25 09:04:14', '1'),
(7, 'manay', 'harshika', '2016-03-26 09:32:47', '1'),
(8, 'mahendra', 'manay', '2016-04-05 13:25:37', '0'),
(9, 'mayank', 'supriya', '2016-04-06 15:25:41', '1'),
(10, 'himanshu', 'mayank', '2016-04-06 15:47:17', '0'),
(11, 'ekta', 'mayank', '2016-04-06 15:47:48', '0'),
(12, 'sumit', 'mayank', '2016-04-06 15:49:11', '0'),
(13, 'adarsh', 'mayank', '2016-04-06 15:49:56', '0'),
(14, 'akash', 'mayank', '2016-04-06 15:50:26', '0'),
(15, 'yash', 'mayank', '2016-04-06 15:51:02', '0'),
(16, 'shruti', 'mayank', '2016-04-06 15:52:45', '0'),
(17, 'mayank', 'minku', '2016-05-14 06:52:51', '0');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(8) NOT NULL,
  `s_uname` varchar(18) NOT NULL,
  `r_uname` varchar(18) NOT NULL,
  `message` text NOT NULL,
  `timesent` datetime NOT NULL,
  `sdelete` enum('0','1') NOT NULL,
  `rdelete` enum('0','1') NOT NULL,
  `messages_last_seen` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `s_uname`, `r_uname`, `message`, `timesent`, `sdelete`, `rdelete`, `messages_last_seen`) VALUES
(1, 'manay', 'mayank', 'Hiiiiii', '2016-03-25 17:14:21', '0', '0', '2016-05-14 22:51:45'),
(2, 'mayank', 'manay', 'hii back', '2016-03-25 17:14:36', '0', '0', '2016-10-02 11:57:30'),
(3, 'supriya', 'mayank', 'Hey.... Budddy How are you ', '2016-04-06 15:34:22', '0', '0', '2016-10-02 11:54:09'),
(4, 'mayank', 'supriya', 'I m Fine How are You', '2016-04-06 15:34:55', '0', '0', '2016-04-06 15:45:37');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL,
  `initiator` varchar(16) NOT NULL,
  `reciever` varchar(16) NOT NULL,
  `tagged_to` varchar(25) NOT NULL,
  `info_id` int(20) NOT NULL,
  `comment_id` int(20) NOT NULL,
  `type` varchar(25) NOT NULL,
  `did_read` enum('0','1') NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL,
  `word_best_score` varchar(15) NOT NULL,
  `memory_best_time` varchar(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `initiator`, `reciever`, `tagged_to`, `info_id`, `comment_id`, `type`, `did_read`, `date_time`, `word_best_score`, `memory_best_time`) VALUES
(11, 'manay', 'mayank', '', 2, 1, 'commented', '1', '2016-03-26 07:54:24', '', ''),
(12, 'mayank', 'manay', '', 0, 0, 'profile picture', '1', '2016-03-26 13:43:37', '', ''),
(13, 'mayank', 'mahendra', '', 0, 0, 'profile picture', '1', '2016-03-26 13:43:37', '', ''),
(14, 'mayank', 'manisha', '', 0, 0, 'profile picture', '1', '2016-03-26 13:43:37', '', ''),
(15, 'mayank', 'satvik', '', 0, 0, 'profile picture', '1', '2016-03-26 13:43:37', '', ''),
(16, 'mayank', 'shivanshu', '', 0, 0, 'profile picture', '1', '2016-03-26 13:43:37', '', ''),
(17, 'mayank', 'manay', '', 0, 0, 'profile picture', '1', '2016-03-26 13:50:15', '', ''),
(18, 'mayank', 'mahendra', '', 0, 0, 'profile picture', '1', '2016-03-26 13:50:15', '', ''),
(19, 'mayank', 'manisha', '', 0, 0, 'profile picture', '1', '2016-03-26 13:50:15', '', ''),
(20, 'mayank', 'satvik', '', 0, 0, 'profile picture', '1', '2016-03-26 13:50:16', '', ''),
(21, 'mayank', 'shivanshu', '', 0, 0, 'profile picture', '1', '2016-03-26 13:50:16', '', ''),
(22, 'mayank', 'manay', '', 0, 0, 'timeline pic', '1', '2016-03-26 13:51:46', '', ''),
(23, 'mayank', 'mahendra', '', 0, 0, 'timeline pic', '1', '2016-03-26 13:51:46', '', ''),
(24, 'mayank', 'manisha', '', 0, 0, 'timeline pic', '1', '2016-03-26 13:51:46', '', ''),
(25, 'mayank', 'satvik', '', 0, 0, 'timeline pic', '1', '2016-03-26 13:51:46', '', ''),
(26, 'mayank', 'shivanshu', '', 0, 0, 'timeline pic', '1', '2016-03-26 13:51:47', '', ''),
(27, 'mayank', 'manay', '', 0, 0, 'profile picture', '1', '2016-03-26 13:55:21', '', ''),
(28, 'mayank', 'mahendra', '', 0, 0, 'profile picture', '1', '2016-03-26 13:55:21', '', ''),
(29, 'mayank', 'manisha', '', 0, 0, 'profile picture', '1', '2016-03-26 13:55:21', '', ''),
(30, 'mayank', 'satvik', '', 0, 0, 'profile picture', '1', '2016-03-26 13:55:21', '', ''),
(31, 'mayank', 'shivanshu', '', 0, 0, 'profile picture', '1', '2016-03-26 13:55:21', '', ''),
(32, 'manay', 'harshika', '', 0, 0, 'gallery', '0', '2016-03-26 14:06:13', '', ''),
(33, 'manay', 'mayank', '', 0, 0, 'gallery', '1', '2016-03-26 14:06:13', '', ''),
(36, 'mayank', 'manay', '', 0, 0, 'profile picture', '1', '2016-03-27 14:02:27', '', ''),
(37, 'mayank', 'mahendra', '', 0, 0, 'profile picture', '1', '2016-03-27 14:02:27', '', ''),
(38, 'mayank', 'manisha', '', 0, 0, 'profile picture', '1', '2016-03-27 14:02:28', '', ''),
(39, 'mayank', 'satvik', '', 0, 0, 'profile picture', '1', '2016-03-27 14:02:28', '', ''),
(40, 'mayank', 'shivanshu', '', 0, 0, 'profile picture', '1', '2016-03-27 14:02:28', '', ''),
(42, 'manay', 'harshika', '', 0, 0, 'challenged', '0', '2016-03-30 09:39:16', '3', ''),
(43, 'manay', 'mayank', '', 0, 0, 'loss_wordrush', '1', '2016-03-30 10:09:09', '', ''),
(44, 'manay', 'manay', '', 0, 0, 'win_wordrush', '1', '2016-03-30 10:09:10', '', ''),
(45, 'mayank', 'manay', '', 0, 0, 'loss_wordrush', '1', '2016-03-30 10:14:04', '', ''),
(46, 'mayank', 'mayank', '', 0, 0, 'win_wordrush', '1', '2016-03-30 10:14:04', '', ''),
(52, 'manay', 'mayank', '', 0, 0, 'loss_wordrush', '1', '2016-03-31 07:10:30', '', ''),
(53, 'manay', 'manay', '', 0, 0, 'win_wordrush', '1', '2016-03-31 07:10:30', '', ''),
(54, 'satvik', 'mayank', '', 0, 0, 'loss_wordrush', '1', '2016-03-31 07:13:52', '', ''),
(55, 'satvik', 'satvik', '', 0, 0, 'win_wordrush', '1', '2016-03-31 07:13:52', '', ''),
(56, 'manisha', 'mayank', '', 0, 0, 'loss_wordrush', '1', '2016-03-31 07:18:58', '', ''),
(57, 'manisha', 'manisha', '', 0, 0, 'win_wordrush', '1', '2016-03-31 07:18:58', '', ''),
(58, 'mahendra', 'mayank', '', 0, 0, 'win_wordrush', '1', '2016-03-31 07:28:54', '', ''),
(59, 'mahendra', 'mahendra', '', 0, 0, 'loss_wordrush', '1', '2016-03-31 07:28:54', '', ''),
(60, 'shivanshu', 'mayank', '', 0, 0, 'win_wordrush', '1', '2016-03-31 07:34:39', '', ''),
(61, 'shivanshu', 'shivanshu', '', 0, 0, 'loss_wordrush', '1', '2016-03-31 07:34:39', '', ''),
(62, 'satvik', 'mayank', '', 0, 0, 'loss_wordrush', '1', '2016-03-31 07:37:50', '', ''),
(63, 'satvik', 'satvik', '', 0, 0, 'win__wordrush', '1', '2016-03-31 07:37:50', '', ''),
(74, 'mayank', 'manay', '', 0, 0, 'timeline pic', '1', '2016-04-03 16:03:24', '', ''),
(75, 'mayank', 'mahendra', '', 0, 0, 'timeline pic', '1', '2016-04-03 16:03:24', '', ''),
(76, 'mayank', 'manisha', '', 0, 0, 'timeline pic', '0', '2016-04-03 16:03:24', '', ''),
(77, 'mayank', 'satvik', '', 0, 0, 'timeline pic', '0', '2016-04-03 16:03:24', '', ''),
(78, 'mayank', 'shivanshu', '', 0, 0, 'timeline pic', '1', '2016-04-03 16:03:24', '', ''),
(79, 'mayank', 'manay', '', 0, 0, 'timeline pic', '1', '2016-04-03 16:03:43', '', ''),
(80, 'mayank', 'mahendra', '', 0, 0, 'timeline pic', '1', '2016-04-03 16:03:43', '', ''),
(81, 'mayank', 'manisha', '', 0, 0, 'timeline pic', '0', '2016-04-03 16:03:43', '', ''),
(82, 'mayank', 'satvik', '', 0, 0, 'timeline pic', '0', '2016-04-03 16:03:43', '', ''),
(83, 'mayank', 'shivanshu', '', 0, 0, 'timeline pic', '1', '2016-04-03 16:03:43', '', ''),
(84, 'mayank', 'manay', '', 0, 0, 'profile picture', '1', '2016-04-03 16:03:59', '', ''),
(85, 'mayank', 'mahendra', '', 0, 0, 'profile picture', '1', '2016-04-03 16:03:59', '', ''),
(86, 'mayank', 'manisha', '', 0, 0, 'profile picture', '0', '2016-04-03 16:03:59', '', ''),
(87, 'mayank', 'satvik', '', 0, 0, 'profile picture', '0', '2016-04-03 16:04:00', '', ''),
(88, 'mayank', 'shivanshu', '', 0, 0, 'profile picture', '1', '2016-04-03 16:04:00', '', ''),
(89, 'manay', 'harshika', '', 0, 0, 'posted', '0', '2016-04-05 20:22:34', '', ''),
(90, 'manay', 'mayank', '', 0, 0, 'posted', '1', '2016-04-05 20:22:34', '', ''),
(91, 'manay', 'harshika', '', 0, 0, 'posted', '0', '2016-04-05 20:22:41', '', ''),
(92, 'manay', 'mayank', '', 0, 0, 'posted', '1', '2016-04-05 20:22:41', '', ''),
(93, 'manay', 'harshika', '', 0, 0, 'posted', '0', '2016-04-05 20:22:55', '', ''),
(94, 'manay', 'mayank', '', 0, 0, 'posted', '1', '2016-04-05 20:22:55', '', ''),
(95, 'manay', 'harshika', '', 0, 0, 'posted', '0', '2016-04-05 20:23:09', '', ''),
(96, 'manay', 'mayank', '', 0, 0, 'posted', '1', '2016-04-05 20:23:09', '', ''),
(97, 'manay', 'harshika', '', 0, 0, 'posted', '0', '2016-04-05 20:23:20', '', ''),
(98, 'manay', 'mayank', '', 0, 0, 'posted', '1', '2016-04-05 20:23:20', '', ''),
(99, 'manay', 'harshika', '', 0, 0, 'posted an image', '0', '2016-04-05 20:23:46', '', ''),
(100, 'manay', 'mayank', '', 0, 0, 'posted an image', '1', '2016-04-05 20:23:46', '', ''),
(101, 'manay', 'harshika', '', 5, 0, 'posted', '0', '2016-04-05 20:25:13', '', ''),
(102, 'manay', 'mayank', '', 5, 0, 'posted', '1', '2016-04-05 20:25:13', '', ''),
(103, 'mayank', 'manay', '', 5, 2, 'commented', '1', '2016-04-05 21:01:13', '', ''),
(139, 'mayank', 'manay', '', 13, 0, 'posted an image', '1', '2016-04-06 15:23:28', '', ''),
(140, 'mayank', 'mahendra', '', 13, 0, 'posted an image', '0', '2016-04-06 15:23:28', '', ''),
(141, 'mayank', 'manisha', '', 13, 0, 'posted an image', '0', '2016-04-06 15:23:28', '', ''),
(142, 'mayank', 'satvik', '', 13, 0, 'posted an image', '0', '2016-04-06 15:23:28', '', ''),
(143, 'mayank', 'shivanshu', '', 13, 0, 'posted an image', '1', '2016-04-06 15:23:28', '', ''),
(144, 'supriya', 'mayank', '', 13, 5, 'commented', '1', '2016-04-06 15:26:58', '', ''),
(146, 'mayank', 'satvik', '', 0, 0, 'challenged', '0', '2016-05-11 17:23:42', '11', ''),
(147, 'mayank', 'shivanshu', '', 0, 0, 'challenged', '1', '2016-05-11 17:23:42', '11', ''),
(148, 'mayank', 'supriya', '', 0, 0, 'challenged', '0', '2016-05-11 17:23:42', '11', ''),
(149, 'manay', 'mayank', '', 0, 0, 'loss_wordrush', '1', '2016-05-11 17:26:01', '', ''),
(150, 'manay', 'manay', '', 0, 0, 'win_wordrush', '1', '2016-05-11 17:26:01', '', ''),
(152, 'mayank', 'satvik', '', 0, 0, 'challenged', '0', '2016-05-15 06:05:30', '1', ''),
(154, 'manay', 'mayank', '', 0, 0, 'win_wordrush', '1', '2016-05-15 06:06:57', '', ''),
(155, 'manay', 'manay', '', 0, 0, 'loss_wordrush', '1', '2016-05-15 06:06:57', '', ''),
(156, 'shivanshu', 'mayank', '', 0, 0, 'win_wordrush', '1', '2016-05-15 06:11:01', '', ''),
(157, 'shivanshu', 'shivanshu', '', 0, 0, 'win_wordrush', '1', '2016-05-15 06:11:01', '', ''),
(158, 'shivanshu', 'mayank', '', 13, 0, 'liked', '1', '2016-05-15 06:14:06', '', ''),
(161, 'shivanshu', 'mayank', '', 0, 0, 'loss_wordrush', '1', '2016-05-15 07:14:50', '', ''),
(162, 'shivanshu', 'shivanshu', '', 0, 0, 'loss_wordrush', '1', '2016-05-15 07:14:50', '', ''),
(163, 'manay', 'mayank', '', 0, 0, 'win_wordrush', '1', '2016-05-15 07:14:58', '', ''),
(164, 'manay', 'manay', '', 0, 0, 'win_wordrush', '1', '2016-05-15 07:14:58', '', ''),
(165, 'mayank', 'manay', '', 14, 0, 'posted', '1', '2016-10-02 11:54:39', '', ''),
(166, 'mayank', 'mahendra', '', 14, 0, 'posted', '0', '2016-10-02 11:54:39', '', ''),
(167, 'mayank', 'manisha', '', 14, 0, 'posted', '0', '2016-10-02 11:54:39', '', ''),
(168, 'mayank', 'satvik', '', 14, 0, 'posted', '0', '2016-10-02 11:54:39', '', ''),
(169, 'mayank', 'shivanshu', '', 14, 0, 'posted', '0', '2016-10-02 11:54:39', '', ''),
(170, 'mayank', 'pradeep', '', 14, 0, 'posted', '0', '2016-10-02 11:54:39', '', ''),
(171, 'mayank', 'supriya', '', 14, 0, 'posted', '0', '2016-10-02 11:54:39', '', ''),
(172, 'manay', 'harshika', '', 15, 0, 'posted an image', '0', '2016-10-02 11:59:27', '', ''),
(173, 'manay', 'mayank', '', 15, 0, 'posted an image', '1', '2016-10-02 11:59:27', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `id` int(11) NOT NULL,
  `user` varchar(16) NOT NULL,
  `gallery` varchar(16) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `uploaddate` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`id`, `user`, `gallery`, `filename`, `description`, `uploaddate`) VALUES
(1, 'manay', 'The Handsome Her', 'SatMar265144020168777.jpg', NULL, '2016-03-26 09:44:41'),
(2, 'manay', 'The Handsome Her', 'SatMar269361320161796.jpg', NULL, '2016-03-26 14:06:13'),
(3, 'mayank', 'Car', 'SatMay1420121320165378.jpg', NULL, '2016-05-14 23:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `post_id` int(8) NOT NULL,
  `poster` varchar(20) NOT NULL,
  `postto` varchar(20) NOT NULL,
  `post_time` datetime NOT NULL,
  `post` text NOT NULL,
  `post_image` varchar(100) NOT NULL,
  `type` varchar(1) NOT NULL,
  `likes` int(8) NOT NULL,
  `dislikes` int(8) NOT NULL,
  `likefriends` varchar(255) NOT NULL,
  `dislikefriends` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `poster`, `postto`, `post_time`, `post`, `post_image`, `type`, `likes`, `dislikes`, `likefriends`, `dislikefriends`) VALUES
(5, 'manay', '', '2016-04-05 20:25:13', 'posteddata', '', 'a', 0, 0, '', ''),
(13, 'mayank', '', '2016-04-06 15:23:27', 'Hiii Friends Check This Out What a beautiful scenary', '1072009750.jpg', 'a', 1, 0, 'shivanshu', ''),
(14, 'mayank', '', '2016-10-02 11:54:39', 'This Is The Check Only ', '', 'a', 0, 0, '', ''),
(15, 'manay', '', '2016-10-02 11:59:27', 'Check this out', '-198426755.jpg', 'a', 0, 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `posts_comment`
--

CREATE TABLE IF NOT EXISTS `posts_comment` (
  `comment_id` int(10) NOT NULL,
  `post_id` int(8) NOT NULL,
  `commenter` varchar(20) NOT NULL,
  `comment` text NOT NULL,
  `comment_time` datetime NOT NULL,
  `comment_like` int(10) NOT NULL,
  `comment_dislike` int(10) NOT NULL,
  `comment_like_friends` varchar(255) NOT NULL,
  `comment_dislike_friends` varchar(255) NOT NULL,
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `posts_comment`
--

INSERT INTO `posts_comment` (`comment_id`, `post_id`, `commenter`, `comment`, `comment_time`, `comment_like`, `comment_dislike`, `comment_like_friends`, `comment_dislike_friends`, `deleted`) VALUES
(1, 2, 'manay', 'Australia jeet gyi', '2016-03-26 07:54:24', 0, 0, '', '', '0'),
(2, 5, 'mayank', 'yess', '2016-04-05 21:01:13', 0, 0, '', '', '0'),
(3, 7, 'mayank', 'The project report is based on â€œSOCIAL NETWORKING SITEâ€, has been completed under the curriculum of centre for computer science Ewing Christian College, Allahabad.The project report is based on â€œSOCIAL NETWORKING SITEâ€, has been completed under the curriculum of centre for computer science Ewing Christian College, Allahabad.The project report is based on â€œSOCIAL NETWORKING SITEâ€, has been completed under the curriculum of centre for computer science Ewing Christian College, Allahabad.', '2016-04-05 21:25:56', 0, 0, '', '', '0'),
(4, 7, 'mayank', '<h2>Bombastic</h2>', '2016-04-06 07:42:58', 0, 0, '', '', '0'),
(5, 13, 'supriya', 'Ya Really Awesum', '2016-04-06 15:26:58', 0, 0, '', '', '0');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `status_user` varchar(16) NOT NULL,
  `statusdata` text NOT NULL,
  `statusdate` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `status_id`, `status_user`, `statusdata`, `statusdate`) VALUES
(1, 1, 'mayank', 'Hello Friends ', '2016-05-14 06:49:46'),
(2, 21, 'shruti', 'Hey!!! Welcome Me on FriendBuzz', '2016-04-06 15:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `f_name` varchar(100) NOT NULL,
  `l_name` varchar(100) NOT NULL,
  `u_name` varchar(100) NOT NULL,
  `u_email` varchar(100) NOT NULL,
  `u_pass` varchar(100) NOT NULL,
  `u_gen` enum('m','f') NOT NULL,
  `u_country` varchar(50) NOT NULL,
  `u_bir_date` date NOT NULL,
  `signup_date` date NOT NULL,
  `lastlogin_date` date NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `timeline` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `notescheck` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `f_name`, `l_name`, `u_name`, `u_email`, `u_pass`, `u_gen`, `u_country`, `u_bir_date`, `signup_date`, `lastlogin_date`, `avatar`, `timeline`, `ip`, `notescheck`) VALUES
(1, 'Mayank', 'Sharma', 'mayank', 'mayanksharma9454@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1995-11-01', '2015-11-09', '2016-10-02', '299512721.jpg', '333550742.jpg', '1', '2016-10-02 11:59:33'),
(2, 'Manay', 'Sharma', 'manay', 'manaysharma@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1997-09-16', '2015-11-09', '2016-10-02', '-697908023.jpg', '617181147.jpg', '1', '2016-10-02 11:57:23'),
(3, 'Satvik', 'Singh', 'satvik', 'stvkchauhan@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1995-08-15', '2015-11-09', '2016-03-31', '1191261422.jpg', '790276935.jpg', '1', '2016-03-31 08:35:33'),
(4, 'Shivanshu', 'Kulsherasth', 'shivanshu', 'shivanshu@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1994-07-18', '2015-11-09', '2016-05-15', '5267672.jpg', '', '1', '2016-05-15 07:14:51'),
(5, 'Supriya', 'Yadav', 'supriya', 'supriyayadav@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'f', 'India', '1995-06-15', '2015-11-09', '2016-04-06', '', '', '1', '0000-00-00 00:00:00'),
(6, 'Mahendra', 'Sharma', 'mahendra', 'mahendra@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1974-09-01', '2015-11-09', '2016-04-06', '-141202557.jpg', '-383916178.jpg', '1', '2016-04-05 13:34:21'),
(7, 'Manisha', 'Sharma', 'manisha', 'manisha@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1977-11-01', '2015-11-09', '2016-03-31', '', '', '1', '2016-03-31 08:36:41'),
(8, 'Sumit', 'Prakash', 'sumit', 'sumit@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1994-12-25', '2015-11-09', '2016-04-06', '', '', '1', '2015-11-09 21:58:25'),
(9, 'Ekta', 'Srivastava', 'ekta', 'ekta@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'f', 'India', '1995-10-05', '2015-11-09', '2016-04-06', '', '', '1', '2015-11-09 23:14:05'),
(10, 'Himanshu', 'Srivastava', 'himanshu', 'himanshu@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1994-07-15', '2015-11-09', '2016-04-06', '', '', '1', '0000-00-00 00:00:00'),
(11, 'Adarsh', 'Srivastava', 'adarsh', 'adarsh@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1994-10-29', '2015-11-09', '2016-04-06', '', '', '1', '0000-00-00 00:00:00'),
(12, 'Pradeep', 'Singh', 'pradeep', 'pradeep@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1994-12-05', '2015-11-10', '2016-04-06', '', '', '1', '0000-00-00 00:00:00'),
(13, 'Akash', 'Singh', 'akash', 'akash@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1994-06-10', '2015-11-10', '2016-04-06', '', '', '1', '2016-03-31 08:38:06'),
(14, 'Yash', 'Ranjan', 'yash', 'yash@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1995-06-21', '2015-11-10', '2016-04-06', '', '', '1', '0000-00-00 00:00:00'),
(15, 'Aditya', 'Sharma', 'aditya', 'aditya@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '2001-10-04', '2015-11-10', '2015-11-25', '41203282.jpg', '', '1', '0000-00-00 00:00:00'),
(16, 'Abhinav', 'Singh', 'abhinav', 'abhinav@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1995-07-27', '2015-11-11', '2016-02-04', '', '', '1', '0000-00-00 00:00:00'),
(17, 'Lalit', 'Sharma', 'lalit', 'lalit@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '2005-07-05', '2015-11-12', '2015-11-12', '288423679.jpg', '', '1', '0000-00-00 00:00:00'),
(18, 'Harshika', 'Rawat', 'harshika', 'harshikaprakash08@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'f', 'India', '0000-00-00', '2015-11-21', '2016-03-26', '881005457.jpg', '', '1', '0000-00-00 00:00:00'),
(19, 'Sourab', 'Singh', 'deep', 'souravsingh@gmail.com', '6627415e807ee33c7302917216e7da68', 'm', 'India', '2005-12-23', '2015-12-27', '2015-12-29', '1062996197.png', '', '1', '0000-00-00 00:00:00'),
(20, 'Mayank', 'Sharma', 'minku', 'mayanksharma@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '1995-11-01', '2016-03-23', '2016-03-23', '', '', '1', '2016-03-23 12:38:22'),
(21, 'Shruti', 'Srivastava', 'shruti', 'shruti@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'm', 'India', '0000-00-00', '2016-04-06', '2016-04-06', '', '', '1', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `useroptions`
--

CREATE TABLE IF NOT EXISTS `useroptions` (
  `id` int(11) NOT NULL,
  `username` varchar(16) NOT NULL,
  `background` varchar(255) NOT NULL,
  `fav_color` varchar(100) NOT NULL,
  `fav_film` varchar(100) NOT NULL,
  `best_friend` varchar(100) NOT NULL,
  `married_status` enum('single','married','in a relationship','') NOT NULL,
  `field_of_interest` varchar(80) NOT NULL,
  `fav_songs` varchar(80) NOT NULL,
  `theme` varchar(20) NOT NULL,
  `best_score` int(50) NOT NULL,
  `best_time` varchar(20) NOT NULL DEFAULT '99:99:99:999'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `useroptions`
--

INSERT INTO `useroptions` (`id`, `username`, `background`, `fav_color`, `fav_film`, `best_friend`, `married_status`, `field_of_interest`, `fav_songs`, `theme`, `best_score`, `best_time`) VALUES
(1, 'mayank', 'original', 'Blue', 'P.K', 'Sumit', 'single', 'Maths', 'Aaja nach le', 'orkut', 129, '00:00:08:551'),
(2, 'manay', 'original', 'Yellow', '3 Idiots', 'Harshika Prakash Rawat', 'in a relationship', 'Cooking', 'Agar Tum Saath ho', 'friendbuzz', 19, '00:00:50:030'),
(3, 'satvik', 'original', '', '', '', '', '', '', '', 33, '99:99:99:999'),
(4, 'shivanshu', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(5, 'supriya', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(6, 'mahendra', 'original', '', '', '', '', '', '', '', 6, '99:99:99:999'),
(7, 'manisha', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(8, 'sumit', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(9, 'ekta', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(10, 'himanshu', 'original', '', '', '', '', '', '', '', 1, '99:99:99:999'),
(11, 'adarsh', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(12, 'pradeep', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(13, 'akash', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(14, 'yash', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(15, 'aditya', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(16, 'abhinav', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(17, 'lalit', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(18, 'harshika', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(19, 'deep', 'original', '', '', '', '', '', '', '', 0, '99:99:99:999'),
(20, 'minku', 'original', '', '', '', 'single', '', '', '', 0, '99:99:99:999'),
(21, 'shruti', 'original', '', '', '', 'single', '', '', '', 0, '99:99:99:999');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blockedusers`
--
ALTER TABLE `blockedusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`);

--
-- Indexes for table `conversations_member`
--
ALTER TABLE `conversations_member`
  ADD UNIQUE KEY `conversation_id_2` (`conversation_id`,`user_id`),
  ADD KEY `conversation_id` (`conversation_id`,`user_id`);

--
-- Indexes for table `conversations_messages`
--
ALTER TABLE `conversations_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `posts_comment`
--
ALTER TABLE `posts_comment`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `useroptions`
--
ALTER TABLE `useroptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blockedusers`
--
ALTER TABLE `blockedusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` int(8) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `conversations_messages`
--
ALTER TABLE `conversations_messages`
  MODIFY `message_id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(8) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=174;
--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(8) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `posts_comment`
--
ALTER TABLE `posts_comment`
  MODIFY `comment_id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
