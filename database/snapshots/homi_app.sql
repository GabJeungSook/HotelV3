/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `activity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `assigned_frontdesks`;
CREATE TABLE `assigned_frontdesks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `cash_drawer_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `frontdesk_id` bigint unsigned NOT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `branches`;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autorization_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_autorization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension_time_reset` int DEFAULT NULL,
  `initial_deposit` decimal(10,2) NOT NULL DEFAULT '200.00',
  `discount_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '50.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cash_drawers`;
CREATE TABLE `cash_drawers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_drawers_branch_id_foreign` (`branch_id`),
  CONSTRAINT `cash_drawers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cash_on_drawers`;
CREATE TABLE `cash_on_drawers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `frontdesk_id` bigint unsigned NOT NULL,
  `cash_drawer_id` bigint unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `deduction` decimal(15,2) NOT NULL DEFAULT '0.00',
  `transaction_date` date NOT NULL,
  `transaction_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `check_out_guest_reports`;
CREATE TABLE `check_out_guest_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `room_id` bigint unsigned NOT NULL,
  `checkin_details_id` bigint unsigned NOT NULL,
  `shift_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frontdesk_id` int NOT NULL,
  `partner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `checkin_details`;
CREATE TABLE `checkin_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` bigint unsigned NOT NULL,
  `frontdesk_id` int DEFAULT NULL,
  `type_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `rate_id` bigint unsigned NOT NULL,
  `static_room_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `static_amount` int NOT NULL,
  `hours_stayed` int NOT NULL,
  `total_deposit` int DEFAULT NULL,
  `total_deduction` int NOT NULL DEFAULT '0',
  `check_in_at` datetime NOT NULL,
  `check_out_at` datetime NOT NULL,
  `is_check_out` tinyint(1) NOT NULL DEFAULT '0',
  `is_long_stay` tinyint(1) NOT NULL,
  `number_of_hours` int NOT NULL DEFAULT '0',
  `next_extension_is_original` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cleaning_histories`;
CREATE TABLE `cleaning_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `floor_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `start_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `current_assigned_floor_id` tinyint(1) NOT NULL,
  `expected_end_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cleaning_duration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delayed_cleaning` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `discounts`;
CREATE TABLE `discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int NOT NULL,
  `is_percentage` tinyint(1) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `expense_categories`;
CREATE TABLE `expense_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_category_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_user_id_foreign` (`user_id`),
  CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `extended_guest_reports`;
CREATE TABLE `extended_guest_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `checkin_details_id` bigint unsigned NOT NULL,
  `number_of_extension` int NOT NULL,
  `total_hours` int NOT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frontdesk_id` int NOT NULL,
  `partner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `extension_rates`;
CREATE TABLE `extension_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `hour` int NOT NULL,
  `amount` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `floor_user`;
CREATE TABLE `floor_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `floor_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `floor_user_user_id_foreign` (`user_id`),
  KEY `floor_user_floor_id_foreign` (`floor_id`),
  CONSTRAINT `floor_user_floor_id_foreign` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `floor_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `floors`;
CREATE TABLE `floors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `number` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `frontdesk_categories`;
CREATE TABLE `frontdesk_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `frontdesk_inventories`;
CREATE TABLE `frontdesk_inventories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `frontdesk_menu_id` bigint unsigned NOT NULL,
  `number_of_serving` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `frontdesk_menus`;
CREATE TABLE `frontdesk_menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `frontdesk_category_id` bigint unsigned NOT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `frontdesks`;
CREATE TABLE `frontdesks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `guests`;
CREATE TABLE `guests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `previous_room_id` bigint unsigned DEFAULT NULL,
  `rate_id` bigint unsigned NOT NULL,
  `type_id` bigint unsigned NOT NULL,
  `static_amount` int NOT NULL,
  `is_long_stay` tinyint(1) NOT NULL DEFAULT '0',
  `number_of_days` int DEFAULT NULL,
  `has_discount` tinyint(1) NOT NULL DEFAULT '0',
  `discount_amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_kiosk_check_out` tinyint(1) NOT NULL DEFAULT '0',
  `is_co` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `hotel_items`;
CREATE TABLE `hotel_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `inventories`;
CREATE TABLE `inventories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `menu_id` bigint unsigned NOT NULL,
  `number_of_serving` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `menu_categories`;
CREATE TABLE `menu_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `menu_category_id` bigint unsigned NOT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `new_guest_reports`;
CREATE TABLE `new_guest_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `checkin_details_id` bigint unsigned NOT NULL,
  `shift_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frontdesk_id` int NOT NULL,
  `partner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pub_categories`;
CREATE TABLE `pub_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pub_inventories`;
CREATE TABLE `pub_inventories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `pub_menu_id` bigint unsigned NOT NULL,
  `number_of_serving` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pub_menus`;
CREATE TABLE `pub_menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `pub_category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `rates`;
CREATE TABLE `rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `staying_hour_id` bigint unsigned NOT NULL,
  `type_id` bigint unsigned NOT NULL,
  `amount` int NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `has_discount` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `requestable_items`;
CREATE TABLE `requestable_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `room_boy_reports`;
CREATE TABLE `room_boy_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT '1',
  `room_id` bigint unsigned NOT NULL,
  `checkin_details_id` bigint unsigned NOT NULL,
  `roomboy_id` bigint unsigned NOT NULL,
  `cleaning_start` datetime NOT NULL,
  `cleaning_end` datetime NOT NULL,
  `total_hours_spent` int NOT NULL,
  `interval` int NOT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_cleaned` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `floor_id` bigint unsigned NOT NULL,
  `number` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Main',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `type_id` bigint unsigned NOT NULL,
  `is_priority` tinyint(1) NOT NULL DEFAULT '0',
  `last_checkin_at` datetime DEFAULT NULL,
  `last_checkout_at` datetime DEFAULT NULL,
  `time_to_terminate_queue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `time_to_clean` datetime DEFAULT NULL,
  `started_cleaning_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `shift_logs`;
CREATE TABLE `shift_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `frontdesk_id` bigint unsigned DEFAULT NULL,
  `cash_drawer_id` bigint unsigned DEFAULT NULL,
  `time_in` datetime NOT NULL,
  `time_out` datetime DEFAULT NULL,
  `frontdesk_ids` json NOT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `stay_extensions`;
CREATE TABLE `stay_extensions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` bigint unsigned NOT NULL,
  `extension_id` bigint unsigned NOT NULL,
  `hours` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frontdesk_ids` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `staying_hours`;
CREATE TABLE `staying_hours` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `number` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `temporary_check_in_kiosks`;
CREATE TABLE `temporary_check_in_kiosks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `guest_id` bigint unsigned NOT NULL,
  `terminated_at` datetime NOT NULL,
  `is_opened` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `temporary_reserveds`;
CREATE TABLE `temporary_reserveds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `guest_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `transaction_types`;
CREATE TABLE `transaction_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `cash_drawer_id` bigint unsigned DEFAULT NULL,
  `room_id` bigint unsigned NOT NULL,
  `guest_id` bigint unsigned NOT NULL,
  `floor_id` bigint unsigned NOT NULL,
  `transaction_type_id` bigint unsigned NOT NULL,
  `assigned_frontdesk_id` json NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payable_amount` int NOT NULL DEFAULT '0',
  `paid_amount` int NOT NULL DEFAULT '0',
  `change_amount` int NOT NULL DEFAULT '0',
  `deposit_amount` int NOT NULL DEFAULT '0',
  `paid_at` datetime DEFAULT NULL,
  `override_at` datetime DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `transfer_reason_id` bigint unsigned DEFAULT NULL,
  `is_co` tinyint(1) NOT NULL DEFAULT '0',
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_override` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `transfer_reasons`;
CREATE TABLE `transfer_reasons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_reasons_branch_id_foreign` (`branch_id`),
  CONSTRAINT `transfer_reasons_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `types`;
CREATE TABLE `types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `unoccupied_room_reports`;
CREATE TABLE `unoccupied_room_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rooms` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `frontdesk_id` int NOT NULL,
  `partner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `cash_drawer_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `branch_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint unsigned DEFAULT NULL,
  `roomboy_assigned_floor_id` bigint unsigned DEFAULT NULL,
  `roomboy_cleaning_room_id` bigint unsigned DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_frontdesks` json DEFAULT NULL,
  `time_in` datetime DEFAULT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `activity_logs` (`id`, `branch_id`, `user_id`, `activity`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Update Extension Rate', 'Updated extension rate for 6 hour(s)', '2026-02-11 09:21:22', '2026-02-11 09:21:22'),
(2, 1, 2, 'Update Extension Rate', 'Updated extension rate for 12 hour(s)', '2026-02-11 09:21:28', '2026-02-11 09:21:28'),
(3, 1, 2, 'Update Extension Rate', 'Updated extension rate for 18 hour(s)', '2026-02-11 09:21:34', '2026-02-11 09:21:34'),
(4, 1, 2, 'Update Extension Rate', 'Updated extension rate for 24 hour(s)', '2026-02-11 09:21:39', '2026-02-11 09:21:39'),
(5, 1, 3, 'Check In from Kiosk', 'Checked in guest test 1 from kiosk', '2026-02-11 17:48:06', '2026-02-11 17:48:06'),
(6, 1, 3, 'Add Amenities', 'Added new amenities of ₱100 for guest test 1', '2026-02-11 17:50:49', '2026-02-11 17:50:49'),
(7, 1, 3, 'Add Damage Charges', 'Added new damage charges of ₱40 for guest test 1', '2026-02-11 17:53:13', '2026-02-11 17:53:13'),
(8, 1, 3, 'Check In from Kiosk', 'Checked in guest test 2 from kiosk', '2026-02-11 21:23:35', '2026-02-11 21:23:35');

INSERT INTO `branches` (`id`, `name`, `address`, `autorization_code`, `old_autorization`, `extension_time_reset`, `initial_deposit`, `discount_enabled`, `discount_amount`, `created_at`, `updated_at`) VALUES
(1, 'ALMA RESIDENCES GENSAN', 'Brgy. 1, Gensan, South Cotabato', '12345', NULL, 24, '200.00', 1, '50.00', '2026-02-11 09:20:29', '2026-02-11 09:20:29');
INSERT INTO `cash_drawers` (`id`, `branch_id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Drawer 1', 1, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(2, 1, 'Drawer 2', 1, '2026-02-11 09:20:30', '2026-02-11 09:20:30');
INSERT INTO `cash_on_drawers` (`id`, `branch_id`, `frontdesk_id`, `cash_drawer_id`, `amount`, `deduction`, `transaction_date`, `transaction_type`, `shift`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '224.00', '0.00', '2026-02-11', 'check-in', 'AM', '2026-02-11 17:48:06', '2026-02-11 17:48:06'),
(2, 1, 1, 1, '200.00', '0.00', '2026-02-11', 'deposit', 'AM', '2026-02-11 17:48:06', '2026-02-11 17:48:06'),
(3, 1, 1, 1, '224.00', '0.00', '2026-02-11', 'check-in', 'PM', '2026-02-11 21:23:35', '2026-02-11 21:23:35'),
(4, 1, 1, 1, '200.00', '0.00', '2026-02-11', 'deposit', 'PM', '2026-02-11 21:23:35', '2026-02-11 21:23:35');

INSERT INTO `checkin_details` (`id`, `guest_id`, `frontdesk_id`, `type_id`, `room_id`, `rate_id`, `static_room_amount`, `static_amount`, `hours_stayed`, `total_deposit`, `total_deduction`, `check_in_at`, `check_out_at`, `is_check_out`, `is_long_stay`, `number_of_hours`, `next_extension_is_original`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 88, 1, '224.00', 424, 6, 200, 0, '2026-02-11 17:48:06', '2026-02-11 23:48:06', 0, 0, 6, 0, '2026-02-11 17:48:06', '2026-02-11 17:48:06'),
(2, 2, 1, 1, 89, 1, '224.00', 424, 6, 200, 0, '2026-02-11 21:23:35', '2026-02-12 03:23:35', 0, 0, 6, 0, '2026-02-11 21:23:35', '2026-02-11 21:23:35');





INSERT INTO `extension_rates` (`id`, `branch_id`, `hour`, `amount`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 112, '2026-02-11 09:20:31', '2026-02-11 09:21:22'),
(2, 1, 12, 224, '2026-02-11 09:20:31', '2026-02-11 09:21:28'),
(3, 1, 18, 336, '2026-02-11 09:20:31', '2026-02-11 09:21:34'),
(4, 1, 24, 448, '2026-02-11 09:20:31', '2026-02-11 09:21:39');


INSERT INTO `floors` (`id`, `branch_id`, `number`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(2, 1, 2, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(3, 1, 3, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(4, 1, 4, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(5, 1, 5, '2026-02-11 09:20:30', '2026-02-11 09:20:30');



INSERT INTO `frontdesks` (`id`, `branch_id`, `user_id`, `name`, `number`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Frontdesk', '+639000000000', '2026-02-11 09:20:30', '2026-02-11 09:20:30');
INSERT INTO `guests` (`id`, `branch_id`, `name`, `contact`, `qr_code`, `room_id`, `previous_room_id`, `rate_id`, `type_id`, `static_amount`, `is_long_stay`, `number_of_days`, `has_discount`, `discount_amount`, `has_kiosk_check_out`, `is_co`, `created_at`, `updated_at`) VALUES
(1, 1, 'test 1', 'N/A', '1260001', 88, NULL, 1, 1, 424, 0, 0, 0, '50.00', 0, 0, '2026-02-11 09:22:12', '2026-02-11 17:48:06'),
(2, 1, 'test 2', 'N/A', '1260002', 89, NULL, 1, 1, 424, 0, 0, 0, '50.00', 0, 0, '2026-02-11 09:22:22', '2026-02-11 21:23:35'),
(3, 1, 'test 3', 'N/A', '1260003', 130, NULL, 2, 1, 336, 0, 0, 0, NULL, 0, 0, '2026-02-11 09:22:30', '2026-02-11 09:22:30'),
(4, 1, 'test 4', 'N/A', '1260004', 131, NULL, 1, 1, 224, 0, 0, 0, NULL, 0, 0, '2026-02-11 09:22:38', '2026-02-11 09:22:38'),
(5, 1, 'test 5', 'N/A', '1260005', 171, NULL, 3, 1, 560, 0, 0, 0, NULL, 0, 0, '2026-02-11 09:22:47', '2026-02-11 09:22:47'),
(6, 1, 'test 6', 'N/A', '1260006', 172, NULL, 1, 1, 224, 0, 0, 0, NULL, 0, 0, '2026-02-11 09:22:56', '2026-02-11 09:22:56'),
(7, 1, 'test 7', 'N/A', '1260007', 205, NULL, 1, 1, 224, 0, 0, 0, NULL, 0, 0, '2026-02-11 09:23:05', '2026-02-11 09:23:05'),
(8, 1, 'test 8', 'N/A', '1260008', 33, NULL, 1, 1, 224, 0, 0, 0, NULL, 0, 0, '2026-02-11 09:23:13', '2026-02-11 09:23:13'),
(9, 1, 'test 9', 'N/A', '1260009', 34, NULL, 2, 1, 336, 0, 0, 0, NULL, 0, 0, '2026-02-11 09:23:26', '2026-02-11 09:23:26'),
(10, 1, 'test 10', 'N/A', '1260010', 48, NULL, 3, 1, 560, 0, 0, 0, NULL, 0, 0, '2026-02-11 09:23:36', '2026-02-11 09:23:36');
INSERT INTO `hotel_items` (`id`, `branch_id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 'MAIN DOOR', 5000, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(2, 1, 'PURTAHAN SA C.R.', 2500, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(3, 1, 'SUGA SA ROOM', 150, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(4, 1, 'SUGA SA C.R.', 130, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(5, 1, 'SAMIN SULOD SA ROOM', 1000, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(6, 1, 'SAMIN SULOD SA C.R.', 1000, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(7, 1, 'SAMIN SA GAWAS', 1500, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(8, 1, 'SALOG SA ROOM PER TILES', 1200, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(9, 1, 'SALOG SA C.R. PER TILE', 1200, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(10, 1, 'RUG/ TRAPO SA SALOG', 40, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(11, 1, 'UNLAN', 500, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(12, 1, 'HABOL', 500, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(13, 1, 'PUNDA', 200, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(14, 1, 'PUNDA WITH MANTSA(HAIR DYE)', 500, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(15, 1, 'BEDSHEET WITH INK', 500, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(16, 1, 'BED PAD WITH INK', 800, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(17, 1, 'BED SKIRT WITH INK', 1500, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(18, 1, 'TOWEL', 300, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(19, 1, 'DOORKNOB C.R.', 350, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(20, 1, 'MAIN DOOR DOORKNOB', 500, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(21, 1, 'T.V.', 30000, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(22, 1, 'TELEPHONE', 1000, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(23, 1, 'DECODER PARA SA CABLE', 1600, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(24, 1, 'CORD SA DECODER', 100, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(25, 1, 'CHARGER SA DECODER', 400, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(26, 1, 'WIRING SA TELEPHONE', 100, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(27, 1, 'WIRINGS SA T.V.', 200, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(28, 1, 'WIRING SA DECODER', 50, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(29, 1, 'CEILING', 0, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(30, 1, 'SHOWER HEAD', 800, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(31, 1, 'SHOWER BULB', 800, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(32, 1, 'BIDET', 400, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(33, 1, 'HINGES/ TOWEL BAR', 200, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(34, 1, 'TAKLOB SA TANGKE', 1200, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(35, 1, 'TANGKE SA BOWL', 3000, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(36, 1, 'TAKLOB SA BOWL', 1000, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(37, 1, 'ILALOM SA LABABO', 0, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(38, 1, 'SINK/LABABO', 1500, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(39, 1, 'BASURAHAN', 70, '2026-02-11 09:20:31', '2026-02-11 09:20:31');




INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2023_01_09_003353_create_sessions_table', 1),
(7, '2023_01_09_011352_create_permission_tables', 1),
(8, '2023_01_09_013909_create_branches_table', 1),
(9, '2023_01_09_044158_create_types_table', 1),
(10, '2023_01_09_062131_create_staying_hours_table', 1),
(11, '2023_01_09_062147_create_rates_table', 1),
(12, '2023_01_10_013013_create_floors_table', 1),
(13, '2023_01_10_053310_create_rooms_table', 1),
(14, '2023_01_11_054703_create_discounts_table', 1),
(15, '2023_01_11_072554_create_extension_rates_table', 1),
(16, '2023_01_11_082241_create_hotel_items_table', 1),
(17, '2023_01_11_112128_create_requestable_items_table', 1),
(18, '2023_01_13_015319_create_guests_table', 1),
(19, '2023_01_13_015911_create_temporary_check_in_kiosks_table', 1),
(20, '2023_01_13_110452_create_jobs_table', 1),
(21, '2023_01_13_133418_create_transactions_table', 1),
(22, '2023_01_13_133436_create_checkin_details_table', 1),
(23, '2023_01_17_110924_create_transaction_types_table', 1),
(24, '2023_01_24_111333_create_cleaning_histories_table', 1),
(25, '2023_01_24_170546_create_menu_categories_table', 1),
(26, '2023_01_24_184549_create_menus_table', 1),
(27, '2023_01_24_184821_create_inventories_table', 1),
(28, '2023_01_31_110026_create_frontdesks_table', 1),
(29, '2023_01_31_114024_create_assigned_frontdesks_table', 1),
(30, '2023_02_06_205903_create_expense_categories_table', 1),
(31, '2023_02_07_081720_create_expenses_table', 1),
(32, '2023_02_08_204445_create_shift_logs_table', 1),
(33, '2023_02_09_101803_create_stay_extensions_table', 1),
(34, '2023_02_28_110640_create_new_guest_reports_table', 1),
(35, '2023_02_28_143009_create_check_out_guest_reports_table', 1),
(36, '2023_03_01_115621_create_room_boy_reports_table', 1),
(37, '2023_03_02_135506_create_unoccupied_room_reports_table', 1),
(38, '2023_03_02_144943_create_extended_guest_reports_table', 1),
(39, '2023_03_09_214821_create_temporary_reserveds_table', 1),
(40, '2024_05_06_083859_create_frontdesk_categories_table', 1),
(41, '2024_05_06_084015_create_frontdesk_menus_table', 1),
(42, '2024_05_06_084026_create_frontdesk_inventories_table', 1),
(43, '2024_05_06_170704_create_pub_categories_table', 1),
(44, '2024_05_06_170716_create_pub_menus_table', 1),
(45, '2024_05_06_170724_create_pub_inventories_table', 1),
(46, '2024_05_20_150902_add_column_previous_room_id_on_guests_table', 1),
(47, '2025_06_05_092953_change_data_type_on_rooms_table', 1),
(48, '2025_06_16_090707_add_column_initial_deposit_to_branch_table', 1),
(49, '2025_06_18_102414_add_column_deposit_enabled_to_branches_table', 1),
(50, '2025_06_18_140238_add_column_has_discount_to_guests_table', 1),
(51, '2025_06_24_102431_add_column_has_discount_to_rates_table', 1),
(52, '2025_07_01_104113_add_column_has_kiosk_check_out_to_guests_table', 1),
(53, '2025_07_21_111252_add_column_user_id_to_expenses_table', 1),
(54, '2025_08_07_135126_add_column_image_on_frontdesk_menus', 1),
(55, '2025_08_08_142506_add_column_branch_id_to_room_boy_reports', 1),
(56, '2025_08_12_135730_create_floor_user_table', 1),
(57, '2025_08_28_161029_create_transfer_reasons_table', 1),
(58, '2025_08_29_083259_add_column_is_active_to_users_table', 1),
(59, '2025_09_11_161912_create_activity_logs_table', 1),
(60, '2025_09_24_145204_add_column_frontdesk_id_to_checkin_details_table', 1),
(61, '2025_10_10_081926_add_column_item_code_to_menus_table', 1),
(62, '2025_10_10_083455_add_column_item_code_to_frontdesk_menus_table', 1),
(63, '2025_11_18_141733_add_column_is_co_to_transactions_table', 1),
(64, '2025_11_19_102546_add_column_is_co_to_guests_table', 1),
(65, '2026_01_06_081235_add_column_is_opened_to_temporary_check_in_kiosks_table', 1),
(66, '2026_01_27_091907_create_cash_drawers_table', 1),
(67, '2026_01_28_103528_add_column_user_id_to_frontdesks_table', 1),
(68, '2026_01_30_115738_add_column_shift_to_shift_logs_table', 1),
(69, '2026_01_30_121811_add_column_shift_to_users_table', 1),
(70, '2026_01_30_124636_add_column_shift_to_transactions_table', 1),
(71, '2026_01_30_213630_add_column_frontdesk_id_to_shift_logs_table', 1),
(72, '2026_01_31_103608_create_cash_on_drawers_table', 1),
(73, '2026_02_05_162232_add_column_next_extension_is_original_to_checkin_details_table', 1),
(74, '2026_02_06_091712_add_column_static_room_amount_to_checkin_details_table', 1),
(75, '2026_02_10_154032_add_column_is_override_to_transactions_table', 1),
(76, '2026_02_11_130152_add_column_deductions_to_cash_on_drawers_table', 2);

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 7),
(5, 'App\\Models\\User', 5),
(6, 'App\\Models\\User', 4),
(7, 'App\\Models\\User', 6),
(8, 'App\\Models\\User', 8);
INSERT INTO `new_guest_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `shift_date`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(1, 1, 88, 1, 'February 11, 2026', 'AM', 1, 'N/A', '2026-02-11 17:48:06', '2026-02-11 17:48:06'),
(2, 1, 89, 2, 'February 11, 2026', 'PM', 1, 'N/A', '2026-02-11 21:23:35', '2026-02-11 21:23:35');






INSERT INTO `rates` (`id`, `branch_id`, `staying_hour_id`, `type_id`, `amount`, `is_available`, `has_discount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 224, 1, 0, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(2, 1, 2, 1, 336, 1, 0, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(3, 1, 4, 1, 560, 1, 0, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(4, 1, 1, 2, 280, 1, 0, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(5, 1, 2, 2, 392, 1, 0, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(6, 1, 4, 2, 616, 1, 0, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(7, 1, 1, 3, 336, 1, 0, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(8, 1, 2, 3, 448, 1, 0, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(9, 1, 4, 3, 672, 1, 0, '2026-02-11 09:20:31', '2026-02-11 09:20:31');
INSERT INTO `requestable_items` (`id`, `branch_id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 'EXTRA PERSON WITH FREE PILLOW, BLANKET,TOWEL', 100, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(2, 1, 'EXTRA PILLOW', 20, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(3, 1, 'EXTRA TOWEL', 20, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(4, 1, 'EXTRA BLANKET', 20, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(5, 1, 'EXTRA FITTED SHEET', 20, '2026-02-11 09:20:31', '2026-02-11 09:20:31');

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'web', '2026-02-11 09:20:29', '2026-02-11 09:20:29'),
(2, 'admin', 'web', '2026-02-11 09:20:29', '2026-02-11 09:20:29'),
(3, 'frontdesk', 'web', '2026-02-11 09:20:29', '2026-02-11 09:20:29'),
(4, 'roomboy', 'web', '2026-02-11 09:20:29', '2026-02-11 09:20:29'),
(5, 'kitchen', 'web', '2026-02-11 09:20:29', '2026-02-11 09:20:29'),
(6, 'kiosk', 'web', '2026-02-11 09:20:29', '2026-02-11 09:20:29'),
(7, 'back_office', 'web', '2026-02-11 09:20:29', '2026-02-11 09:20:29'),
(8, 'pub_kitchen', 'web', '2026-02-11 09:20:31', '2026-02-11 09:20:31');

INSERT INTO `rooms` (`id`, `branch_id`, `floor_id`, `number`, `area`, `status`, `type_id`, `is_priority`, `last_checkin_at`, `last_checkout_at`, `time_to_terminate_queue`, `check_out_time`, `time_to_clean`, `started_cleaning_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '1', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(2, 1, 1, '10', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(3, 1, 1, '11', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(4, 1, 1, '12', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(5, 1, 1, '14', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(6, 1, 1, '15', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(7, 1, 1, '16', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(8, 1, 1, '17', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(9, 1, 1, '18', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(10, 1, 1, '19', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(11, 1, 1, '2', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(12, 1, 1, '20', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(13, 1, 1, '21', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(14, 1, 1, '22', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(15, 1, 1, '23', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(16, 1, 1, '24', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(17, 1, 1, '25', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(18, 1, 1, '26', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(19, 1, 1, '27', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(20, 1, 1, '28', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(21, 1, 1, '29', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(22, 1, 1, '3', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(23, 1, 1, '30', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(24, 1, 1, '31', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(25, 1, 1, '32', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(26, 1, 1, '33', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(27, 1, 1, '34', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(28, 1, 1, '35', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(29, 1, 1, '36', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(30, 1, 1, '37', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(31, 1, 1, '38', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(32, 1, 1, '39', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(33, 1, 1, '4', 'Main', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:53'),
(34, 1, 1, '5', 'Main', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:54'),
(35, 1, 1, '50', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(36, 1, 1, '51', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(37, 1, 1, '52', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(38, 1, 1, '53', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(39, 1, 1, '6', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(40, 1, 1, '7', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(41, 1, 1, '8', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(42, 1, 1, '9', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(43, 1, 2, '100', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(44, 1, 2, '101', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(45, 1, 2, '60', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(46, 1, 2, '61', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(47, 1, 2, '62', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(48, 1, 2, '63', 'Main', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:54'),
(49, 1, 2, '64', 'Main', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:54'),
(50, 1, 2, '65', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(51, 1, 2, '66', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(52, 1, 2, '67', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(53, 1, 2, '68', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(54, 1, 2, '69', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(55, 1, 2, '70', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(56, 1, 2, '71', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(57, 1, 2, '72', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(58, 1, 2, '73', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(59, 1, 2, '74', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(60, 1, 2, '75', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(61, 1, 2, '76', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(62, 1, 2, '77', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(63, 1, 2, '78', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(64, 1, 2, '79', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(65, 1, 2, '80', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(66, 1, 2, '81', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(67, 1, 2, '82', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(68, 1, 2, '83', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(69, 1, 2, '84', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(70, 1, 2, '85', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(71, 1, 2, '86', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(72, 1, 2, '87', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(73, 1, 2, '88', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(74, 1, 2, '89', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(75, 1, 2, '90', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(76, 1, 2, '91', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(77, 1, 2, '92', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(78, 1, 2, '93', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(79, 1, 2, '94', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(80, 1, 2, '95', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(81, 1, 2, '96', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(82, 1, 2, '97', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(83, 1, 2, '98', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(84, 1, 2, '99', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(85, 1, 3, '120', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(86, 1, 3, '121', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(87, 1, 3, '122', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(88, 1, 3, '123', 'Main', 'Occupied', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 17:48:06'),
(89, 1, 3, '124', 'Main', 'Occupied', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 21:23:35'),
(90, 1, 3, '125', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(91, 1, 3, '126', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(92, 1, 3, '127', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(93, 1, 3, '128', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(94, 1, 3, '129', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(95, 1, 3, '130', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(96, 1, 3, '131', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(97, 1, 3, '132', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(98, 1, 3, '133', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(99, 1, 3, '134', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(100, 1, 3, '135', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(101, 1, 3, '136', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(102, 1, 3, '137', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(103, 1, 3, '138', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(104, 1, 3, '139', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(105, 1, 3, '150', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(106, 1, 3, '151', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(107, 1, 3, '152', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(108, 1, 3, '153', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(109, 1, 3, '154', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(110, 1, 3, '155', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(111, 1, 3, '156', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(112, 1, 3, '157', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(113, 1, 3, '158', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(114, 1, 3, '159', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(115, 1, 3, '160', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(116, 1, 3, '161', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(117, 1, 3, '162', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(118, 1, 3, '163', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(119, 1, 3, '164', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(120, 1, 3, '165', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(121, 1, 3, '166', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(122, 1, 3, '167', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(123, 1, 3, '168', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(124, 1, 3, '169', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(125, 1, 3, '170', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(126, 1, 3, '171', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(127, 1, 4, '200', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(128, 1, 4, '201', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(129, 1, 4, '202', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(130, 1, 4, '203', 'Main', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:52'),
(131, 1, 4, '204', 'Main', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:52'),
(132, 1, 4, '205', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(133, 1, 4, '206', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(134, 1, 4, '207', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(135, 1, 4, '208', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(136, 1, 4, '209', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(137, 1, 4, '210', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(138, 1, 4, '211', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(139, 1, 4, '212', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(140, 1, 4, '214', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(141, 1, 4, '215', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(142, 1, 4, '216', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(143, 1, 4, '217', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(144, 1, 4, '218', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(145, 1, 4, '219', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(146, 1, 4, '220', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(147, 1, 4, '221', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(148, 1, 4, '222', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(149, 1, 4, '223', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(150, 1, 4, '224', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(151, 1, 4, '225', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(152, 1, 4, '226', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(153, 1, 4, '227', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(154, 1, 4, '228', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(155, 1, 4, '229', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(156, 1, 4, '230', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(157, 1, 4, '231', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(158, 1, 4, '232', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(159, 1, 4, '233', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(160, 1, 4, '234', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(161, 1, 4, '235', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(162, 1, 4, '236', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(163, 1, 4, '237', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(164, 1, 4, '238', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(165, 1, 4, '239', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(166, 1, 4, '250', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(167, 1, 4, '251', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(168, 1, 5, '253', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(169, 1, 5, '254', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(170, 1, 5, '255', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(171, 1, 5, '256', 'Main', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:52'),
(172, 1, 5, '257', 'Main', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:53'),
(173, 1, 5, '258', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(174, 1, 5, '259', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(175, 1, 5, '260', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(176, 1, 5, '261', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(177, 1, 5, '262', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(178, 1, 5, '263', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(179, 1, 5, '264', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(180, 1, 5, '265', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(181, 1, 5, '266', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(182, 1, 5, '267', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(183, 1, 5, '268', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(184, 1, 5, '269', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(185, 1, 5, '270', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(186, 1, 5, '271', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(187, 1, 5, '272', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(188, 1, 5, '273', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(189, 1, 5, '274', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(190, 1, 5, '275', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(191, 1, 5, '276', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(192, 1, 5, '277', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(193, 1, 5, '278', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(194, 1, 5, '279', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(195, 1, 5, '280', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(196, 1, 5, '281', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(197, 1, 5, '282', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(198, 1, 5, '283', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(199, 1, 5, '284', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(200, 1, 5, '285', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(201, 1, 5, '286', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(202, 1, 5, '287', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(203, 1, 5, '288', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(204, 1, 5, '289', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(205, 1, 5, '290', 'Main', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:53'),
(206, 1, 5, '294', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 09:20:31', '2026-02-11 09:20:31');
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0jcAKEDahhTaRic2gx42PVXOxyUvaTaHdT549Wk9', NULL, '204.76.203.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiRktNTGhwUFVSZ2pSVFdzZWRvSFdYNDQxZ1VHZnFhMVN5c0htcE5SdiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770850060),
('2vihu3QtNKUaBbm9vFIpJjQAaq04Y8EpIKkvFr3t', NULL, '18.218.118.203', 'visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibkc5UzNMbkJ3RGNMUG5PWVp3N2VyZFRxYTM1dFQ5c012NHpVcGFVayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770840100),
('3yrE5RTxwqbQXh6Peg34ZTsyTk4vJ6LSVYn3oKZj', NULL, '155.94.150.89', 'Python/3.14 aiohttp/3.13.2', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWEdzd1ZndnVodzZ0VmRWSzlXOWhtazM3ZVlRNnhuZUNiUUEwMHRkZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770839595),
('44ByjQ7OBYrcFSvWr7gg8TsmIsjFSxcW2ueWLMZP', NULL, '43.159.152.4', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY2wwVzNhT2gwSnc4TmczcUt2MGhxaW9acnAzTjJxZlFIN1p1VDNQUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770842309),
('6Wao264RPw2KUSBeKsxxaDYhz92f9peZKVPyNHri', NULL, '87.121.84.15', 'Mozilla/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieVJ3VVBzeWM4YlVSOGRjaWlWNGNCM2x3bHpEdTZ1czE4aTZ5VjBSdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770851265),
('9TdBTIK2YITA9xRRnvZv92d04jBMekk4nm0XM54Z', NULL, '204.76.203.206', 'Wget', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNTlmMWhjYnRCUjRWY1Fjc1FWWlRoU3RFbVdiTWJad3R6aHpPaEpuSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770836505),
('B1kGHaYGtwQHtJyakdSriMT1Rj5qyYGvGOigcDl5', NULL, '204.76.203.206', 'Wget', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYnNFeHdjUGNSNHlHTGJSdlRqSHlDM1FONXdkUlplSUhocFVYUkNRcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770845735),
('Bw9WEo8TnGhmiPpMDO5MXzIYOeNMfpR8cNUYakyh', NULL, '204.76.203.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoidk84WldLTG9mR2Q2SndGRzRJRzlKWktZM1BFVkNwUlBtZWFrUGJhSiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770853452),
('cYibxB4vVjMSe32TJ7vKhS3D7ZxYAav06s2n2yp2', NULL, '204.76.203.69', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiTjVGc0k5cVRGczdQeEduQnhXU1A4ZmpJY2ttVW1XRTFmQzNzWFZSaCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770857298),
('FssIzpUl5FmFK6COP6xzILPI6a1fK8HCpO1erjBP', NULL, '18.218.118.203', 'visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTW9kSFcyc285cWVpMmxGQlNjNDMyaDVGeDhxWWtHQkV3TzVtTzVBRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770840108),
('gabhs6O6r2q4Su9nOClhzoA4oZaqqqg0b71y04vh', NULL, '204.76.203.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiT3pVaXdSZDluZko2b0RrQnhGZUpjcEd1VFRNakNUUHhwY2xJR0luOSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770833646),
('GvRIZJfTxohLukkHA3CszPpLxAO5OFMCJK0c78Y2', NULL, '204.76.203.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiN0dMUEczUXVWWmc5Z09pVkJocmYzeFA4M3psUmNmRDdETlR0cEVvSSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770843313),
('HiVNYSq4onBz5eVRjDPv14Vn5xLVcKNz6gpY9K12', NULL, '176.65.148.203', 'Mozilla/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVVdMYmhYaFNUc3hidkdDOTFHaVFKMk1wYVdOajd4dXNvZUM5ZG5wbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770845781),
('iNwGgP3isbr4yTZIMrvI7BriCy3Vzdp912m1PQOH', NULL, '157.15.40.91', 'python-requests/2.32.4', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidkF0bWFSNDJvTWYwaTlHUTZWSXhmZWh0ckpWcWNjRzdqTWIybFNDMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770840085),
('J1FSa2cIcKE8ipWNPCM3TH8T5ude0wkjTawiZV2L', NULL, '204.76.203.206', 'Wget', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia1ZxSnFrcTVwTDYyZ3pnSGRIZ1MwTDNnM0RDY3lpem1BaUwya1VMMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770848358),
('j2W5lCwdFJhCfRWFCXZvtm99NIddCBiiysQhyzKz', NULL, '204.76.203.206', 'Wget', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZFhMbjBjUmRzR2VuWm5taDFRUjgzQlAxYzRWN0JDSENhZk5Ed3VjayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770853372),
('JOKeE2nxTUzYyzPPTATLLJusjccvrf1mTeIa0Zkg', NULL, '204.76.203.206', 'Wget', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOHYwMmExQUxpZEM2a3JvV0Q1RkVMbk91czVNamdyS3VkOEw4YW9vMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770833794),
('lAOwjdXLpifq8NHpKuRVfpPNcvdY24fnUTSknpbx', NULL, '81.29.142.100', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNUYyMUF3ZjdjdVl5RGhGQVp3azRNMzkwMlJvVG1Zb25VckpOUFBkSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770835684),
('liU5EcWqrfVj0yNa03A8fssY43x9PxFRCWhj6oo3', NULL, '93.174.93.12', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.80 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicUNVNzhxOXZVOFZLVXRKeW5vUEZLTWxoaVZ4djZrdlF5S2NBejhqVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770842469),
('O386nrlg3vk1VbhDGuZqByGVeczQ7Y6VCgsAm2Hd', NULL, '43.131.32.36', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU3Rpb2Jkc3BYaVpxcUptNk16UktvamU5ODRrMnVJaVFCV1lrS2NDdyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770854260),
('O65DDj5ZIP5S86YsVG5Knmmdbz1oPzcCVitXJUKa', NULL, '204.76.203.69', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaVhYMWc4eWFuQ0o3ZE5FYjU3WlEwbGRQWUZyT0FxZjlFWGlWbVBUUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770839334),
('PIHjRbhElrWakPFsNqaeYUcX55oiC8oUIMmXXPJK', NULL, '204.76.203.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoicUxheWZocWlSNE9hc2FjVlNIM0twVmVNdHJ6RXQ5a0NMc25YVEwyeCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770847003),
('PO0H0SZsF3GkQDJCVZeAMKkg5xJnEvldFu3TVQWu', NULL, '83.168.68.72', 'libredtail-http', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYUxoNmxhT3FIMUNUblVTQVNDdTROZ2pFVmtVbk9jU1ptTmFaUkJWaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjAwOiJodHRwOi8vMTM5LjU5LjI1Mi4xMDAvaW5kZXgucGhwPyUyRiUzQyUzRmVjaG8lMjhtZDUlMjglMjJoaSUyMiUyOSUyOSUzQiUzRiUzRSUyMCUyRnRtcCUyRmluZGV4MS5waHA9JmNvbmZpZy1jcmVhdGUlMjAlMkY9Jmxhbmc9Li4lMkYuLiUyRi4uJTJGLi4lMkYuLiUyRi4uJTJGLi4lMkYuLiUyRnVzciUyRmxvY2FsJTJGbGliJTJGcGhwJTJGcGVhcmNtZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770833062),
('QLIrm7zMiAQI7DC0Pm3zeiFAMf0ut8CYJ4OJhbDj', NULL, '83.168.68.72', 'libredtail-http', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVWNnZ1B5aHJXVEo3cGRua3ZXdDRtc3Voa2NQMmhNeG96RmtoTDVwOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6ODk6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9pbmRleC5waHA/bGFuZz0uLiUyRi4uJTJGLi4lMkYuLiUyRi4uJTJGLi4lMkYuLiUyRi4uJTJGdG1wJTJGaW5kZXgxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770833062),
('qOL2omOylLG5LBsKQ06osXF1QEV6Mqrj22Rd6Bpt', NULL, '111.123.41.235', 'LinuxGnu', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiODFaenVOaUlUVHJMekNUeDVWckVFaDBrNVV0aU1rWUE0STMwUmxacSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770847499),
('rc2OAp3F8DTk4vSr9oYm3g7lt7BI7Fhe80Fv6w5D', NULL, '64.225.101.76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiazFHZjlXaWQwOFNyYkhlcEdZVHF6bEQ3aUFFOEp0cUl6OTNzMFlQWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770853366),
('RyeTE4IdqFu5LJuCTZKiLqP6lzjCvmuDnjkU6Ug7', NULL, '204.76.203.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiTW10Tkk1M01JTE5jUnI2aDlsc1lLSGRKN3NFcW51YzJuT1lHMG1zTiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770839879),
('sb1nXs0fvGHZfZxwSGdQ07K7C5ZLRliElIlkBprw', 6, '122.52.149.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoianBlMGpXVk13VklZYjhlMmxoWm5KYURvU2pDSWFJbUV6cTZNZnpydCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9iYWNrLW9mZmljZS9yZXBvcnQtaHViIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NjtzOjIxOiJwYXNzd29yZF9oYXNoX3NhbmN0dW0iO3M6NjA6IiQyeSQxMCRDSS53QWs5WndoMkJKa0Q4MTFsOFUuU2pRZ1FmVHAzZkhGbTFrd3RVdVR6anp3WG8vek11RyI7fQ==', 1770857596),
('shNdz4I6T0DFmTgI4rX0dCjKoZtts7dyv5ZOPf4h', NULL, '204.76.203.206', 'Wget', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWUNsME1ieGRqMnhBQ25JRFJtSGtFMWplZzZZYThSM1ZzRXRzN3BxMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770843993),
('SxThLzP7TTHR9zxpFOgZa2TgPEmVmmOAXKFdn5oc', NULL, '152.32.206.246', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSVp3TEZsbWpsZFpuZkNpVDJaT0dpcUNLZVRseHpZOFJ1ZkdENkZGdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770839246),
('tQbhxHO02HrWcNzquSPJ5l7YTQJuZNEe5IBwhNt4', NULL, '204.76.203.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiem4weDF0TWFTdTd4TGo3Q0FQQ1RkaW1pZFV0RWR1enhrMlJ4S3JNYyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770856830),
('UilS9oNFuWsZ3XrT63xWDgCU0zZUbqS8a8ZuThPM', NULL, '204.76.203.206', 'Wget', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTDZsQmdNa0RacUNySTk3d3MyQldVcGJ3Vk56ZUVhcDRES2Zqb1lzNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770856246),
('UkMigw2bxvXwmOeMDHeTn4gSadfKEW7ZQoUySvld', NULL, '204.76.203.206', 'Wget', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiejEzeENMRGR4cmRPMm5YM3V6bk83YTJWeU92NVpnSmkxalNPT3VPZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770851904),
('VcmVJory98EGwRjxqWMomETAYAR5U3V8KmzVcnhe', NULL, '161.35.213.124', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSXp4TUNFeUplRUs0YVV0bkdIS1dUTUp4VkhwQnI4N0pMMFNJS2lPSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770848790),
('VF9aJnd1KnEbeyTo75CxgKaVcJw1KvDGo7CbBe46', NULL, '54.83.72.14', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVEN5VnAxeE4zNDRGbmlvbTRMWnpRSDNxb2J1R3BuU1plR0NQRjRjdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770848538),
('VTT2vAaKHpp8Mn79JjEy60QukRHy561Vc9nqexGu', NULL, '157.245.103.95', 'Mozilla/5.0 (compatible; IPScanner/1.0)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibHJRTlI2Z0p3VHdtdXdZNTRoQ0x0Z0k5SEkwYTl3M1RqdENCSlZ4VCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770836359),
('XLs5P63B00mheERzRZRmqSJ2Gdk6MRoS2qj7Kluz', NULL, '185.242.226.113', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.190 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQmhhckZvV056Q0pIak55Y3JOVlNhSmNvcld3MEE3UzZRWFBIRVZBMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770849961),
('Yc4artB8QSUF8zvjmCFMXfJGSbe8UwFyLVL9dj6L', NULL, '155.94.150.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ0pmb1BIY2xEOVQ2WHByMkw5NjlkOHFkUmZNOWJXZDVncVZtZU1MMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770839592),
('ydBTJrPKF0xXrp0qThAVbJHn90ZRyGf1yRVfXp0H', NULL, '193.142.146.230', 'Go-http-client/1.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaDVhMWMxY0Q2TTg1NTNnaGRlQ1hwWVVidjFuS25FWERjVHNJazlROSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770844370),
('YqFxubizebFDMIFDjyk9C5um8SKU3uL4VovOAMXX', NULL, '204.76.203.206', 'Wget', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicExPQ1FnTXMxemlRTUluOTN6WHFhRkZuSWVjVjZvR1BjWkIySjFLbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770841632),
('yUUzd7XcCjbbIJK72A3hX7Hmx52jzT2v8JZkQjJ0', NULL, '83.168.68.72', 'libredtail-http', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaEVRaFNLczl6ZHllNnZhNFlOVndWd2s1NmRmNE13SFRRYm9nOXRkUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQ3OiJodHRwOi8vMTM5LjU5LjI1Mi4xMDAvaW5kZXgucGhwP2Z1bmN0aW9uPWNhbGxfdXNlcl9mdW5jX2FycmF5JnM9JTJGaW5kZXglMkYlNUN0aGluayU1Q2FwcCUyRmludm9rZWZ1bmN0aW9uJnZhcnMlNUIwJTVEPW1kNSZ2YXJzJTVCMSU1RCU1QjAlNUQ9SGVsbG8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770833061),
('YXfdbZ6IA17BDloYa6RlhYOwovavQ0wAmooz3SVx', NULL, '204.76.203.206', 'Wget', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMEJzU1NzSWgyVXhyUVk4UHV3U0Q4UUJRb3VEeHlPOUVKalNUNmNUOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770837913),
('yzXeWvw68mT2EOrWQU1QSNnkfxtRmoLcOwSm84RR', NULL, '204.76.203.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiREtyd0VJd1FvdmtJR0dLeTFVMU92aTVsdTBZMWJraFJhSHY0STJWQyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770836522);
INSERT INTO `shift_logs` (`id`, `frontdesk_id`, `cash_drawer_id`, `time_in`, `time_out`, `frontdesk_ids`, `shift`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '2026-02-11 09:23:48', NULL, '[1, \"N/A\"]', 'AM', '2026-02-11 09:23:48', '2026-02-11 09:23:48');

INSERT INTO `staying_hours` (`id`, `branch_id`, `number`, `created_at`, `updated_at`) VALUES
(1, 1, 6, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(2, 1, 12, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(3, 1, 18, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(4, 1, 24, '2026-02-11 09:20:30', '2026-02-11 09:20:30');
INSERT INTO `temporary_check_in_kiosks` (`id`, `branch_id`, `room_id`, `guest_id`, `terminated_at`, `is_opened`, `created_at`, `updated_at`) VALUES
(3, 1, 130, 3, '2026-02-11 09:42:30', 0, '2026-02-11 09:22:30', '2026-02-11 09:22:30'),
(4, 1, 131, 4, '2026-02-11 09:42:38', 1, '2026-02-11 09:22:38', '2026-02-11 11:30:42'),
(5, 1, 171, 5, '2026-02-11 09:42:47', 0, '2026-02-11 09:22:47', '2026-02-11 09:22:47'),
(6, 1, 172, 6, '2026-02-11 09:42:56', 0, '2026-02-11 09:22:56', '2026-02-11 09:22:56'),
(7, 1, 205, 7, '2026-02-11 09:43:05', 0, '2026-02-11 09:23:05', '2026-02-11 09:23:05'),
(8, 1, 33, 8, '2026-02-11 09:43:13', 0, '2026-02-11 09:23:13', '2026-02-11 09:23:13'),
(9, 1, 34, 9, '2026-02-11 09:43:26', 0, '2026-02-11 09:23:26', '2026-02-11 09:23:26'),
(10, 1, 48, 10, '2026-02-11 09:43:36', 0, '2026-02-11 09:23:36', '2026-02-11 09:23:36');

INSERT INTO `transaction_types` (`id`, `name`, `position`, `created_at`, `updated_at`) VALUES
(1, 'Check In', '1', '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(2, 'Deposit', '2', '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(3, 'Kitchen Order', '3', '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(4, 'Damage Charges', '4', '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(5, 'Cashout', '5', '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(6, 'Extend', '6', '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(7, 'Transfer Room', '7', '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(8, 'Amenities', '8', '2026-02-11 09:20:31', '2026-02-11 09:20:31'),
(9, 'Food and Beverages', '8', '2026-02-11 09:20:31', '2026-02-11 09:20:31');
INSERT INTO `transactions` (`id`, `branch_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 88, 1, 3, 1, '\"[1,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 224, 500, 76, 0, '2026-02-11 17:48:06', NULL, 'Guest Checked In at room #123', NULL, 0, 'AM', 0, '2026-02-11 17:48:06', '2026-02-11 17:48:06'),
(2, 1, 1, 88, 1, 3, 2, '\"[1,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 500, 76, 200, '2026-02-11 17:48:06', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'AM', 0, '2026-02-11 17:48:06', '2026-02-11 17:48:06'),
(3, 1, 1, 88, 1, 3, 8, '\"[1,\\\"N\\\\/A\\\"]\"', 'Amenities', 100, 0, 0, 0, NULL, NULL, 'Guest Added Amenities: (1) EXTRA PERSON WITH FREE PILLOW, BLANKET,TOWEL', NULL, 0, 'AM', 0, '2026-02-11 17:50:49', '2026-02-11 17:50:49'),
(4, 1, 1, 88, 1, 3, 4, '\"[1,\\\"N\\\\/A\\\"]\"', 'Damage Charges', 40, 0, 0, 0, NULL, NULL, 'Guest Charged for Damage: (1) RUG/ TRAPO SA SALOG', NULL, 0, 'AM', 0, '2026-02-11 17:53:13', '2026-02-11 17:53:13'),
(5, 1, 1, 89, 2, 3, 1, '\"[1,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 224, 500, 76, 0, '2026-02-11 21:23:35', NULL, 'Guest Checked In at room #124', NULL, 0, 'PM', 0, '2026-02-11 21:23:35', '2026-02-11 21:23:35'),
(6, 1, 1, 89, 2, 3, 2, '\"[1,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 500, 76, 200, '2026-02-11 21:23:35', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-02-11 21:23:35', '2026-02-11 21:23:35');
INSERT INTO `transfer_reasons` (`id`, `branch_id`, `reason`, `created_at`, `updated_at`) VALUES
(1, 1, 'Broken Aircon', '2026-02-11 09:21:04', '2026-02-11 09:21:04'),
(2, 1, 'Broken TV', '2026-02-11 09:21:11', '2026-02-11 09:21:11');
INSERT INTO `types` (`id`, `branch_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Single size Bed', NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(2, 1, ' Double size Bed', NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(3, 1, 'Twin size Bed', NULL, '2026-02-11 09:20:30', '2026-02-11 09:20:30');

INSERT INTO `users` (`id`, `branch_id`, `cash_drawer_id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `branch_name`, `remember_token`, `current_team_id`, `roomboy_assigned_floor_id`, `roomboy_cleaning_room_id`, `profile_photo_path`, `assigned_frontdesks`, `time_in`, `shift`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Superadmin', 'superadmin@gmail.com', NULL, '$2y$10$g6078Xvh4.DhheeeYceQb.2KLNt1SZi.yA2Lo3lMliM3gYjiF0NiW', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-02-11 09:20:29', '2026-02-11 09:20:29'),
(2, 1, NULL, 'Admin', 'admin@gmail.com', NULL, '$2y$10$wvH3orwf.rcICoWva787dO5w5Ndll6qx5ITnwRFl.oViIH.ik6nA6', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-02-11 09:20:29', '2026-02-11 09:20:29'),
(3, 1, 1, 'Frontdesk', 'frontdesk@gmail.com', NULL, '$2y$10$fY8/tP0kvVHYBQjKdHc1huzay2whm6b3u2P4JPFizW4x8DeEF9.Vq', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, '\"[1,\\\"N\\\\/A\\\"]\"', '2026-02-11 09:23:48', 'AM', 1, '2026-02-11 09:20:30', '2026-02-11 09:23:48'),
(4, 1, NULL, 'Kiosk', 'kiosk@gmail.com', NULL, '$2y$10$YApE0dbHlPpHkzf9Qla0Q.zqZQdkh72rkvSu6kBtanGljdhPwzJW2', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(5, 1, NULL, 'Kitchen', 'kitchen@gmail.com', NULL, '$2y$10$9wuX6EtpS5RvDqd/GXRJ7u/2xivSzMEnY7XXefK6qzN5c57B8RXJe', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(6, 1, NULL, 'Back Office', 'back-office@gmail.com', NULL, '$2y$10$CI.wAk9Zwh2BJkD811l8U.SjQgQfTp3fHFm1kwtUuTzjzwXo/zMuG', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(7, 1, NULL, 'Roomboy', 'roomboy@gmail.com', NULL, '$2y$10$j9HblQ6uPP01BLMgj3fF1.k2a2CxZCHBy312LJ50plgAewjxqVxWO', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-02-11 09:20:30', '2026-02-11 09:20:30'),
(8, 1, NULL, 'PUB Kitchen', 'pub-kitchen@gmail.com', NULL, '$2y$10$ecn80wSLccnW4yRDrd6I3Oo67bHmxVOca4/X0Bkkr6k36eTL5GiG6', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-02-11 09:20:31', '2026-02-11 09:20:31');


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;