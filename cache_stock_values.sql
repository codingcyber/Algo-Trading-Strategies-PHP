-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 15, 2019 at 06:17 AM
-- Server version: 10.3.15-MariaDB
-- PHP Version: 7.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `stock-market`
--

-- --------------------------------------------------------

--
-- Table structure for table `stock_cache_values`
--

CREATE TABLE `stock_cache_values` (
  `id` int(11) NOT NULL,
  `stockid` int(11) NOT NULL,
  `days` varchar(255) NOT NULL,
  `startprice` varchar(255) NOT NULL,
  `startdate` varchar(255) NOT NULL,
  `currentprice` varchar(255) NOT NULL,
  `currentdate` varchar(255) NOT NULL,
  `atl_price` varchar(255) NOT NULL,
  `atl_date` varchar(255) NOT NULL,
  `ath_price` varchar(255) NOT NULL,
  `ath_date` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `stock_cache_values`
--
ALTER TABLE `stock_cache_values`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `stock_cache_values`
--
ALTER TABLE `stock_cache_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
