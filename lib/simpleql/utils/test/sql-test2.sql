-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 06, 2016 at 06:15 AM
-- Server version: 5.5.50-0+deb8u1
-- PHP Version: 7.0.9-1~dotdeb+8.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `hacktheplanet`
--

-- --------------------------------------------------------

--
-- Table structure for table `game_jobs`
--

CREATE TABLE IF NOT EXISTS `game_jobs` (
`id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(300) NOT NULL,
  `created_at` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_at` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `game_requirements`
--

CREATE TABLE IF NOT EXISTS `game_requirements` (
`id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `value` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `game_requirement_types`
--

CREATE TABLE IF NOT EXISTS `game_requirement_types` (
`id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(300) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `game_rewards`
--

CREATE TABLE IF NOT EXISTS `game_rewards` (
`id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `value` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `game_reward_types`
--

CREATE TABLE IF NOT EXISTS `game_reward_types` (
`id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(300) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `modified_at` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `game_shop`
--

CREATE TABLE IF NOT EXISTS `game_shop` (
`id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(300) NOT NULL,
  `type` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `status` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `created_at` int(11) NOT NULL,
  `modified_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
`id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `game_jobs`
--
ALTER TABLE `game_jobs`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_requirements`
--
ALTER TABLE `game_requirements`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_requirement_types`
--
ALTER TABLE `game_requirement_types`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_rewards`
--
ALTER TABLE `game_rewards`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_reward_types`
--
ALTER TABLE `game_reward_types`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_shop`
--
ALTER TABLE `game_shop`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `game_jobs`
--
ALTER TABLE `game_jobs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `game_requirements`
--
ALTER TABLE `game_requirements`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `game_requirement_types`
--
ALTER TABLE `game_requirement_types`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `game_rewards`
--
ALTER TABLE `game_rewards`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `game_reward_types`
--
ALTER TABLE `game_reward_types`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `game_shop`
--
ALTER TABLE `game_shop`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_groups`
--
ALTER TABLE `user_groups`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
