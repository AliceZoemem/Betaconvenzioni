-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Creato il: Gen 25, 2018 alle 13:56
-- Versione del server: 5.6.26
-- Versione PHP: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_betaconvenzioni`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_allegati`
--

CREATE TABLE IF NOT EXISTS `tbl_allegati` (
  `IdAllegato` int(11) NOT NULL,
  `NomeFile` varchar(250) NOT NULL,
  `IdConvenzione` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `tbl_allegati`
--

INSERT INTO `tbl_allegati` (`IdAllegato`, `NomeFile`, `IdConvenzione`) VALUES
(1, '@900 - Copia.pdf', 1),
(2, '1984-it.pdf', 1),
(3, 'animalfarmit.pdf', 1),
(4, 'l-agnese-va-a-morire-8806174843.pdf', 1),
(5, 'BraveNewWorld.pdf', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_categorie`
--

CREATE TABLE IF NOT EXISTS `tbl_categorie` (
  `IdCategoria` int(11) NOT NULL,
  `Nome` varchar(250) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `tbl_categorie`
--

INSERT INTO `tbl_categorie` (`IdCategoria`, `Nome`) VALUES
(1, 'automobili'),
(2, 'sport');

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_convenzioni`
--

CREATE TABLE IF NOT EXISTS `tbl_convenzioni` (
  `IdConvenzione` int(11) NOT NULL,
  `Titolo` varchar(250) NOT NULL,
  `Descrizione` varchar(250) NOT NULL,
  `Posizione` varchar(250) NOT NULL,
  `DataCreazione` date NOT NULL,
  `DataScadenza` date NOT NULL,
  `IdCategoria` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `tbl_convenzioni`
--

INSERT INTO `tbl_convenzioni` (`IdConvenzione`, `Titolo`, `Descrizione`, `Posizione`, `DataCreazione`, `DataScadenza`, `IdCategoria`) VALUES
(1, 'conv1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure do', '45.067507|7.583635', '2018-01-25', '2018-04-26', 1),
(2, 'Convenzione2', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure do', '45.067507|7.583635', '2018-01-09', '2018-10-05', 1),
(3, 'conv3', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure do', '45.070242|7.585244', '2018-01-01', '2018-01-22', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_feedback`
--

CREATE TABLE IF NOT EXISTS `tbl_feedback` (
  `IdUtente` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL,
  `Voto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_immagini`
--

CREATE TABLE IF NOT EXISTS `tbl_immagini` (
  `IdImmagine` int(11) NOT NULL,
  `NomeFile` varchar(250) NOT NULL,
  `Ordine` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `tbl_immagini`
--

INSERT INTO `tbl_immagini` (`IdImmagine`, `NomeFile`, `Ordine`, `IdConvenzione`) VALUES
(1, 'i1.jpg', 1, 1),
(2, 'i2.jpg', 2, 1),
(3, 'i3.jpg', 3, 1),
(4, 'i4.jpg', 4, 1),
(5, 'i5.jpg', 1, 2),
(6, 'i6.jpg', 2, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_log`
--

CREATE TABLE IF NOT EXISTS `tbl_log` (
  `IdUtente` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL,
  `Contatore` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_utenti`
--

CREATE TABLE IF NOT EXISTS `tbl_utenti` (
  `IdUtente` int(11) NOT NULL,
  `Cognome` varchar(250) NOT NULL,
  `Nome` varchar(250) NOT NULL,
  `Email` varchar(250) NOT NULL,
  `Password` varchar(250) NOT NULL,
  `Posizione` varchar(250) NOT NULL,
  `IsAmminstratore` tinyint(1) NOT NULL,
  `Attivo` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `tbl_utenti`
--

INSERT INTO `tbl_utenti` (`IdUtente`, `Cognome`, `Nome`, `Email`, `Password`, `Posizione`, `IsAmminstratore`, `Attivo`) VALUES
(1, 'aa', 'aa', 'aa@gmail.com', '0cc175b9c0f1b6a831c399e269772661', '45.067628|7.585748', 0, 1),
(2, 'bb', 'bb', 'bb@gmail.com', '92eb5ffee6ae2fec3ad71c777531578f', '45.067628|7.585748', 0, 1);

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
  MODIFY `IdAllegato` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT per la tabella `tbl_categorie`
--
ALTER TABLE `tbl_categorie`
  MODIFY `IdCategoria` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT per la tabella `tbl_convenzioni`
--
ALTER TABLE `tbl_convenzioni`
  MODIFY `IdConvenzione` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT per la tabella `tbl_immagini`
--
ALTER TABLE `tbl_immagini`
  MODIFY `IdImmagine` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT per la tabella `tbl_utenti`
--
ALTER TABLE `tbl_utenti`
  MODIFY `IdUtente` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
