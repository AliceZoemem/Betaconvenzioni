-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2018 at 10:40 PM
-- Server version: 10.1.16-MariaDB
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_betaconvenzioni`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `vw_ListaConvenzioni` (IN `Utente` INT)  NO SQL
SELECT tbl_convenzioni.*, (SELECT sp_CalculateDistance(tbl_convenzioni.Lat, tbl_convenzioni.Lng, tbl_utenti.Lat, tbl_utenti.Lng) AS sp_CalculateDistance) AS Distanza 
FROM tbl_convenzioni, tbl_utenti 
WHERE IdUtente = Utente
ORDER BY Distanza ASC$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `sp_CalculateCouponScore` (`idCoupon` INT) RETURNS FLOAT NO SQL
    DETERMINISTIC
RETURN (SELECT SUM(Pt) FROM 
(
  (SELECT tbl_feedback.IdConvenzione, ((AVG(Voto) * 6)/5) AS Pt FROM tbl_feedback GROUP BY IdConvenzione) 
  UNION
  (SELECT IdConvenzione, (Contatore * 2.5/(select max(Contatore) from tbl_log)) as Pt FROM tbl_log)
  UNION 
  (SELECT tbl_convenzioni.IdConvenzione, AvG(Voto)*1.5/5 FROM tbl_feedback INNER JOIN tbl_convenzioni ON tbl_convenzioni.IdConvenzione = tbl_feedback.IdConvenzione INNER JOIN tbl_categorie ON tbl_categorie.IdCategoria = tbl_convenzioni.IdCategoria GROUP BY tbl_categorie.IdCategoria)
) 
AS FinalTable 
WHERE FinalTable.IdConvenzione = idCoupon
GROUP BY IdConvenzione)$$

CREATE DEFINER=`root`@`localhost` FUNCTION `sp_CalculateDistance` (`lat1` FLOAT, `lng1` FLOAT, `lat2` FLOAT, `lng2` FLOAT) RETURNS FLOAT NO SQL
    DETERMINISTIC
RETURN 6371 * 2 * ASIN(SQRT(
            POWER(SIN((lat1 - abs(lat2)) * pi()/180 / 2),
            2) + COS(lat1 * pi()/180 ) * COS(abs(lat2) *
            pi()/180) * POWER(SIN((lng1 - lng2) *
            pi()/180 / 2), 2) ))$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_allegati`
--

CREATE TABLE `tbl_allegati` (
  `IdAllegato` int(11) NOT NULL,
  `NomeFile` varchar(250) NOT NULL,
  `IdConvenzione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_categorie`
--

CREATE TABLE `tbl_categorie` (
  `IdCategoria` int(11) NOT NULL,
  `Nome` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_categorie`
--

INSERT INTO `tbl_categorie` (`IdCategoria`, `Nome`) VALUES
(1, 'Libri'),
(2, 'Musica');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_convenzioni`
--

CREATE TABLE `tbl_convenzioni` (
  `IdConvenzione` int(11) NOT NULL,
  `Titolo` varchar(250) NOT NULL,
  `Descrizione` longtext NOT NULL,
  `Luogo` varchar(250) NOT NULL,
  `Lat` float NOT NULL,
  `Lng` float NOT NULL,
  `DataCreazione` date NOT NULL,
  `DataScadenza` date NOT NULL,
  `IdCategoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_convenzioni`
--

INSERT INTO `tbl_convenzioni` (`IdConvenzione`, `Titolo`, `Descrizione`, `Luogo`, `Lat`, `Lng`, `DataCreazione`, `DataScadenza`, `IdCategoria`) VALUES
(1, 'Convenzione 1', '<h1>Lorem ipsum dolor sit amet, consectetur adipiscing elit</h1>\r\n<p>aghoshdsohdfohdfspihsdfpohpaaaaaa</p>', 'Torino', 45.1501, 7.12555, '2018-01-26', '0000-00-00', 1),
(2, 'Convenzione 2', '<h1 style=''#f00''>Lorem ipsum dolor sit amet, consectetur adipiscing elit</h1>\r\n<p>aghoshdsohdfohdfspihsdfpohp</p>', 'Via Roma 10, Torino', 45.0705, 7.68455, '2018-01-08', '2018-05-23', 2),
(146, 'Nuova convenzione', '<p><strong>Ayeeeee</strong></p>', '', 0, 0, '2018-04-09', '2018-04-25', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feedback`
--

CREATE TABLE `tbl_feedback` (
  `IdUtente` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL,
  `Voto` int(11) NOT NULL,
  `Commento` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_feedback`
--

INSERT INTO `tbl_feedback` (`IdUtente`, `IdConvenzione`, `Voto`, `Commento`) VALUES
(1, 1, 3, '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_immagini`
--

CREATE TABLE `tbl_immagini` (
  `IdImmagine` int(11) NOT NULL,
  `NomeFile` varchar(250) NOT NULL,
  `Ordine` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_immagini`
--

INSERT INTO `tbl_immagini` (`IdImmagine`, `NomeFile`, `Ordine`, `IdConvenzione`) VALUES
(1, 'i1.jpg', 0, 1),
(2, 'i5.jpg', 0, 2),
(20, 'phpD6DF.jpg', 0, 123),
(21, 'phpEF3F.jpg', 0, 145),
(22, 'phpEF50.jpg', 0, 145),
(23, 'php7B7B.png', 0, 146),
(24, 'php7B7C.png', 0, 146),
(25, 'php7B7D.png', 0, 146);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_indirizzi`
--

CREATE TABLE `tbl_indirizzi` (
  `IdIndirizzo` int(11) NOT NULL,
  `IdRegione` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL,
  `Luogo` text NOT NULL,
  `Lat` float NOT NULL,
  `Lng` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_indirizzi`
--

INSERT INTO `tbl_indirizzi` (`IdIndirizzo`, `IdRegione`, `IdConvenzione`, `Luogo`, `Lat`, `Lng`) VALUES
(1, 1, 1, '', 45.1501, 7.12555),
(2, 1, 2, '', 45.0705, 7.68455),
(11, 3, 143, 'Via torino', 37.3413, -121.981),
(12, 2, 144, 'Via nizza, milano', 45.5032, 9.18856),
(13, 1, 144, 'Via monte cimone 10 torino', 45.0684, 7.61888),
(14, 3, 144, '[ovunque]', 0, 0),
(15, 2, 145, 'Via roma, Milano', 45.4423, 9.09553),
(16, 1, 145, '[ovunque]', 0, 0),
(17, 3, 145, 'AAAAAAA', 36.1586, -115.234),
(18, 2, 146, 'Via Roma, Segrate, Milano, Italia', 45.4926, 9.29333),
(19, 1, 146, 'Via Nizza, Torino, TO, Italia', 45.0396, 7.67029),
(20, 3, 146, '[ovunque]', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_log`
--

CREATE TABLE `tbl_log` (
  `IdUtente` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL,
  `Contatore` int(11) NOT NULL,
  `UltimaVisualizzazione` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_log`
--

INSERT INTO `tbl_log` (`IdUtente`, `IdConvenzione`, `Contatore`, `UltimaVisualizzazione`) VALUES
(1, 1, 12, '2018-04-09 19:24:02'),
(1, 2, 1, '2018-04-09 19:24:13'),
(1, 97, 1, '2018-04-03 21:59:57'),
(1, 98, 1, '2018-04-03 22:02:22'),
(1, 99, 1, '2018-04-03 22:05:12'),
(1, 100, 1, '2018-04-03 22:07:35'),
(1, 102, 1, '2018-04-03 22:11:30'),
(1, 103, 1, '2018-04-03 22:13:51'),
(1, 104, 1, '2018-04-03 22:15:09'),
(1, 105, 1, '2018-04-03 22:16:49'),
(1, 120, 1, '2018-04-03 22:28:52'),
(1, 121, 1, '2018-04-03 22:29:57'),
(1, 123, 1, '2018-04-03 22:57:42'),
(1, 124, 1, '2018-04-03 22:57:25'),
(1, 143, 1, '2018-04-07 19:38:39'),
(1, 144, 1, '2018-04-09 19:17:21'),
(1, 145, 1, '2018-04-09 19:21:03'),
(1, 146, 1, '2018-04-09 22:19:40'),
(2, 2, 4, '2018-03-12 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_regioni`
--

CREATE TABLE `tbl_regioni` (
  `Id` int(11) NOT NULL,
  `Nome` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_regioni`
--

INSERT INTO `tbl_regioni` (`Id`, `Nome`) VALUES
(1, 'Piemonte'),
(2, 'Lombardia'),
(3, 'Veneto');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_utenti`
--

CREATE TABLE `tbl_utenti` (
  `IdUtente` int(11) NOT NULL,
  `Cognome` varchar(250) NOT NULL,
  `Nome` varchar(250) NOT NULL,
  `Email` varchar(250) NOT NULL,
  `Password` varchar(250) NOT NULL,
  `IdRegione` int(11) NOT NULL,
  `Lat` float NOT NULL,
  `Lng` float NOT NULL,
  `IsAmministratore` tinyint(1) NOT NULL,
  `Attivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_utenti`
--

INSERT INTO `tbl_utenti` (`IdUtente`, `Cognome`, `Nome`, `Email`, `Password`, `IdRegione`, `Lat`, `Lng`, `IsAmministratore`, `Attivo`) VALUES
(1, 'Admin', 'Stefany', 'aa@gmail.com', '0cc175b9c0f1b6a831c399e269772661', 1, 45.0684, 7.61888, 1, 1),
(2, 'bb', 'bb', 'bb@gmail.com', '92eb5ffee6ae2fec3ad71c777531578f', 2, 2.2, 2.2, 0, 1),
(7, 'sdfff', 'sd', 'asff@gmail.com', '4b129f0db87cbbe2245e294a7ea6a233', 3, 45.0674, 7.62637, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_allegati`
--
ALTER TABLE `tbl_allegati`
  ADD PRIMARY KEY (`IdAllegato`);

--
-- Indexes for table `tbl_categorie`
--
ALTER TABLE `tbl_categorie`
  ADD PRIMARY KEY (`IdCategoria`);

--
-- Indexes for table `tbl_convenzioni`
--
ALTER TABLE `tbl_convenzioni`
  ADD PRIMARY KEY (`IdConvenzione`);

--
-- Indexes for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD PRIMARY KEY (`IdUtente`,`IdConvenzione`);

--
-- Indexes for table `tbl_immagini`
--
ALTER TABLE `tbl_immagini`
  ADD PRIMARY KEY (`IdImmagine`);

--
-- Indexes for table `tbl_indirizzi`
--
ALTER TABLE `tbl_indirizzi`
  ADD PRIMARY KEY (`IdIndirizzo`);

--
-- Indexes for table `tbl_log`
--
ALTER TABLE `tbl_log`
  ADD PRIMARY KEY (`IdUtente`,`IdConvenzione`);

--
-- Indexes for table `tbl_regioni`
--
ALTER TABLE `tbl_regioni`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tbl_utenti`
--
ALTER TABLE `tbl_utenti`
  ADD PRIMARY KEY (`IdUtente`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_allegati`
--
ALTER TABLE `tbl_allegati`
  MODIFY `IdAllegato` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_categorie`
--
ALTER TABLE `tbl_categorie`
  MODIFY `IdCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tbl_convenzioni`
--
ALTER TABLE `tbl_convenzioni`
  MODIFY `IdConvenzione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;
--
-- AUTO_INCREMENT for table `tbl_immagini`
--
ALTER TABLE `tbl_immagini`
  MODIFY `IdImmagine` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `tbl_indirizzi`
--
ALTER TABLE `tbl_indirizzi`
  MODIFY `IdIndirizzo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `tbl_regioni`
--
ALTER TABLE `tbl_regioni`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_utenti`
--
ALTER TABLE `tbl_utenti`
  MODIFY `IdUtente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
