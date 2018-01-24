-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 24, 2018 alle 11:35
-- Versione del server: 10.1.29-MariaDB
-- Versione PHP: 7.1.12

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

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_convenzioni`
--

CREATE TABLE `tbl_convenzioni` (
  `IdConvenzione` int(11) NOT NULL,
  `Titolo` varchar(250) NOT NULL,
  `Descrizione` varchar(250) NOT NULL,
  `Posizione` varchar(250) NOT NULL,
  `DataCreazione` date NOT NULL,
  `DataScadenza` date NOT NULL,
  `IdCategoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_feedback`
--

CREATE TABLE `tbl_feedback` (
  `IdUtente` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL,
  `Voto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_log`
--

CREATE TABLE `tbl_log` (
  `IdUtente` int(11) NOT NULL,
  `IdConvenzione` int(11) NOT NULL,
  `Contatore` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `Posizione` varchar(250) NOT NULL,
  `IsAmminstratore` tinyint(1) NOT NULL,
  `Attivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  MODIFY `IdAllegato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tbl_categorie`
--
ALTER TABLE `tbl_categorie`
  MODIFY `IdCategoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tbl_convenzioni`
--
ALTER TABLE `tbl_convenzioni`
  MODIFY `IdConvenzione` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tbl_immagini`
--
ALTER TABLE `tbl_immagini`
  MODIFY `IdImmagine` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tbl_utenti`
--
ALTER TABLE `tbl_utenti`
  MODIFY `IdUtente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
