-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 27, 2025 at 09:09 PM
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
(11, 'naudotojas2', 'Gražu', '2025-11-27 22:26:57', 20, 'e2d71ad7ecb4e4e432a84362deb14522');

-- --------------------------------------------------------

--
-- Table structure for table `konkursas`
--

CREATE TABLE `konkursas` (
  `id` int NOT NULL,
  `pavadinimas` varchar(150) NOT NULL,
  `aprasas` text NOT NULL,
  `ikelimo_pradzia` datetime NOT NULL,
  `vertinimo_pradzia` datetime NOT NULL,
  `vertinimo_pabaiga` datetime NOT NULL,
  `fk_Vartotojasuid` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `konkursas`
--

INSERT INTO `konkursas` (`id`, `pavadinimas`, `aprasas`, `ikelimo_pradzia`, `vertinimo_pradzia`, `vertinimo_pabaiga`, `fk_Vartotojasuid`) VALUES
(20, 'Dar neprasidėjęs', 'Dar neprasidėjęs konkursas', '2025-12-03 12:00:00', '2025-12-10 12:00:00', '2025-12-17 12:00:00', '0b12e25057898f95e136fdda6baef296'),
(21, 'Įkėlime', 'Konkursas į kurį galima įkelti', '2025-11-23 12:10:00', '2025-12-06 06:00:00', '2025-12-13 12:34:00', '0b12e25057898f95e136fdda6baef296'),
(22, 'Vertinime', 'Konkursas kuriame nuotraukos yra vertinamos', '2025-11-16 10:10:00', '2025-11-23 12:12:00', '2025-12-07 13:13:00', '0b12e25057898f95e136fdda6baef296'),
(23, 'Pasibaigęs', 'Pasibaigęs konkursas', '2025-11-16 10:10:00', '2025-11-23 10:10:00', '2025-11-25 10:10:00', '0b12e25057898f95e136fdda6baef296'),
(25, 'Įkėlime 2', 'Antras konkursas įkėlime', '2025-11-23 12:00:00', '2025-11-29 12:00:00', '2025-12-06 12:00:00', '0b12e25057898f95e136fdda6baef296');

-- --------------------------------------------------------

--
-- Table structure for table `paveikslas`
--

CREATE TABLE `paveikslas` (
  `id` int NOT NULL,
  `pavadinimas` varchar(150) NOT NULL,
  `komentaras` text NOT NULL,
  `ikelimo_data` datetime NOT NULL,
  `failo_vieta` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `fk_Vartotojasuid` varchar(32) NOT NULL,
  `fk_Konkursasid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `paveikslas`
--

INSERT INTO `paveikslas` (`id`, `pavadinimas`, `komentaras`, `ikelimo_data`, `failo_vieta`, `fk_Vartotojasuid`, `fk_Konkursasid`) VALUES
(19, 'Gėlės', 'Gėlių puokštė', '2025-11-27 20:53:11', 'uploads/21/6c4746882bed6ffd1f3d.jpg', '52ab643e80b5caeb0e77fc2d882aab9e', 21),
(20, 'Laukas', 'Lauko takelis', '2025-11-27 20:53:25', 'uploads/21/c2ad2832e0dfb4efb3e7.png', '52ab643e80b5caeb0e77fc2d882aab9e', 21),
(21, 'Naktis', 'nakties piešinys', '2025-11-27 20:53:39', 'uploads/25/bc5860634453e57711be.jpg', '52ab643e80b5caeb0e77fc2d882aab9e', 25),
(23, 'Saulėgrąžos', 'Saulėgrąžų grąžą', '2025-11-27 20:55:07', 'uploads/25/d65ec2a155b76191f24c.jpg', '52ab643e80b5caeb0e77fc2d882aab9e', 25),
(24, 'Veidas', 'autoportretas', '2025-11-27 22:22:45', 'uploads/22/76684fcf8ed77f6a0262.png', 'e2d71ad7ecb4e4e432a84362deb14522', 22),
(25, 'Paveiksliukas', 'gitarprotetas', '2025-11-27 22:23:12', 'uploads/21/36ab29f217d9a3375980.jpg', 'e2d71ad7ecb4e4e432a84362deb14522', 21),
(26, 'Veidas', 'gitarportretas', '2025-11-27 22:23:46', 'uploads/22/29598c952e9cb4265270.jpg', 'e2d71ad7ecb4e4e432a84362deb14522', 22),
(27, 'Portretukas', 'mano draugas', '2025-11-27 22:25:00', 'uploads/23/d14ad8b9bf5c68dcf371.png', 'e2d71ad7ecb4e4e432a84362deb14522', 23),
(29, 'autoportretukas', 'aš', '2025-11-27 22:25:55', 'uploads/23/a39f667d3f3a0a3a277a.jpg', 'e2d71ad7ecb4e4e432a84362deb14522', 23),
(30, 'Gėlės', 'gėlėlėlėlės', '2025-11-27 22:27:45', 'uploads/22/bc6fd1d55fbd4dd7e185.jpg', '52ab643e80b5caeb0e77fc2d882aab9e', 22),
(31, 'Antros gėlės', 'ne tokios gražios', '2025-11-27 22:28:01', 'uploads/22/bd0b6df5f8b0f35d1f3a.jpg', '52ab643e80b5caeb0e77fc2d882aab9e', 22),
(32, 'Dangus', 'naktis', '2025-11-27 22:28:14', 'uploads/23/b34e1dbc32d89435556b.jpg', '52ab643e80b5caeb0e77fc2d882aab9e', 23),
(33, 'Laukas', 'takas', '2025-11-27 22:28:29', 'uploads/23/59fbd41546d767920c33.png', '52ab643e80b5caeb0e77fc2d882aab9e', 23),
(34, 'Gėlės', 'Gėlytės', '2025-11-27 22:34:19', 'uploads/23/944dca06410a9c5cefc9.jpg', '52ab643e80b5caeb0e77fc2d882aab9e', 23);

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
('0b12e25057898f95e136fdda6baef296', 'admin', '6e5b5410415bde908bd4dee15dfb167a', 'admin', '0001-01-01', '0000-01-01', 20),
('52ab643e80b5caeb0e77fc2d882aab9e', 'naudotojas', 'c2acd92812ef99acd3dcdbb746b9a434', 'stud', '2004-02-02', '2025-11-27', 5),
('6a67e334a2dc17299eda01fc263ba31a', 'vertintojas1', 'c2acd92812ef99acd3dcdbb746b9a434', 'Pirmas', '2003-04-04', '2025-11-27', 10),
('6f21f9e121de9f92158acc64e7ad3d83', 'vertintojas4', 'c2acd92812ef99acd3dcdbb746b9a434', 'ketvirtas', '2000-11-18', '2025-11-27', 10),
('a30160b83c4e0dfeaeae21c158257c7f', 'vertintojas5', 'c2acd92812ef99acd3dcdbb746b9a434', 'penktas', '1997-10-10', '2025-11-27', 10),
('e2d71ad7ecb4e4e432a84362deb14522', 'naudotojas2', 'c2acd92812ef99acd3dcdbb746b9a434', 'stud', '1987-04-11', '2025-11-27', 5),
('e6a8d8d5cfdae4a5b9d28427bd8d5f80', 'vertintojas3', 'c2acd92812ef99acd3dcdbb746b9a434', 'trečias', '1997-12-14', '2025-11-27', 10),
('fb55008c73cf25c3d1e0bc418e793d43', 'vertintojas2', 'c2acd92812ef99acd3dcdbb746b9a434', 'antras', '2005-12-05', '2025-11-27', 10);

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
(13, 5, 5, 5, '2025-11-27', 30, '6a67e334a2dc17299eda01fc263ba31a'),
(14, 5, 5, 5, '2025-11-27', 33, '6a67e334a2dc17299eda01fc263ba31a'),
(15, 10, 10, 10, '2025-11-27', 32, '6a67e334a2dc17299eda01fc263ba31a'),
(16, 5, 5, 5, '2025-11-27', 29, '6a67e334a2dc17299eda01fc263ba31a'),
(17, 1, 3, 5, '2025-11-27', 27, '6a67e334a2dc17299eda01fc263ba31a'),
(20, 5, 5, 5, '2025-11-27', 33, 'fb55008c73cf25c3d1e0bc418e793d43'),
(21, 10, 10, 10, '2025-11-27', 32, 'fb55008c73cf25c3d1e0bc418e793d43'),
(22, 5, 5, 5, '2025-11-27', 29, 'fb55008c73cf25c3d1e0bc418e793d43'),
(23, 1, 3, 5, '2025-11-27', 27, 'fb55008c73cf25c3d1e0bc418e793d43'),
(24, 5, 5, 5, '2025-11-27', 33, 'e6a8d8d5cfdae4a5b9d28427bd8d5f80'),
(25, 10, 10, 10, '2025-11-27', 32, 'e6a8d8d5cfdae4a5b9d28427bd8d5f80'),
(26, 5, 5, 5, '2025-11-27', 29, 'e6a8d8d5cfdae4a5b9d28427bd8d5f80'),
(27, 1, 3, 5, '2025-11-27', 27, 'e6a8d8d5cfdae4a5b9d28427bd8d5f80'),
(28, 10, 10, 10, '2025-11-27', 34, '6f21f9e121de9f92158acc64e7ad3d83'),
(29, 5, 5, 5, '2025-11-27', 33, '6f21f9e121de9f92158acc64e7ad3d83'),
(30, 10, 10, 10, '2025-11-27', 32, '6f21f9e121de9f92158acc64e7ad3d83'),
(31, 5, 5, 5, '2025-11-27', 29, '6f21f9e121de9f92158acc64e7ad3d83'),
(32, 6, 4, 8, '2025-11-27', 27, '6f21f9e121de9f92158acc64e7ad3d83'),
(33, 10, 10, 10, '2025-11-27', 34, 'a30160b83c4e0dfeaeae21c158257c7f'),
(34, 5, 5, 5, '2025-11-27', 33, 'a30160b83c4e0dfeaeae21c158257c7f'),
(35, 10, 10, 10, '2025-11-27', 32, 'a30160b83c4e0dfeaeae21c158257c7f'),
(36, 5, 5, 5, '2025-11-27', 29, 'a30160b83c4e0dfeaeae21c158257c7f'),
(37, 1, 1, 1, '2025-11-27', 27, 'a30160b83c4e0dfeaeae21c158257c7f');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `konkursas`
--
ALTER TABLE `konkursas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `paveikslas`
--
ALTER TABLE `paveikslas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `vertinimas`
--
ALTER TABLE `vertinimas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

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
