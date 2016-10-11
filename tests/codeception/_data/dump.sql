-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Час створення: Жов 11 2016 р., 10:10
-- Версія сервера: 5.6.17
-- Версія PHP: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База даних: `yii2advancedapp`
--

-- --------------------------------------------------------

--
-- Структура таблиці `migration`
--

CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
  ('m000000_000000_base', 1474041366),
  ('m130524_201442_init', 1474041374);

-- --------------------------------------------------------

--
-- Структура таблиці `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article` varchar(255) NOT NULL,
  `intro_text` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `author` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=82 ;

--
-- Дамп даних таблиці `news`
--

INSERT INTO `news` (`id`, `article`, `intro_text`, `description`, `author`, `image`, `created_at`, `updated_at`) VALUES
  (80, '2', '2', '2', '2', 'download_0.jpg', '2016-09-20 21:52:10', '2016-09-20 22:33:01'),
  (81, '3', '3', '3', '3', 'download_1.jpg', '2016-09-20 22:14:08', '2016-09-20 22:14:08');

-- --------------------------------------------------------

--
-- Структура таблиці `time_task`
--

CREATE TABLE IF NOT EXISTS `time_task` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=63 ;

--
-- Дамп даних таблиці `time_task`
--

INSERT INTO `time_task` (`id`, `begin`, `end`) VALUES
  (37, '2016-09-25 23:00:00', '2016-09-26 03:00:00'),
  (38, '2016-09-26 07:00:00', '2016-09-26 09:00:00'),
  (39, '2016-09-26 08:30:00', '2016-09-26 09:30:00'),
  (41, '2016-09-26 10:00:00', '2016-09-26 12:30:00'),
  (42, '2016-09-26 12:00:00', '2016-09-26 13:00:00'),
  (43, '2016-09-26 16:00:00', '2016-09-26 17:30:00'),
  (44, '2016-09-27 08:00:00', '2016-09-27 10:00:00'),
  (45, '2016-09-27 12:00:00', '2016-09-27 13:00:00'),
  (46, '2016-09-27 16:00:00', '2016-09-27 16:45:00'),
  (47, '2016-09-28 12:00:00', '2016-09-28 13:00:00'),
  (48, '2016-09-30 16:00:00', '2016-09-30 17:45:00'),
  (61, '2016-10-04 11:00:00', '2016-10-04 12:00:00');

-- --------------------------------------------------------

--
-- Структура таблиці `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп даних таблиці `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`) VALUES
  (1, 'Stas', 'eTS_2rvfqVyd2aOinXPEeGVEj04VCnfn', '$2y$13$1UVG7/J/eDvGggCURqc42uzaYnAPzGGnMA0BqomZ2tFPT4pTjKtRW', NULL, 'staskozar.91@gmail.com', 10, 1474041676, 1474041676);

-- --------------------------------------------------------

--
-- Структура таблиці `work_schedule`
--

CREATE TABLE IF NOT EXISTS `work_schedule` (
  `day` smallint(6) NOT NULL,
  `begin` int(11) NOT NULL,
  `end` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `work_schedule`
--

INSERT INTO `work_schedule` (`day`, `begin`, `end`) VALUES
  (1, 28800, 61200),
  (2, 28800, 61200),
  (4, 28800, 61200),
  (5, 28800, 61200);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
