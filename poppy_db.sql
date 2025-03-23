-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2025 at 10:28 PM
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
-- Database: `poppy_db`
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
  `active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `password`, `email`, `active`) VALUES
(3, 'Makowka', '$2y$10$bXkdD9j93e88yhtwarSB.OaaHUIR7c/wvHnlmbf94gDR7mtHN2uky', 'makowka@wp.pl', 1),
(11, 'Zuzanna', '$2y$10$p3im.YEWe3zVyfR5CzRJLOSL22xdGpYxwhx9TbU7aMil13PqiSQMS', 'zuz@wp.pl', 1),
(12, 'Paweł', '$2y$10$UCEZlK6prFkXsFg/ueLs7e2TGWUYBtzaUix4zIWf3lfiba7M6BMFq', 'test@test.pl', 1),
(13, 'Anna', '$2y$10$69nOxGoS41rIE0nrEmSyc.3XxIsTaGlZBCO15mQQWsZJX9FId8w46', 'zuzia@wp.pl', 1),
(15, 'test2', '$2y$10$QkiOTxM6/E5iSe7a5t39qOAjxZq2GpM3nK0MUPHsvldqBwHjqnfo2', 'test@wp.pl', 1),
(16, 'test3', '$2y$10$gpAo74RHv6eAwiYgJO2WmOTZ4.J/Q3.Izcel7gsPe44feqTDOQiLG', 'test3@wp.pl', 1),
(17, 'test4', '$2y$10$3ojSzK5BMwh/9.Wd3/6ylOBlHfvl/xlXbJdP.k/1YM7/IwXhvhQa6', 'test@wp.pl', 0),
(18, 'test45', '$2y$10$SnQdnLpc2Vgsq0Z90QFjM.PC0AzF/XdituEujsY1Ufz2bq4A5z9fC', 'makowka@wp.pl', 0),
(19, 'makowka2', '$2y$10$2A6l8hy7W2rrZ5ihi1F52uVfj0il8GKE4bGbOAXMpyWAijevBm8o2', 'makowka@onet.pl', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `campaigns`
--

CREATE TABLE `campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `goal_amount` bigint(20) NOT NULL,
  `current_amount` bigint(20) NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`id`, `name`, `goal_amount`, `current_amount`, `end_date`) VALUES
(1, 'Drużyna AMP Kraków', 5000, 5927, '2025-02-28'),
(2, 'Na wózku do pracy', 10000, 10500, '2025-02-28'),
(3, 'Pacjenci onkologiczni', 20000, 800, '2025-02-28'),
(4, 'Dom samotnej matki w Krakowie', 7000, 7000, '2025-02-28'),
(5, 'Dla najmłodszych podopiecznych', 12000, 12015, '2025-02-28'),
(6, 'Dom starców w Krakowie', 14500, 55, '2025-02-28');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `is_read`) VALUES
(1, 'Jakub Mak', 'jawdka@wp.pl', 'Test', 'Testowa wiadomość', '2025-03-23 19:51:30', 1),
(2, 'Zuzanna Test', 'zuza@wp.pl', 'Sprawdzam', 'Czy wiadomości trafiają do adresata?', '2025-03-23 20:00:38', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` enum('Opublikowano','W przygotowaniu') DEFAULT 'W przygotowaniu',
  `code` varchar(50) NOT NULL,
  `published_date` date NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `title`, `status`, `code`, `published_date`, `file_path`) VALUES
(1, 'Sprawozdanie - 2025', 'Opublikowano', 'HTWUITHYJK', '2025-02-20', '../license'),
(2, 'Sprawozdanie - 2024', 'Opublikowano', 'HNGBBJUKO', '2024-02-02', '../license'),
(3, 'Sprawozdanie - 2023', 'Opublikowano', 'FYEQPALGTF', '2023-01-17', '../license'),
(4, 'Sprawozdanie - 2022', 'Opublikowano', 'NMGFDORPI', '2022-02-18', '../license'),
(5, 'Sprawozdanie - 2021', 'Opublikowano', 'BZXCFRKIOF', '2021-01-10', '../license'),
(6, 'Sprawozdanie - 2020', 'Opublikowano', 'KJFURTDPGY', '2020-02-15', '../license'),
(7, 'Sprawozdanie - 2019', 'Opublikowano', 'HHWQTYGFP', '2019-01-25', '../license');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `statuses`
--

CREATE TABLE `statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id`, `category`, `description`) VALUES
(1, 'OK', 'Transakcja udana');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `timestamp` datetime NOT NULL,
  `amount` bigint(20) NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` int(11) NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL,
  `status_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `timestamp`, `amount`, `type_id`, `account_id`, `campaign_id`, `status_id`) VALUES
(41, '2025-03-18 20:40:30', 30, 34, 3, 3, 1),
(42, '2025-03-18 20:44:08', 50, 3, 3, 6, 1),
(43, '2025-03-18 21:40:05', 1, 3, 3, 6, 1),
(44, '2025-03-18 22:00:05', 50, 6, 3, 3, 1),
(45, '2025-02-27 07:00:00', 797, 28, 12, 4, 1),
(46, '2025-03-18 07:00:00', 751, 19, 12, 3, 1),
(47, '2025-03-04 14:00:00', 794, 30, 13, 5, 1),
(48, '2025-02-20 05:00:00', 775, 33, 11, 2, 1),
(49, '2025-03-04 16:00:00', 1468, 33, 13, 2, 1),
(50, '2025-02-26 17:00:00', 644, 39, 11, 5, 1),
(51, '2025-02-19 10:00:00', 576, 21, 11, 2, 1),
(52, '2025-03-10 17:00:00', 1087, 17, 13, 6, 1),
(53, '2025-03-12 10:00:00', 655, 33, 12, 6, 1),
(54, '2025-03-10 02:00:00', 721, 3, 13, 2, 1),
(55, '2025-03-11 22:00:00', 1294, 19, 12, 5, 1),
(56, '2025-03-19 17:00:00', 883, 31, 13, 1, 1),
(57, '2025-03-18 22:00:00', 675, 17, 12, 1, 1),
(58, '2025-03-03 12:00:00', 1242, 26, 12, 6, 1),
(59, '2025-02-24 08:00:00', 464, 18, 11, 1, 1),
(60, '2025-03-06 04:00:00', 703, 32, 11, 4, 1),
(61, '2025-03-16 14:00:00', 937, 13, 12, 3, 1),
(62, '2025-03-14 14:00:00', 708, 20, 13, 3, 1),
(63, '2025-03-04 02:00:00', 1441, 21, 12, 4, 1),
(64, '2025-02-26 03:00:00', 803, 8, 11, 6, 1),
(65, '2025-02-28 12:00:00', 1036, 36, 3, 2, 1),
(66, '2025-03-05 19:00:00', 1131, 10, 13, 6, 1),
(67, '2025-02-27 19:00:00', 1456, 18, 3, 4, 1),
(68, '2025-03-09 02:00:00', 694, 38, 3, 1, 1),
(69, '2025-03-17 03:00:00', 646, 27, 13, 5, 1),
(70, '2025-02-24 18:00:00', 929, 28, 11, 3, 1),
(71, '2025-03-10 13:00:00', 1370, 35, 11, 4, 1),
(72, '2025-02-22 09:00:00', 575, 28, 3, 1, 1),
(73, '2025-03-19 20:19:57', 150, 25, 3, 3, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `types`
--

CREATE TABLE `types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo_path` varchar(255) NOT NULL,
  `group` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`id`, `name`, `logo_path`, `group`) VALUES
(1, 'mTransfer', '_images/channel_1.png', 'bank-przelew'),
(2, 'Płacę z Inteligo', '_images/channel_2.png', 'bank-przelew'),
(3, 'Płacę z iPKO', '_images/channel_4.png', 'bank-przelew'),
(4, 'Przelew24', '_images/channel_6.png', 'bank-przelew'),
(5, 'Pekao24Przelew', '_images/channel_36.png', 'bank-przelew'),
(6, 'Płać z ING', '_images/channel_38.png', 'bank-przelew'),
(7, 'Millennium - Płatności', '_images/channel_44.png', 'bank-przelew'),
(8, 'Płacę z Alior Bankiem', '_images/channel_45.png', 'bank-przelew'),
(9, 'Płacę z Citi Handlowy', '_images/channel_46.png', 'bank-przelew'),
(10, 'Pay Way Toyota Bank', '_images/channel_50.png', 'bank-przelew'),
(11, 'Płać z BOŚ', '_images/channel_51.png', 'bank-przelew'),
(12, 'Bank Nowy BFG S.A.', '_images/channel_66.png', 'bank-przelew'),
(13, 'Pocztowy24', '_images/channel_70.png', 'bank-przelew'),
(14, 'Banki Spółdzielcze', '_images/channel_74.png', 'bank-przelew'),
(15, 'Płacę z Plus Bank', '_images/channel_75.png', 'bank-przelew'),
(16, 'Getin Bank PBL', '_images/channel_76.png', 'bank-przelew'),
(17, 'EnveloBank', '_images/channel_83.png', 'bank-przelew'),
(18, 'Credit Agricole PBL', '_images/channel_87.png', 'bank-przelew'),
(19, 'MasterPass', '_images/channel_71.png', 'karty'),
(20, 'Karty płatnicze', '_images/channel_246.png', 'karty'),
(21, 'Karty płatnicze (PLN, EUR, USD, GBP)', '_images/channel_248.png', 'karty'),
(22, 'Visa SRC', '_images/channel_249.png', 'karty'),
(23, 'Google Pay', '_images/channel_260.png', 'karty'),
(24, 'Apple Pay', '_images/channel_262.png', 'karty'),
(25, 'Przelew/Przekaz', '_images/channel_11.png', 'gotowka'),
(26, 'Przelew SEPA', '_images/channel_82.png', 'gotowka'),
(27, 'SkyCash', '_images/channel_52.png', 'portfele'),
(28, 'CinkciarzPAY', '_images/channel_59.png', 'portfele'),
(29, 'paysafecard', '_images/channel_218.png', 'portfele'),
(30, 'Raty z Alior Bankiem', '_images/channel_55.png', 'raty'),
(31, 'mRaty', '_images/channel_68.png', 'raty'),
(32, 'Kupuj teraz zapłać później', '_images/channel_94.png', 'odroczone'),
(33, 'PayPo', '_images/channel_95.png', 'odroczone'),
(34, 'BLIK', '_images/channel_73.png', 'mobilne'),
(35, 'Orange', '_images/channel_231.png', 'mobilne'),
(36, 'T-Mobile', '_images/channel_232.png', 'mobilne'),
(37, 'PLAY', '_images/channel_233.png', 'mobilne'),
(38, 'Plus', '_images/channel_234.png', 'mobilne'),
(39, 'Noble Pay', '_images/channel_80.png', 'inne'),
(40, 'Idea Cloud', '_images/channel_81.png', 'inne'),
(41, 'TrustPay', '_images/channel_86.png', 'inne'),
(42, 'Bitcoin', '_images/channel_300.png', 'inne');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indeksy dla tabeli `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `campaign_id` (`campaign_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indeksy dla tabeli `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `types`
--
ALTER TABLE `types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_4` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
