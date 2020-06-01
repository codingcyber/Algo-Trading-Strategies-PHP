-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 14, 2019 at 08:22 AM
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
-- Table structure for table `stock_daily_values`
--

CREATE TABLE `stock_monthly_values` (
  `id` int(11) NOT NULL,
  `stockid` int(11) NOT NULL,
  `price_open` varchar(255) NOT NULL,
  `price_high` varchar(255) NOT NULL,
  `price_low` varchar(255) NOT NULL,
  `price_close` varchar(255) NOT NULL,
  `volume` varchar(255) NOT NULL,
  `trade_date` date NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `stock_daily_values`
--
ALTER TABLE `stock_monthly_values`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `stock_daily_values`
--
ALTER TABLE `stock_monthly_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
