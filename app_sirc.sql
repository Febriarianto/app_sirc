-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 16, 2023 at 04:41 AM
-- Server version: 8.1.0
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app_sirc`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_global` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `id_transaksi` bigint UNSIGNED NOT NULL,
  `over_time` bigint UNSIGNED NOT NULL,
  `biaya` bigint UNSIGNED NOT NULL,
  `sisa` bigint UNSIGNED NOT NULL,
  `metode_pelunasan` enum('cash','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_pelunasan` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `id_transaksi`, `over_time`, `biaya`, `sisa`, `metode_pelunasan`, `bukti_pelunasan`, `created_at`, `updated_at`) VALUES
(2, 4, 0, 100000, 0, 'cash', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jenis`
--

CREATE TABLE `jenis` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_12` decimal(9,0) NOT NULL,
  `harga_24` decimal(9,0) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jenis`
--

INSERT INTO `jenis` (`id`, `nama`, `harga_12`, `harga_24`, `created_at`, `updated_at`) VALUES
(1, 'All New Xenia', 300000, 350000, '2023-10-15 05:11:06', '2023-10-15 05:11:06'),
(2, 'All New Veloz', 350000, 400000, '2023-10-14 23:12:08', '2023-10-14 23:12:08'),
(4, 'NEW HONDA BRIO', 250000, 300000, '2023-10-19 06:27:26', '2023-10-19 06:27:26');

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id` bigint UNSIGNED NOT NULL,
  `id_pemilik` bigint UNSIGNED NOT NULL,
  `id_jenis` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_kendaraan` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warna` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id`, `id_pemilik`, `id_jenis`, `no_kendaraan`, `tahun`, `warna`, `foto`, `created_at`, `updated_at`) VALUES
(7, 2, '4', 'B1212UV', '2022', 'HITAM', 'sewa-mobil-brio-surabaya.jpg', '2023-10-19 06:29:38', '2023-11-01 23:09:03');

-- --------------------------------------------------------

--
-- Table structure for table `menu_managers`
--

CREATE TABLE `menu_managers` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_id` tinyint NOT NULL DEFAULT '0',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('module','header','line','static') COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_managers`
--

INSERT INTO `menu_managers` (`id`, `parent_id`, `title`, `slug`, `path_url`, `icon`, `type`, `position`, `sort`) VALUES
(1, 0, 'Dashboard', '', '/backend/dashboard', 'fas fa-tachometer-alt', 'module', NULL, 1),
(2, 0, 'Setting', NULL, NULL, 'fas fa-cogs', 'static', NULL, 5),
(8, 2, 'Users', 'users', '/backend/users', 'fas fa-users', 'module', NULL, 1),
(9, 2, 'Roles', 'roles', '/backend/roles', 'fas fa-user-tag', 'module', NULL, 2),
(10, 2, 'Menu Manager', 'menu-manager', '/backend/menu-manager', 'fas fa-bars', 'module', NULL, 3),
(11, 2, 'Web', 'setting', '/backend/setting', 'far fa-window-restore', 'module', NULL, 4),
(12, 0, 'Master Data', NULL, NULL, 'fas fa-database', 'static', NULL, 2),
(13, 12, 'Pemilik', 'pemilik', '/backend/pemilik', 'fas fa-user-check', 'module', NULL, 2),
(14, 12, 'Kendaraan', 'kendaraan', '/backend/kendaraan', 'fas fa-car', 'module', NULL, 3),
(15, 12, 'Penyewa', 'penyewa', '/backend/penyewa', 'fas fa-user-tag', 'module', NULL, 4),
(18, 0, 'Laporan', NULL, NULL, 'far fa-file-alt', 'static', NULL, 4),
(19, 12, 'Jenis', 'jenis', '/backend/jenis', 'fas fa-tags', 'module', NULL, 1),
(21, 0, 'Transaksi', NULL, NULL, 'fas fa-money-check-alt', 'static', NULL, 3),
(22, 21, 'Pemesanan', 'pemesanan', '/backend/pemesanan', 'fas fa-bookmark', 'module', NULL, 1),
(23, 21, 'Invoice', 'invoice', '/backend/invoice', 'fas fa-file-invoice-dollar', 'module', NULL, 3),
(24, 12, 'Referral', 'referral', '/backend/referral', 'fas fa-user-tie', 'module', NULL, 5),
(25, 18, 'Laporan Bulanan', 'laporan-bulanan', '/backend/laporan-bulanan', 'fas fa-file-alt', 'module', NULL, 2),
(26, 18, 'Laporan Referral', 'laporan-referral', '/backend/laporan-referral', 'fas fa-file-alt', 'module', NULL, 3),
(27, 21, 'Penyewaan', 'penyewaan', '/backend/penyewaan', 'fas fa-car-side', 'module', NULL, 2),
(28, 18, 'Laporan Harian', 'laporan-harian', '/backend/laporan-harian', 'fas fa-file-alt', 'module', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu_manager_role`
--

CREATE TABLE `menu_manager_role` (
  `id` bigint UNSIGNED NOT NULL,
  `menu_manager_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_manager_role`
--

INSERT INTO `menu_manager_role` (`id`, `menu_manager_id`, `role_id`) VALUES
(90, 1, 1),
(91, 19, 1),
(92, 13, 1),
(93, 14, 1),
(94, 15, 1),
(95, 24, 1),
(96, 22, 1),
(97, 27, 1),
(98, 23, 1),
(99, 28, 1),
(100, 25, 1),
(101, 26, 1),
(102, 8, 1),
(103, 9, 1),
(104, 10, 1),
(105, 11, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2013_08_19_080120_create_roles_table', 1),
(2, '2014_10_12_000000_create_users_table', 1),
(3, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2021_10_07_130202_create_menu_managers_table', 1),
(7, '2021_11_19_115145_create_permissions_table', 1),
(8, '2021_11_19_115611_create_permission_role_table', 1),
(9, '2022_04_01_070620_create_app_settings_table', 1),
(10, '2022_08_20_052305_create_menu_manager_role_table', 1),
(11, '2022_09_06_012850_create_sessions_table', 1),
(12, '2022_10_25_154523_create_setting_table', 1),
(13, '2023_09_26_061434_create_pages_table', 2),
(15, '2023_10_09_133642_create_pemilik_table', 3),
(16, '2023_10_09_133703_create_kendaraan_table', 3),
(17, '2023_10_09_133721_create_penyewa_table', 3),
(19, '2023_10_09_130807_create_jenis_table', 4),
(22, '2023_10_18_043721_create_referrals_table', 6),
(23, '2023_10_18_055838_add_referral_id_to_penyewa_table', 6),
(25, '2023_11_02_102500_create_tanggal_table', 8),
(26, '2023_10_09_133732_create_transaksi_table', 9),
(27, '2023_10_22_104047_create_invoices_table', 10),
(29, '2023_11_02_103405_create_range_transaksi_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_id` tinyint NOT NULL DEFAULT '0',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pemilik`
--

CREATE TABLE `pemilik` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pemilik`
--

INSERT INTO `pemilik` (`id`, `nama`, `alamat`, `no_hp`, `created_at`, `updated_at`) VALUES
(2, 'FEBRI ARIANTO', 'Pringsewu', '081300112012', '2023-10-12 15:18:42', '2023-10-12 15:18:42');

-- --------------------------------------------------------

--
-- Table structure for table `penyewa`
--

CREATE TABLE `penyewa` (
  `id` bigint UNSIGNED NOT NULL,
  `nik` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ktp` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kk` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `referral_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penyewa`
--

INSERT INTO `penyewa` (`id`, `nik`, `nama`, `no_hp`, `alamat`, `ktp`, `kk`, `foto`, `created_at`, `updated_at`, `referral_id`) VALUES
(2, '1810011702940002', 'Ananda Adam', '0812121111', 'Gading', 'images.jpeg', 'download.jpeg', NULL, '2023-10-15 11:55:20', '2023-11-12 09:39:09', 2);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `menu_manager_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `menu_manager_id`, `name`, `slug`) VALUES
(1, 1, 'Dashboard List', 'list'),
(2, 1, 'Dashboard Create', 'create'),
(3, 1, 'Dashboard Edit', 'edit'),
(4, 1, 'Dashboard Delete', 'delete'),
(5, 8, 'Users List', 'users-list'),
(6, 8, 'Users Create', 'users-create'),
(7, 8, 'Users Edit', 'users-edit'),
(8, 8, 'Users Delete', 'users-delete'),
(9, 9, 'Roles List', 'roles-list'),
(10, 9, 'Roles Create', 'roles-create'),
(11, 9, 'Roles Edit', 'roles-edit'),
(12, 9, 'Roles Delete', 'roles-delete'),
(13, 10, 'Menu Manager List', 'menu-manager-list'),
(14, 10, 'Menu Manager Create', 'menu-manager-create'),
(15, 10, 'Menu Manager Edit', 'menu-manager-edit'),
(16, 10, 'Menu Manager Delete', 'menu-manager-delete'),
(17, 11, 'Web List', 'setting-list'),
(18, 11, 'Web Create', 'setting-create'),
(19, 11, 'Web Edit', 'setting-edit'),
(20, 11, 'Web Delete', 'setting-delete'),
(21, 13, 'Pemilik List', 'pemilik-list'),
(22, 13, 'Pemilik Create', 'pemilik-create'),
(23, 13, 'Pemilik Edit', 'pemilik-edit'),
(24, 13, 'Pemilik Delete', 'pemilik-delete'),
(25, 14, 'Kendaraan List', 'kendaraan-list'),
(26, 14, 'Kendaraan Create', 'kendaraan-create'),
(27, 14, 'Kendaraan Edit', 'kendaraan-edit'),
(28, 14, 'Kendaraan Delete', 'kendaraan-delete'),
(29, 15, 'Penyewa List', 'penyewa-list'),
(30, 15, 'Penyewa Create', 'penyewa-create'),
(31, 15, 'Penyewa Edit', 'penyewa-edit'),
(32, 15, 'Penyewa Delete', 'penyewa-delete'),
(41, 19, 'Jenis List', 'jenis-list'),
(42, 19, 'Jenis Create', 'jenis-create'),
(43, 19, 'Jenis Edit', 'jenis-edit'),
(44, 19, 'Jenis Delete', 'jenis-delete'),
(45, 22, 'Pemesanan List', 'pemesanan-list'),
(46, 22, 'Pemesanan Create', 'pemesanan-create'),
(47, 22, 'Pemesanan Edit', 'pemesanan-edit'),
(48, 22, 'Pemesanan Delete', 'pemesanan-delete'),
(49, 23, 'Invoice List', 'invoice-list'),
(50, 23, 'Invoice Create', 'invoice-create'),
(51, 23, 'Invoice Edit', 'invoice-edit'),
(52, 23, 'Invoice Delete', 'invoice-delete'),
(53, 24, 'Referral List', 'referral-list'),
(54, 24, 'Referral Create', 'referral-create'),
(55, 24, 'Referral Edit', 'referral-edit'),
(56, 24, 'Referral Delete', 'referral-delete'),
(57, 25, 'Laporan Bulanan List', 'laporan-bulanan-list'),
(58, 25, 'Laporan Bulanan Create', 'laporan-bulanan-create'),
(59, 25, 'Laporan Bulanan Edit', 'laporan-bulanan-edit'),
(60, 25, 'Laporan Bulanan Delete', 'laporan-bulanan-delete'),
(61, 26, 'Laporan Referral List', 'laporan-referral-list'),
(62, 26, 'Laporan Referral Create', 'laporan-referral-create'),
(63, 26, 'Laporan Referral Edit', 'laporan-referral-edit'),
(64, 26, 'Laporan Referral Delete', 'laporan-referral-delete'),
(65, 27, 'Penyewaan List', 'penyewaan-list'),
(66, 27, 'Penyewaan Create', 'penyewaan-create'),
(67, 27, 'Penyewaan Edit', 'penyewaan-edit'),
(68, 27, 'Penyewaan Delete', 'penyewaan-delete'),
(69, 28, 'Laporan Harian List', 'laporan-harian-list'),
(70, 28, 'Laporan Harian Create', 'laporan-harian-create'),
(71, 28, 'Laporan Harian Edit', 'laporan-harian-edit'),
(72, 28, 'Laporan Harian Delete', 'laporan-harian-delete');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `id` bigint UNSIGNED NOT NULL,
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`id`, `permission_id`, `role_id`) VALUES
(357, 1, 1),
(358, 2, 1),
(359, 3, 1),
(360, 4, 1),
(361, 41, 1),
(362, 42, 1),
(363, 43, 1),
(364, 44, 1),
(365, 21, 1),
(366, 22, 1),
(367, 23, 1),
(368, 24, 1),
(369, 25, 1),
(370, 26, 1),
(371, 27, 1),
(372, 28, 1),
(373, 29, 1),
(374, 30, 1),
(375, 31, 1),
(376, 32, 1),
(377, 53, 1),
(378, 54, 1),
(379, 55, 1),
(380, 56, 1),
(381, 45, 1),
(382, 46, 1),
(383, 47, 1),
(384, 48, 1),
(385, 65, 1),
(386, 66, 1),
(387, 67, 1),
(388, 68, 1),
(389, 49, 1),
(390, 50, 1),
(391, 51, 1),
(392, 52, 1),
(393, 69, 1),
(394, 70, 1),
(395, 71, 1),
(396, 72, 1),
(397, 57, 1),
(398, 58, 1),
(399, 59, 1),
(400, 60, 1),
(401, 61, 1),
(402, 62, 1),
(403, 63, 1),
(404, 64, 1),
(405, 5, 1),
(406, 6, 1),
(407, 7, 1),
(408, 8, 1),
(409, 9, 1),
(410, 10, 1),
(411, 11, 1),
(412, 12, 1),
(413, 13, 1),
(414, 14, 1),
(415, 15, 1),
(416, 16, 1),
(417, 17, 1),
(418, 18, 1),
(419, 19, 1),
(420, 20, 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `range_transaksi`
--

CREATE TABLE `range_transaksi` (
  `id` bigint UNSIGNED NOT NULL,
  `id_transaksi` bigint UNSIGNED NOT NULL,
  `id_kendaraan` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `range_transaksi`
--

INSERT INTO `range_transaksi` (`id`, `id_transaksi`, `id_kendaraan`, `tanggal`) VALUES
(5, 4, 7, '2023-11-12'),
(6, 4, 7, '2023-11-13'),
(7, 4, 7, '2023-11-14');

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_rekening` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referrals`
--

INSERT INTO `referrals` (`id`, `nama`, `alamat`, `no_hp`, `no_rekening`, `created_at`, `updated_at`) VALUES
(1, '-', '-', '-', '-', '2023-10-18 23:31:19', '2023-10-18 23:33:11'),
(2, 'Feri Setiadi', 'Tataan', '081212', '92891201', '2023-10-18 23:52:09', '2023-10-18 23:52:09');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dashboard_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `dashboard_url`) VALUES
(1, 'Super Admin', 'super-admin', '/backend/dashboard');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` bigint UNSIGNED NOT NULL,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maps` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telp` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `logo`, `favicon`, `title`, `deskripsi`, `alamat`, `maps`, `telp`, `fax`, `email`, `facebook`, `instagram`, `youtube`, `created_at`, `updated_at`) VALUES
(1, '285855471-421646933152393-2313994858009917102-n-1698905753172.jpg', '285855471-421646933152393-2313994858009917102-n-16989057533220.jpg', 'Concept Autorent', '-', '-', '-', '-', '-', '-', '-', '-', '-', '2023-10-09 05:24:54', '2023-11-01 23:15:53');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` bigint UNSIGNED NOT NULL,
  `id_penyewa` bigint UNSIGNED NOT NULL,
  `kota_tujuan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_kendaraan` bigint UNSIGNED NOT NULL,
  `lama_sewa` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paket` enum('tahunan','bulanana','mingguan','harian') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keberangkatan` date NOT NULL,
  `kepulangan` date NOT NULL,
  `dp` bigint UNSIGNED NOT NULL,
  `metode_dp` enum('cash','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_dp` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe` enum('pemesanan','sewa') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('proses','selesai','batal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_penyewa`, `kota_tujuan`, `id_kendaraan`, `lama_sewa`, `paket`, `keberangkatan`, `kepulangan`, `dp`, `metode_dp`, `bukti_dp`, `tipe`, `status`, `created_at`, `updated_at`) VALUES
(4, 2, 't', 7, '2', NULL, '2023-11-12', '2023-11-14', 100000, 'cash', '', 'sewa', 'selesai', '2023-11-11 23:08:42', '2023-11-12 10:01:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `role_id`, `image`, `email_verified_at`, `password`, `active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin', 'admin@admin.com', 1, 'man-avatar-icon-flat-vector-19152370-16989058239768.jpg', '2023-10-09 05:24:54', '$2y$10$iHVbHCEbnfWtizoRqROE5u14SHwGfWKzQvTF.1kb4krsIhRwd8N..', 1, NULL, '2023-10-09 05:24:54', '2023-11-01 23:17:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_settings_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoices_id_transaksi_foreign` (`id_transaksi`);

--
-- Indexes for table `jenis`
--
ALTER TABLE `jenis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kendaraan_no_kendaraan_unique` (`no_kendaraan`);

--
-- Indexes for table `menu_managers`
--
ALTER TABLE `menu_managers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menu_managers_slug_unique` (`slug`);

--
-- Indexes for table `menu_manager_role`
--
ALTER TABLE `menu_manager_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_manager_role_menu_manager_id_foreign` (`menu_manager_id`),
  ADD KEY `menu_manager_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `pemilik`
--
ALTER TABLE `pemilik`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `penyewa_referral_id_foreign` (`referral_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`),
  ADD KEY `permissions_menu_manager_id_foreign` (`menu_manager_id`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permission_role_permission_id_foreign` (`permission_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `range_transaksi`
--
ALTER TABLE `range_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `range_transaksi_id_transaksi_foreign` (`id_transaksi`),
  ADD KEY `range_transaksi_id_kendaraan_foreign` (`id_kendaraan`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id_penyewa_foreign` (`id_penyewa`),
  ADD KEY `transaksi_id_kendaraan_foreign` (`id_kendaraan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jenis`
--
ALTER TABLE `jenis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `menu_managers`
--
ALTER TABLE `menu_managers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `menu_manager_role`
--
ALTER TABLE `menu_manager_role`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pemilik`
--
ALTER TABLE `pemilik`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penyewa`
--
ALTER TABLE `penyewa`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `permission_role`
--
ALTER TABLE `permission_role`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=421;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `range_transaksi`
--
ALTER TABLE `range_transaksi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD CONSTRAINT `app_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_id_transaksi_foreign` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menu_manager_role`
--
ALTER TABLE `menu_manager_role`
  ADD CONSTRAINT `menu_manager_role_menu_manager_id_foreign` FOREIGN KEY (`menu_manager_id`) REFERENCES `menu_managers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `menu_manager_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD CONSTRAINT `penyewa_referral_id_foreign` FOREIGN KEY (`referral_id`) REFERENCES `referrals` (`id`);

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_menu_manager_id_foreign` FOREIGN KEY (`menu_manager_id`) REFERENCES `menu_managers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `range_transaksi`
--
ALTER TABLE `range_transaksi`
  ADD CONSTRAINT `range_transaksi_id_kendaraan_foreign` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `range_transaksi_id_transaksi_foreign` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_id_kendaraan_foreign` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_id_penyewa_foreign` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
