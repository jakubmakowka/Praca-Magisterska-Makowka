-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2025 at 07:21 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phplogin`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `database_count` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `password`, `email`, `database_count`, `active`) VALUES
(3, 'makowka', '$2y$10$bXkdD9j93e88yhtwarSB.OaaHUIR7c/wvHnlmbf94gDR7mtHN2uky', 'makowka@wp.pl', 2, 1),
(4, 'makowka1', '$2y$10$NiKAe0wJ7P7E0zdRB3uHkepUFQ4OZF/JywRPtePKMRCtzMJdQ8BCy', 'mak@wp.pl', 0, 1),
(5, 'jakub', '$2y$10$GGwvbvI0W2F.jIW9o1qCSOtuicvAjNopM7RO15M7lSH2tpB3a9tK6', 'jakub@wp.pl', 0, 1),
(6, 'testowanie', '$2y$10$tUZgPxHMOWllNxqEazi4Ye5myFaD5gRtLCeLMOFo1hhQenVHYDNoC', 'test@test.pl', 3, 1),
(7, 'activation', '$2y$10$7vH6JZKqwYzP8a9LoF29ROBBA9yIhPOV/3SIQJbLJ1dNgMqecg81u', 'activation@wp.pl', 0, 1);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
