-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mar 17, 2018 alle 18:35
-- Versione del server: 10.1.25-MariaDB
-- Versione PHP: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
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
-- Procedure
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `vw_ListaConvenzioni` (IN `Utente` INT)  NO SQL
SELECT tbl_convenzioni.*, (SELECT sp_CalculateDistance(tbl_convenzioni.Lat, tbl_convenzioni.Lng, tbl_utenti.Lat, tbl_utenti.Lng) AS sp_CalculateDistance) AS Distanza 
FROM tbl_convenzioni, tbl_utenti 
WHERE IdUtente = Utente
ORDER BY Distanza ASC$$

--
-- Funzioni
--
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
-- Struttura della tabella `tbl_allegati`
--

CREATE TABLE `tbl_allegati` (
  `IdAllegato` int(11) NOT NULL,
  `NomeFile` varchar(250) NOT NULL,
  `IdConvenzione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_categorie`
--

CREATE TABLE `tbl_categorie` (
  `IdCategoria` int(11) NOT NULL,
  `Nome` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `tbl_categorie`
--

INSERT INTO `tbl_categorie` (`IdCategoria`, `Nome`) VALUES
(1, 'Libri'),
(2, 'Musica');

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_convenzioni`
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
-- Dump dei dati per la tabella `tbl_convenzioni`
--

INSERT INTO `tbl_convenzioni` (`IdConvenzione`, `Titolo`, `Descrizione`, `Luogo`, `Lat`, `Lng`, `DataCreazione`, `DataScadenza`, `IdCategoria`) VALUES
(1, 'Convenzione 1', '<h1 style=\'#f00\'>Lorem ipsum dolor sit amet, consectetur adipiscing elit</h1>\r\n<p>aghoshdsohdfohdfspihsdfpohp</p>', 'Torino', 1, 1, '2018-01-26', '0000-00-00', 1),
(2, 'Convenzione 2', '<h1 style=\'#f00\'>Lorem ipsum dolor sit amet, consectetur adipiscing elit</h1>\r\n<p>aghoshdsohdfohdfspihsdfpohp</p>', 'Via Roma 10, Torino', 45.0705, 7.68455, '2018-01-08', '2018-03-22', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_feedback`
--

CREATE TABLE `tbl_feedback` (
  `IdUtente` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL,
  `Voto` int(11) NOT NULL,
  `Commento` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `tbl_feedback`
--

INSERT INTO `tbl_feedback` (`IdUtente`, `IdConvenzione`, `Voto`, `Commento`) VALUES
(1, 2, 5, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_immagini`
--

CREATE TABLE `tbl_immagini` (
  `IdImmagine` int(11) NOT NULL,
  `NomeFile` varchar(250) NOT NULL,
  `Ordine` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `tbl_immagini`
--

INSERT INTO `tbl_immagini` (`IdImmagine`, `NomeFile`, `Ordine`, `IdConvenzione`) VALUES
(1, 'i1.jpg', 0, 1),
(2, 'i5.jpg', 0, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_log`
--

CREATE TABLE `tbl_log` (
  `IdUtente` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL,
  `Contatore` int(11) NOT NULL,
  `UltimaVisualizzazione` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `tbl_log`
--

INSERT INTO `tbl_log` (`IdUtente`, `IdConvenzione`, `Contatore`, `UltimaVisualizzazione`) VALUES
(1, 1, 3, '2018-03-13 10:14:09'),
(2, 2, 4, '2018-03-12 00:00:00');

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_utenti`
--

CREATE TABLE `tbl_utenti` (
  `IdUtente` int(11) NOT NULL,
  `Cognome` varchar(250) NOT NULL,
  `Nome` varchar(250) NOT NULL,
  `Email` varchar(250) NOT NULL,
  `Password` varchar(250) NOT NULL,
  `Lat` float NOT NULL,
  `Lng` float NOT NULL,
  `IsAmministratore` tinyint(1) NOT NULL,
  `Attivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `tbl_utenti`
--

INSERT INTO `tbl_utenti` (`IdUtente`, `Cognome`, `Nome`, `Email`, `Password`, `Lat`, `Lng`, `IsAmministratore`, `Attivo`) VALUES
(1, 'aa', 'aa', 'aa@gmail.com', '0cc175b9c0f1b6a831c399e269772661', 45.0349, 7.66687, 0, 1),
(2, 'bb', 'bb', 'bb@gmail.com', '92eb5ffee6ae2fec3ad71c777531578f', 2.2, 2.2, 0, 1),
(7, 'sdfff', 'sd', 'asff@gmail.com', '4b129f0db87cbbe2245e294a7ea6a233', 45.0674, 7.62637, 0, 1);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `tbl_allegati`
--
ALTER TABLE `tbl_allegati`
  ADD PRIMARY KEY (`IdAllegato`);

--
-- Indici per le tabelle `tbl_categorie`
--
ALTER TABLE `tbl_categorie`
  ADD PRIMARY KEY (`IdCategoria`);

--
-- Indici per le tabelle `tbl_convenzioni`
--
ALTER TABLE `tbl_convenzioni`
  ADD PRIMARY KEY (`IdConvenzione`);

--
-- Indici per le tabelle `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD PRIMARY KEY (`IdUtente`,`IdConvenzione`);

--
-- Indici per le tabelle `tbl_immagini`
--
ALTER TABLE `tbl_immagini`
  ADD PRIMARY KEY (`IdImmagine`);

--
-- Indici per le tabelle `tbl_log`
--
ALTER TABLE `tbl_log`
  ADD PRIMARY KEY (`IdUtente`,`IdConvenzione`);

--
-- Indici per le tabelle `tbl_utenti`
--
ALTER TABLE `tbl_utenti`
  ADD PRIMARY KEY (`IdUtente`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `tbl_allegati`
--
ALTER TABLE `tbl_allegati`
  MODIFY `IdAllegato` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `tbl_categorie`
--
ALTER TABLE `tbl_categorie`
  MODIFY `IdCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT per la tabella `tbl_convenzioni`
--
ALTER TABLE `tbl_convenzioni`
  MODIFY `IdConvenzione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT per la tabella `tbl_immagini`
--
ALTER TABLE `tbl_immagini`
  MODIFY `IdImmagine` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT per la tabella `tbl_utenti`
--
ALTER TABLE `tbl_utenti`
  MODIFY `IdUtente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
