-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 11, 2025 at 05:02 PM
-- Server version: 8.0.41-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `piesiniai`
--

-- --------------------------------------------------------

--
-- Table structure for table `komentaras`
--

CREATE TABLE `komentaras` (
  `id` int NOT NULL,
  `autorius` varchar(50) NOT NULL,
  `turinys` text NOT NULL,
  `sukurta` date NOT NULL,
  `fk_Paveikslasid` int NOT NULL,
  `fk_Vartotojasuid` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `konkursas`
--

CREATE TABLE `konkursas` (
  `id` int NOT NULL,
  `pavadinimas` varchar(150) NOT NULL,
  `aprasas` text NOT NULL,
  `pradzia` date NOT NULL,
  `pabaiga` date NOT NULL,
  `fk_Vartotojasuid` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `paveikslas`
--

CREATE TABLE `paveikslas` (
  `id` int NOT NULL,
  `pavadinimas` varchar(150) NOT NULL,
  `komentaras` text NOT NULL,
  `ikelimo_data` date NOT NULL,
  `failo_vieta` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `fk_Vartotojasuid` varchar(32) NOT NULL,
  `fk_Konkursasid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `vardas` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `vardas`) VALUES
(5, 'Naudotojas'),
(10, 'Vertintojas'),
(20, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `vartotojas`
--

CREATE TABLE `vartotojas` (
  `uid` varchar(32) NOT NULL,
  `slapyvardis` varchar(50) NOT NULL,
  `slaptazodis` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pilnas_vardas` varchar(100) NOT NULL,
  `gimtadienis` date NOT NULL,
  `sukurta` date NOT NULL,
  `role` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `vartotojas`
--

INSERT INTO `vartotojas` (`uid`, `slapyvardis`, `slaptazodis`, `pilnas_vardas`, `gimtadienis`, `sukurta`, `role`) VALUES
('3acd5dc5f40fa438dcfc59fe109a7224', 'admin', '6e5b5410415bde908bd4dee15dfb167a', 'admin', '0001-01-01', '2025-11-11', 20),
('5242fe8c2156a5a0fa8514a04d7a47e8', 'ignasas', '476be17fee68bc62580525b81daddd75', 'ignas pyr', '2004-02-04', '2025-11-11', 10);

-- --------------------------------------------------------

--
-- Table structure for table `vertinimas`
--

CREATE TABLE `vertinimas` (
  `id` int NOT NULL,
  `kompozicija` tinyint NOT NULL,
  `spalvingumas` tinyint NOT NULL,
  `temos_atitikimas` tinyint NOT NULL,
  `sukurta` date NOT NULL,
  `fk_Paveikslasid` int NOT NULL,
  `fk_Vartotojasuid` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `komentaras`
--
ALTER TABLE `komentaras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Paveikslasid` (`fk_Paveikslasid`),
  ADD KEY `fk_Vartotojasuid` (`fk_Vartotojasuid`);

--
-- Indexes for table `konkursas`
--
ALTER TABLE `konkursas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Vartotojasuid` (`fk_Vartotojasuid`);

--
-- Indexes for table `paveikslas`
--
ALTER TABLE `paveikslas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Konkursasid` (`fk_Konkursasid`),
  ADD KEY `fk_Vartotojasuid` (`fk_Vartotojasuid`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vartotojas`
--
ALTER TABLE `vartotojas`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `vertinimas`
--
ALTER TABLE `vertinimas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Paveikslasid` (`fk_Paveikslasid`),
  ADD KEY `fk_Vartotojasuid` (`fk_Vartotojasuid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `komentaras`
--
ALTER TABLE `komentaras`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `konkursas`
--
ALTER TABLE `konkursas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paveikslas`
--
ALTER TABLE `paveikslas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `vertinimas`
--
ALTER TABLE `vertinimas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `komentaras`
--
ALTER TABLE `komentaras`
  ADD CONSTRAINT `komentaras_ibfk_1` FOREIGN KEY (`fk_Paveikslasid`) REFERENCES `paveikslas` (`id`),
  ADD CONSTRAINT `komentaras_ibfk_2` FOREIGN KEY (`fk_Vartotojasuid`) REFERENCES `vartotojas` (`uid`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `konkursas`
--
ALTER TABLE `konkursas`
  ADD CONSTRAINT `konkursas_ibfk_1` FOREIGN KEY (`fk_Vartotojasuid`) REFERENCES `vartotojas` (`uid`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `paveikslas`
--
ALTER TABLE `paveikslas`
  ADD CONSTRAINT `paveikslas_ibfk_1` FOREIGN KEY (`fk_Konkursasid`) REFERENCES `konkursas` (`id`),
  ADD CONSTRAINT `paveikslas_ibfk_2` FOREIGN KEY (`fk_Vartotojasuid`) REFERENCES `vartotojas` (`uid`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `vertinimas`
--
ALTER TABLE `vertinimas`
  ADD CONSTRAINT `vertinimas_ibfk_1` FOREIGN KEY (`fk_Paveikslasid`) REFERENCES `paveikslas` (`id`),
  ADD CONSTRAINT `vertinimas_ibfk_2` FOREIGN KEY (`fk_Vartotojasuid`) REFERENCES `vartotojas` (`uid`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
