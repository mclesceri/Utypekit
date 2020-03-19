-- Adminer 4.6.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1,	'2014_10_12_000000_create_users_table',	1),
(2,	'2014_10_12_100000_create_password_resets_table',	1);

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pdf_name` varchar(100) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `printer_liner` varchar(50) NOT NULL,
  `paper_stock` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `orders` (`id`, `pdf_name`, `tag`, `printer_liner`, `paper_stock`, `created_at`, `updated_at`) VALUES
(1,	'Test',	'TEst',	'Military',	'Civic',	'2018-06-25 05:53:47',	'2018-06-25 05:53:47'),
(2,	'Test',	'tttt',	'Church',	'State Agency',	'2018-06-25 06:13:02',	'2018-06-25 06:13:02'),
(3,	'Test',	'tttt',	'Church',	'State Agency',	'2018-06-25 06:14:08',	'2018-06-25 06:14:08');

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lname` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `org_type` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `org_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_1` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_2` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_title` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `username`, `name`, `lname`, `email`, `password`, `phone`, `org_type`, `org_name`, `address_1`, `address_2`, `city`, `state`, `zip`, `order_title`, `remember_token`, `created_at`, `updated_at`) VALUES
(2,	'',	'Michael Clesceri',	NULL,	'mike@crunchgrowth.com',	'$2y$10$NiWH6T0oUFSlJEar3KMYm.g4uciuPK5jbsfxDpRW9rdkwwKilWZJy',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'8lOsiHXWpBtxp9WbHChcZiBeDBwmr9kV4QgEWrkUALaO283q4ShZOYx01hxU',	'2018-06-13 18:56:33',	'2018-06-13 18:56:33');

-- 2018-06-25 11:46:37