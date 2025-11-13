-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 13, 2025 at 10:57 PM
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
  `sukurta` datetime NOT NULL,
  `fk_Paveikslasid` int NOT NULL,
  `fk_Vartotojasuid` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `komentaras`
--

INSERT INTO `komentaras` (`id`, `autorius`, `turinys`, `sukurta`, `fk_Paveikslasid`, `fk_Vartotojasuid`) VALUES
(9, 'naudotojas', 'Labai gražus portretas', '2025-11-13 23:25:31', 8, '57c4455bbe94c48718e58f8c02c96d40'),
(10, 'Anonimas', 'Puikus piešinys', '2025-11-14 00:21:01', 8, NULL);

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

--
-- Dumping data for table `konkursas`
--

INSERT INTO `konkursas` (`id`, `pavadinimas`, `aprasas`, `pradzia`, `pabaiga`, `fk_Vartotojasuid`) VALUES
(11, 'Lietuvos mokylų konkursas', 'Konkursas skirtas Lietuvos mokylų menui demonstruoti', '2025-10-01', '2025-10-31', '0b12e25057898f95e136fdda6baef296'),
(12, 'KTU konkursas', 'KTU studentų konkursas', '2025-11-01', '2025-11-30', '0b12e25057898f95e136fdda6baef296'),
(15, 'Konkursas', 'Konkursas ataskaitai', '2025-11-09', '2025-11-15', '0b12e25057898f95e136fdda6baef296');

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

--
-- Dumping data for table `paveikslas`
--

INSERT INTO `paveikslas` (`id`, `pavadinimas`, `komentaras`, `ikelimo_data`, `failo_vieta`, `fk_Vartotojasuid`, `fk_Konkursasid`) VALUES
(8, 'Gitara', 'Mano gitaros portretas', '2025-11-13', 'uploads/12/1e67a7a321b4f2949d9d.jpg', '0a772568c0eb8ffce02318472f6b6808', 12),
(9, 'Jaudulys', 'NAILS', '2025-11-13', 'uploads/12/1d394e392e9becf05dde.jpg', '0a772568c0eb8ffce02318472f6b6808', 12),
(10, 'Saulėgrąžos', 'saulės', '2025-11-13', 'uploads/12/3e21b36ca75ae70281d5.jpg', '57c4455bbe94c48718e58f8c02c96d40', 12),
(11, 'Naktis', 'naktis su žvaigždėmis', '2025-11-13', 'uploads/12/d262b8fde53fa9243b11.jpg', '57c4455bbe94c48718e58f8c02c96d40', 12),
(12, 'Laukas', 'takelis per laukelį', '2025-11-13', 'uploads/12/74ec7002e7a3aee283de.png', '57c4455bbe94c48718e58f8c02c96d40', 12),
(13, 'Gėlės', 'Gėlytės', '2025-11-13', 'uploads/12/dc162e96544a45e78896.jpg', '57c4455bbe94c48718e58f8c02c96d40', 12);

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
('048f80ef9a8609a386fd69701fb27bf1', 'vertintojas', 'c2acd92812ef99acd3dcdbb746b9a434', 'Vertintojas 1', '2000-04-04', '2025-11-13', 10),
('0a772568c0eb8ffce02318472f6b6808', 'test', '081884c7d659a2feaa0c55ad015a3bf4', 'test test', '2025-11-12', '2025-11-13', 5),
('0b12e25057898f95e136fdda6baef296', 'admin', '6e5b5410415bde908bd4dee15dfb167a', 'admin', '0001-01-01', '0000-01-01', 20),
('57c4455bbe94c48718e58f8c02c96d40', 'naudotojas', 'c2acd92812ef99acd3dcdbb746b9a434', 'Naudotojas Vienas', '1997-01-01', '2025-11-13', 5),
('aabf5431577b7006b97afb9aa1908aab', 'stud', 'c2acd92812ef99acd3dcdbb746b9a434', 'studentas studentas', '2004-01-01', '2025-11-14', 5),
('d863f630baa19b06d7a46ede3107a2a5', 'vertintojas2', 'c2acd92812ef99acd3dcdbb746b9a434', 'vertintojas du', '1977-05-18', '2025-11-13', 10);

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
-- Dumping data for table `vertinimas`
--

INSERT INTO `vertinimas` (`id`, `kompozicija`, `spalvingumas`, `temos_atitikimas`, `sukurta`, `fk_Paveikslasid`, `fk_Vartotojasuid`) VALUES
(5, 6, 10, 3, '2025-11-13', 8, '048f80ef9a8609a386fd69701fb27bf1'),
(6, 10, 6, 8, '2025-11-13', 9, '048f80ef9a8609a386fd69701fb27bf1'),
(7, 7, 3, 5, '2025-11-13', 8, 'd863f630baa19b06d7a46ede3107a2a5'),
(8, 10, 5, 5, '2025-11-13', 12, 'd863f630baa19b06d7a46ede3107a2a5'),
(9, 10, 9, 8, '2025-11-13', 11, 'd863f630baa19b06d7a46ede3107a2a5');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `komentaras`
--
ALTER TABLE `komentaras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `komentaras_ibfk_1` (`fk_Paveikslasid`),
  ADD KEY `komentaras_ibfk_2` (`fk_Vartotojasuid`);

--
-- Indexes for table `konkursas`
--
ALTER TABLE `konkursas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `konkursas_ibfk_1` (`fk_Vartotojasuid`);

--
-- Indexes for table `paveikslas`
--
ALTER TABLE `paveikslas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paveikslas_ibfk_1` (`fk_Konkursasid`),
  ADD KEY `paveikslas_ibfk_2` (`fk_Vartotojasuid`);

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
  ADD KEY `vertinimas_ibfk_1` (`fk_Paveikslasid`),
  ADD KEY `vertinimas_ibfk_2` (`fk_Vartotojasuid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `komentaras`
--
ALTER TABLE `komentaras`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `konkursas`
--
ALTER TABLE `konkursas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `paveikslas`
--
ALTER TABLE `paveikslas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `vertinimas`
--
ALTER TABLE `vertinimas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `komentaras`
--
ALTER TABLE `komentaras`
  ADD CONSTRAINT `komentaras_ibfk_1` FOREIGN KEY (`fk_Paveikslasid`) REFERENCES `paveikslas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `komentaras_ibfk_2` FOREIGN KEY (`fk_Vartotojasuid`) REFERENCES `vartotojas` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `konkursas`
--
ALTER TABLE `konkursas`
  ADD CONSTRAINT `konkursas_ibfk_1` FOREIGN KEY (`fk_Vartotojasuid`) REFERENCES `vartotojas` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `paveikslas`
--
ALTER TABLE `paveikslas`
  ADD CONSTRAINT `paveikslas_ibfk_1` FOREIGN KEY (`fk_Konkursasid`) REFERENCES `konkursas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `paveikslas_ibfk_2` FOREIGN KEY (`fk_Vartotojasuid`) REFERENCES `vartotojas` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vertinimas`
--
ALTER TABLE `vertinimas`
  ADD CONSTRAINT `vertinimas_ibfk_1` FOREIGN KEY (`fk_Paveikslasid`) REFERENCES `paveikslas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vertinimas_ibfk_2` FOREIGN KEY (`fk_Vartotojasuid`) REFERENCES `vartotojas` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
