/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `activity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=315 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `kiosk_time_limit` int NOT NULL DEFAULT '10',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=249 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `expense_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shift_log_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `extension_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `hour` int NOT NULL,
  `amount` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `floors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `number` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `frontdesk_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `frontdesk_inventories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `frontdesk_menu_id` bigint unsigned NOT NULL,
  `number_of_serving` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `frontdesks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `passcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '12345',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hotel_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `menu_id` bigint unsigned NOT NULL,
  `number_of_serving` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `menu_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `pub_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pub_inventories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `pub_menu_id` bigint unsigned NOT NULL,
  `number_of_serving` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `remittances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shift_log_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `total_remittance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `requestable_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=232 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `shift_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `frontdesk_id` bigint unsigned DEFAULT NULL,
  `cash_drawer_id` bigint unsigned DEFAULT NULL,
  `beginning_cash` decimal(15,2) NOT NULL DEFAULT '0.00',
  `end_cash` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `total_expenses` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_remittances` decimal(15,2) NOT NULL DEFAULT '0.00',
  `time_in` datetime NOT NULL,
  `time_out` datetime DEFAULT NULL,
  `frontdesk_ids` json NOT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `staying_hours` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `number` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `temporary_reserveds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `guest_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transaction_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `shift_log_id` bigint unsigned DEFAULT NULL,
  `checkin_detail_id` bigint unsigned DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=264 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transfer_reasons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_reasons_branch_id_foreign` (`branch_id`),
  CONSTRAINT `transfer_reasons_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transfered_guest_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `checkin_detail_id` bigint unsigned NOT NULL,
  `previous_room_id` bigint unsigned NOT NULL,
  `new_room_id` bigint unsigned NOT NULL,
  `rate_id` bigint unsigned NOT NULL,
  `previous_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `new_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `original_check_in_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `activity_logs` (`id`, `branch_id`, `user_id`, `activity`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Create Frontdesk', 'Created frontdesk Hannah', '2026-03-08 20:03:25', '2026-03-08 20:03:25');
INSERT INTO `activity_logs` (`id`, `branch_id`, `user_id`, `activity`, `description`, `created_at`, `updated_at`) VALUES
(2, 1, 2, 'Create User', 'Created user Hannah', '2026-03-08 20:03:25', '2026-03-08 20:03:25');
INSERT INTO `activity_logs` (`id`, `branch_id`, `user_id`, `activity`, `description`, `created_at`, `updated_at`) VALUES
(3, 1, 9, 'Check In from Kiosk', 'Checked in guest Art from kiosk', '2026-03-08 20:05:49', '2026-03-08 20:05:49');
INSERT INTO `activity_logs` (`id`, `branch_id`, `user_id`, `activity`, `description`, `created_at`, `updated_at`) VALUES
(4, 1, 9, 'Check In from Kiosk', 'Checked in guest Joseph Maranan from kiosk', '2026-03-08 20:11:33', '2026-03-08 20:11:33'),
(5, 1, 9, 'Check In from Kiosk', 'Checked in guest Levi samonte from kiosk', '2026-03-08 20:14:57', '2026-03-08 20:14:57'),
(6, 1, 2, 'Create Category', 'Created category Juices', '2026-03-08 20:19:11', '2026-03-08 20:19:11'),
(7, 1, 2, 'Create Category', 'Created category JunkFoods', '2026-03-08 20:19:24', '2026-03-08 20:19:24'),
(8, 1, 2, 'Create Menu', 'Created menu FOUR SEASON', '2026-03-08 20:20:03', '2026-03-08 20:20:03'),
(9, 1, 2, 'Create Menu', 'Created menu MANGO NECTAR', '2026-03-08 20:21:09', '2026-03-08 20:21:09'),
(10, 1, 2, 'Create Menu', 'Created menu PINEORANGE', '2026-03-08 20:21:52', '2026-03-08 20:21:52'),
(11, 1, 2, 'Create Menu', 'Created menu PINEAPPLE', '2026-03-08 20:22:12', '2026-03-08 20:22:12'),
(12, 1, 2, 'Create Menu', 'Created menu FIT N RIGHT', '2026-03-08 20:22:34', '2026-03-08 20:22:34'),
(13, 1, 9, 'Check In from Kiosk', 'Checked in guest Efren embrado from kiosk', '2026-03-08 20:23:04', '2026-03-08 20:23:04'),
(14, 1, 2, 'Create Category', 'Created category MINERAL WATER', '2026-03-08 20:23:13', '2026-03-08 20:23:13'),
(15, 1, 2, 'Create Menu', 'Created menu 1 LITER MINERAL', '2026-03-08 20:24:07', '2026-03-08 20:24:07'),
(16, 1, 9, 'Check In from Kiosk', 'Checked in guest Rey rian jay palmero from kiosk', '2026-03-08 20:24:16', '2026-03-08 20:24:16'),
(17, 1, 2, 'Create Menu', 'Created menu 500ML MINERAL', '2026-03-08 20:24:28', '2026-03-08 20:24:28'),
(18, 1, 2, 'Create Category', 'Created category SOFT DRINKS', '2026-03-08 20:24:48', '2026-03-08 20:24:48'),
(19, 1, 9, 'Check In from Kiosk', 'Checked in guest Lebron from kiosk', '2026-03-08 20:24:48', '2026-03-08 20:24:48'),
(20, 1, 2, 'Create Menu', 'Created menu MOUNTAIN DEW', '2026-03-08 20:26:08', '2026-03-08 20:26:08'),
(21, 1, 2, 'Create Menu', 'Created menu C2', '2026-03-08 20:26:34', '2026-03-08 20:26:34'),
(22, 1, 2, 'Create Menu', 'Created menu MINUTE MAID', '2026-03-08 20:26:57', '2026-03-08 20:26:57'),
(23, 1, 2, 'Create Menu', 'Created menu 7-UP', '2026-03-08 20:28:04', '2026-03-08 20:28:04'),
(24, 1, 9, 'Check In from Kiosk', 'Checked in guest Robert Satonero from kiosk', '2026-03-08 20:28:17', '2026-03-08 20:28:17'),
(25, 1, 2, 'Add Inventory', 'Added inventory for menu MINUTE MAID', '2026-03-08 20:28:42', '2026-03-08 20:28:42'),
(26, 1, 9, 'Check In from Kiosk', 'Checked in guest Jay from kiosk', '2026-03-08 20:30:40', '2026-03-08 20:30:40'),
(27, 1, 9, 'Check In from Kiosk', 'Checked in guest Jesus Gallano Jr from kiosk', '2026-03-08 20:31:17', '2026-03-08 20:31:17'),
(28, 1, 9, 'Check In from Kiosk', 'Checked in guest Ma.bella Salo from kiosk', '2026-03-08 20:32:05', '2026-03-08 20:32:05'),
(29, 1, 9, 'Check In from Kiosk', 'Checked in guest Rachel jucom from kiosk', '2026-03-08 20:36:12', '2026-03-08 20:36:12'),
(30, 1, 2, 'Create Menu', 'Created menu JUNKFOODS', '2026-03-08 20:36:44', '2026-03-08 20:36:44'),
(31, 1, 2, 'Add Inventory', 'Added inventory for menu JUNKFOODS', '2026-03-08 20:40:02', '2026-03-08 20:40:02'),
(32, 1, 2, 'Create Menu', 'Created menu COKE', '2026-03-08 20:41:04', '2026-03-08 20:41:04'),
(33, 1, 2, 'Create Category', 'Created category TOILETRIES', '2026-03-08 20:42:25', '2026-03-08 20:42:25'),
(34, 1, 2, 'Create Menu', 'Created menu TOOTHBRUSH', '2026-03-08 20:43:26', '2026-03-08 20:43:26'),
(35, 1, 2, 'Create Menu', 'Created menu SHAVER', '2026-03-08 20:43:47', '2026-03-08 20:43:47'),
(36, 1, 2, 'Create Menu', 'Created menu TOOTHPASTE', '2026-03-08 20:44:06', '2026-03-08 20:44:06'),
(37, 1, 2, 'Create Menu', 'Created menu CONDITIONER', '2026-03-08 20:44:31', '2026-03-08 20:44:31'),
(38, 1, 2, 'Create Menu', 'Created menu MODESS', '2026-03-08 20:44:51', '2026-03-08 20:44:51'),
(39, 1, 2, 'Create Menu', 'Created menu SOAP', '2026-03-08 20:45:09', '2026-03-08 20:45:09'),
(40, 1, 2, 'Create Menu', 'Created menu SHAMPOO', '2026-03-08 20:45:25', '2026-03-08 20:45:25'),
(41, 1, 2, 'Create Menu', 'Created menu SPRITE', '2026-03-08 20:46:16', '2026-03-08 20:46:16'),
(42, 1, 2, 'Create Menu', 'Created menu ROYAL', '2026-03-08 20:46:51', '2026-03-08 20:46:51'),
(43, 1, 9, 'Check In from Kiosk', 'Checked in guest Shyra felipe from kiosk', '2026-03-08 20:46:55', '2026-03-08 20:46:55'),
(44, 1, 2, 'Create Menu', 'Created menu CALI', '2026-03-08 20:47:18', '2026-03-08 20:47:18'),
(45, 1, 2, 'Create Menu', 'Created menu COBRA', '2026-03-08 20:47:42', '2026-03-08 20:47:42'),
(46, 1, 2, 'Add Inventory', 'Added inventory for menu 1 LITER MINERAL', '2026-03-08 20:49:07', '2026-03-08 20:49:07'),
(47, 1, 9, 'Check In from Kiosk', 'Checked in guest Don Agbulos from kiosk', '2026-03-08 20:50:17', '2026-03-08 20:50:17'),
(48, 1, 9, 'Check In from Kiosk', 'Checked in guest Tsang david from kiosk', '2026-03-08 20:50:45', '2026-03-08 20:50:45'),
(49, 1, 9, 'Check In from Kiosk', 'Checked in guest Charito Cantor from kiosk', '2026-03-08 21:01:09', '2026-03-08 21:01:09'),
(50, 1, 9, 'Check In from Kiosk', 'Checked in guest Monica uy from kiosk', '2026-03-08 21:02:16', '2026-03-08 21:02:16'),
(51, 1, 9, 'Check In from Kiosk', 'Checked in guest Kairo sarte from kiosk', '2026-03-08 21:04:26', '2026-03-08 21:04:26'),
(52, 1, 9, 'Add Food and Beverages', 'Added new food and beverages of ₱45 for guest Tsang david', '2026-03-08 21:05:35', '2026-03-08 21:05:35'),
(53, 1, 9, 'Payment', 'Payment of ₱45 for guest Tsang david', '2026-03-08 21:05:41', '2026-03-08 21:05:41'),
(54, 1, 9, 'Check In from Kiosk', 'Checked in guest Alhaya tia from kiosk', '2026-03-08 21:07:23', '2026-03-08 21:07:23'),
(55, 1, 9, 'Add Deposit', 'Added new deposit of ₱8 for guest Alhaya tia', '2026-03-08 21:07:44', '2026-03-08 21:07:44'),
(56, 1, 2, 'Add Inventory', 'Added inventory for menu TOOTHBRUSH', '2026-03-08 21:08:21', '2026-03-08 21:08:21'),
(57, 1, 2, 'Add Inventory', 'Added inventory for menu SHAVER', '2026-03-08 21:08:29', '2026-03-08 21:08:29'),
(58, 1, 2, 'Add Inventory', 'Added inventory for menu TOOTHPASTE', '2026-03-08 21:08:36', '2026-03-08 21:08:36'),
(59, 1, 2, 'Add Inventory', 'Added inventory for menu CONDITIONER', '2026-03-08 21:08:44', '2026-03-08 21:08:44'),
(60, 1, 2, 'Add Inventory', 'Added inventory for menu MODESS', '2026-03-08 21:09:04', '2026-03-08 21:09:04'),
(61, 1, 2, 'Add Inventory', 'Added inventory for menu SOAP', '2026-03-08 21:09:14', '2026-03-08 21:09:14'),
(62, 1, 9, 'Check In from Kiosk', 'Checked in guest Angel from kiosk', '2026-03-08 21:09:41', '2026-03-08 21:09:41'),
(63, 1, 2, 'Add Inventory', 'Added inventory for menu SHAMPOO', '2026-03-08 21:10:15', '2026-03-08 21:10:15'),
(64, 1, 9, 'Check In from Kiosk', 'Checked in guest Robert Dampan from kiosk', '2026-03-08 21:10:38', '2026-03-08 21:10:38'),
(65, 1, 9, 'Check In from Kiosk', 'Checked in guest James rolly from kiosk', '2026-03-08 21:20:13', '2026-03-08 21:20:13'),
(66, 1, 9, 'Check In from Kiosk', 'Checked in guest Juleth generale from kiosk', '2026-03-08 21:24:27', '2026-03-08 21:24:27'),
(67, 1, 9, 'Check In from Kiosk', 'Checked in guest Luigi garzon from kiosk', '2026-03-08 21:25:00', '2026-03-08 21:25:00'),
(68, 1, 9, 'Check In from Kiosk', 'Checked in guest Marvin Bacos from kiosk', '2026-03-08 21:25:45', '2026-03-08 21:25:45'),
(69, 1, 9, 'Check In from Kiosk', 'Checked in guest Winnie from kiosk', '2026-03-08 21:30:12', '2026-03-08 21:30:12'),
(70, 1, 2, 'Add Inventory', 'Added inventory for menu SPRITE', '2026-03-08 21:30:16', '2026-03-08 21:30:16'),
(71, 1, 9, 'Check In from Kiosk', 'Checked in guest Rahima samad from kiosk', '2026-03-08 21:32:28', '2026-03-08 21:32:28'),
(72, 1, 9, 'Check In from Kiosk', 'Checked in guest Brylle hugo from kiosk', '2026-03-08 21:33:18', '2026-03-08 21:33:18'),
(73, 1, 9, 'Check In from Kiosk', 'Checked in guest Jeamae esmael from kiosk', '2026-03-08 21:34:20', '2026-03-08 21:34:20'),
(74, 1, 9, 'Check In from Kiosk', 'Checked in guest Jay Ann from kiosk', '2026-03-08 21:35:19', '2026-03-08 21:35:19'),
(75, 1, 9, 'Check In from Kiosk', 'Checked in guest Paul delfinado from kiosk', '2026-03-08 21:36:45', '2026-03-08 21:36:45'),
(76, 1, 2, 'Create Damage Charges', 'Created damage charges for WET BED', '2026-03-08 21:39:03', '2026-03-08 21:39:03'),
(77, 1, 2, 'Create Damage Charges', 'Created damage charges for FLUSHER', '2026-03-08 21:40:13', '2026-03-08 21:40:13'),
(78, 1, 2, 'Create Damage Charges', 'Created damage charges for FAUCET SA UBOS', '2026-03-08 21:40:40', '2026-03-08 21:40:40'),
(79, 1, 9, 'Add Food and Beverages', 'Added new food and beverages of ₱55 for guest Shyra felipe', '2026-03-08 21:40:48', '2026-03-08 21:40:48'),
(80, 1, 2, 'Create Damage Charges', 'Created damage charges for FAUCET SA SINK', '2026-03-08 21:41:02', '2026-03-08 21:41:02'),
(81, 1, 9, 'Payment with Deposit', 'Payment of ₱55 with deposit for guest Shyra felipe', '2026-03-08 21:41:03', '2026-03-08 21:41:03'),
(82, 1, 2, 'Create Damage Charges', 'Created damage charges for SMOKING', '2026-03-08 21:41:29', '2026-03-08 21:41:29'),
(83, 1, 9, 'Add Food and Beverages', 'Added new food and beverages of ₱6 for guest Kairo sarte', '2026-03-08 21:41:48', '2026-03-08 21:41:48'),
(84, 1, 9, 'Payment with Deposit', 'Payment of ₱6 with deposit for guest Kairo sarte', '2026-03-08 21:41:56', '2026-03-08 21:41:56'),
(85, 1, 2, 'Create Damage Charges', 'Created damage charges for AIRCON COVER', '2026-03-08 21:42:04', '2026-03-08 21:42:04'),
(86, 1, 9, 'Add Food and Beverages', 'Added new food and beverages of ₱3 for guest James rolly', '2026-03-08 21:44:42', '2026-03-08 21:44:42'),
(87, 1, 9, 'Payment', 'Payment of ₱3 for guest James rolly', '2026-03-08 21:44:50', '2026-03-08 21:44:50'),
(88, 1, 9, 'Add Food and Beverages', 'Added new food and beverages of ₱2 for guest James rolly', '2026-03-08 21:45:08', '2026-03-08 21:45:08'),
(89, 1, 9, 'Payment', 'Payment of ₱2 for guest James rolly', '2026-03-08 21:45:13', '2026-03-08 21:45:13'),
(90, 1, 9, 'Check In from Kiosk', 'Checked in guest Ronel pino from kiosk', '2026-03-08 21:46:36', '2026-03-08 21:46:36'),
(91, 1, 2, 'Create Damage Charges', 'Created damage charges for EFFICASCENT/OIL VERY SMALL', '2026-03-08 21:46:44', '2026-03-08 21:46:44'),
(92, 1, 2, 'Create Damage Charges', 'Created damage charges for EFFICASCENT/OIL SMALL', '2026-03-08 21:47:18', '2026-03-08 21:47:18'),
(93, 1, 2, 'Create Damage Charges', 'Created damage charges for EFFICASCENT/OIL MEDIUM', '2026-03-08 21:47:35', '2026-03-08 21:47:35'),
(94, 1, 2, 'Create Damage Charges', 'Created damage charges for EFFICASCENT/OIL LARGE', '2026-03-08 21:47:53', '2026-03-08 21:47:53'),
(95, 1, 2, 'Create Damage Charges', 'Created damage charges for EFFICASCENT/OIL VERY LARGE', '2026-03-08 21:48:08', '2026-03-08 21:48:08'),
(96, 1, 2, 'Create Damage Charges', 'Created damage charges for BLOOD STAIN SMALL', '2026-03-08 21:48:54', '2026-03-08 21:48:54'),
(97, 1, 2, 'Create Damage Charges', 'Created damage charges for BLOOD STAIN LARGE', '2026-03-08 21:49:14', '2026-03-08 21:49:14'),
(98, 1, 9, 'Check In from Kiosk', 'Checked in guest Tato tandual from kiosk', '2026-03-08 21:49:21', '2026-03-08 21:49:21'),
(99, 1, 2, 'Create Damage Charges', 'Created damage charges for BLOOD STAIN WORST', '2026-03-08 21:49:28', '2026-03-08 21:49:28'),
(100, 1, 2, 'Create Damage Charges', 'Created damage charges for VANDALISM', '2026-03-08 21:49:46', '2026-03-08 21:49:46'),
(101, 1, 2, 'Create Damage Charges', 'Created damage charges for BEER / LIQUOR', '2026-03-08 21:50:04', '2026-03-08 21:50:04'),
(102, 1, 9, 'Check In from Kiosk', 'Checked in guest Jhonna lou binangga from kiosk', '2026-03-08 21:50:38', '2026-03-08 21:50:38'),
(103, 1, 2, 'Create Damage Charges', 'Created damage charges for DURIAN / MARANG / LANGKA', '2026-03-08 21:51:03', '2026-03-08 21:51:03'),
(104, 1, 9, 'Check In from Kiosk', 'Checked in guest Christiene jade arabit from kiosk', '2026-03-08 21:51:35', '2026-03-08 21:51:35'),
(105, 1, 2, 'Create Damage Charges', 'Created damage charges for KITCHEN UTENSIL: BOWL', '2026-03-08 21:51:48', '2026-03-08 21:51:48'),
(106, 1, 2, 'Create Damage Charges', 'Created damage charges for PLATE', '2026-03-08 21:52:24', '2026-03-08 21:52:24'),
(107, 1, 2, 'Create Damage Charges', 'Created damage charges for SAUCER', '2026-03-08 21:55:24', '2026-03-08 21:55:24'),
(108, 1, 9, 'Check In from Kiosk', 'Checked in guest Jeanny Babes  Tayo from kiosk', '2026-03-08 21:55:36', '2026-03-08 21:55:36'),
(109, 1, 2, 'Create Damage Charges', 'Created damage charges for GLASS', '2026-03-08 21:58:16', '2026-03-08 21:58:16'),
(110, 1, 9, 'Check In from Kiosk', 'Checked in guest Edison bonilla from kiosk', '2026-03-08 21:58:45', '2026-03-08 21:58:45'),
(111, 1, 2, 'Create Damage Charges', 'Created damage charges for COMPUTER USAGE', '2026-03-08 21:59:05', '2026-03-08 21:59:05'),
(112, 1, 2, 'Create Damage Charges', 'Created damage charges for LINENS, PUNDA WITH BUBBLE GUM', '2026-03-08 21:59:33', '2026-03-08 21:59:33'),
(113, 1, 2, 'Create Damage Charges', 'Created damage charges for STICKER(S) ', '2026-03-08 22:00:39', '2026-03-08 22:00:39'),
(114, 1, 2, 'Create Damage Charges', 'Created damage charges for MENU', '2026-03-08 22:00:56', '2026-03-08 22:00:56'),
(115, 1, 2, 'Create Damage Charges', 'Created damage charges for CLIPSAL', '2026-03-08 22:01:52', '2026-03-08 22:01:52'),
(116, 1, 2, 'Create Damage Charges', 'Created damage charges for KEY', '2026-03-08 22:02:39', '2026-03-08 22:02:39'),
(117, 1, 9, 'Check In from Kiosk', 'Checked in guest Sergio Seroyla from kiosk', '2026-03-08 22:02:57', '2026-03-08 22:02:57'),
(118, 1, 2, 'Create Damage Charges', 'Created damage charges for BACKOUT GUEST: DIRTY ROOM / C.R', '2026-03-08 22:03:13', '2026-03-08 22:03:13'),
(119, 1, 2, 'Create Damage Charges', 'Created damage charges for BACKOUT GUEST: DIRTY BEDDINGS', '2026-03-08 22:03:33', '2026-03-08 22:03:33'),
(120, 1, 2, 'Create Damage Charges', 'Created damage charges for REMOTE', '2026-03-08 22:03:55', '2026-03-08 22:03:55'),
(121, 1, 2, 'Create Damage Charges', 'Created damage charges for DOUBLE CHARGE EXTRA PERSON SINGLE', '2026-03-08 22:05:45', '2026-03-08 22:05:45'),
(122, 1, 2, 'Create Damage Charges', 'Created damage charges for DOUBLE CHARGE EXTRA PERSON DOUBLE', '2026-03-08 22:06:00', '2026-03-08 22:06:00'),
(123, 1, 9, 'Check In from Kiosk', 'Checked in guest Albert rosario from kiosk', '2026-03-08 22:06:05', '2026-03-08 22:06:05'),
(124, 1, 2, 'Create Damage Charges', 'Created damage charges for DOUBLE CHARGE EXTRA PERSON TWIN BED', '2026-03-08 22:06:24', '2026-03-08 22:06:24'),
(125, 1, 9, 'Check In from Kiosk', 'Checked in guest Ronald ortega from kiosk', '2026-03-08 22:09:36', '2026-03-08 22:09:36'),
(126, 1, 2, 'Create Damage Charges', 'Created damage charges for EXTRA TOWEL', '2026-03-08 22:12:59', '2026-03-08 22:12:59'),
(127, 1, 2, 'Create Damage Charges', 'Created damage charges for EXTRA BLANKET', '2026-03-08 22:13:17', '2026-03-08 22:13:17'),
(128, 1, 2, 'Create Damage Charges', 'Created damage charges for EXTRA FITTED SHEET', '2026-03-08 22:13:33', '2026-03-08 22:13:33'),
(129, 1, 2, 'Create User', 'Created user GEORGE MENDOZA', '2026-03-08 22:23:09', '2026-03-08 22:23:09'),
(130, 1, 2, 'Update Roomboy Designation', 'Updated roomboy designation for GEORGE MENDOZA', '2026-03-08 22:24:10', '2026-03-08 22:24:10'),
(131, 1, 9, 'Add Amenities', 'Added new amenities of ₱20 for guest Brylle hugo', '2026-03-08 22:24:23', '2026-03-08 22:24:23'),
(132, 1, 9, 'Payment with Deposit', 'Payment of ₱20 with deposit for guest Brylle hugo', '2026-03-08 22:24:25', '2026-03-08 22:24:25'),
(133, 1, 2, 'Create User', 'Created user LEO ESTILLORE', '2026-03-08 22:27:37', '2026-03-08 22:27:37'),
(134, 1, 2, 'Update Roomboy Designation', 'Updated roomboy designation for LEO ESTILLORE', '2026-03-08 22:28:04', '2026-03-08 22:28:04'),
(135, 1, 9, 'Check In from Kiosk', 'Checked in guest SPARKS MAGDAYAO from kiosk', '2026-03-08 22:29:20', '2026-03-08 22:29:20'),
(136, 1, 2, 'Create User', 'Created user FRANCISCO', '2026-03-08 22:30:49', '2026-03-08 22:30:49'),
(137, 1, 2, 'Update Roomboy Designation', 'Updated roomboy designation for FRANCISCO', '2026-03-08 22:31:05', '2026-03-08 22:31:05'),
(138, 1, 9, 'Add Food and Beverages', 'Added new food and beverages of ₱78 for guest SPARKS MAGDAYAO', '2026-03-08 22:32:08', '2026-03-08 22:32:08'),
(139, 1, 9, 'Payment with Deposit', 'Payment of ₱78 with deposit for guest SPARKS MAGDAYAO', '2026-03-08 22:32:14', '2026-03-08 22:32:14'),
(140, 1, 9, 'Add Food and Beverages', 'Added new food and beverages of ₱15 for guest SPARKS MAGDAYAO', '2026-03-08 22:32:29', '2026-03-08 22:32:29'),
(141, 1, 9, 'Payment with Deposit', 'Payment of ₱15 with deposit for guest SPARKS MAGDAYAO', '2026-03-08 22:32:39', '2026-03-08 22:32:39'),
(142, 1, 9, 'Add Food and Beverages', 'Added new food and beverages of ₱12 for guest SPARKS MAGDAYAO', '2026-03-08 22:33:16', '2026-03-08 22:33:16'),
(143, 1, 9, 'Payment with Deposit', 'Payment of ₱12 with deposit for guest SPARKS MAGDAYAO', '2026-03-08 22:33:28', '2026-03-08 22:33:28'),
(144, 1, 2, 'Create User', 'Created user JOHN KARL', '2026-03-08 22:33:51', '2026-03-08 22:33:51'),
(145, 1, 2, 'Update Roomboy Designation', 'Updated roomboy designation for JOHN KARL', '2026-03-08 22:34:16', '2026-03-08 22:34:16'),
(146, 1, 9, 'Add Food and Beverages', 'Added new food and beverages of ₱15 for guest SPARKS MAGDAYAO', '2026-03-08 22:34:32', '2026-03-08 22:34:32');
INSERT INTO `activity_logs` (`id`, `branch_id`, `user_id`, `activity`, `description`, `created_at`, `updated_at`) VALUES
(147, 1, 9, 'Payment with Deposit', 'Payment of ₱15 with deposit for guest SPARKS MAGDAYAO', '2026-03-08 22:34:39', '2026-03-08 22:34:39');
INSERT INTO `activity_logs` (`id`, `branch_id`, `user_id`, `activity`, `description`, `created_at`, `updated_at`) VALUES
(148, 1, 2, 'Create User', 'Created user REYMART', '2026-03-08 22:36:17', '2026-03-08 22:36:17');
INSERT INTO `activity_logs` (`id`, `branch_id`, `user_id`, `activity`, `description`, `created_at`, `updated_at`) VALUES
(149, 1, 2, 'Update Roomboy Designation', 'Updated roomboy designation for REYMART', '2026-03-08 22:36:36', '2026-03-08 22:36:36'),
(150, 1, 9, 'Check In from Kiosk', 'Checked in guest JOSELITO CUEVAS from kiosk', '2026-03-08 22:36:57', '2026-03-08 22:36:57'),
(151, 1, 9, 'Check In from Kiosk', 'Checked in guest REX PENIALOSA from kiosk', '2026-03-08 22:37:28', '2026-03-08 22:37:28'),
(152, 1, 2, 'Create User', 'Created user RAMILL MILLADO', '2026-03-08 22:39:46', '2026-03-08 22:39:46'),
(153, 1, 9, 'Check In from Kiosk', 'Checked in guest JERWIN LABANDO from kiosk', '2026-03-08 22:40:11', '2026-03-08 22:40:11'),
(154, 1, 9, 'Check In from Kiosk', 'Checked in guest Saudi Pangilamin from kiosk', '2026-03-08 22:41:04', '2026-03-08 22:41:04'),
(155, 1, 2, 'Create User', 'Created user EDDIE SENAJON', '2026-03-08 22:41:07', '2026-03-08 22:41:07'),
(156, 1, 9, 'Check In from Kiosk', 'Checked in guest KRISTINE GARCIA from kiosk', '2026-03-08 22:41:49', '2026-03-08 22:41:49'),
(157, 1, 2, 'Create User', 'Created user CHRIS BARAN', '2026-03-08 22:42:32', '2026-03-08 22:42:32'),
(158, 1, 9, 'Check In from Kiosk', 'Checked in guest TRIXIE from kiosk', '2026-03-08 22:43:25', '2026-03-08 22:43:25'),
(159, 1, 2, 'Create User', 'Created user MOISES TUAZON', '2026-03-08 22:43:49', '2026-03-08 22:43:49'),
(160, 1, 2, 'Add Inventory', 'Added inventory for menu FOUR SEASON', '2026-03-08 22:44:10', '2026-03-08 22:44:10'),
(161, 1, 2, 'Add Inventory', 'Added inventory for menu MANGO NECTAR', '2026-03-08 22:44:21', '2026-03-08 22:44:21'),
(162, 1, 2, 'Add Inventory', 'Added inventory for menu PINEORANGE', '2026-03-08 22:44:44', '2026-03-08 22:44:44'),
(163, 1, 2, 'Add Inventory', 'Added inventory for menu PINEAPPLE', '2026-03-08 22:44:54', '2026-03-08 22:44:54'),
(164, 1, 9, 'Check In from Kiosk', 'Checked in guest Melson sanchez from kiosk', '2026-03-08 22:45:03', '2026-03-08 22:45:03'),
(165, 1, 9, 'Check In from Kiosk', 'Checked in guest Melson from kiosk', '2026-03-08 22:45:11', '2026-03-08 22:45:11'),
(166, 1, 2, 'Add Inventory', 'Added inventory for menu FIT N RIGHT', '2026-03-08 22:45:11', '2026-03-08 22:45:11'),
(167, 1, 2, 'Create User', 'Created user REY SUMIL', '2026-03-08 22:46:03', '2026-03-08 22:46:03'),
(168, 1, 2, 'Add Inventory', 'Added inventory for menu COBRA', '2026-03-08 22:46:41', '2026-03-08 22:46:41'),
(169, 1, 2, 'Create User', 'Created user RICKY MENDEZEBAL', '2026-03-08 22:47:23', '2026-03-08 22:47:23'),
(170, 1, 2, 'Add Inventory', 'Added inventory for menu CALI', '2026-03-08 22:47:29', '2026-03-08 22:47:29'),
(171, 1, 2, 'Update Roomboy Designation', 'Updated roomboy designation for RAMILL MILLADO', '2026-03-08 22:47:47', '2026-03-08 22:47:47'),
(172, 1, 2, 'Update Roomboy Designation', 'Updated roomboy designation for EDDIE SENAJON', '2026-03-08 22:48:00', '2026-03-08 22:48:00'),
(173, 1, 2, 'Update Roomboy Designation', 'Updated roomboy designation for CHRIS BARAN', '2026-03-08 22:48:14', '2026-03-08 22:48:14'),
(174, 1, 2, 'Update Roomboy Designation', 'Updated roomboy designation for MOISES TUAZON', '2026-03-08 22:48:28', '2026-03-08 22:48:28'),
(175, 1, 2, 'Update Roomboy Designation', 'Updated roomboy designation for REY SUMIL', '2026-03-08 22:48:43', '2026-03-08 22:48:43'),
(176, 1, 2, 'Update Roomboy Designation', 'Updated roomboy designation for RICKY MENDEZEBAL', '2026-03-08 22:49:02', '2026-03-08 22:49:02'),
(177, 1, 9, 'Check In from Kiosk', 'Checked in guest MARY LOU PALMA from kiosk', '2026-03-08 22:58:28', '2026-03-08 22:58:28'),
(178, 1, 9, 'Check In from Kiosk', 'Checked in guest PETER ORTIZ JR. from kiosk', '2026-03-08 22:59:16', '2026-03-08 22:59:16'),
(179, 1, 9, 'Check In from Kiosk', 'Checked in guest Jenny munez from kiosk', '2026-03-08 22:59:59', '2026-03-08 22:59:59'),
(180, 1, 9, 'Add Food and Beverages', 'Added new food and beverages of ₱45 for guest Jenny munez', '2026-03-08 23:00:34', '2026-03-08 23:00:34'),
(181, 1, 9, 'Payment', 'Payment of ₱45 for guest Jenny munez', '2026-03-08 23:00:44', '2026-03-08 23:00:44'),
(182, 1, 2, 'Create Frontdesk', 'Created frontdesk JENEATH LECIAS', '2026-03-08 23:07:11', '2026-03-08 23:07:11'),
(183, 1, 2, 'Create User', 'Created user JENEATH LECIAS', '2026-03-08 23:07:11', '2026-03-08 23:07:11'),
(184, 1, 9, 'Check In from Kiosk', 'Checked in guest Jhialel rudy from kiosk', '2026-03-08 23:07:43', '2026-03-08 23:07:43'),
(185, 1, 2, 'Create Frontdesk', 'Created frontdesk RUBY GOLD', '2026-03-08 23:08:44', '2026-03-08 23:08:44'),
(186, 1, 2, 'Create User', 'Created user RUBY GOLD', '2026-03-08 23:08:44', '2026-03-08 23:08:44'),
(187, 1, 2, 'Create Frontdesk', 'Created frontdesk KATHLEEN DREW', '2026-03-08 23:10:11', '2026-03-08 23:10:11'),
(188, 1, 2, 'Create User', 'Created user KATHLEEN DREW', '2026-03-08 23:10:11', '2026-03-08 23:10:11'),
(189, 1, 2, 'Create Frontdesk', 'Created frontdesk SEANNE KARYLLE', '2026-03-08 23:12:02', '2026-03-08 23:12:02'),
(190, 1, 2, 'Create User', 'Created user SEANNE KARYLLE', '2026-03-08 23:12:02', '2026-03-08 23:12:02'),
(191, 1, 2, 'Create Frontdesk', 'Created frontdesk JINKY OBAG', '2026-03-08 23:13:37', '2026-03-08 23:13:37'),
(192, 1, 2, 'Create User', 'Created user JINKY OBAG', '2026-03-08 23:13:37', '2026-03-08 23:13:37'),
(193, 1, 9, 'Check In from Kiosk', 'Checked in guest MONICA NICOLAS from kiosk', '2026-03-08 23:21:14', '2026-03-08 23:21:14'),
(194, 1, 9, 'Check In from Kiosk', 'Checked in guest JOY CADUSALE from kiosk', '2026-03-08 23:22:09', '2026-03-08 23:22:09'),
(195, 1, 9, 'Room Transfer', 'Guest REX PENIALOSA transferred from Room #153 to Room #131', '2026-03-08 23:30:14', '2026-03-08 23:30:14'),
(196, 1, 9, 'Check In from Kiosk', 'Checked in guest ELIJAH LANAQUE from kiosk', '2026-03-08 23:32:12', '2026-03-08 23:32:12'),
(197, 1, 2, 'Update Room', 'Updated room 260', '2026-03-08 23:37:33', '2026-03-08 23:37:33'),
(198, 1, 2, 'Update Room', 'Updated room 266', '2026-03-08 23:37:56', '2026-03-08 23:37:56'),
(199, 1, 2, 'Update Room', 'Updated room 153', '2026-03-08 23:38:12', '2026-03-08 23:38:12'),
(200, 1, 2, 'Update Room', 'Updated room 206', '2026-03-08 23:39:29', '2026-03-08 23:39:29'),
(201, 1, 2, 'Update Room', 'Updated room 268', '2026-03-08 23:39:44', '2026-03-08 23:39:44'),
(202, 1, 2, 'Update Room', 'Updated room 256', '2026-03-08 23:40:04', '2026-03-08 23:40:04'),
(203, 1, 2, 'Update Room', 'Updated room 28', '2026-03-08 23:41:20', '2026-03-08 23:41:20'),
(204, 1, 9, 'Check In from Kiosk', 'Checked in guest Jayson padilla from kiosk', '2026-03-08 23:42:26', '2026-03-08 23:42:26'),
(205, 1, 9, 'Check In from Kiosk', 'Checked in guest JHUAY MASALON from kiosk', '2026-03-08 23:44:18', '2026-03-08 23:44:18'),
(206, 1, 9, 'Check In from Kiosk', 'Checked in guest RANDY LADICA from kiosk', '2026-03-08 23:46:02', '2026-03-08 23:46:02'),
(207, 1, 2, 'Update Room', 'Updated room 28', '2026-03-08 23:46:39', '2026-03-08 23:46:39'),
(208, 1, 9, 'Room Transfer', 'Guest Brylle hugo transferred from Room #14 to Room #28', '2026-03-08 23:46:56', '2026-03-08 23:46:56'),
(209, 1, 2, 'Update Room', 'Updated room 14', '2026-03-08 23:47:22', '2026-03-08 23:47:22'),
(210, 1, 9, 'Check In from Kiosk', 'Checked in guest Genard from kiosk', '2026-03-08 23:53:45', '2026-03-08 23:53:45'),
(211, 1, 9, 'Check In from Kiosk', 'Checked in guest May jsne dela cruz from kiosk', '2026-03-08 23:57:35', '2026-03-08 23:57:35'),
(212, 1, 9, 'Check In from Kiosk', 'Checked in guest Yarie Jasmin from kiosk', '2026-03-08 23:58:11', '2026-03-08 23:58:11'),
(213, 1, 9, 'Add Amenities', 'Added new amenities of ₱20 for guest Kairo sarte', '2026-03-09 00:05:56', '2026-03-09 00:05:56'),
(214, 1, 9, 'Payment', 'Payment of ₱20 for guest Kairo sarte', '2026-03-09 00:06:06', '2026-03-09 00:06:06'),
(215, 1, 9, 'Check In from Kiosk', 'Checked in guest Frean Boluso from kiosk', '2026-03-09 00:12:11', '2026-03-09 00:12:11'),
(216, 1, 9, 'Check In from Kiosk', 'Checked in guest Dawn sinangote from kiosk', '2026-03-09 00:14:57', '2026-03-09 00:14:57'),
(217, 1, 9, 'Check In from Kiosk', 'Checked in guest Klent john Tomabini from kiosk', '2026-03-09 00:16:53', '2026-03-09 00:16:53'),
(218, 1, 9, 'Check In from Kiosk', 'Checked in guest Reynalyn bate from kiosk', '2026-03-09 00:26:31', '2026-03-09 00:26:31'),
(219, 1, 9, 'Check In from Kiosk', 'Checked in guest Josephine frial from kiosk', '2026-03-09 00:26:57', '2026-03-09 00:26:57'),
(220, 1, 9, 'Check In from Kiosk', 'Checked in guest Niel Del rosario from kiosk', '2026-03-09 00:28:22', '2026-03-09 00:28:22'),
(221, 1, 2, 'Update Room', 'Updated room 287', '2026-03-09 00:28:25', '2026-03-09 00:28:25'),
(222, 1, 9, 'Add Food and Beverages', 'Added new food and beverages of ₱6 for guest Niel Del rosario', '2026-03-09 00:32:40', '2026-03-09 00:32:40'),
(223, 1, 9, 'Payment with Deposit', 'Payment of ₱6 with deposit for guest Niel Del rosario', '2026-03-09 00:32:45', '2026-03-09 00:32:45'),
(224, 1, 9, 'Check In from Kiosk', 'Checked in guest Jhay Dolores from kiosk', '2026-03-09 01:03:52', '2026-03-09 01:03:52'),
(225, 1, 9, 'Check In from Kiosk', 'Checked in guest Justin from kiosk', '2026-03-09 01:11:20', '2026-03-09 01:11:20'),
(226, 1, 9, 'Room Transfer', 'Guest Justin transferred from Room #2 to Room #272', '2026-03-09 01:19:28', '2026-03-09 01:19:28'),
(227, 1, 2, 'Update Room', 'Updated room 2', '2026-03-09 01:20:17', '2026-03-09 01:20:17'),
(228, 1, 9, 'Check In from Kiosk', 'Checked in guest GRACE from kiosk', '2026-03-09 01:22:10', '2026-03-09 01:22:10'),
(229, 1, 9, 'Check In from Kiosk', 'Checked in guest ALFREDO from kiosk', '2026-03-09 01:36:39', '2026-03-09 01:36:39'),
(230, 1, 9, 'Check In from Kiosk', 'Checked in guest Gillyn from kiosk', '2026-03-09 01:44:43', '2026-03-09 01:44:43'),
(231, 1, 9, 'Room Transfer', 'Guest Gillyn transferred from Room #294 to Room #136', '2026-03-09 01:51:51', '2026-03-09 01:51:51'),
(232, 1, 2, 'Update Room', 'Updated room 294', '2026-03-09 01:52:22', '2026-03-09 01:52:22'),
(233, 1, 9, 'Check Out', 'Checked out guest Christiene jade arabit from Room #132', '2026-03-09 01:54:09', '2026-03-09 01:54:09'),
(234, 1, 9, 'Check In from Kiosk', 'Checked in guest Chris from kiosk', '2026-03-09 02:02:14', '2026-03-09 02:02:14'),
(235, 1, 9, 'Add Extension', 'Added new extension of ₱112 for guest Winnie', '2026-03-09 02:34:21', '2026-03-09 02:34:21'),
(236, 1, 9, 'Payment', 'Payment of ₱112 for guest Winnie', '2026-03-09 02:34:41', '2026-03-09 02:34:41'),
(237, 1, 9, 'Check In from Kiosk', 'Checked in guest Gin from kiosk', '2026-03-09 02:35:30', '2026-03-09 02:35:30'),
(238, 1, 9, 'Check Out', 'Checked out guest Marvin Bacos from Room #286', '2026-03-09 02:39:40', '2026-03-09 02:39:40'),
(239, 1, 2, 'Update Room', 'Updated room 2', '2026-03-09 02:47:03', '2026-03-09 02:47:03'),
(240, 1, 9, 'Check In from Kiosk', 'Checked in guest ALEXIS C MENIORIA from kiosk', '2026-03-09 02:49:26', '2026-03-09 02:49:26'),
(241, 1, 9, 'Check Out', 'Checked out guest Paul delfinado from Room #72', '2026-03-09 02:51:55', '2026-03-09 02:51:55'),
(242, 1, 9, 'Check In from Kiosk', 'Checked in guest KENT MIKO LAGRMA from kiosk', '2026-03-09 02:56:04', '2026-03-09 02:56:04'),
(243, 1, 9, 'Check In from Kiosk', 'Checked in guest Roljohn laquinario from kiosk', '2026-03-09 02:59:56', '2026-03-09 02:59:56'),
(244, 1, 9, 'Check In from Kiosk', 'Checked in guest JIERRY ANNE from kiosk', '2026-03-09 03:01:19', '2026-03-09 03:01:19'),
(245, 1, 9, 'Check Out', 'Checked out guest Luigi garzon from Room #120', '2026-03-09 03:04:23', '2026-03-09 03:04:23'),
(246, 1, 9, 'Check In from Kiosk', 'Checked in guest Ynna from kiosk', '2026-03-09 03:39:04', '2026-03-09 03:39:04'),
(247, 1, 9, 'Add Deposit', 'Added new deposit of ₱8 for guest Charito Cantor', '2026-03-09 03:43:41', '2026-03-09 03:43:41'),
(248, 1, 9, 'Deduct Deposit', 'Deducted deposit of ₱8 for guest Charito Cantor', '2026-03-09 03:43:57', '2026-03-09 03:43:57'),
(249, 1, 9, 'Check In from Kiosk', 'Checked in guest Jezrel from kiosk', '2026-03-09 03:48:04', '2026-03-09 03:48:04'),
(250, 1, 9, 'Check Out', 'Checked out guest MONICA NICOLAS from Room #10', '2026-03-09 03:48:27', '2026-03-09 03:48:27'),
(251, 1, 9, 'Check Out', 'Checked out guest JOY CADUSALE from Room #21', '2026-03-09 03:48:36', '2026-03-09 03:48:36'),
(252, 1, 9, 'Check In from Kiosk', 'Checked in guest Pakito from kiosk', '2026-03-09 03:48:51', '2026-03-09 03:48:51'),
(253, 1, 9, 'Check In from Kiosk', 'Checked in guest John paul from kiosk', '2026-03-09 03:49:00', '2026-03-09 03:49:00'),
(254, 1, 9, 'Check Out', 'Checked out guest Albert rosario from Room #155', '2026-03-09 03:53:57', '2026-03-09 03:53:57'),
(255, 1, 9, 'Check In from Kiosk', 'Checked in guest ALI from kiosk', '2026-03-09 03:58:44', '2026-03-09 03:58:44'),
(256, 1, 9, 'Check In from Kiosk', 'Checked in guest SALAMA from kiosk', '2026-03-09 04:00:08', '2026-03-09 04:00:08'),
(257, 1, 9, 'Check In from Kiosk', 'Checked in guest Earl diaz from kiosk', '2026-03-09 04:09:57', '2026-03-09 04:09:57'),
(258, 1, 9, 'Add Extension', 'Added new extension of ₱112 for guest Melson sanchez', '2026-03-09 04:10:26', '2026-03-09 04:10:26'),
(259, 1, 9, 'Payment', 'Payment of ₱112 for guest Melson sanchez', '2026-03-09 04:10:43', '2026-03-09 04:10:43'),
(260, 1, 9, 'Add Extension', 'Added new extension of ₱112 for guest Melson', '2026-03-09 04:11:03', '2026-03-09 04:11:03'),
(261, 1, 9, 'Payment', 'Payment of ₱112 for guest Melson', '2026-03-09 04:11:15', '2026-03-09 04:11:15'),
(262, 1, 9, 'Check In from Kiosk', 'Checked in guest Vince dagoc from kiosk', '2026-03-09 04:12:30', '2026-03-09 04:12:30'),
(263, 1, 9, 'Check Out', 'Checked out guest JERWIN LABANDO from Room #157', '2026-03-09 04:12:55', '2026-03-09 04:12:55'),
(264, 1, 9, 'Check Out', 'Checked out guest KRISTINE GARCIA from Room #66', '2026-03-09 04:21:16', '2026-03-09 04:21:16'),
(265, 1, 9, 'Check In from Kiosk', 'Checked in guest RAMZES MANGCOT from kiosk', '2026-03-09 04:32:59', '2026-03-09 04:32:59'),
(266, 1, 9, 'Check Out', 'Checked out guest PETER ORTIZ JR. from Room #162', '2026-03-09 04:37:07', '2026-03-09 04:37:07'),
(267, 1, 9, 'Check Out', 'Checked out guest JHUAY MASALON from Room #78', '2026-03-09 04:50:44', '2026-03-09 04:50:44'),
(268, 1, 9, 'Add Damage Charges', 'Added new damage charges of ₱100 for guest ALI', '2026-03-09 04:51:55', '2026-03-09 04:51:55'),
(269, 1, 9, 'Add Damage Charges', 'Added new damage charges of ₱100 for guest ALI', '2026-03-09 04:54:02', '2026-03-09 04:54:02'),
(270, 1, 9, 'Payment', 'Payment of ₱100 for guest ALI', '2026-03-09 04:54:21', '2026-03-09 04:54:21'),
(271, 1, 9, 'Payment', 'Payment of ₱100 for guest ALI', '2026-03-09 04:54:33', '2026-03-09 04:54:33'),
(272, 1, 9, 'Add Damage Charges', 'Added new damage charges of ₱100 for guest ALI', '2026-03-09 04:55:07', '2026-03-09 04:55:07'),
(273, 1, 9, 'Payment', 'Payment of ₱200 for guest ALI', '2026-03-09 04:55:22', '2026-03-09 04:55:22'),
(274, 1, 9, 'Check Out', 'Checked out guest Robert Satonero from Room #1', '2026-03-09 04:56:27', '2026-03-09 04:56:27'),
(275, 1, 9, 'Check Out', 'Checked out guest MARY LOU PALMA from Room #161', '2026-03-09 04:57:10', '2026-03-09 04:57:10'),
(276, 1, 9, 'Check Out', 'Checked out guest ALI from Room #278', '2026-03-09 04:57:55', '2026-03-09 04:57:55'),
(277, 1, 9, 'Check Out', 'Checked out guest Frean Boluso from Room #202', '2026-03-09 04:58:26', '2026-03-09 04:58:26'),
(278, 1, 9, 'Check Out', 'Checked out guest Angel from Room #79', '2026-03-09 04:59:15', '2026-03-09 04:59:15'),
(279, 1, 9, 'Check Out', 'Checked out guest Justin from Room #272', '2026-03-09 05:01:32', '2026-03-09 05:01:32'),
(280, 1, 9, 'Check Out', 'Checked out guest Rachel jucom from Room #3', '2026-03-09 05:03:24', '2026-03-09 05:03:24'),
(281, 1, 9, 'Check Out', 'Checked out guest Genard from Room #163', '2026-03-09 05:05:20', '2026-03-09 05:05:20'),
(282, 1, 9, 'Check In from Kiosk', 'Checked in guest Atilla Alcazaren from kiosk', '2026-03-09 05:07:25', '2026-03-09 05:07:25'),
(283, 1, 9, 'Check Out', 'Checked out guest Ronald ortega from Room #6', '2026-03-09 05:18:25', '2026-03-09 05:18:25'),
(284, 1, 9, 'Check Out', 'Checked out guest RANDY LADICA from Room #158', '2026-03-09 05:18:57', '2026-03-09 05:18:57'),
(285, 1, 2, 'Create Requestable Item', 'Created requestable item for TOOTHBRUSH', '2026-03-09 05:20:12', '2026-03-09 05:20:12'),
(286, 1, 2, 'Create Requestable Item', 'Created requestable item for SHAVER', '2026-03-09 05:20:28', '2026-03-09 05:20:28'),
(287, 1, 2, 'Create Requestable Item', 'Created requestable item for TOOTHPASTE', '2026-03-09 05:20:56', '2026-03-09 05:20:56'),
(288, 1, 9, 'Check Out', 'Checked out guest Jhialel rudy from Room #124', '2026-03-09 05:21:57', '2026-03-09 05:21:57'),
(289, 1, 2, 'Create Requestable Item', 'Created requestable item for CONDITIONER', '2026-03-09 05:24:05', '2026-03-09 05:24:05'),
(290, 1, 2, 'Create Requestable Item', 'Created requestable item for MODESS', '2026-03-09 05:24:46', '2026-03-09 05:24:46'),
(291, 1, 2, 'Create Requestable Item', 'Created requestable item for SOAP', '2026-03-09 05:25:00', '2026-03-09 05:25:00'),
(292, 1, 2, 'Create Requestable Item', 'Created requestable item for SHAMPOO', '2026-03-09 05:25:14', '2026-03-09 05:25:14'),
(293, 1, 9, 'Check Out', 'Checked out guest Jayson padilla from Room #209', '2026-03-09 05:25:47', '2026-03-09 05:25:47'),
(294, 1, 9, 'Check Out', 'Checked out guest ELIJAH LANAQUE from Room #252', '2026-03-09 05:32:38', '2026-03-09 05:32:38'),
(295, 1, 9, 'Check Out', 'Checked out guest REX PENIALOSA from Room #131', '2026-03-09 05:34:52', '2026-03-09 05:34:52'),
(296, 1, 9, 'Check Out', 'Checked out guest KENT MIKO LAGRMA from Room #2', '2026-03-09 05:35:53', '2026-03-09 05:35:53'),
(297, 1, 9, 'Check In from Kiosk', 'Checked in guest Gerald Mendoza from kiosk', '2026-03-09 05:42:14', '2026-03-09 05:42:14'),
(298, 1, 9, 'Add Extension', 'Added new extension of ₱112 for guest Reynalyn bate', '2026-03-09 05:45:56', '2026-03-09 05:45:56'),
(299, 1, 9, 'Payment', 'Payment of ₱112 for guest Reynalyn bate', '2026-03-09 05:46:13', '2026-03-09 05:46:13'),
(300, 1, 9, 'Check Out', 'Checked out guest JOSELITO CUEVAS from Room #152', '2026-03-09 05:52:43', '2026-03-09 05:52:43'),
(301, 1, 9, 'Check Out', 'Checked out guest Melson from Room #5E', '2026-03-09 05:56:02', '2026-03-09 05:56:02'),
(302, 1, 2, 'Create Damage Charges', 'Created damage charges for BLOOD STAIN EXTRA LARGE', '2026-03-09 05:58:01', '2026-03-09 05:58:01'),
(303, 1, 9, 'Check Out', 'Checked out guest Melson sanchez from Room #19', '2026-03-09 05:58:46', '2026-03-09 05:58:46'),
(304, 1, 9, 'Check Out', 'Checked out guest Levi samonte from Room #4C', '2026-03-09 06:03:25', '2026-03-09 06:03:25'),
(305, 1, 9, 'Check Out', 'Checked out guest Rahima samad from Room #251', '2026-03-09 06:03:56', '2026-03-09 06:03:56'),
(306, 1, 9, 'Check Out', 'Checked out guest Jeamae esmael from Room #205', '2026-03-09 06:04:32', '2026-03-09 06:04:32'),
(307, 1, 9, 'Check Out', 'Checked out guest Sergio Seroyla from Room #7', '2026-03-09 06:08:29', '2026-03-09 06:08:29'),
(308, 1, 9, 'Check Out', 'Checked out guest Edison bonilla from Room #9', '2026-03-09 06:08:53', '2026-03-09 06:08:53'),
(309, 1, 9, 'Check Out', 'Checked out guest Klent john Tomabini from Room #170', '2026-03-09 06:09:55', '2026-03-09 06:09:55'),
(310, 1, 9, 'Check Out', 'Checked out guest Kairo sarte from Room #127', '2026-03-09 06:14:53', '2026-03-09 06:14:53'),
(311, 1, 9, 'Add Extension', 'Added new extension of ₱112 for guest May jsne dela cruz', '2026-03-09 06:16:07', '2026-03-09 06:16:07'),
(312, 1, 9, 'Payment', 'Payment of ₱112. for guest May jsne dela cruz', '2026-03-09 06:16:18', '2026-03-09 06:16:18'),
(313, 1, 9, 'Check In from Kiosk', 'Checked in guest Timothy arnaiz from kiosk', '2026-03-09 06:17:42', '2026-03-09 06:17:42'),
(314, 1, 9, 'Check Out', 'Checked out guest Efren embrado from Room #3F', '2026-03-09 06:25:51', '2026-03-09 06:25:51');



INSERT INTO `branches` (`id`, `name`, `address`, `autorization_code`, `old_autorization`, `extension_time_reset`, `initial_deposit`, `discount_enabled`, `discount_amount`, `kiosk_time_limit`, `created_at`, `updated_at`) VALUES
(1, 'ALMA RESIDENCES GENSAN', 'Brgy. 1, Gensan, South Cotabato', '12345', NULL, 24, '200.00', 1, '50.00', 30, '2026-03-08 20:01:58', '2026-03-08 20:51:44');


INSERT INTO `cash_drawers` (`id`, `branch_id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Drawer 1', 1, '2026-03-08 20:01:59', '2026-03-08 20:03:41');
INSERT INTO `cash_drawers` (`id`, `branch_id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 1, 'Drawer 2', 0, '2026-03-08 20:01:59', '2026-03-08 20:01:59');


INSERT INTO `cash_on_drawers` (`id`, `branch_id`, `frontdesk_id`, `cash_drawer_id`, `amount`, `deduction`, `transaction_date`, `transaction_type`, `shift`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:05:49', '2026-03-08 20:05:49');
INSERT INTO `cash_on_drawers` (`id`, `branch_id`, `frontdesk_id`, `cash_drawer_id`, `amount`, `deduction`, `transaction_date`, `transaction_type`, `shift`, `created_at`, `updated_at`) VALUES
(2, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:05:49', '2026-03-08 20:05:49');
INSERT INTO `cash_on_drawers` (`id`, `branch_id`, `frontdesk_id`, `cash_drawer_id`, `amount`, `deduction`, `transaction_date`, `transaction_type`, `shift`, `created_at`, `updated_at`) VALUES
(3, 1, 2, 1, '672.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:11:33', '2026-03-08 20:11:33');
INSERT INTO `cash_on_drawers` (`id`, `branch_id`, `frontdesk_id`, `cash_drawer_id`, `amount`, `deduction`, `transaction_date`, `transaction_type`, `shift`, `created_at`, `updated_at`) VALUES
(4, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:11:33', '2026-03-08 20:11:33'),
(5, 1, 2, 1, '3.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:11:33', '2026-03-08 20:11:33'),
(6, 1, 2, 1, '336.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:14:57', '2026-03-08 20:14:57'),
(7, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:14:57', '2026-03-08 20:14:57'),
(8, 1, 2, 1, '14.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:14:57', '2026-03-08 20:14:57'),
(9, 1, 2, 1, '336.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:23:04', '2026-03-08 20:23:04'),
(10, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:23:04', '2026-03-08 20:23:04'),
(11, 1, 2, 1, '14.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:23:04', '2026-03-08 20:23:04'),
(12, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:24:16', '2026-03-08 20:24:16'),
(13, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:24:16', '2026-03-08 20:24:16'),
(14, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:24:16', '2026-03-08 20:24:16'),
(15, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:24:48', '2026-03-08 20:24:48'),
(16, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:24:48', '2026-03-08 20:24:48'),
(17, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:24:48', '2026-03-08 20:24:48'),
(18, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:28:17', '2026-03-08 20:28:17'),
(19, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:28:17', '2026-03-08 20:28:17'),
(20, 1, 2, 1, '6.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:28:17', '2026-03-08 20:28:17'),
(21, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:30:40', '2026-03-08 20:30:40'),
(22, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:30:40', '2026-03-08 20:30:40'),
(23, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:30:40', '2026-03-08 20:30:40'),
(24, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:31:17', '2026-03-08 20:31:17'),
(25, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:31:17', '2026-03-08 20:31:17'),
(26, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:31:17', '2026-03-08 20:31:17'),
(27, 1, 2, 1, '616.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:32:05', '2026-03-08 20:32:05'),
(28, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:32:05', '2026-03-08 20:32:05'),
(29, 1, 2, 1, '184.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:32:05', '2026-03-08 20:32:05'),
(30, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:36:12', '2026-03-08 20:36:12'),
(31, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:36:12', '2026-03-08 20:36:12'),
(32, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:36:12', '2026-03-08 20:36:12'),
(33, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:46:55', '2026-03-08 20:46:55'),
(34, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:46:55', '2026-03-08 20:46:55'),
(35, 1, 2, 1, '408.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:46:55', '2026-03-08 20:46:55'),
(36, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:50:17', '2026-03-08 20:50:17'),
(37, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:50:17', '2026-03-08 20:50:17'),
(38, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:50:17', '2026-03-08 20:50:17'),
(39, 1, 2, 1, '616.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 20:50:45', '2026-03-08 20:50:45'),
(40, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 20:50:45', '2026-03-08 20:50:45'),
(41, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:01:09', '2026-03-08 21:01:09'),
(42, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:01:09', '2026-03-08 21:01:09'),
(43, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:02:16', '2026-03-08 21:02:16'),
(44, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:02:16', '2026-03-08 21:02:16'),
(45, 1, 2, 1, '408.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:02:16', '2026-03-08 21:02:16'),
(46, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:04:26', '2026-03-08 21:04:26'),
(47, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:04:26', '2026-03-08 21:04:26'),
(48, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:04:26', '2026-03-08 21:04:26'),
(49, 1, 2, 1, '45.00', '0.00', '2026-03-08', 'Payment from Food and Beverages', 'PM', '2026-03-08 21:05:41', '2026-03-08 21:05:41'),
(50, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:07:23', '2026-03-08 21:07:23'),
(51, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:07:23', '2026-03-08 21:07:23'),
(52, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:09:41', '2026-03-08 21:09:41'),
(53, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:09:41', '2026-03-08 21:09:41'),
(54, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:09:41', '2026-03-08 21:09:41'),
(55, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:10:38', '2026-03-08 21:10:38'),
(56, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:10:38', '2026-03-08 21:10:38'),
(57, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:10:38', '2026-03-08 21:10:38'),
(58, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:20:13', '2026-03-08 21:20:13'),
(59, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:20:13', '2026-03-08 21:20:13'),
(60, 1, 2, 1, '3.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:20:13', '2026-03-08 21:20:13'),
(61, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:24:27', '2026-03-08 21:24:27'),
(62, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:24:27', '2026-03-08 21:24:27'),
(63, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:24:27', '2026-03-08 21:24:27'),
(64, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:25:00', '2026-03-08 21:25:00'),
(65, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:25:00', '2026-03-08 21:25:00'),
(66, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:25:45', '2026-03-08 21:25:45'),
(67, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:25:45', '2026-03-08 21:25:45'),
(68, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:30:12', '2026-03-08 21:30:12'),
(69, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:30:12', '2026-03-08 21:30:12'),
(70, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:32:28', '2026-03-08 21:32:28'),
(71, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:32:28', '2026-03-08 21:32:28'),
(72, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:32:28', '2026-03-08 21:32:28'),
(73, 1, 2, 1, '616.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:33:18', '2026-03-08 21:33:18'),
(74, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:33:18', '2026-03-08 21:33:18'),
(75, 1, 2, 1, '184.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:33:18', '2026-03-08 21:33:18'),
(76, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:34:20', '2026-03-08 21:34:20'),
(77, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:34:20', '2026-03-08 21:34:20'),
(78, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:34:20', '2026-03-08 21:34:20'),
(79, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:35:18', '2026-03-08 21:35:18'),
(80, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:35:18', '2026-03-08 21:35:18'),
(81, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:35:18', '2026-03-08 21:35:18'),
(82, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:36:45', '2026-03-08 21:36:45'),
(83, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:36:45', '2026-03-08 21:36:45'),
(84, 1, 2, 1, '0.00', '55.00', '2026-03-08', 'Payment from deposit - Food and Beverages', 'PM', '2026-03-08 21:41:03', '2026-03-08 21:41:03'),
(85, 1, 2, 1, '0.00', '6.00', '2026-03-08', 'Payment from deposit - Food and Beverages', 'PM', '2026-03-08 21:41:56', '2026-03-08 21:41:56'),
(86, 1, 2, 1, '3.00', '0.00', '2026-03-08', 'Payment from Food and Beverages', 'PM', '2026-03-08 21:44:50', '2026-03-08 21:44:50'),
(87, 1, 2, 1, '2.00', '0.00', '2026-03-08', 'Payment from Food and Beverages', 'PM', '2026-03-08 21:45:13', '2026-03-08 21:45:13'),
(88, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:46:36', '2026-03-08 21:46:36'),
(89, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:46:36', '2026-03-08 21:46:36'),
(90, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:46:36', '2026-03-08 21:46:36'),
(91, 1, 2, 1, '448.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:49:21', '2026-03-08 21:49:21'),
(92, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:49:21', '2026-03-08 21:49:21'),
(93, 1, 2, 1, '2.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:49:21', '2026-03-08 21:49:21'),
(94, 1, 2, 1, '448.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:50:38', '2026-03-08 21:50:38'),
(95, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:50:38', '2026-03-08 21:50:38'),
(96, 1, 2, 1, '352.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:50:38', '2026-03-08 21:50:38'),
(97, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:51:35', '2026-03-08 21:51:35'),
(98, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:51:35', '2026-03-08 21:51:35'),
(99, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:55:36', '2026-03-08 21:55:36'),
(100, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:55:36', '2026-03-08 21:55:36'),
(101, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:55:36', '2026-03-08 21:55:36'),
(102, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 21:58:45', '2026-03-08 21:58:45'),
(103, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:58:45', '2026-03-08 21:58:45'),
(104, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 21:58:45', '2026-03-08 21:58:45'),
(105, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:02:57', '2026-03-08 22:02:57'),
(106, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:02:57', '2026-03-08 22:02:57'),
(107, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:02:57', '2026-03-08 22:02:57'),
(108, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:06:05', '2026-03-08 22:06:05'),
(109, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:06:05', '2026-03-08 22:06:05'),
(110, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:09:36', '2026-03-08 22:09:36'),
(111, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:09:36', '2026-03-08 22:09:36'),
(112, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:09:36', '2026-03-08 22:09:36'),
(113, 1, 2, 1, '0.00', '20.00', '2026-03-08', 'Payment from deposit - Amenities', 'PM', '2026-03-08 22:24:25', '2026-03-08 22:24:25'),
(114, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:29:20', '2026-03-08 22:29:20'),
(115, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:29:20', '2026-03-08 22:29:20'),
(116, 1, 2, 1, '408.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:29:20', '2026-03-08 22:29:20'),
(117, 1, 2, 1, '0.00', '78.00', '2026-03-08', 'Payment from deposit - Food and Beverages', 'PM', '2026-03-08 22:32:14', '2026-03-08 22:32:14'),
(118, 1, 2, 1, '0.00', '15.00', '2026-03-08', 'Payment from deposit - Food and Beverages', 'PM', '2026-03-08 22:32:39', '2026-03-08 22:32:39'),
(119, 1, 2, 1, '0.00', '12.00', '2026-03-08', 'Payment from deposit - Guest Check In', 'PM', '2026-03-08 22:33:28', '2026-03-08 22:33:28'),
(120, 1, 2, 1, '0.00', '15.00', '2026-03-08', 'Payment from deposit - Guest Check In', 'PM', '2026-03-08 22:34:39', '2026-03-08 22:34:39'),
(121, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:36:57', '2026-03-08 22:36:57'),
(122, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:36:57', '2026-03-08 22:36:57'),
(123, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:37:28', '2026-03-08 22:37:28'),
(124, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:37:28', '2026-03-08 22:37:28'),
(125, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:40:11', '2026-03-08 22:40:11'),
(126, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:40:11', '2026-03-08 22:40:11'),
(127, 1, 2, 1, '336.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:41:04', '2026-03-08 22:41:04'),
(128, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:41:04', '2026-03-08 22:41:04'),
(129, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:41:49', '2026-03-08 22:41:49'),
(130, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:41:49', '2026-03-08 22:41:49'),
(131, 1, 2, 1, '616.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:43:25', '2026-03-08 22:43:25'),
(132, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:43:25', '2026-03-08 22:43:25'),
(133, 1, 2, 1, '133.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:43:25', '2026-03-08 22:43:25'),
(134, 1, 2, 1, '336.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:45:03', '2026-03-08 22:45:03'),
(135, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:45:03', '2026-03-08 22:45:03'),
(136, 1, 2, 1, '224.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:45:11', '2026-03-08 22:45:11'),
(137, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:45:11', '2026-03-08 22:45:11'),
(138, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:58:28', '2026-03-08 22:58:28'),
(139, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:58:28', '2026-03-08 22:58:28'),
(140, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:58:28', '2026-03-08 22:58:28'),
(141, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:59:16', '2026-03-08 22:59:16'),
(142, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:59:16', '2026-03-08 22:59:16'),
(143, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 22:59:59', '2026-03-08 22:59:59'),
(144, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:59:59', '2026-03-08 22:59:59'),
(145, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 22:59:59', '2026-03-08 22:59:59'),
(146, 1, 2, 1, '45.00', '0.00', '2026-03-08', 'Payment from Food and Beverages', 'PM', '2026-03-08 23:00:44', '2026-03-08 23:00:44'),
(147, 1, 2, 1, '224.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 23:07:43', '2026-03-08 23:07:43'),
(148, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 23:07:43', '2026-03-08 23:07:43'),
(149, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 23:21:14', '2026-03-08 23:21:14'),
(150, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 23:21:14', '2026-03-08 23:21:14'),
(151, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 23:22:09', '2026-03-08 23:22:09'),
(152, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 23:22:09', '2026-03-08 23:22:09'),
(153, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 23:32:12', '2026-03-08 23:32:12'),
(154, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 23:32:12', '2026-03-08 23:32:12'),
(155, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 23:42:26', '2026-03-08 23:42:26'),
(156, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 23:42:26', '2026-03-08 23:42:26'),
(157, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 23:44:18', '2026-03-08 23:44:18'),
(158, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 23:44:18', '2026-03-08 23:44:18'),
(159, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 23:46:02', '2026-03-08 23:46:02'),
(160, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 23:46:02', '2026-03-08 23:46:02'),
(161, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 23:53:45', '2026-03-08 23:53:45'),
(162, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 23:53:45', '2026-03-08 23:53:45'),
(163, 1, 2, 1, '280.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 23:57:35', '2026-03-08 23:57:35'),
(164, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 23:57:35', '2026-03-08 23:57:35'),
(165, 1, 2, 1, '392.00', '0.00', '2026-03-08', 'check-in', 'PM', '2026-03-08 23:58:11', '2026-03-08 23:58:11'),
(166, 1, 2, 1, '200.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 23:58:11', '2026-03-08 23:58:11'),
(167, 1, 2, 1, '8.00', '0.00', '2026-03-08', 'deposit', 'PM', '2026-03-08 23:58:11', '2026-03-08 23:58:11'),
(168, 1, 2, 1, '20.00', '0.00', '2026-03-09', 'Payment from Amenities', 'PM', '2026-03-09 00:06:06', '2026-03-09 00:06:06'),
(169, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 00:12:11', '2026-03-09 00:12:11'),
(170, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 00:12:11', '2026-03-09 00:12:11'),
(171, 1, 2, 1, '392.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 00:14:57', '2026-03-09 00:14:57'),
(172, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 00:14:57', '2026-03-09 00:14:57'),
(173, 1, 2, 1, '8.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 00:14:57', '2026-03-09 00:14:57'),
(174, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 00:16:53', '2026-03-09 00:16:53');
INSERT INTO `cash_on_drawers` (`id`, `branch_id`, `frontdesk_id`, `cash_drawer_id`, `amount`, `deduction`, `transaction_date`, `transaction_type`, `shift`, `created_at`, `updated_at`) VALUES
(175, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 00:16:53', '2026-03-09 00:16:53');
INSERT INTO `cash_on_drawers` (`id`, `branch_id`, `frontdesk_id`, `cash_drawer_id`, `amount`, `deduction`, `transaction_date`, `transaction_type`, `shift`, `created_at`, `updated_at`) VALUES
(176, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 00:26:31', '2026-03-09 00:26:31');
INSERT INTO `cash_on_drawers` (`id`, `branch_id`, `frontdesk_id`, `cash_drawer_id`, `amount`, `deduction`, `transaction_date`, `transaction_type`, `shift`, `created_at`, `updated_at`) VALUES
(177, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 00:26:31', '2026-03-09 00:26:31'),
(178, 1, 2, 1, '392.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 00:26:56', '2026-03-09 00:26:56'),
(179, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 00:26:57', '2026-03-09 00:26:57'),
(180, 1, 2, 1, '8.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 00:26:57', '2026-03-09 00:26:57'),
(181, 1, 2, 1, '392.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 00:28:22', '2026-03-09 00:28:22'),
(182, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 00:28:22', '2026-03-09 00:28:22'),
(183, 1, 2, 1, '8.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 00:28:22', '2026-03-09 00:28:22'),
(184, 1, 2, 1, '0.00', '6.00', '2026-03-09', 'Payment from deposit - Food and Beverages', 'PM', '2026-03-09 00:32:45', '2026-03-09 00:32:45'),
(185, 1, 2, 1, '392.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 01:03:52', '2026-03-09 01:03:52'),
(186, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 01:03:52', '2026-03-09 01:03:52'),
(187, 1, 2, 1, '8.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 01:03:52', '2026-03-09 01:03:52'),
(188, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 01:11:20', '2026-03-09 01:11:20'),
(189, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 01:11:20', '2026-03-09 01:11:20'),
(190, 1, 2, 1, '392.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 01:22:10', '2026-03-09 01:22:10'),
(191, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 01:22:10', '2026-03-09 01:22:10'),
(192, 1, 2, 1, '8.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 01:22:10', '2026-03-09 01:22:10'),
(193, 1, 2, 1, '392.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 01:36:39', '2026-03-09 01:36:39'),
(194, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 01:36:39', '2026-03-09 01:36:39'),
(195, 1, 2, 1, '8.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 01:36:39', '2026-03-09 01:36:39'),
(196, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 01:44:43', '2026-03-09 01:44:43'),
(197, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 01:44:43', '2026-03-09 01:44:43'),
(198, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 02:02:14', '2026-03-09 02:02:14'),
(199, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 02:02:14', '2026-03-09 02:02:14'),
(200, 1, 2, 1, '112.00', '0.00', '2026-03-09', 'Payment from Extension', 'PM', '2026-03-09 02:34:41', '2026-03-09 02:34:41'),
(201, 1, 2, 1, '616.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 02:35:29', '2026-03-09 02:35:29'),
(202, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 02:35:30', '2026-03-09 02:35:30'),
(203, 1, 2, 1, '68.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 02:35:30', '2026-03-09 02:35:30'),
(204, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 02:49:25', '2026-03-09 02:49:25'),
(205, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 02:49:25', '2026-03-09 02:49:25'),
(206, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 02:56:04', '2026-03-09 02:56:04'),
(207, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 02:56:04', '2026-03-09 02:56:04'),
(208, 1, 2, 1, '392.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 02:59:56', '2026-03-09 02:59:56'),
(209, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 02:59:56', '2026-03-09 02:59:56'),
(210, 1, 2, 1, '8.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 02:59:56', '2026-03-09 02:59:56'),
(211, 1, 2, 1, '392.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 03:01:19', '2026-03-09 03:01:19'),
(212, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 03:01:19', '2026-03-09 03:01:19'),
(213, 1, 2, 1, '8.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 03:01:19', '2026-03-09 03:01:19'),
(214, 1, 2, 1, '336.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 03:39:04', '2026-03-09 03:39:04'),
(215, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 03:39:04', '2026-03-09 03:39:04'),
(216, 1, 2, 1, '464.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 03:39:04', '2026-03-09 03:39:04'),
(217, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 03:48:04', '2026-03-09 03:48:04'),
(218, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 03:48:04', '2026-03-09 03:48:04'),
(219, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 03:48:51', '2026-03-09 03:48:51'),
(220, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 03:48:51', '2026-03-09 03:48:51'),
(221, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 03:49:00', '2026-03-09 03:49:00'),
(222, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 03:49:00', '2026-03-09 03:49:00'),
(223, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 03:58:44', '2026-03-09 03:58:44'),
(224, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 03:58:44', '2026-03-09 03:58:44'),
(225, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 04:00:08', '2026-03-09 04:00:08'),
(226, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 04:00:08', '2026-03-09 04:00:08'),
(227, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 04:09:57', '2026-03-09 04:09:57'),
(228, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 04:09:57', '2026-03-09 04:09:57'),
(229, 1, 2, 1, '112.00', '0.00', '2026-03-09', 'Payment from Extension', 'PM', '2026-03-09 04:10:43', '2026-03-09 04:10:43'),
(230, 1, 2, 1, '112.00', '0.00', '2026-03-09', 'Payment from Extension', 'PM', '2026-03-09 04:11:15', '2026-03-09 04:11:15'),
(231, 1, 2, 1, '616.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 04:12:30', '2026-03-09 04:12:30'),
(232, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 04:12:30', '2026-03-09 04:12:30'),
(233, 1, 2, 1, '184.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 04:12:30', '2026-03-09 04:12:30'),
(234, 1, 2, 1, '280.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 04:32:59', '2026-03-09 04:32:59'),
(235, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 04:32:59', '2026-03-09 04:32:59'),
(236, 1, 2, 1, '100.00', '0.00', '2026-03-09', 'Payment from Damage Charges', 'PM', '2026-03-09 04:54:21', '2026-03-09 04:54:21'),
(237, 1, 2, 1, '100.00', '0.00', '2026-03-09', 'Payment from Damage Charges', 'PM', '2026-03-09 04:54:33', '2026-03-09 04:54:33'),
(238, 1, 2, 1, '100.00', '0.00', '2026-03-09', 'Payment from Damage Charges', 'PM', '2026-03-09 04:55:22', '2026-03-09 04:55:22'),
(239, 1, 2, 1, '224.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 05:07:25', '2026-03-09 05:07:25'),
(240, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 05:07:25', '2026-03-09 05:07:25'),
(241, 1, 2, 1, '336.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 05:42:14', '2026-03-09 05:42:14'),
(242, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 05:42:14', '2026-03-09 05:42:14'),
(243, 1, 2, 1, '464.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 05:42:14', '2026-03-09 05:42:14'),
(244, 1, 2, 1, '112.00', '0.00', '2026-03-09', 'Payment from Extension', 'PM', '2026-03-09 05:46:13', '2026-03-09 05:46:13'),
(245, 1, 2, 1, '112.00', '0.00', '2026-03-09', 'Payment from Extension', 'PM', '2026-03-09 06:16:18', '2026-03-09 06:16:18'),
(246, 1, 2, 1, '392.00', '0.00', '2026-03-09', 'check-in', 'PM', '2026-03-09 06:17:42', '2026-03-09 06:17:42'),
(247, 1, 2, 1, '200.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 06:17:42', '2026-03-09 06:17:42'),
(248, 1, 2, 1, '8.00', '0.00', '2026-03-09', 'deposit', 'PM', '2026-03-09 06:17:42', '2026-03-09 06:17:42');

INSERT INTO `check_out_guest_reports` (`id`, `room_id`, `checkin_details_id`, `shift_date`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(1, 97, 34, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 01:54:09', '2026-03-09 01:54:09');
INSERT INTO `check_out_guest_reports` (`id`, `room_id`, `checkin_details_id`, `shift_date`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(2, 216, 24, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 02:39:40', '2026-03-09 02:39:40');
INSERT INTO `check_out_guest_reports` (`id`, `room_id`, `checkin_details_id`, `shift_date`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(3, 55, 30, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 02:51:55', '2026-03-09 02:51:55');
INSERT INTO `check_out_guest_reports` (`id`, `room_id`, `checkin_details_id`, `shift_date`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(4, 85, 23, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 03:04:23', '2026-03-09 03:04:23'),
(5, 10, 53, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 03:48:27', '2026-03-09 03:48:27'),
(6, 20, 54, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 03:48:36', '2026-03-09 03:48:36'),
(7, 110, 38, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 03:53:57', '2026-03-09 03:53:57'),
(8, 112, 43, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:12:55', '2026-03-09 04:12:55'),
(9, 49, 45, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:21:16', '2026-03-09 04:21:16'),
(10, 117, 50, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:37:07', '2026-03-09 04:37:07'),
(11, 61, 57, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:50:44', '2026-03-09 04:50:44'),
(12, 1, 7, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:56:27', '2026-03-09 04:56:27'),
(13, 116, 49, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:57:10', '2026-03-09 04:57:10'),
(14, 208, 83, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:57:55', '2026-03-09 04:57:55'),
(15, 136, 62, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:58:26', '2026-03-09 04:58:26'),
(16, 62, 19, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:59:15', '2026-03-09 04:59:15'),
(17, 202, 69, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:01:32', '2026-03-09 05:01:32'),
(18, 3, 11, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:03:24', '2026-03-09 05:03:24'),
(19, 118, 59, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:05:20', '2026-03-09 05:05:20'),
(20, 6, 39, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:18:25', '2026-03-09 05:18:25'),
(21, 113, 58, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:18:57', '2026-03-09 05:18:57'),
(22, 89, 52, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:21:57', '2026-03-09 05:21:57'),
(23, 143, 56, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:25:47', '2026-03-09 05:25:47'),
(24, 175, 55, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:32:38', '2026-03-09 05:32:38'),
(25, 96, 42, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:34:52', '2026-03-09 05:34:52'),
(26, 2, 76, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:35:53', '2026-03-09 05:35:53'),
(27, 107, 41, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:52:43', '2026-03-09 05:52:43'),
(28, 229, 48, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:56:02', '2026-03-09 05:56:02'),
(29, 18, 47, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:58:46', '2026-03-09 05:58:46'),
(30, 178, 3, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 06:03:25', '2026-03-09 06:03:25'),
(31, 174, 26, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 06:03:56', '2026-03-09 06:03:56'),
(32, 139, 28, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 06:04:32', '2026-03-09 06:04:32'),
(33, 7, 37, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 06:08:29', '2026-03-09 06:08:29'),
(34, 9, 36, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 06:08:53', '2026-03-09 06:08:53'),
(35, 125, 64, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 06:09:55', '2026-03-09 06:09:55'),
(36, 92, 17, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 06:14:53', '2026-03-09 06:14:53'),
(37, 132, 4, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 06:25:51', '2026-03-09 06:25:51');

INSERT INTO `checkin_details` (`id`, `guest_id`, `frontdesk_id`, `type_id`, `room_id`, `rate_id`, `static_room_amount`, `static_amount`, `hours_stayed`, `total_deposit`, `total_deduction`, `check_in_at`, `check_out_at`, `is_check_out`, `is_long_stay`, `number_of_hours`, `next_extension_is_original`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 2, 84, 5, '392.00', 592, 12, 200, 0, '2026-03-08 20:05:49', '2026-03-09 08:05:49', 0, 0, 12, 0, '2026-03-08 20:05:49', '2026-03-08 20:05:49');
INSERT INTO `checkin_details` (`id`, `guest_id`, `frontdesk_id`, `type_id`, `room_id`, `rate_id`, `static_room_amount`, `static_amount`, `hours_stayed`, `total_deposit`, `total_deduction`, `check_in_at`, `check_out_at`, `is_check_out`, `is_long_stay`, `number_of_hours`, `next_extension_is_original`, `created_at`, `updated_at`) VALUES
(2, 2, 2, 3, 14, 9, '672.00', 872, 24, 203, 0, '2026-03-08 20:11:33', '2026-03-09 20:11:33', 0, 0, 0, 1, '2026-03-08 20:11:33', '2026-03-08 20:11:33');
INSERT INTO `checkin_details` (`id`, `guest_id`, `frontdesk_id`, `type_id`, `room_id`, `rate_id`, `static_room_amount`, `static_amount`, `hours_stayed`, `total_deposit`, `total_deduction`, `check_in_at`, `check_out_at`, `is_check_out`, `is_long_stay`, `number_of_hours`, `next_extension_is_original`, `created_at`, `updated_at`) VALUES
(3, 3, 2, 1, 178, 2, '336.00', 536, 12, 214, 0, '2026-03-08 20:14:57', '2026-03-09 06:03:25', 1, 0, 12, 0, '2026-03-08 20:14:57', '2026-03-09 06:03:25');
INSERT INTO `checkin_details` (`id`, `guest_id`, `frontdesk_id`, `type_id`, `room_id`, `rate_id`, `static_room_amount`, `static_amount`, `hours_stayed`, `total_deposit`, `total_deduction`, `check_in_at`, `check_out_at`, `is_check_out`, `is_long_stay`, `number_of_hours`, `next_extension_is_original`, `created_at`, `updated_at`) VALUES
(4, 4, 2, 1, 132, 2, '336.00', 536, 12, 214, 0, '2026-03-08 20:23:04', '2026-03-09 06:25:51', 1, 0, 12, 0, '2026-03-08 20:23:04', '2026-03-09 06:25:51'),
(5, 5, 2, 2, 167, 5, '392.00', 592, 12, 208, 0, '2026-03-08 20:24:16', '2026-03-09 08:24:16', 0, 0, 12, 0, '2026-03-08 20:24:16', '2026-03-08 20:24:16'),
(6, 6, 2, 2, 43, 5, '392.00', 592, 12, 208, 0, '2026-03-08 20:24:48', '2026-03-09 08:24:48', 0, 0, 12, 0, '2026-03-08 20:24:48', '2026-03-08 20:24:48'),
(7, 7, 2, 2, 1, 5, '392.00', 592, 12, 206, 0, '2026-03-08 20:28:17', '2026-03-09 04:56:27', 1, 0, 12, 0, '2026-03-08 20:28:17', '2026-03-09 04:56:27'),
(8, 8, 2, 2, 19, 5, '392.00', 592, 12, 208, 0, '2026-03-08 20:30:40', '2026-03-09 08:30:40', 0, 0, 12, 0, '2026-03-08 20:30:40', '2026-03-08 20:30:40'),
(9, 9, 2, 2, 12, 5, '392.00', 592, 12, 208, 0, '2026-03-08 20:31:17', '2026-03-09 08:31:17', 0, 0, 12, 0, '2026-03-08 20:31:17', '2026-03-08 20:31:17'),
(10, 10, 2, 2, 58, 6, '616.00', 816, 24, 384, 0, '2026-03-08 20:32:05', '2026-03-09 20:32:05', 0, 0, 0, 1, '2026-03-08 20:32:05', '2026-03-08 20:32:05'),
(11, 11, 2, 2, 3, 5, '392.00', 592, 12, 208, 0, '2026-03-08 20:36:12', '2026-03-09 05:03:24', 1, 0, 12, 0, '2026-03-08 20:36:12', '2026-03-09 05:03:24'),
(12, 14, 2, 2, 8, 5, '392.00', 592, 12, 608, 55, '2026-03-08 20:46:55', '2026-03-09 08:46:55', 0, 0, 12, 0, '2026-03-08 20:46:55', '2026-03-08 21:41:03'),
(13, 15, 2, 2, 162, 5, '392.00', 592, 12, 208, 0, '2026-03-08 20:50:17', '2026-03-09 08:50:17', 0, 0, 12, 0, '2026-03-08 20:50:17', '2026-03-08 20:50:17'),
(14, 13, 2, 2, 121, 6, '616.00', 816, 24, 200, 0, '2026-03-08 20:50:45', '2026-03-09 20:50:45', 0, 0, 0, 1, '2026-03-08 20:50:45', '2026-03-08 20:50:45'),
(15, 16, 2, 2, 17, 5, '392.00', 542, 12, 208, 8, '2026-03-08 21:01:09', '2026-03-09 09:01:09', 0, 0, 12, 0, '2026-03-08 21:01:09', '2026-03-09 03:43:57'),
(16, 17, 2, 2, 209, 5, '392.00', 592, 12, 608, 0, '2026-03-08 21:02:16', '2026-03-09 09:02:16', 0, 0, 12, 0, '2026-03-08 21:02:16', '2026-03-08 21:02:16'),
(17, 18, 2, 2, 92, 5, '392.00', 592, 12, 208, 6, '2026-03-08 21:04:26', '2026-03-09 06:14:53', 1, 0, 12, 0, '2026-03-08 21:04:26', '2026-03-09 06:14:53'),
(18, 19, 2, 2, 34, 5, '392.00', 592, 12, 208, 0, '2026-03-08 21:07:23', '2026-03-09 09:07:23', 0, 0, 12, 0, '2026-03-08 21:07:23', '2026-03-08 21:07:44'),
(19, 20, 2, 2, 62, 5, '392.00', 592, 12, 208, 0, '2026-03-08 21:09:41', '2026-03-09 04:59:15', 1, 0, 12, 0, '2026-03-08 21:09:41', '2026-03-09 04:59:15'),
(20, 21, 2, 2, 70, 5, '392.00', 592, 12, 208, 0, '2026-03-08 21:10:38', '2026-03-09 09:10:38', 0, 0, 12, 0, '2026-03-08 21:10:38', '2026-03-08 21:10:38'),
(21, 22, 2, 2, 83, 5, '392.00', 592, 12, 203, 0, '2026-03-08 21:20:13', '2026-03-09 09:20:13', 0, 0, 12, 0, '2026-03-08 21:20:13', '2026-03-08 21:20:13'),
(22, 23, 2, 2, 75, 5, '392.00', 592, 12, 208, 0, '2026-03-08 21:24:27', '2026-03-09 09:24:27', 0, 0, 12, 0, '2026-03-08 21:24:27', '2026-03-08 21:24:27'),
(23, 24, 2, 2, 85, 4, '280.00', 480, 6, 200, 0, '2026-03-08 21:25:00', '2026-03-09 03:04:23', 1, 0, 6, 0, '2026-03-08 21:25:00', '2026-03-09 03:04:23'),
(24, 25, 2, 2, 216, 4, '280.00', 480, 6, 200, 0, '2026-03-08 21:25:45', '2026-03-09 02:39:40', 1, 0, 6, 0, '2026-03-08 21:25:45', '2026-03-09 02:39:40'),
(25, 26, 2, 2, 120, 4, '280.00', 480, 6, 200, 0, '2026-03-08 21:30:12', '2026-03-09 09:30:12', 0, 0, 12, 0, '2026-03-08 21:30:12', '2026-03-09 02:34:21'),
(26, 27, 2, 2, 174, 5, '392.00', 592, 12, 208, 0, '2026-03-08 21:32:28', '2026-03-09 06:03:56', 1, 0, 12, 0, '2026-03-08 21:32:28', '2026-03-09 06:03:56'),
(27, 28, 2, 2, 27, 6, '616.00', 816, 24, 384, 20, '2026-03-08 21:33:18', '2026-03-09 21:33:18', 0, 0, 0, 1, '2026-03-08 21:33:18', '2026-03-08 23:46:56'),
(28, 29, 2, 2, 139, 5, '392.00', 592, 12, 208, 0, '2026-03-08 21:34:20', '2026-03-09 06:04:32', 1, 0, 12, 0, '2026-03-08 21:34:20', '2026-03-09 06:04:32'),
(29, 30, 2, 2, 103, 5, '392.00', 592, 12, 208, 0, '2026-03-08 21:35:18', '2026-03-09 09:35:18', 0, 0, 12, 0, '2026-03-08 21:35:18', '2026-03-08 21:35:18'),
(30, 31, 2, 2, 55, 4, '280.00', 480, 6, 200, 0, '2026-03-08 21:36:45', '2026-03-09 02:51:55', 1, 0, 6, 0, '2026-03-08 21:36:45', '2026-03-09 02:51:55'),
(31, 32, 2, 2, 32, 5, '392.00', 592, 12, 208, 0, '2026-03-08 21:46:36', '2026-03-09 09:46:36', 0, 0, 12, 0, '2026-03-08 21:46:36', '2026-03-08 21:46:36'),
(32, 33, 2, 3, 147, 8, '448.00', 648, 12, 202, 0, '2026-03-08 21:49:21', '2026-03-09 09:49:21', 0, 0, 12, 0, '2026-03-08 21:49:21', '2026-03-08 21:49:21'),
(33, 34, 2, 3, 53, 8, '448.00', 648, 12, 552, 0, '2026-03-08 21:50:38', '2026-03-09 09:50:38', 0, 0, 12, 0, '2026-03-08 21:50:38', '2026-03-08 21:50:38'),
(34, 35, 2, 2, 97, 4, '280.00', 480, 6, 200, 0, '2026-03-08 21:51:34', '2026-03-09 01:54:09', 1, 0, 6, 0, '2026-03-08 21:51:34', '2026-03-09 01:54:09'),
(35, 36, 2, 2, 16, 5, '392.00', 592, 12, 208, 0, '2026-03-08 21:55:36', '2026-03-09 09:55:36', 0, 0, 12, 0, '2026-03-08 21:55:36', '2026-03-08 21:55:36'),
(36, 37, 2, 2, 9, 5, '392.00', 592, 12, 208, 0, '2026-03-08 21:58:45', '2026-03-09 06:08:53', 1, 0, 12, 0, '2026-03-08 21:58:45', '2026-03-09 06:08:53'),
(37, 38, 2, 2, 7, 5, '392.00', 592, 12, 208, 0, '2026-03-08 22:02:57', '2026-03-09 06:08:29', 1, 0, 12, 0, '2026-03-08 22:02:57', '2026-03-09 06:08:29'),
(38, 39, 2, 2, 110, 4, '280.00', 480, 6, 200, 0, '2026-03-08 22:06:05', '2026-03-09 03:53:57', 1, 0, 6, 0, '2026-03-08 22:06:05', '2026-03-09 03:53:57'),
(39, 40, 2, 2, 6, 5, '392.00', 592, 12, 208, 0, '2026-03-08 22:09:36', '2026-03-09 05:18:25', 1, 0, 12, 0, '2026-03-08 22:09:36', '2026-03-09 05:18:25'),
(40, 41, 2, 2, 126, 5, '392.00', 592, 12, 608, 120, '2026-03-08 22:29:20', '2026-03-09 10:29:20', 0, 0, 12, 0, '2026-03-08 22:29:20', '2026-03-08 22:34:39'),
(41, 42, 2, 2, 107, 5, '392.00', 542, 12, 200, 0, '2026-03-08 22:36:57', '2026-03-09 05:52:43', 1, 0, 12, 0, '2026-03-08 22:36:57', '2026-03-09 05:52:43'),
(42, 43, 2, 2, 96, 5, '392.00', 592, 12, 200, 0, '2026-03-08 22:37:28', '2026-03-09 05:34:52', 1, 0, 12, 0, '2026-03-08 22:37:28', '2026-03-09 05:34:52'),
(43, 44, 2, 2, 112, 4, '280.00', 480, 6, 200, 0, '2026-03-08 22:40:11', '2026-03-09 04:12:55', 1, 0, 6, 0, '2026-03-08 22:40:11', '2026-03-09 04:12:55'),
(44, 45, 2, 1, 179, 2, '336.00', 536, 12, 200, 0, '2026-03-08 22:41:04', '2026-03-09 10:41:04', 0, 0, 12, 0, '2026-03-08 22:41:04', '2026-03-08 22:41:04'),
(45, 46, 2, 2, 49, 4, '280.00', 480, 6, 200, 0, '2026-03-08 22:41:49', '2026-03-09 04:21:16', 1, 0, 6, 0, '2026-03-08 22:41:49', '2026-03-09 04:21:16'),
(46, 47, 2, 2, 45, 6, '616.00', 816, 24, 333, 0, '2026-03-08 22:43:25', '2026-03-09 22:43:25', 0, 0, 0, 1, '2026-03-08 22:43:25', '2026-03-08 22:43:25'),
(47, 48, 2, 3, 18, 7, '336.00', 536, 6, 200, 0, '2026-03-08 22:45:03', '2026-03-09 05:58:46', 1, 0, 12, 0, '2026-03-08 22:45:03', '2026-03-09 05:58:46'),
(48, 49, 2, 1, 229, 1, '224.00', 424, 6, 200, 0, '2026-03-08 22:45:11', '2026-03-09 05:56:02', 1, 0, 12, 0, '2026-03-08 22:45:11', '2026-03-09 05:56:02'),
(49, 50, 2, 2, 116, 5, '392.00', 592, 12, 208, 0, '2026-03-08 22:58:28', '2026-03-09 04:57:10', 1, 0, 12, 0, '2026-03-08 22:58:28', '2026-03-09 04:57:10'),
(50, 52, 2, 2, 117, 4, '280.00', 480, 6, 200, 0, '2026-03-08 22:59:16', '2026-03-09 04:37:07', 1, 0, 6, 0, '2026-03-08 22:59:16', '2026-03-09 04:37:07'),
(51, 51, 2, 2, 26, 5, '392.00', 592, 12, 208, 0, '2026-03-08 22:59:59', '2026-03-09 10:59:59', 0, 0, 12, 0, '2026-03-08 22:59:59', '2026-03-08 22:59:59'),
(52, 53, 2, 1, 89, 1, '224.00', 424, 6, 200, 0, '2026-03-08 23:07:43', '2026-03-09 05:21:57', 1, 0, 6, 0, '2026-03-08 23:07:43', '2026-03-09 05:21:57'),
(53, 54, 2, 2, 10, 4, '280.00', 480, 6, 200, 0, '2026-03-08 23:21:14', '2026-03-09 03:48:27', 1, 0, 6, 0, '2026-03-08 23:21:14', '2026-03-09 03:48:27'),
(54, 55, 2, 2, 20, 4, '280.00', 480, 6, 200, 0, '2026-03-08 23:22:09', '2026-03-09 03:48:36', 1, 0, 6, 0, '2026-03-08 23:22:09', '2026-03-09 03:48:36'),
(55, 56, 2, 2, 175, 4, '280.00', 480, 6, 200, 0, '2026-03-08 23:32:12', '2026-03-09 05:32:38', 1, 0, 6, 0, '2026-03-08 23:32:12', '2026-03-09 05:32:38'),
(56, 57, 2, 2, 143, 4, '280.00', 480, 6, 200, 0, '2026-03-08 23:42:26', '2026-03-09 05:25:47', 1, 0, 6, 0, '2026-03-08 23:42:26', '2026-03-09 05:25:47'),
(57, 59, 2, 2, 61, 4, '280.00', 480, 6, 200, 0, '2026-03-08 23:44:18', '2026-03-09 04:50:44', 1, 0, 6, 0, '2026-03-08 23:44:18', '2026-03-09 04:50:44'),
(58, 60, 2, 2, 113, 4, '280.00', 480, 6, 200, 0, '2026-03-08 23:46:02', '2026-03-09 05:18:57', 1, 0, 6, 0, '2026-03-08 23:46:02', '2026-03-09 05:18:57'),
(59, 61, 2, 2, 118, 4, '280.00', 480, 6, 200, 0, '2026-03-08 23:53:45', '2026-03-09 05:05:20', 1, 0, 6, 0, '2026-03-08 23:53:45', '2026-03-09 05:05:20'),
(60, 62, 2, 2, 123, 4, '280.00', 480, 6, 200, 0, '2026-03-08 23:57:35', '2026-03-09 11:57:35', 0, 0, 12, 0, '2026-03-08 23:57:35', '2026-03-09 06:16:07'),
(61, 63, 2, 2, 211, 5, '392.00', 592, 12, 208, 0, '2026-03-08 23:58:11', '2026-03-09 11:58:11', 0, 0, 12, 0, '2026-03-08 23:58:11', '2026-03-08 23:58:11'),
(62, 64, 2, 2, 136, 4, '280.00', 480, 6, 200, 0, '2026-03-09 00:12:11', '2026-03-09 04:58:26', 1, 0, 6, 0, '2026-03-09 00:12:11', '2026-03-09 04:58:26'),
(63, 65, 2, 2, 124, 5, '392.00', 592, 12, 208, 0, '2026-03-09 00:14:57', '2026-03-09 12:14:57', 0, 0, 12, 0, '2026-03-09 00:14:57', '2026-03-09 00:14:57'),
(64, 66, 2, 2, 125, 4, '280.00', 480, 6, 200, 0, '2026-03-09 00:16:53', '2026-03-09 06:09:55', 1, 0, 6, 0, '2026-03-09 00:16:53', '2026-03-09 06:09:55'),
(65, 67, 2, 2, 185, 4, '280.00', 480, 6, 200, 0, '2026-03-09 00:26:31', '2026-03-09 12:26:31', 0, 0, 12, 0, '2026-03-09 00:26:31', '2026-03-09 05:45:56'),
(66, 68, 2, 2, 215, 5, '392.00', 592, 12, 208, 0, '2026-03-09 00:26:56', '2026-03-09 12:26:56', 0, 0, 12, 0, '2026-03-09 00:26:56', '2026-03-09 00:26:56'),
(67, 70, 2, 2, 193, 5, '392.00', 592, 12, 208, 6, '2026-03-09 00:28:22', '2026-03-09 12:28:22', 0, 0, 12, 0, '2026-03-09 00:28:22', '2026-03-09 00:32:45'),
(68, 71, 2, 2, 50, 5, '392.00', 592, 12, 208, 0, '2026-03-09 01:03:52', '2026-03-09 13:03:52', 0, 0, 12, 0, '2026-03-09 01:03:52', '2026-03-09 01:03:52'),
(69, 72, 2, 2, 202, 4, '280.00', 480, 6, 200, 0, '2026-03-09 01:11:20', '2026-03-09 05:01:32', 1, 0, 6, 0, '2026-03-09 01:11:20', '2026-03-09 05:01:32'),
(70, 73, 2, 2, 122, 5, '392.00', 592, 12, 208, 0, '2026-03-09 01:22:10', '2026-03-09 13:22:10', 0, 0, 12, 0, '2026-03-09 01:22:10', '2026-03-09 01:22:10'),
(71, 74, 2, 2, 199, 5, '392.00', 592, 12, 208, 0, '2026-03-09 01:36:39', '2026-03-09 13:36:39', 0, 0, 12, 0, '2026-03-09 01:36:39', '2026-03-09 01:36:39'),
(72, 75, 2, 2, 101, 4, '280.00', 480, 6, 200, 0, '2026-03-09 01:44:43', '2026-03-09 07:44:43', 0, 0, 6, 0, '2026-03-09 01:44:43', '2026-03-09 01:51:51'),
(73, 76, 2, 2, 200, 4, '280.00', 480, 6, 200, 0, '2026-03-09 02:02:14', '2026-03-09 08:02:14', 0, 0, 6, 0, '2026-03-09 02:02:14', '2026-03-09 02:02:14'),
(74, 77, 2, 2, 157, 6, '616.00', 816, 24, 268, 0, '2026-03-09 02:35:29', '2026-03-10 02:35:29', 0, 0, 0, 1, '2026-03-09 02:35:29', '2026-03-09 02:35:29');
INSERT INTO `checkin_details` (`id`, `guest_id`, `frontdesk_id`, `type_id`, `room_id`, `rate_id`, `static_room_amount`, `static_amount`, `hours_stayed`, `total_deposit`, `total_deduction`, `check_in_at`, `check_out_at`, `is_check_out`, `is_long_stay`, `number_of_hours`, `next_extension_is_original`, `created_at`, `updated_at`) VALUES
(75, 78, 2, 2, 97, 4, '280.00', 480, 6, 200, 0, '2026-03-09 02:49:25', '2026-03-09 08:49:25', 0, 0, 6, 0, '2026-03-09 02:49:25', '2026-03-09 02:49:25'),
(76, 79, 2, 2, 2, 4, '280.00', 480, 6, 200, 0, '2026-03-09 02:56:04', '2026-03-09 05:35:53', 1, 0, 6, 0, '2026-03-09 02:56:04', '2026-03-09 05:35:53'),
(77, 80, 2, 2, 204, 5, '392.00', 592, 12, 208, 0, '2026-03-09 02:59:55', '2026-03-09 14:59:55', 0, 0, 12, 0, '2026-03-09 02:59:55', '2026-03-09 02:59:55'),
(78, 81, 2, 2, 206, 5, '392.00', 592, 12, 208, 0, '2026-03-09 03:01:19', '2026-03-09 15:01:19', 0, 0, 12, 0, '2026-03-09 03:01:19', '2026-03-09 03:01:19'),
(79, 82, 2, 1, 225, 2, '336.00', 536, 12, 664, 0, '2026-03-09 03:39:04', '2026-03-09 15:39:04', 0, 0, 12, 0, '2026-03-09 03:39:04', '2026-03-09 03:39:04'),
(80, 83, 2, 2, 85, 4, '280.00', 480, 6, 200, 0, '2026-03-09 03:48:04', '2026-03-09 09:48:04', 0, 0, 6, 0, '2026-03-09 03:48:04', '2026-03-09 03:48:04'),
(81, 84, 2, 2, 207, 4, '280.00', 480, 6, 200, 0, '2026-03-09 03:48:51', '2026-03-09 09:48:51', 0, 0, 6, 0, '2026-03-09 03:48:51', '2026-03-09 03:48:51'),
(82, 85, 2, 2, 216, 4, '280.00', 480, 6, 200, 0, '2026-03-09 03:49:00', '2026-03-09 09:49:00', 0, 0, 6, 0, '2026-03-09 03:49:00', '2026-03-09 03:49:00'),
(83, 86, 2, 2, 208, 4, '280.00', 480, 6, 200, 0, '2026-03-09 03:58:44', '2026-03-09 04:57:55', 1, 0, 6, 0, '2026-03-09 03:58:44', '2026-03-09 04:57:55'),
(84, 87, 2, 2, 55, 4, '280.00', 480, 6, 200, 0, '2026-03-09 04:00:08', '2026-03-09 10:00:08', 0, 0, 6, 0, '2026-03-09 04:00:08', '2026-03-09 04:00:08'),
(85, 89, 2, 2, 141, 4, '280.00', 480, 6, 200, 0, '2026-03-09 04:09:57', '2026-03-09 10:09:57', 0, 0, 6, 0, '2026-03-09 04:09:57', '2026-03-09 04:09:57'),
(86, 90, 2, 2, 135, 6, '616.00', 816, 24, 384, 0, '2026-03-09 04:12:30', '2026-03-10 04:12:30', 0, 0, 0, 1, '2026-03-09 04:12:30', '2026-03-09 04:12:30'),
(87, 92, 2, 2, 10, 4, '280.00', 480, 6, 200, 0, '2026-03-09 04:32:59', '2026-03-09 10:32:59', 0, 0, 6, 0, '2026-03-09 04:32:59', '2026-03-09 04:32:59'),
(88, 93, 2, 1, 130, 1, '224.00', 424, 6, 200, 0, '2026-03-09 05:07:25', '2026-03-09 11:07:25', 0, 0, 6, 0, '2026-03-09 05:07:25', '2026-03-09 05:07:25'),
(89, 94, 2, 1, 131, 2, '336.00', 536, 12, 664, 0, '2026-03-09 05:42:14', '2026-03-09 17:42:14', 0, 0, 12, 0, '2026-03-09 05:42:14', '2026-03-09 05:42:14'),
(90, 95, 2, 2, 115, 5, '392.00', 592, 12, 208, 0, '2026-03-09 06:17:42', '2026-03-09 18:17:42', 0, 0, 12, 0, '2026-03-09 06:17:42', '2026-03-09 06:17:42');

INSERT INTO `cleaning_histories` (`id`, `user_id`, `room_id`, `floor_id`, `branch_id`, `start_time`, `end_time`, `current_assigned_floor_id`, `expected_end_time`, `cleaning_duration`, `delayed_cleaning`, `created_at`, `updated_at`) VALUES
(1, 13, 97, 3, 1, '2026-03-09 01:58:58', '2026-03-09 02:17:32', 0, '2026-03-09 04:54:09', '18', 0, '2026-03-09 02:17:32', '2026-03-09 02:17:32');
INSERT INTO `cleaning_histories` (`id`, `user_id`, `room_id`, `floor_id`, `branch_id`, `start_time`, `end_time`, `current_assigned_floor_id`, `expected_end_time`, `cleaning_duration`, `delayed_cleaning`, `created_at`, `updated_at`) VALUES
(2, 13, 55, 2, 1, '2026-03-09 02:58:32', '2026-03-09 03:13:59', 0, '2026-03-09 05:51:55', '15', 0, '2026-03-09 03:13:59', '2026-03-09 03:13:59');
INSERT INTO `cleaning_histories` (`id`, `user_id`, `room_id`, `floor_id`, `branch_id`, `start_time`, `end_time`, `current_assigned_floor_id`, `expected_end_time`, `cleaning_duration`, `delayed_cleaning`, `created_at`, `updated_at`) VALUES
(3, 14, 216, 5, 1, '2026-03-09 03:08:27', '2026-03-09 03:24:08', 0, '2026-03-09 05:39:40', '15', 0, '2026-03-09 03:24:08', '2026-03-09 03:24:08');
INSERT INTO `cleaning_histories` (`id`, `user_id`, `room_id`, `floor_id`, `branch_id`, `start_time`, `end_time`, `current_assigned_floor_id`, `expected_end_time`, `cleaning_duration`, `delayed_cleaning`, `created_at`, `updated_at`) VALUES
(4, 13, 85, 3, 1, '2026-03-09 03:14:09', '2026-03-09 03:29:28', 0, '2026-03-09 06:04:23', '15', 0, '2026-03-09 03:29:28', '2026-03-09 03:29:28'),
(5, 13, 10, 1, 1, '2026-03-09 04:03:15', '2026-03-09 04:21:33', 1, '2026-03-09 06:48:27', '18', 0, '2026-03-09 04:21:33', '2026-03-09 04:21:33'),
(6, 13, 20, 1, 1, '2026-03-09 04:21:39', '2026-03-09 04:37:32', 1, '2026-03-09 06:48:36', '15', 0, '2026-03-09 04:37:32', '2026-03-09 04:37:32'),
(7, 13, 49, 2, 1, '2026-03-09 04:37:50', '2026-03-09 04:59:20', 0, '2026-03-09 07:21:16', '21', 0, '2026-03-09 04:59:20', '2026-03-09 04:59:20'),
(8, 13, 110, 3, 1, '2026-03-09 04:59:53', '2026-03-09 05:15:02', 0, '2026-03-09 06:53:57', '15', 0, '2026-03-09 05:15:02', '2026-03-09 05:15:02'),
(9, 14, 112, 3, 1, '2026-03-09 05:02:14', '2026-03-09 05:20:19', 1, '2026-03-09 07:12:55', '18', 0, '2026-03-09 05:20:19', '2026-03-09 05:20:19'),
(10, 13, 61, 2, 1, '2026-03-09 05:15:19', '2026-03-09 05:31:19', 0, '2026-03-09 07:50:44', '16', 0, '2026-03-09 05:31:19', '2026-03-09 05:31:19'),
(11, 14, 117, 3, 1, '2026-03-09 05:20:24', '2026-03-09 05:37:29', 1, '2026-03-09 07:37:07', '17', 0, '2026-03-09 05:37:29', '2026-03-09 05:37:29'),
(12, 13, 1, 1, 1, '2026-03-09 05:31:30', '2026-03-09 05:48:05', 1, '2026-03-09 07:56:27', '16', 0, '2026-03-09 05:48:05', '2026-03-09 05:48:05'),
(13, 14, 136, 4, 1, '2026-03-09 05:38:04', '2026-03-09 05:55:16', 0, '2026-03-09 07:58:26', '17', 0, '2026-03-09 05:55:16', '2026-03-09 05:55:16'),
(14, 13, 62, 2, 1, '2026-03-09 05:48:33', '2026-03-09 06:08:56', 0, '2026-03-09 07:59:15', '20', 0, '2026-03-09 06:08:56', '2026-03-09 06:08:56'),
(15, 14, 116, 3, 1, '2026-03-09 05:55:41', '2026-03-09 06:18:07', 1, '2026-03-09 07:57:10', '22', 0, '2026-03-09 06:18:07', '2026-03-09 06:18:07'),
(16, 13, 3, 1, 1, '2026-03-09 06:09:03', '2026-03-09 06:26:46', 1, '2026-03-09 08:03:24', '17', 0, '2026-03-09 06:26:46', '2026-03-09 06:26:46');







INSERT INTO `extended_guest_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `number_of_extension`, `total_hours`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(1, 1, 120, 25, 1, 6, 'PM', 2, 'N/A', '2026-03-09 02:34:21', '2026-03-09 02:34:21');
INSERT INTO `extended_guest_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `number_of_extension`, `total_hours`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(2, 1, 18, 47, 1, 6, 'PM', 2, 'N/A', '2026-03-09 04:10:26', '2026-03-09 04:10:26');
INSERT INTO `extended_guest_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `number_of_extension`, `total_hours`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(3, 1, 229, 48, 1, 6, 'PM', 2, 'N/A', '2026-03-09 04:11:03', '2026-03-09 04:11:03');
INSERT INTO `extended_guest_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `number_of_extension`, `total_hours`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(4, 1, 185, 65, 1, 6, 'PM', 2, 'N/A', '2026-03-09 05:45:56', '2026-03-09 05:45:56'),
(5, 1, 123, 60, 1, 6, 'PM', 2, 'N/A', '2026-03-09 06:16:07', '2026-03-09 06:16:07');

INSERT INTO `extension_rates` (`id`, `branch_id`, `hour`, `amount`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 112, '2026-03-08 20:02:00', '2026-03-08 20:02:00');
INSERT INTO `extension_rates` (`id`, `branch_id`, `hour`, `amount`, `created_at`, `updated_at`) VALUES
(2, 1, 12, 224, '2026-03-08 20:02:00', '2026-03-08 20:02:00');
INSERT INTO `extension_rates` (`id`, `branch_id`, `hour`, `amount`, `created_at`, `updated_at`) VALUES
(3, 1, 18, 336, '2026-03-08 20:02:00', '2026-03-08 20:02:00');
INSERT INTO `extension_rates` (`id`, `branch_id`, `hour`, `amount`, `created_at`, `updated_at`) VALUES
(4, 1, 24, 448, '2026-03-08 20:02:00', '2026-03-08 20:02:00');



INSERT INTO `floor_user` (`id`, `user_id`, `floor_id`, `created_at`, `updated_at`) VALUES
(1, 10, 2, NULL, NULL);
INSERT INTO `floor_user` (`id`, `user_id`, `floor_id`, `created_at`, `updated_at`) VALUES
(2, 10, 3, NULL, NULL);
INSERT INTO `floor_user` (`id`, `user_id`, `floor_id`, `created_at`, `updated_at`) VALUES
(3, 10, 4, NULL, NULL);
INSERT INTO `floor_user` (`id`, `user_id`, `floor_id`, `created_at`, `updated_at`) VALUES
(4, 11, 4, NULL, NULL),
(5, 11, 5, NULL, NULL),
(6, 12, 1, NULL, NULL),
(7, 12, 2, NULL, NULL),
(8, 13, 1, NULL, NULL),
(9, 13, 2, NULL, NULL),
(10, 13, 3, NULL, NULL),
(11, 14, 3, NULL, NULL),
(12, 14, 4, NULL, NULL),
(13, 14, 5, NULL, NULL),
(14, 15, 1, NULL, NULL),
(15, 16, 2, NULL, NULL),
(16, 17, 3, NULL, NULL),
(17, 18, 4, NULL, NULL),
(18, 19, 5, NULL, NULL),
(19, 20, 1, NULL, NULL),
(20, 20, 2, NULL, NULL),
(21, 20, 3, NULL, NULL),
(22, 20, 4, NULL, NULL),
(23, 20, 5, NULL, NULL);

INSERT INTO `floors` (`id`, `branch_id`, `number`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `floors` (`id`, `branch_id`, `number`, `created_at`, `updated_at`) VALUES
(2, 1, 2, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `floors` (`id`, `branch_id`, `number`, `created_at`, `updated_at`) VALUES
(3, 1, 3, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `floors` (`id`, `branch_id`, `number`, `created_at`, `updated_at`) VALUES
(4, 1, 4, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(5, 1, 5, '2026-03-08 20:01:59', '2026-03-08 20:01:59');

INSERT INTO `frontdesk_categories` (`id`, `branch_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'Juices', '2026-03-08 20:19:11', '2026-03-08 20:19:11');
INSERT INTO `frontdesk_categories` (`id`, `branch_id`, `name`, `created_at`, `updated_at`) VALUES
(2, 1, 'JunkFoods', '2026-03-08 20:19:24', '2026-03-08 20:19:24');
INSERT INTO `frontdesk_categories` (`id`, `branch_id`, `name`, `created_at`, `updated_at`) VALUES
(3, 1, 'MINERAL WATER', '2026-03-08 20:23:13', '2026-03-08 20:23:13');
INSERT INTO `frontdesk_categories` (`id`, `branch_id`, `name`, `created_at`, `updated_at`) VALUES
(4, 1, 'SOFT DRINKS', '2026-03-08 20:24:48', '2026-03-08 20:24:48'),
(5, 1, 'TOILETRIES', '2026-03-08 20:42:25', '2026-03-08 20:42:25');

INSERT INTO `frontdesk_inventories` (`id`, `branch_id`, `frontdesk_menu_id`, `number_of_serving`, `created_at`, `updated_at`) VALUES
(1, 1, 10, 99, '2026-03-08 20:28:42', '2026-03-08 20:28:42');
INSERT INTO `frontdesk_inventories` (`id`, `branch_id`, `frontdesk_menu_id`, `number_of_serving`, `created_at`, `updated_at`) VALUES
(2, 1, 12, 100, '2026-03-08 20:40:02', '2026-03-08 20:40:02');
INSERT INTO `frontdesk_inventories` (`id`, `branch_id`, `frontdesk_menu_id`, `number_of_serving`, `created_at`, `updated_at`) VALUES
(3, 1, 6, 98, '2026-03-08 20:49:07', '2026-03-08 23:00:34');
INSERT INTO `frontdesk_inventories` (`id`, `branch_id`, `frontdesk_menu_id`, `number_of_serving`, `created_at`, `updated_at`) VALUES
(4, 1, 14, 98, '2026-03-08 21:08:21', '2026-03-08 22:32:08'),
(5, 1, 15, 100, '2026-03-08 21:08:29', '2026-03-08 21:08:29'),
(6, 1, 16, 99.5, '2026-03-08 21:08:36', '2026-03-08 22:34:32'),
(7, 1, 17, 99, '2026-03-08 21:08:44', '2026-03-08 22:32:29'),
(8, 1, 18, 100, '2026-03-08 21:09:04', '2026-03-08 21:09:04'),
(9, 1, 19, 91, '2026-03-08 21:09:14', '2026-03-09 00:32:40'),
(10, 1, 20, 99, '2026-03-08 21:10:15', '2026-03-08 21:45:08'),
(11, 1, 21, 99, '2026-03-08 21:30:16', '2026-03-08 21:40:48'),
(12, 1, 1, 8, '2026-03-08 22:44:10', '2026-03-08 22:44:10'),
(13, 1, 2, 9, '2026-03-08 22:44:21', '2026-03-08 22:44:21'),
(14, 1, 3, 8, '2026-03-08 22:44:44', '2026-03-08 22:44:44'),
(15, 1, 4, 6, '2026-03-08 22:44:54', '2026-03-08 22:44:54'),
(16, 1, 5, 8, '2026-03-08 22:45:11', '2026-03-08 22:45:11'),
(17, 1, 24, 7, '2026-03-08 22:46:41', '2026-03-08 22:46:41'),
(18, 1, 23, 8, '2026-03-08 22:47:29', '2026-03-08 22:47:29');

INSERT INTO `frontdesk_menus` (`id`, `branch_id`, `frontdesk_category_id`, `item_code`, `name`, `price`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'JUICES-001', 'FOUR SEASON', '55', NULL, '2026-03-08 20:20:03', '2026-03-08 20:20:03');
INSERT INTO `frontdesk_menus` (`id`, `branch_id`, `frontdesk_category_id`, `item_code`, `name`, `price`, `image`, `created_at`, `updated_at`) VALUES
(2, 1, 1, 'JUICES-002', 'MANGO NECTAR', '55', NULL, '2026-03-08 20:21:09', '2026-03-08 20:21:09');
INSERT INTO `frontdesk_menus` (`id`, `branch_id`, `frontdesk_category_id`, `item_code`, `name`, `price`, `image`, `created_at`, `updated_at`) VALUES
(3, 1, 1, 'JUICES-003', 'PINEORANGE', '55', NULL, '2026-03-08 20:21:52', '2026-03-08 20:21:52');
INSERT INTO `frontdesk_menus` (`id`, `branch_id`, `frontdesk_category_id`, `item_code`, `name`, `price`, `image`, `created_at`, `updated_at`) VALUES
(4, 1, 1, 'JUICES-004', 'PINEAPPLE', '55', NULL, '2026-03-08 20:22:12', '2026-03-08 20:22:12'),
(5, 1, 1, 'JUICES-005', 'FIT N RIGHT', '55', NULL, '2026-03-08 20:22:34', '2026-03-08 20:22:34'),
(6, 1, 3, 'WATER-001', '1 LITER MINERAL', '45', NULL, '2026-03-08 20:24:07', '2026-03-08 20:24:07'),
(7, 1, 3, 'WATER-002', '500ML MINERAL', '29', NULL, '2026-03-08 20:24:28', '2026-03-08 20:24:28'),
(8, 1, 4, 'SOFTDRINKS-001', 'MOUNTAIN DEW', '55', NULL, '2026-03-08 20:26:08', '2026-03-08 20:26:08'),
(9, 1, 1, 'JUICES-006', 'C2', '55', NULL, '2026-03-08 20:26:34', '2026-03-08 20:26:34'),
(10, 1, 1, 'JUICES-007', 'MINUTE MAID', '55', NULL, '2026-03-08 20:26:57', '2026-03-08 20:26:57'),
(11, 1, 4, 'SOFTDRINKS-002', '7-UP', '55', NULL, '2026-03-08 20:28:04', '2026-03-08 20:28:04'),
(12, 1, 2, 'JUNKFOODS-001', 'JUNKFOODS', '45', NULL, '2026-03-08 20:36:44', '2026-03-08 20:36:44'),
(13, 1, 4, 'SOFTDRINKS-003', 'COKE', '55', NULL, '2026-03-08 20:41:04', '2026-03-08 20:41:04'),
(14, 1, 5, 'TOILETRIES-001', 'TOOTHBRUSH', '39', NULL, '2026-03-08 20:43:26', '2026-03-08 20:43:26'),
(15, 1, 5, 'TOILETRIES-002', 'SHAVER', '39', NULL, '2026-03-08 20:43:47', '2026-03-08 20:43:47'),
(16, 1, 5, 'TOILETRIES-003', 'TOOTHPASTE', '30', NULL, '2026-03-08 20:44:06', '2026-03-08 20:44:06'),
(17, 1, 5, 'TOILETRIES-004', 'CONDITIONER', '15', NULL, '2026-03-08 20:44:31', '2026-03-08 20:44:31'),
(18, 1, 5, 'TOILETRIES-005', 'MODESS', '15', NULL, '2026-03-08 20:44:51', '2026-03-08 20:44:51'),
(19, 1, 5, 'TOILETRIES-006', 'SOAP', '3', NULL, '2026-03-08 20:45:09', '2026-03-08 20:45:09'),
(20, 1, 5, 'TOILETRIES-007', 'SHAMPOO', '2', NULL, '2026-03-08 20:45:24', '2026-03-08 20:45:24'),
(21, 1, 4, 'SOFTDRINKS-004', 'SPRITE', '55', NULL, '2026-03-08 20:46:16', '2026-03-08 20:46:16'),
(22, 1, 4, 'SOFTDRINKS-005', 'ROYAL', '55', NULL, '2026-03-08 20:46:51', '2026-03-08 20:46:51'),
(23, 1, 4, 'SOFTDRINKS-006', 'CALI', '55', NULL, '2026-03-08 20:47:18', '2026-03-08 20:47:18'),
(24, 1, 4, 'SOFTDRINKS-007', 'COBRA', '55', NULL, '2026-03-08 20:47:42', '2026-03-08 20:47:42');

INSERT INTO `frontdesks` (`id`, `branch_id`, `user_id`, `name`, `number`, `passcode`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Frontdesk', '+639000000000', '12345', '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `frontdesks` (`id`, `branch_id`, `user_id`, `name`, `number`, `passcode`, `created_at`, `updated_at`) VALUES
(2, 1, 9, 'Hannah', '+639000000000', '12345', '2026-03-08 20:03:25', '2026-03-08 20:03:25');
INSERT INTO `frontdesks` (`id`, `branch_id`, `user_id`, `name`, `number`, `passcode`, `created_at`, `updated_at`) VALUES
(3, 1, 21, 'JENEATH LECIAS', '+639000000000', '12345', '2026-03-08 23:07:11', '2026-03-08 23:07:11');
INSERT INTO `frontdesks` (`id`, `branch_id`, `user_id`, `name`, `number`, `passcode`, `created_at`, `updated_at`) VALUES
(4, 1, 22, 'RUBY GOLD', '+639000000000', '12345', '2026-03-08 23:08:44', '2026-03-08 23:08:44'),
(5, 1, 23, 'KATHLEEN DREW', '+639000000000', '12345', '2026-03-08 23:10:11', '2026-03-08 23:10:11'),
(6, 1, 24, 'SEANNE KARYLLE', '+639000000000', '12345', '2026-03-08 23:12:02', '2026-03-08 23:12:02'),
(7, 1, 25, 'JINKY OBAG', '+639000000000', '12345', '2026-03-08 23:13:37', '2026-03-08 23:13:37');

INSERT INTO `guests` (`id`, `branch_id`, `name`, `contact`, `qr_code`, `room_id`, `previous_room_id`, `rate_id`, `type_id`, `static_amount`, `is_long_stay`, `number_of_days`, `has_discount`, `discount_amount`, `has_kiosk_check_out`, `is_co`, `created_at`, `updated_at`) VALUES
(1, 1, 'Art', 'N/A', '1260001', 84, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 20:03:26', '2026-03-08 20:05:49');
INSERT INTO `guests` (`id`, `branch_id`, `name`, `contact`, `qr_code`, `room_id`, `previous_room_id`, `rate_id`, `type_id`, `static_amount`, `is_long_stay`, `number_of_days`, `has_discount`, `discount_amount`, `has_kiosk_check_out`, `is_co`, `created_at`, `updated_at`) VALUES
(2, 1, 'Joseph Maranan', '09060829813', '1260002', 14, NULL, 9, 3, 872, 0, 0, 0, '50.00', 0, 0, '2026-03-08 20:09:01', '2026-03-08 20:11:33');
INSERT INTO `guests` (`id`, `branch_id`, `name`, `contact`, `qr_code`, `room_id`, `previous_room_id`, `rate_id`, `type_id`, `static_amount`, `is_long_stay`, `number_of_days`, `has_discount`, `discount_amount`, `has_kiosk_check_out`, `is_co`, `created_at`, `updated_at`) VALUES
(3, 1, 'Levi samonte', 'N/A', '1260003', 178, NULL, 2, 1, 536, 0, 0, 0, '50.00', 1, 0, '2026-03-08 20:12:46', '2026-03-09 06:01:21');
INSERT INTO `guests` (`id`, `branch_id`, `name`, `contact`, `qr_code`, `room_id`, `previous_room_id`, `rate_id`, `type_id`, `static_amount`, `is_long_stay`, `number_of_days`, `has_discount`, `discount_amount`, `has_kiosk_check_out`, `is_co`, `created_at`, `updated_at`) VALUES
(4, 1, 'Efren embrado', 'N/A', '1260004', 132, NULL, 2, 1, 536, 0, 0, 0, '50.00', 1, 0, '2026-03-08 20:20:45', '2026-03-09 06:25:22'),
(5, 1, 'Rey rian jay palmero', 'N/A', '1260005', 167, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 20:22:17', '2026-03-08 20:24:16'),
(6, 1, 'Lebron', 'N/A', '1260006', 43, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 20:23:06', '2026-03-08 20:24:48'),
(7, 1, 'Robert Satonero', 'N/A', '1260007', 1, NULL, 5, 2, 592, 0, 0, 0, '50.00', 1, 0, '2026-03-08 20:26:46', '2026-03-09 04:50:26'),
(8, 1, 'Jay', 'N/A', '1260008', 19, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 20:27:44', '2026-03-08 20:30:40'),
(9, 1, 'Jesus Gallano Jr', 'N/A', '1260009', 12, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 20:29:17', '2026-03-08 20:31:17'),
(10, 1, 'Ma.bella Salo', 'N/A', '1260010', 58, NULL, 6, 2, 816, 0, 0, 0, '50.00', 0, 0, '2026-03-08 20:31:10', '2026-03-08 20:32:05'),
(11, 1, 'Rachel jucom', 'N/A', '1260011', 3, NULL, 5, 2, 592, 0, 0, 0, '50.00', 1, 0, '2026-03-08 20:34:08', '2026-03-09 04:59:27'),
(13, 1, 'Tsang david', 'N/A', '1260012', 121, NULL, 6, 2, 816, 0, 0, 0, '50.00', 0, 0, '2026-03-08 20:45:08', '2026-03-08 20:50:45'),
(14, 1, 'Shyra felipe', 'N/A', '1260013', 8, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 20:45:42', '2026-03-08 20:46:55'),
(15, 1, 'Don Agbulos', 'N/A', '1260014', 162, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 20:48:54', '2026-03-08 20:50:17'),
(16, 1, 'Charito Cantor', 'N/A', '1260015', 17, NULL, 5, 2, 542, 0, 0, 1, '50.00', 0, 0, '2026-03-08 20:59:46', '2026-03-08 21:01:09'),
(17, 1, 'Monica uy', 'N/A', '1260016', 209, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:00:37', '2026-03-08 21:02:16'),
(18, 1, 'Kairo sarte', 'N/A', '1260017', 92, NULL, 5, 2, 592, 0, 0, 0, '50.00', 1, 0, '2026-03-08 21:03:27', '2026-03-09 06:14:28'),
(19, 1, 'Alhaya tia', 'N/A', '1260018', 34, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:06:04', '2026-03-08 21:07:23'),
(20, 1, 'Angel', 'N/A', '1260019', 62, NULL, 5, 2, 592, 0, 0, 0, '50.00', 1, 0, '2026-03-08 21:07:04', '2026-03-09 04:48:43'),
(21, 1, 'Robert Dampan', 'N/A', '1260020', 70, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:07:54', '2026-03-08 21:10:38'),
(22, 1, 'James rolly', '09100097562', '1260021', 83, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:18:57', '2026-03-08 21:20:13'),
(23, 1, 'Juleth generale', '09159843658', '1260022', 75, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:21:22', '2026-03-08 21:24:27'),
(24, 1, 'Luigi garzon', 'N/A', '1260023', 85, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 21:21:57', '2026-03-09 03:03:15'),
(25, 1, 'Marvin Bacos', 'N/A', '1260024', 216, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 21:23:27', '2026-03-09 02:37:52'),
(26, 1, 'Winnie', 'N/A', '1260025', 120, NULL, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:28:09', '2026-03-08 21:30:12'),
(27, 1, 'Rahima samad', '09530849240', '1260026', 174, NULL, 5, 2, 592, 0, 0, 0, '50.00', 1, 0, '2026-03-08 21:31:10', '2026-03-09 05:58:58'),
(28, 1, 'Brylle hugo', 'N/A', '1260027', 27, 13, 6, 2, 816, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:31:37', '2026-03-08 23:46:56'),
(29, 1, 'Jeamae esmael', '09759370094', '1260028', 139, NULL, 5, 2, 592, 0, 0, 0, '50.00', 1, 0, '2026-03-08 21:33:02', '2026-03-09 05:58:16'),
(30, 1, 'Jay Ann', 'N/A', '1260029', 103, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:33:07', '2026-03-08 21:35:18'),
(31, 1, 'Paul delfinado', 'N/A', '1260030', 55, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 21:34:38', '2026-03-09 02:51:07'),
(32, 1, 'Ronel pino', 'N/A', '1260031', 32, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:44:10', '2026-03-08 21:46:36'),
(33, 1, 'Tato tandual', '09754345654', '1260032', 147, NULL, 8, 3, 648, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:47:50', '2026-03-08 21:49:21'),
(34, 1, 'Jhonna lou binangga', '09161808024', '1260033', 53, NULL, 8, 3, 648, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:48:03', '2026-03-08 21:50:38'),
(35, 1, 'Christiene jade arabit', '09536444202', '1260034', 97, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 21:49:11', '2026-03-09 01:50:54'),
(36, 1, 'Jeanny Babes  Tayo', '09166178250', '1260035', 16, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 21:54:32', '2026-03-08 21:55:36'),
(37, 1, 'Edison bonilla', '09062576157', '1260036', 9, NULL, 5, 2, 592, 0, 0, 0, '50.00', 1, 0, '2026-03-08 21:57:09', '2026-03-09 06:07:44'),
(38, 1, 'Sergio Seroyla', 'N/A', '1260037', 7, NULL, 5, 2, 592, 0, 0, 0, '50.00', 1, 0, '2026-03-08 22:01:26', '2026-03-09 06:06:22'),
(39, 1, 'Albert rosario', 'N/A', '1260038', 110, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 22:05:11', '2026-03-09 03:51:18'),
(40, 1, 'Ronald ortega', 'N/A', '1260039', 6, NULL, 5, 2, 592, 0, 0, 0, '50.00', 1, 0, '2026-03-08 22:08:10', '2026-03-09 05:15:45'),
(41, 1, 'SPARKS MAGDAYAO', '09770736104', '1260040', 126, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 22:28:24', '2026-03-08 22:29:20'),
(42, 1, 'JOSELITO CUEVAS', 'N/A', '1260041', 107, NULL, 5, 2, 542, 0, 0, 1, '50.00', 1, 0, '2026-03-08 22:33:11', '2026-03-09 05:51:17'),
(43, 1, 'REX PENIALOSA', 'N/A', '1260042', 96, 108, 5, 2, 592, 0, 0, 0, '50.00', 1, 0, '2026-03-08 22:35:36', '2026-03-09 05:33:43'),
(44, 1, 'JERWIN LABANDO', 'N/A', '1260043', 112, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 22:38:57', '2026-03-09 04:12:39'),
(45, 1, 'Saudi Pangilamin', '09051342880', '1260044', 179, NULL, 2, 1, 536, 0, 0, 0, '50.00', 0, 0, '2026-03-08 22:39:04', '2026-03-08 22:41:04'),
(46, 1, 'KRISTINE GARCIA', 'N/A', '1260045', 49, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 22:40:13', '2026-03-09 04:20:05'),
(47, 1, 'TRIXIE', '09126346068', '1260046', 45, NULL, 6, 2, 816, 0, 0, 0, '50.00', 0, 0, '2026-03-08 22:41:50', '2026-03-08 22:43:25'),
(48, 1, 'Melson sanchez', 'N/A', '1260047', 18, NULL, 7, 3, 536, 0, 0, 0, '50.00', 1, 0, '2026-03-08 22:42:14', '2026-03-09 05:55:23'),
(49, 1, 'Melson', 'N/A', '1260048', 229, NULL, 1, 1, 424, 0, 0, 0, '50.00', 1, 0, '2026-03-08 22:43:40', '2026-03-09 05:55:48'),
(50, 1, 'MARY LOU PALMA', '09063237435', '1260049', 116, NULL, 5, 2, 592, 0, 0, 0, '50.00', 1, 0, '2026-03-08 22:56:40', '2026-03-09 04:48:16'),
(51, 1, 'Jenny munez', 'N/A', '1260050', 26, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 22:57:54', '2026-03-08 22:59:59'),
(52, 1, 'PETER ORTIZ JR.', '09515054230', '1260051', 117, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 22:58:03', '2026-03-09 04:34:57'),
(53, 1, 'Jhialel rudy', 'N/A', '1260052', 89, NULL, 1, 1, 424, 0, 0, 0, '50.00', 1, 0, '2026-03-08 23:06:12', '2026-03-09 05:19:15'),
(54, 1, 'MONICA NICOLAS', 'N/A', '1260053', 10, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 23:19:57', '2026-03-09 03:46:23'),
(55, 1, 'JOY CADUSALE', 'N/A', '1260054', 20, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 23:21:46', '2026-03-09 03:46:59'),
(56, 1, 'ELIJAH LANAQUE', '09514690472', '1260055', 175, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 23:30:30', '2026-03-09 05:30:33'),
(57, 1, 'Jayson padilla', '09362427582', '1260056', 143, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 23:39:17', '2026-03-09 05:25:30'),
(59, 1, 'JHUAY MASALON', '09517804067', '1260058', 61, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 23:42:32', '2026-03-09 04:47:49'),
(60, 1, 'RANDY LADICA', 'N/A', '1260058', 113, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 23:44:28', '2026-03-09 05:18:42'),
(61, 1, 'Genard', 'N/A', '1260059', 118, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-08 23:52:55', '2026-03-09 05:03:14'),
(62, 1, 'May jsne dela cruz', 'N/A', '1260060', 123, NULL, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-08 23:56:04', '2026-03-08 23:57:35'),
(63, 1, 'Yarie Jasmin', 'N/A', '1260061', 211, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-08 23:56:58', '2026-03-08 23:58:11'),
(64, 1, 'Frean Boluso', 'N/A', '1260062', 136, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-09 00:11:11', '2026-03-09 04:56:57'),
(65, 1, 'Dawn sinangote', '09093968225', '1260063', 124, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-09 00:13:36', '2026-03-09 00:14:57'),
(66, 1, 'Klent john Tomabini', '09677283313', '1260064', 125, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-09 00:16:24', '2026-03-09 06:05:23'),
(67, 1, 'Reynalyn bate', '09069174240', '1260065', 185, NULL, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-09 00:18:27', '2026-03-09 00:26:31'),
(68, 1, 'Josephine frial', 'N/A', '1260066', 215, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-09 00:24:22', '2026-03-09 00:26:56'),
(70, 1, 'Niel Del rosario', 'N/A', '1260067', 193, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-09 00:27:59', '2026-03-09 00:28:22'),
(71, 1, 'Jhay Dolores', 'N/A', '1260068', 50, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-09 01:02:55', '2026-03-09 01:03:52'),
(72, 1, 'Justin', 'N/A', '1260069', 202, 2, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-09 01:10:40', '2026-03-09 05:01:10'),
(73, 1, 'GRACE', 'N/A', '1260070', 122, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-09 01:21:28', '2026-03-09 01:22:10'),
(74, 1, 'ALFREDO', 'N/A', '1260071', 199, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-09 01:35:35', '2026-03-09 01:36:39'),
(75, 1, 'Gillyn', '09099320501', '1260072', 101, 224, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-09 01:43:49', '2026-03-09 01:51:51'),
(76, 1, 'Chris', 'N/A', '1260073', 200, NULL, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-09 01:55:06', '2026-03-09 02:02:14'),
(77, 1, 'Gin', 'N/A', '1260074', 157, NULL, 6, 2, 816, 0, 0, 0, '50.00', 0, 0, '2026-03-09 02:33:43', '2026-03-09 02:35:29'),
(78, 1, 'ALEXIS C MENIORIA', 'N/A', '1260075', 97, NULL, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-09 02:47:02', '2026-03-09 02:49:25'),
(79, 1, 'KENT MIKO LAGRMA', 'N/A', '1260076', 2, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-09 02:55:18', '2026-03-09 05:32:19');
INSERT INTO `guests` (`id`, `branch_id`, `name`, `contact`, `qr_code`, `room_id`, `previous_room_id`, `rate_id`, `type_id`, `static_amount`, `is_long_stay`, `number_of_days`, `has_discount`, `discount_amount`, `has_kiosk_check_out`, `is_co`, `created_at`, `updated_at`) VALUES
(80, 1, 'Roljohn laquinario', '09940876848', '1260077', 204, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-09 02:58:09', '2026-03-09 02:59:55'),
(81, 1, 'JIERRY ANNE', 'N/A', '1260078', 206, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-09 03:00:18', '2026-03-09 03:01:19'),
(82, 1, 'Ynna', '09525474014', '1260079', 225, NULL, 2, 1, 536, 0, 0, 0, '50.00', 0, 0, '2026-03-09 03:37:47', '2026-03-09 03:39:04'),
(83, 1, 'Jezrel', '09203850210', '1260080', 85, NULL, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-09 03:42:28', '2026-03-09 03:48:04'),
(84, 1, 'Pakito', '09977366998', '1260081', 207, NULL, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-09 03:43:53', '2026-03-09 03:48:51'),
(85, 1, 'John paul', 'N/A', '1260082', 216, NULL, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-09 03:45:51', '2026-03-09 03:49:00'),
(86, 1, 'ALI', 'N/A', '1260083', 208, NULL, 4, 2, 480, 0, 0, 0, '50.00', 1, 0, '2026-03-09 03:57:34', '2026-03-09 04:46:02'),
(87, 1, 'SALAMA', 'N/A', '1260084', 55, NULL, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-09 03:58:47', '2026-03-09 04:00:08'),
(89, 1, 'Earl diaz', '09810859113', '1260086', 141, NULL, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-09 04:09:07', '2026-03-09 04:09:57'),
(90, 1, 'Vince dagoc', 'N/A', '1260086', 135, NULL, 6, 2, 816, 0, 0, 0, '50.00', 0, 0, '2026-03-09 04:10:31', '2026-03-09 04:12:30'),
(92, 1, 'RAMZES MANGCOT', 'N/A', '1260087', 10, NULL, 4, 2, 480, 0, 0, 0, '50.00', 0, 0, '2026-03-09 04:32:21', '2026-03-09 04:32:59'),
(93, 1, 'Atilla Alcazaren', 'N/A', '1260088', 130, NULL, 1, 1, 424, 0, 0, 0, '50.00', 0, 0, '2026-03-09 05:06:35', '2026-03-09 05:07:25'),
(94, 1, 'Gerald Mendoza', 'N/A', '1260089', 131, NULL, 2, 1, 536, 0, 0, 0, '50.00', 0, 0, '2026-03-09 05:30:40', '2026-03-09 05:42:14'),
(95, 1, 'Timothy arnaiz', '09533671600', '1260090', 115, NULL, 5, 2, 592, 0, 0, 0, '50.00', 0, 0, '2026-03-09 06:16:45', '2026-03-09 06:17:42');

INSERT INTO `hotel_items` (`id`, `branch_id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 'MAIN DOOR', 5000, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `hotel_items` (`id`, `branch_id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(2, 1, 'PURTAHAN SA C.R.', 2500, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `hotel_items` (`id`, `branch_id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(3, 1, 'SUGA SA ROOM', 150, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `hotel_items` (`id`, `branch_id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(4, 1, 'SUGA SA C.R.', 130, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(5, 1, 'SAMIN SULOD SA ROOM', 1000, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(6, 1, 'SAMIN SULOD SA C.R.', 1000, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(7, 1, 'SAMIN SA GAWAS', 1500, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(8, 1, 'SALOG SA ROOM PER TILES', 1200, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(9, 1, 'SALOG SA C.R. PER TILE', 1200, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(10, 1, 'RUG/ TRAPO SA SALOG', 40, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(11, 1, 'UNLAN', 500, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(12, 1, 'HABOL', 500, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(13, 1, 'PUNDA', 200, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(14, 1, 'PUNDA WITH MANTSA(HAIR DYE)', 500, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(15, 1, 'BEDSHEET WITH INK', 500, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(16, 1, 'BED PAD WITH INK', 800, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(17, 1, 'BED SKIRT WITH INK', 1500, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(18, 1, 'TOWEL', 300, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(19, 1, 'DOORKNOB C.R.', 350, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(20, 1, 'MAIN DOOR DOORKNOB', 500, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(21, 1, 'T.V.', 30000, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(22, 1, 'TELEPHONE', 1000, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(23, 1, 'DECODER PARA SA CABLE', 1600, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(24, 1, 'CORD SA DECODER', 100, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(25, 1, 'CHARGER SA DECODER', 400, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(26, 1, 'WIRING SA TELEPHONE', 100, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(27, 1, 'WIRINGS SA T.V.', 200, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(28, 1, 'WIRING SA DECODER', 50, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(29, 1, 'CEILING', 0, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(30, 1, 'SHOWER HEAD', 800, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(31, 1, 'SHOWER BULB', 800, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(32, 1, 'BIDET', 400, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(33, 1, 'HINGES/ TOWEL BAR', 200, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(34, 1, 'TAKLOB SA TANGKE', 1200, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(35, 1, 'TANGKE SA BOWL', 3000, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(36, 1, 'TAKLOB SA BOWL', 1000, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(37, 1, 'ILALOM SA LABABO', 0, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(38, 1, 'SINK/LABABO', 1500, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(39, 1, 'BASURAHAN', 70, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(40, 1, 'WET BED', 500, '2026-03-08 21:39:03', '2026-03-08 21:39:03'),
(41, 1, 'FLUSHER', 150, '2026-03-08 21:40:13', '2026-03-08 21:40:13'),
(42, 1, 'FAUCET SA UBOS', 400, '2026-03-08 21:40:40', '2026-03-08 21:40:40'),
(43, 1, 'FAUCET SA SINK', 500, '2026-03-08 21:41:02', '2026-03-08 21:41:02'),
(44, 1, 'SMOKING', 200, '2026-03-08 21:41:29', '2026-03-08 21:41:29'),
(45, 1, 'AIRCON COVER', 1000, '2026-03-08 21:42:04', '2026-03-08 21:42:04'),
(46, 1, 'EFFICASCENT/OIL VERY SMALL', 100, '2026-03-08 21:46:44', '2026-03-08 21:46:44'),
(47, 1, 'EFFICASCENT/OIL SMALL', 200, '2026-03-08 21:47:18', '2026-03-08 21:47:18'),
(48, 1, 'EFFICASCENT/OIL MEDIUM', 300, '2026-03-08 21:47:35', '2026-03-08 21:47:35'),
(49, 1, 'EFFICASCENT/OIL LARGE', 400, '2026-03-08 21:47:53', '2026-03-08 21:47:53'),
(50, 1, 'EFFICASCENT/OIL VERY LARGE', 500, '2026-03-08 21:48:08', '2026-03-08 21:48:08'),
(51, 1, 'BLOOD STAIN SMALL', 50, '2026-03-08 21:48:54', '2026-03-08 21:48:54'),
(52, 1, 'BLOOD STAIN LARGE', 100, '2026-03-08 21:49:14', '2026-03-08 21:49:14'),
(53, 1, 'BLOOD STAIN WORST', 500, '2026-03-08 21:49:28', '2026-03-08 21:49:28'),
(54, 1, 'VANDALISM', 500, '2026-03-08 21:49:46', '2026-03-08 21:49:46'),
(55, 1, 'BEER / LIQUOR', 200, '2026-03-08 21:50:04', '2026-03-08 21:50:04'),
(56, 1, 'DURIAN / MARANG / LANGKA', 200, '2026-03-08 21:51:03', '2026-03-08 21:51:03'),
(57, 1, 'KITCHEN UTENSIL: BOWL', 150, '2026-03-08 21:51:48', '2026-03-08 21:51:48'),
(58, 1, 'PLATE', 150, '2026-03-08 21:52:24', '2026-03-08 21:52:24'),
(59, 1, 'SAUCER', 150, '2026-03-08 21:55:24', '2026-03-08 21:55:24'),
(60, 1, 'GLASS', 150, '2026-03-08 21:58:16', '2026-03-08 21:58:16'),
(61, 1, 'COMPUTER USAGE', 20, '2026-03-08 21:59:05', '2026-03-08 21:59:05'),
(62, 1, 'LINENS, PUNDA WITH BUBBLE GUM', 500, '2026-03-08 21:59:33', '2026-03-08 21:59:33'),
(63, 1, 'STICKER(S) ', 450, '2026-03-08 22:00:39', '2026-03-08 22:00:39'),
(64, 1, 'MENU', 60, '2026-03-08 22:00:56', '2026-03-08 22:00:56'),
(65, 1, 'CLIPSAL', 200, '2026-03-08 22:01:52', '2026-03-08 22:01:52'),
(66, 1, 'KEY', 200, '2026-03-08 22:02:39', '2026-03-08 22:02:39'),
(67, 1, 'BACKOUT GUEST: DIRTY ROOM / C.R', 50, '2026-03-08 22:03:13', '2026-03-08 22:03:13'),
(68, 1, 'BACKOUT GUEST: DIRTY BEDDINGS', 100, '2026-03-08 22:03:33', '2026-03-08 22:03:33'),
(69, 1, 'REMOTE', 300, '2026-03-08 22:03:55', '2026-03-08 22:03:55'),
(70, 1, 'DOUBLE CHARGE EXTRA PERSON SINGLE', 224, '2026-03-08 22:05:45', '2026-03-08 22:05:45'),
(71, 1, 'DOUBLE CHARGE EXTRA PERSON DOUBLE', 280, '2026-03-08 22:06:00', '2026-03-08 22:06:00'),
(72, 1, 'DOUBLE CHARGE EXTRA PERSON TWIN BED', 336, '2026-03-08 22:06:24', '2026-03-08 22:06:24'),
(73, 1, 'EXTRA TOWEL', 20, '2026-03-08 22:12:59', '2026-03-08 22:12:59'),
(74, 1, 'EXTRA BLANKET', 20, '2026-03-08 22:13:16', '2026-03-08 22:13:16'),
(75, 1, 'EXTRA FITTED SHEET', 20, '2026-03-08 22:13:33', '2026-03-08 22:13:33'),
(76, 1, 'BLOOD STAIN EXTRA LARGE', 200, '2026-03-09 05:58:01', '2026-03-09 05:58:01');









INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(2, '2014_10_12_100000_create_password_resets_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
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
(76, '2026_02_11_130152_add_column_deductions_to_cash_on_drawers_table', 1),
(77, '2026_02_16_134017_add_column_checkin_detail_id_to_transactions_table', 1),
(78, '2026_02_26_085751_add_column_kiosk_time_limit_to_branches_table', 1),
(79, '2026_02_26_132707_create_transfered_guest_reports_table', 1),
(80, '2026_03_05_213433_add_column_passcode_to_frontdesks_table', 1),
(81, '2026_03_05_222809_add_column_beggining_cash_to_shift_logs_table', 1),
(82, '2026_03_05_230745_add_column_branch_id_to_expenses_table', 1),
(83, '2026_03_05_234422_add_column_description_to_shift_logs_table', 1),
(84, '2026_03_06_101236_add_column_shift_log_id_to_expenses_table', 1),
(85, '2026_03_06_111352_add_column_total_expenses_to_shift_logs_table', 1),
(86, '2026_03_08_094207_add_column_shift_log_id_to_transactions_table', 1),
(87, '2026_03_08_144754_create_remittances_table', 1),
(88, '2026_03_08_153550_add_column_total_remittance_on_shift_logs_table', 1);



INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(2, 'App\\Models\\User', 2);
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(3, 'App\\Models\\User', 3);
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(6, 'App\\Models\\User', 4),
(5, 'App\\Models\\User', 5),
(7, 'App\\Models\\User', 6),
(4, 'App\\Models\\User', 7),
(8, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 9),
(4, 'App\\Models\\User', 10),
(4, 'App\\Models\\User', 11),
(4, 'App\\Models\\User', 12),
(4, 'App\\Models\\User', 13),
(4, 'App\\Models\\User', 14),
(4, 'App\\Models\\User', 15),
(4, 'App\\Models\\User', 16),
(4, 'App\\Models\\User', 17),
(4, 'App\\Models\\User', 18),
(4, 'App\\Models\\User', 19),
(4, 'App\\Models\\User', 20),
(3, 'App\\Models\\User', 21),
(3, 'App\\Models\\User', 22),
(3, 'App\\Models\\User', 23),
(3, 'App\\Models\\User', 24),
(3, 'App\\Models\\User', 25);

INSERT INTO `new_guest_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `shift_date`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(1, 1, 84, 1, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:05:49', '2026-03-08 20:05:49');
INSERT INTO `new_guest_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `shift_date`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(2, 1, 14, 2, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:11:33', '2026-03-08 20:11:33');
INSERT INTO `new_guest_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `shift_date`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(3, 1, 178, 3, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:14:57', '2026-03-08 20:14:57');
INSERT INTO `new_guest_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `shift_date`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(4, 1, 132, 4, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:23:04', '2026-03-08 20:23:04'),
(5, 1, 167, 5, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:24:16', '2026-03-08 20:24:16'),
(6, 1, 43, 6, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:24:48', '2026-03-08 20:24:48'),
(7, 1, 1, 7, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:28:17', '2026-03-08 20:28:17'),
(8, 1, 19, 8, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:30:40', '2026-03-08 20:30:40'),
(9, 1, 12, 9, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:31:17', '2026-03-08 20:31:17'),
(10, 1, 58, 10, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:32:05', '2026-03-08 20:32:05'),
(11, 1, 3, 11, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:36:12', '2026-03-08 20:36:12'),
(12, 1, 8, 12, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:46:55', '2026-03-08 20:46:55'),
(13, 1, 162, 13, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:50:17', '2026-03-08 20:50:17'),
(14, 1, 121, 14, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 20:50:45', '2026-03-08 20:50:45'),
(15, 1, 17, 15, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:01:09', '2026-03-08 21:01:09'),
(16, 1, 209, 16, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:02:16', '2026-03-08 21:02:16'),
(17, 1, 92, 17, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:04:26', '2026-03-08 21:04:26'),
(18, 1, 34, 18, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:07:23', '2026-03-08 21:07:23'),
(19, 1, 62, 19, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:09:41', '2026-03-08 21:09:41'),
(20, 1, 70, 20, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:10:38', '2026-03-08 21:10:38'),
(21, 1, 83, 21, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:20:13', '2026-03-08 21:20:13'),
(22, 1, 75, 22, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:24:27', '2026-03-08 21:24:27'),
(23, 1, 85, 23, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:25:00', '2026-03-08 21:25:00'),
(24, 1, 216, 24, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:25:45', '2026-03-08 21:25:45'),
(25, 1, 120, 25, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:30:12', '2026-03-08 21:30:12'),
(26, 1, 174, 26, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:32:28', '2026-03-08 21:32:28'),
(27, 1, 13, 27, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:33:18', '2026-03-08 21:33:18'),
(28, 1, 139, 28, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:34:20', '2026-03-08 21:34:20'),
(29, 1, 103, 29, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:35:19', '2026-03-08 21:35:19'),
(30, 1, 55, 30, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:36:45', '2026-03-08 21:36:45'),
(31, 1, 32, 31, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:46:36', '2026-03-08 21:46:36'),
(32, 1, 147, 32, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:49:21', '2026-03-08 21:49:21'),
(33, 1, 53, 33, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:50:38', '2026-03-08 21:50:38'),
(34, 1, 97, 34, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:51:35', '2026-03-08 21:51:35'),
(35, 1, 16, 35, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:55:36', '2026-03-08 21:55:36'),
(36, 1, 9, 36, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 21:58:45', '2026-03-08 21:58:45'),
(37, 1, 7, 37, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:02:57', '2026-03-08 22:02:57'),
(38, 1, 110, 38, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:06:05', '2026-03-08 22:06:05'),
(39, 1, 6, 39, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:09:36', '2026-03-08 22:09:36'),
(40, 1, 126, 40, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:29:20', '2026-03-08 22:29:20'),
(41, 1, 107, 41, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:36:57', '2026-03-08 22:36:57'),
(42, 1, 108, 42, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:37:28', '2026-03-08 22:37:28'),
(43, 1, 112, 43, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:40:11', '2026-03-08 22:40:11'),
(44, 1, 179, 44, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:41:04', '2026-03-08 22:41:04'),
(45, 1, 49, 45, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:41:49', '2026-03-08 22:41:49'),
(46, 1, 45, 46, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:43:25', '2026-03-08 22:43:25'),
(47, 1, 18, 47, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:45:03', '2026-03-08 22:45:03'),
(48, 1, 229, 48, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:45:11', '2026-03-08 22:45:11'),
(49, 1, 116, 49, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:58:28', '2026-03-08 22:58:28'),
(50, 1, 117, 50, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:59:16', '2026-03-08 22:59:16'),
(51, 1, 26, 51, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 22:59:59', '2026-03-08 22:59:59'),
(52, 1, 89, 52, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 23:07:43', '2026-03-08 23:07:43'),
(53, 1, 10, 53, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 23:21:14', '2026-03-08 23:21:14'),
(54, 1, 20, 54, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 23:22:09', '2026-03-08 23:22:09'),
(55, 1, 175, 55, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 23:32:12', '2026-03-08 23:32:12'),
(56, 1, 143, 56, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 23:42:26', '2026-03-08 23:42:26'),
(57, 1, 61, 57, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 23:44:18', '2026-03-08 23:44:18'),
(58, 1, 113, 58, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 23:46:02', '2026-03-08 23:46:02'),
(59, 1, 118, 59, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 23:53:45', '2026-03-08 23:53:45'),
(60, 1, 123, 60, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 23:57:35', '2026-03-08 23:57:35'),
(61, 1, 211, 61, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-08 23:58:11', '2026-03-08 23:58:11'),
(62, 1, 136, 62, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 00:12:11', '2026-03-09 00:12:11'),
(63, 1, 124, 63, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 00:14:57', '2026-03-09 00:14:57'),
(64, 1, 125, 64, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 00:16:53', '2026-03-09 00:16:53'),
(65, 1, 185, 65, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 00:26:31', '2026-03-09 00:26:31'),
(66, 1, 215, 66, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 00:26:57', '2026-03-09 00:26:57'),
(67, 1, 193, 67, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 00:28:22', '2026-03-09 00:28:22'),
(68, 1, 50, 68, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 01:03:52', '2026-03-09 01:03:52'),
(69, 1, 2, 69, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 01:11:20', '2026-03-09 01:11:20'),
(70, 1, 122, 70, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 01:22:10', '2026-03-09 01:22:10'),
(71, 1, 199, 71, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 01:36:39', '2026-03-09 01:36:39'),
(72, 1, 224, 72, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 01:44:43', '2026-03-09 01:44:43'),
(73, 1, 200, 73, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 02:02:14', '2026-03-09 02:02:14');
INSERT INTO `new_guest_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `shift_date`, `shift`, `frontdesk_id`, `partner_name`, `created_at`, `updated_at`) VALUES
(74, 1, 157, 74, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 02:35:30', '2026-03-09 02:35:30'),
(75, 1, 97, 75, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 02:49:25', '2026-03-09 02:49:25'),
(76, 1, 2, 76, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 02:56:04', '2026-03-09 02:56:04'),
(77, 1, 204, 77, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 02:59:56', '2026-03-09 02:59:56'),
(78, 1, 206, 78, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 03:01:19', '2026-03-09 03:01:19'),
(79, 1, 225, 79, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 03:39:04', '2026-03-09 03:39:04'),
(80, 1, 85, 80, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 03:48:04', '2026-03-09 03:48:04'),
(81, 1, 207, 81, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 03:48:51', '2026-03-09 03:48:51'),
(82, 1, 216, 82, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 03:49:00', '2026-03-09 03:49:00'),
(83, 1, 208, 83, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 03:58:44', '2026-03-09 03:58:44'),
(84, 1, 55, 84, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:00:08', '2026-03-09 04:00:08'),
(85, 1, 141, 85, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:09:57', '2026-03-09 04:09:57'),
(86, 1, 135, 86, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:12:30', '2026-03-09 04:12:30'),
(87, 1, 10, 87, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 04:32:59', '2026-03-09 04:32:59'),
(88, 1, 130, 88, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:07:25', '2026-03-09 05:07:25'),
(89, 1, 131, 89, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 05:42:14', '2026-03-09 05:42:14'),
(90, 1, 115, 90, 'March 8, 2026', 'PM', 2, 'N/A', '2026-03-09 06:17:42', '2026-03-09 06:17:42');













INSERT INTO `rates` (`id`, `branch_id`, `staying_hour_id`, `type_id`, `amount`, `is_available`, `has_discount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 224, 1, 0, '2026-03-08 20:02:00', '2026-03-08 20:02:00');
INSERT INTO `rates` (`id`, `branch_id`, `staying_hour_id`, `type_id`, `amount`, `is_available`, `has_discount`, `created_at`, `updated_at`) VALUES
(2, 1, 2, 1, 336, 1, 0, '2026-03-08 20:02:00', '2026-03-08 20:02:00');
INSERT INTO `rates` (`id`, `branch_id`, `staying_hour_id`, `type_id`, `amount`, `is_available`, `has_discount`, `created_at`, `updated_at`) VALUES
(3, 1, 4, 1, 560, 1, 0, '2026-03-08 20:02:00', '2026-03-08 20:02:00');
INSERT INTO `rates` (`id`, `branch_id`, `staying_hour_id`, `type_id`, `amount`, `is_available`, `has_discount`, `created_at`, `updated_at`) VALUES
(4, 1, 1, 2, 280, 1, 0, '2026-03-08 20:02:00', '2026-03-08 20:02:00'),
(5, 1, 2, 2, 392, 1, 1, '2026-03-08 20:02:00', '2026-03-08 20:02:16'),
(6, 1, 4, 2, 616, 1, 1, '2026-03-08 20:02:00', '2026-03-08 20:02:21'),
(7, 1, 1, 3, 336, 1, 0, '2026-03-08 20:02:00', '2026-03-08 20:02:00'),
(8, 1, 2, 3, 448, 1, 1, '2026-03-08 20:02:00', '2026-03-08 20:02:26'),
(9, 1, 4, 3, 672, 1, 1, '2026-03-08 20:02:00', '2026-03-08 20:02:33');



INSERT INTO `requestable_items` (`id`, `branch_id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 'EXTRA PERSON WITH FREE PILLOW, BLANKET,TOWEL', 100, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `requestable_items` (`id`, `branch_id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(2, 1, 'EXTRA PILLOW', 20, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `requestable_items` (`id`, `branch_id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(3, 1, 'EXTRA TOWEL', 20, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `requestable_items` (`id`, `branch_id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(4, 1, 'EXTRA BLANKET', 20, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(5, 1, 'EXTRA FITTED SHEET', 20, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(6, 1, 'TOOTHBRUSH', 39, '2026-03-09 05:20:12', '2026-03-09 05:20:12'),
(7, 1, 'SHAVER', 39, '2026-03-09 05:20:28', '2026-03-09 05:20:28'),
(8, 1, 'TOOTHPASTE', 30, '2026-03-09 05:20:56', '2026-03-09 05:20:56'),
(9, 1, 'CONDITIONER', 15, '2026-03-09 05:24:05', '2026-03-09 05:24:05'),
(10, 1, 'MODESS', 15, '2026-03-09 05:24:46', '2026-03-09 05:24:46'),
(11, 1, 'SOAP', 3, '2026-03-09 05:25:00', '2026-03-09 05:25:00'),
(12, 1, 'SHAMPOO', 2, '2026-03-09 05:25:14', '2026-03-09 05:25:14');



INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'web', '2026-03-08 20:01:58', '2026-03-08 20:01:58');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(2, 'admin', 'web', '2026-03-08 20:01:58', '2026-03-08 20:01:58');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(3, 'frontdesk', 'web', '2026-03-08 20:01:58', '2026-03-08 20:01:58');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(4, 'roomboy', 'web', '2026-03-08 20:01:58', '2026-03-08 20:01:58'),
(5, 'kitchen', 'web', '2026-03-08 20:01:58', '2026-03-08 20:01:58'),
(6, 'kiosk', 'web', '2026-03-08 20:01:58', '2026-03-08 20:01:58'),
(7, 'back_office', 'web', '2026-03-08 20:01:58', '2026-03-08 20:01:58'),
(8, 'pub_kitchen', 'web', '2026-03-08 20:02:00', '2026-03-08 20:02:00');

INSERT INTO `room_boy_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `roomboy_id`, `cleaning_start`, `cleaning_end`, `total_hours_spent`, `interval`, `shift`, `is_cleaned`, `created_at`, `updated_at`) VALUES
(1, 1, 97, 34, 13, '2026-03-09 01:58:58', '2026-03-09 02:17:32', 19, 0, 'PM', 1, '2026-03-09 01:58:58', '2026-03-09 02:17:32');
INSERT INTO `room_boy_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `roomboy_id`, `cleaning_start`, `cleaning_end`, `total_hours_spent`, `interval`, `shift`, `is_cleaned`, `created_at`, `updated_at`) VALUES
(2, 1, 55, 30, 13, '2026-03-09 02:58:32', '2026-03-09 03:13:59', 16, 41, 'PM', 1, '2026-03-09 02:58:32', '2026-03-09 03:13:59');
INSERT INTO `room_boy_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `roomboy_id`, `cleaning_start`, `cleaning_end`, `total_hours_spent`, `interval`, `shift`, `is_cleaned`, `created_at`, `updated_at`) VALUES
(3, 1, 216, 24, 14, '2026-03-09 03:08:27', '2026-03-09 03:24:08', 16, 0, 'PM', 1, '2026-03-09 03:08:27', '2026-03-09 03:24:08');
INSERT INTO `room_boy_reports` (`id`, `branch_id`, `room_id`, `checkin_details_id`, `roomboy_id`, `cleaning_start`, `cleaning_end`, `total_hours_spent`, `interval`, `shift`, `is_cleaned`, `created_at`, `updated_at`) VALUES
(4, 1, 85, 23, 13, '2026-03-09 03:14:09', '2026-03-09 03:29:28', 16, 0, 'PM', 1, '2026-03-09 03:14:09', '2026-03-09 03:29:28'),
(5, 1, 10, 53, 13, '2026-03-09 04:03:15', '2026-03-09 04:21:33', 19, 33, 'PM', 1, '2026-03-09 04:03:15', '2026-03-09 04:21:33'),
(6, 1, 20, 54, 13, '2026-03-09 04:21:39', '2026-03-09 04:37:32', 16, 0, 'PM', 1, '2026-03-09 04:21:39', '2026-03-09 04:37:32'),
(7, 1, 49, 45, 13, '2026-03-09 04:37:50', '2026-03-09 04:59:20', 22, 0, 'PM', 1, '2026-03-09 04:37:50', '2026-03-09 04:59:20'),
(8, 1, 110, 38, 13, '2026-03-09 04:59:53', '2026-03-09 05:15:02', 16, 0, 'PM', 1, '2026-03-09 04:59:53', '2026-03-09 05:15:02'),
(9, 1, 112, 43, 14, '2026-03-09 05:02:14', '2026-03-09 05:20:19', 19, 98, 'PM', 1, '2026-03-09 05:02:14', '2026-03-09 05:20:19'),
(10, 1, 61, 57, 13, '2026-03-09 05:15:19', '2026-03-09 05:31:19', 16, 0, 'PM', 1, '2026-03-09 05:15:19', '2026-03-09 05:31:19'),
(11, 1, 117, 50, 14, '2026-03-09 05:20:24', '2026-03-09 05:37:29', 18, 0, 'PM', 1, '2026-03-09 05:20:24', '2026-03-09 05:37:29'),
(12, 1, 1, 7, 13, '2026-03-09 05:31:30', '2026-03-09 05:48:05', 17, 0, 'PM', 1, '2026-03-09 05:31:30', '2026-03-09 05:48:05'),
(13, 1, 136, 62, 14, '2026-03-09 05:38:04', '2026-03-09 05:55:16', 18, 0, 'PM', 1, '2026-03-09 05:38:04', '2026-03-09 05:55:16'),
(14, 1, 62, 19, 13, '2026-03-09 05:48:33', '2026-03-09 06:08:56', 21, 0, 'PM', 1, '2026-03-09 05:48:33', '2026-03-09 06:08:56'),
(15, 1, 116, 49, 14, '2026-03-09 05:55:41', '2026-03-09 06:18:07', 23, 0, 'PM', 1, '2026-03-09 05:55:41', '2026-03-09 06:18:07'),
(16, 1, 3, 11, 13, '2026-03-09 06:09:03', '2026-03-09 06:26:46', 18, 0, 'PM', 1, '2026-03-09 06:09:03', '2026-03-09 06:26:46'),
(17, 1, 208, 83, 14, '2026-03-09 06:18:19', '2026-03-09 06:33:19', 0, 0, 'PM', 0, '2026-03-09 06:18:19', '2026-03-09 06:18:19'),
(18, 1, 6, 39, 13, '2026-03-09 06:26:50', '2026-03-09 06:41:50', 0, 0, 'PM', 0, '2026-03-09 06:26:50', '2026-03-09 06:26:50');

INSERT INTO `rooms` (`id`, `branch_id`, `floor_id`, `number`, `area`, `status`, `type_id`, `is_priority`, `last_checkin_at`, `last_checkout_at`, `time_to_terminate_queue`, `check_out_time`, `time_to_clean`, `started_cleaning_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '1', 'Main', 'Available', 2, 1, '2026-03-08 20:28:17', '2026-03-09 04:56:27', NULL, '2026-03-09 04:56:27', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:48:05');
INSERT INTO `rooms` (`id`, `branch_id`, `floor_id`, `number`, `area`, `status`, `type_id`, `is_priority`, `last_checkin_at`, `last_checkout_at`, `time_to_terminate_queue`, `check_out_time`, `time_to_clean`, `started_cleaning_at`, `created_at`, `updated_at`) VALUES
(2, 1, 1, '2', 'Main', 'Uncleaned', 2, 1, '2026-03-09 02:56:04', '2026-03-09 05:35:53', NULL, '2026-03-09 05:35:53', '2026-03-09 08:35:53', NULL, '2026-03-08 20:01:59', '2026-03-09 05:35:53');
INSERT INTO `rooms` (`id`, `branch_id`, `floor_id`, `number`, `area`, `status`, `type_id`, `is_priority`, `last_checkin_at`, `last_checkout_at`, `time_to_terminate_queue`, `check_out_time`, `time_to_clean`, `started_cleaning_at`, `created_at`, `updated_at`) VALUES
(3, 1, 1, '3', 'Main', 'Available', 2, 1, '2026-03-08 20:36:12', '2026-03-09 05:03:24', NULL, '2026-03-09 05:03:24', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 06:26:46');
INSERT INTO `rooms` (`id`, `branch_id`, `floor_id`, `number`, `area`, `status`, `type_id`, `is_priority`, `last_checkin_at`, `last_checkout_at`, `time_to_terminate_queue`, `check_out_time`, `time_to_clean`, `started_cleaning_at`, `created_at`, `updated_at`) VALUES
(4, 1, 1, '4', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(5, 1, 1, '5', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(6, 1, 1, '6', 'Main', 'Cleaning', 2, 1, '2026-03-08 22:09:36', '2026-03-09 05:18:25', NULL, '2026-03-09 05:18:25', '2026-03-09 08:18:25', '2026-03-09 06:26:50', '2026-03-08 20:01:59', '2026-03-09 06:26:50'),
(7, 1, 1, '7', 'Main', 'Uncleaned', 2, 1, '2026-03-08 22:02:57', '2026-03-09 06:08:29', NULL, '2026-03-09 06:08:29', '2026-03-09 09:08:29', NULL, '2026-03-08 20:01:59', '2026-03-09 06:08:29'),
(8, 1, 1, '8', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:46:55'),
(9, 1, 1, '9', 'Main', 'Uncleaned', 2, 1, '2026-03-08 21:58:45', '2026-03-09 06:08:53', NULL, '2026-03-09 06:08:53', '2026-03-09 09:08:53', NULL, '2026-03-08 20:01:59', '2026-03-09 06:08:53'),
(10, 1, 1, '10', 'Main', 'Occupied', 2, 1, '2026-03-08 23:21:14', '2026-03-09 03:48:27', NULL, '2026-03-09 03:48:27', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 04:32:59'),
(11, 1, 1, '11', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(12, 1, 1, '12', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:31:17'),
(13, 1, 1, '14', 'Main', 'Maintenance', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 23:47:22'),
(14, 1, 1, '15', 'Main', 'Occupied', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:11:33'),
(15, 1, 1, '16', 'Main', 'Available', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 04:06:06'),
(16, 1, 1, '17', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:55:36'),
(17, 1, 1, '18', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:01:09'),
(18, 1, 1, '19', 'Main', 'Uncleaned', 3, 1, '2026-03-08 22:45:03', '2026-03-09 05:58:46', NULL, '2026-03-09 05:58:46', '2026-03-09 08:58:46', NULL, '2026-03-08 20:01:59', '2026-03-09 05:58:46'),
(19, 1, 1, '20', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:30:40'),
(20, 1, 1, '21', 'Main', 'Available', 2, 1, '2026-03-08 23:22:09', '2026-03-09 03:48:36', NULL, '2026-03-09 03:48:36', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 04:37:32'),
(21, 1, 1, '22', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(22, 1, 1, '23', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(23, 1, 1, '24', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(24, 1, 1, '25', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(25, 1, 1, '26', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(26, 1, 1, '27', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 22:59:59'),
(27, 1, 1, '28', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 23:46:56'),
(28, 1, 1, '29', 'Main', 'Available', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:11:06'),
(29, 1, 1, '30', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(30, 1, 1, '31', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(31, 1, 1, '32', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(32, 1, 1, '33', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:46:36'),
(33, 1, 1, '34', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(34, 1, 1, '35', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:07:23'),
(35, 1, 1, '36', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(36, 1, 1, '37', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(37, 1, 1, '38', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(38, 1, 1, '39', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(39, 1, 1, '50', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(40, 1, 1, '51', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(41, 1, 1, '52', 'Main', 'Available', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 04:02:29'),
(42, 1, 1, '53', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(43, 1, 2, '60', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:24:48'),
(44, 1, 2, '61', 'Main', 'Available', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:11:47'),
(45, 1, 2, '62', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 22:43:25'),
(46, 1, 2, '63', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(47, 1, 2, '64', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(48, 1, 2, '65', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(49, 1, 2, '66', 'Main', 'Available', 2, 1, '2026-03-08 22:41:49', '2026-03-09 04:21:16', NULL, '2026-03-09 04:21:16', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 04:59:20'),
(50, 1, 2, '67', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 01:03:52'),
(51, 1, 2, '68', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(52, 1, 2, '69', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(53, 1, 2, '70', 'Main', 'Occupied', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:50:38'),
(54, 1, 2, '71', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(55, 1, 2, '72', 'Main', 'Occupied', 2, 1, '2026-03-08 21:36:45', '2026-03-09 02:51:55', NULL, '2026-03-09 02:51:55', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 04:00:08'),
(56, 1, 2, '73', 'Main', 'Available', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:05:57'),
(57, 1, 2, '74', 'Main', 'Available', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:09:50'),
(58, 1, 2, '75', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:32:05'),
(59, 1, 2, '76', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(60, 1, 2, '77', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(61, 1, 2, '78', 'Main', 'Available', 2, 1, '2026-03-08 23:44:18', '2026-03-09 04:50:44', NULL, '2026-03-09 04:50:44', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:31:19'),
(62, 1, 2, '79', 'Main', 'Available', 2, 1, '2026-03-08 21:09:41', '2026-03-09 04:59:15', NULL, '2026-03-09 04:59:15', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 06:08:56'),
(63, 1, 2, '80', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(64, 1, 2, '81', 'Main', 'Available', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:11:32'),
(65, 1, 2, '82', 'Main', 'Available', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:11:16'),
(66, 1, 2, '83', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(67, 1, 2, '84', 'Main', 'Available', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:05:51'),
(68, 1, 2, '85', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(69, 1, 2, '86', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(70, 1, 2, '87', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:10:38'),
(71, 1, 2, '88', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(72, 1, 2, '89', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(73, 1, 2, '90', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(74, 1, 2, '91', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(75, 1, 2, '92', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:24:27'),
(76, 1, 2, '93', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(77, 1, 2, '94', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(78, 1, 2, '95', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(79, 1, 2, '96', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(80, 1, 2, '97', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(81, 1, 2, '98', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(82, 1, 2, '99', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(83, 1, 2, '100', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:20:13'),
(84, 1, 2, '101', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:05:49'),
(85, 1, 3, '120', 'Main', 'Occupied', 2, 1, '2026-03-08 21:25:00', '2026-03-09 03:04:23', NULL, '2026-03-09 03:04:23', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 03:48:04'),
(86, 1, 3, '121', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(87, 1, 3, '122', 'Main', 'Available', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:12:39'),
(88, 1, 3, '123', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(89, 1, 3, '124', 'Main', 'Uncleaned', 1, 1, '2026-03-08 23:07:43', '2026-03-09 05:21:57', NULL, '2026-03-09 05:21:57', '2026-03-09 08:21:57', NULL, '2026-03-08 20:01:59', '2026-03-09 05:21:57'),
(90, 1, 3, '125', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(91, 1, 3, '126', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(92, 1, 3, '127', 'Main', 'Uncleaned', 2, 1, '2026-03-08 21:04:26', '2026-03-09 06:14:53', NULL, '2026-03-09 06:14:53', '2026-03-09 09:14:53', NULL, '2026-03-08 20:01:59', '2026-03-09 06:14:53'),
(93, 1, 3, '128', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(94, 1, 3, '129', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(95, 1, 3, '130', 'Main', 'Available', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 02:15:49'),
(96, 1, 3, '131', 'Main', 'Uncleaned', 2, 0, '2026-03-08 22:37:28', '2026-03-09 05:34:52', NULL, '2026-03-09 05:34:52', '2026-03-09 08:34:52', NULL, '2026-03-08 20:01:59', '2026-03-09 05:34:52'),
(97, 1, 3, '132', 'Main', 'Occupied', 2, 1, '2026-03-08 21:51:34', '2026-03-09 01:54:09', NULL, '2026-03-09 01:54:09', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 02:49:25'),
(98, 1, 3, '133', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(99, 1, 3, '134', 'Main', 'Available', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 02:15:43'),
(100, 1, 3, '135', 'Main', 'Available', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:12:19'),
(101, 1, 3, '136', 'Main', 'Occupied', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 01:51:51'),
(102, 1, 3, '137', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(103, 1, 3, '138', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:35:19'),
(104, 1, 3, '139', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(105, 1, 3, '150', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(106, 1, 3, '151', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(107, 1, 3, '152', 'Main', 'Uncleaned', 2, 1, '2026-03-08 22:36:57', '2026-03-09 05:52:43', NULL, '2026-03-09 05:52:43', '2026-03-09 08:52:43', NULL, '2026-03-08 20:01:59', '2026-03-09 05:52:43'),
(108, 1, 3, '153', 'Main', 'Maintenance', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 23:38:12'),
(109, 1, 3, '154', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(110, 1, 3, '155', 'Main', 'Available', 2, 1, '2026-03-08 22:06:05', '2026-03-09 03:53:57', NULL, '2026-03-09 03:53:57', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:15:02'),
(111, 1, 3, '156', 'Main', 'Available', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:45:18'),
(112, 1, 3, '157', 'Main', 'Available', 2, 1, '2026-03-08 22:40:11', '2026-03-09 04:12:55', NULL, '2026-03-09 04:12:55', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:20:19'),
(113, 1, 3, '158', 'Main', 'Uncleaned', 2, 1, '2026-03-08 23:46:02', '2026-03-09 05:18:57', NULL, '2026-03-09 05:18:57', '2026-03-09 08:18:57', NULL, '2026-03-08 20:01:59', '2026-03-09 05:18:57'),
(114, 1, 3, '159', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(115, 1, 3, '160', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 06:17:42'),
(116, 1, 3, '161', 'Main', 'Available', 2, 1, '2026-03-08 22:58:28', '2026-03-09 04:57:10', NULL, '2026-03-09 04:57:10', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 06:18:07'),
(117, 1, 3, '162', 'Main', 'Available', 2, 1, '2026-03-08 22:59:16', '2026-03-09 04:37:07', NULL, '2026-03-09 04:37:07', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:37:29'),
(118, 1, 3, '163', 'Main', 'Uncleaned', 2, 1, '2026-03-08 23:53:45', '2026-03-09 05:05:20', NULL, '2026-03-09 05:05:20', '2026-03-09 08:05:20', NULL, '2026-03-08 20:01:59', '2026-03-09 05:05:20'),
(119, 1, 3, '164', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(120, 1, 3, '165', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:30:12'),
(121, 1, 3, '166', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:50:45'),
(122, 1, 3, '167', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 01:22:10'),
(123, 1, 3, '168', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 23:57:35'),
(124, 1, 3, '169', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 00:14:57'),
(125, 1, 3, '170', 'Main', 'Uncleaned', 2, 1, '2026-03-09 00:16:53', '2026-03-09 06:09:55', NULL, '2026-03-09 06:09:55', '2026-03-09 09:09:55', NULL, '2026-03-08 20:01:59', '2026-03-09 06:09:55'),
(126, 1, 3, '171', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 22:29:20'),
(127, 1, 3, '3A', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(128, 1, 3, '3B', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(129, 1, 3, '3C', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(130, 1, 3, '3D', 'Main', 'Occupied', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:07:25'),
(131, 1, 3, '3E', 'Main', 'Occupied', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:42:14'),
(132, 1, 3, '3F', 'Main', 'Uncleaned', 1, 1, '2026-03-08 20:23:04', '2026-03-09 06:25:51', NULL, '2026-03-09 06:25:51', '2026-03-09 09:25:51', NULL, '2026-03-08 20:01:59', '2026-03-09 06:25:51'),
(133, 1, 3, '3G', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(134, 1, 4, '200', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(135, 1, 4, '201', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 04:12:30'),
(136, 1, 4, '202', 'Main', 'Available', 2, 1, '2026-03-09 00:12:11', '2026-03-09 04:58:26', NULL, '2026-03-09 04:58:26', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:55:16'),
(137, 1, 4, '203', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(138, 1, 4, '204', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(139, 1, 4, '205', 'Main', 'Uncleaned', 2, 1, '2026-03-08 21:34:20', '2026-03-09 06:04:32', NULL, '2026-03-09 06:04:32', '2026-03-09 09:04:32', NULL, '2026-03-08 20:01:59', '2026-03-09 06:04:32'),
(140, 1, 4, '206', 'Main', 'Maintenance', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 23:39:29'),
(141, 1, 4, '207', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 04:09:57'),
(142, 1, 4, '208', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(143, 1, 4, '209', 'Main', 'Uncleaned', 2, 1, '2026-03-08 23:42:26', '2026-03-09 05:25:47', NULL, '2026-03-09 05:25:47', '2026-03-09 08:25:47', NULL, '2026-03-08 20:01:59', '2026-03-09 05:25:47'),
(144, 1, 4, '210', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(145, 1, 4, '211', 'Main', 'Available', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 04:06:37'),
(146, 1, 4, '212', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(147, 1, 4, '214', 'Main', 'Occupied', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:49:21'),
(148, 1, 4, '215', 'Main', 'Available', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:33:07'),
(149, 1, 4, '216', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(150, 1, 4, '217', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(151, 1, 4, '218', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(152, 1, 4, '219', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(153, 1, 4, '220', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(154, 1, 4, '221', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(155, 1, 4, '222', 'Main', 'Available', 3, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(156, 1, 4, '223', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(157, 1, 4, '224', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 02:35:30'),
(158, 1, 4, '225', 'Main', 'Available', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:33:54'),
(159, 1, 4, '226', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(160, 1, 4, '227', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(161, 1, 4, '228', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(162, 1, 4, '229', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:50:17'),
(163, 1, 4, '230', 'Main', 'Available', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 04:06:27'),
(164, 1, 4, '231', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `rooms` (`id`, `branch_id`, `floor_id`, `number`, `area`, `status`, `type_id`, `is_priority`, `last_checkin_at`, `last_checkout_at`, `time_to_terminate_queue`, `check_out_time`, `time_to_clean`, `started_cleaning_at`, `created_at`, `updated_at`) VALUES
(165, 1, 4, '232', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(166, 1, 4, '233', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(167, 1, 4, '234', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:24:16'),
(168, 1, 4, '235', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `rooms` (`id`, `branch_id`, `floor_id`, `number`, `area`, `status`, `type_id`, `is_priority`, `last_checkin_at`, `last_checkout_at`, `time_to_terminate_queue`, `check_out_time`, `time_to_clean`, `started_cleaning_at`, `created_at`, `updated_at`) VALUES
(169, 1, 4, '236', 'Main', 'Available', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:12:30');
INSERT INTO `rooms` (`id`, `branch_id`, `floor_id`, `number`, `area`, `status`, `type_id`, `is_priority`, `last_checkin_at`, `last_checkout_at`, `time_to_terminate_queue`, `check_out_time`, `time_to_clean`, `started_cleaning_at`, `created_at`, `updated_at`) VALUES
(170, 1, 4, '237', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `rooms` (`id`, `branch_id`, `floor_id`, `number`, `area`, `status`, `type_id`, `is_priority`, `last_checkin_at`, `last_checkout_at`, `time_to_terminate_queue`, `check_out_time`, `time_to_clean`, `started_cleaning_at`, `created_at`, `updated_at`) VALUES
(171, 1, 4, '238', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(172, 1, 4, '239', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(173, 1, 4, '250', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(174, 1, 4, '251', 'Main', 'Uncleaned', 2, 1, '2026-03-08 21:32:28', '2026-03-09 06:03:56', NULL, '2026-03-09 06:03:56', '2026-03-09 09:03:56', NULL, '2026-03-08 20:01:59', '2026-03-09 06:03:56'),
(175, 1, 4, '252', 'Main', 'Uncleaned', 2, 1, '2026-03-08 23:32:12', '2026-03-09 05:32:38', NULL, '2026-03-09 05:32:38', '2026-03-09 08:32:38', NULL, '2026-03-08 20:01:59', '2026-03-09 05:32:38'),
(176, 1, 4, '4A', 'Main', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:11:59'),
(177, 1, 4, '4B', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(178, 1, 4, '4C', 'Main', 'Uncleaned', 1, 1, '2026-03-08 20:14:57', '2026-03-09 06:03:25', NULL, '2026-03-09 06:03:25', '2026-03-09 09:03:25', NULL, '2026-03-08 20:01:59', '2026-03-09 06:03:25'),
(179, 1, 4, '4D', 'Main', 'Occupied', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 22:41:04'),
(180, 1, 4, '4E', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(181, 1, 4, '4F', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(182, 1, 4, '4G', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(183, 1, 5, '253', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(184, 1, 5, '254', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(185, 1, 5, '255', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 00:26:31'),
(186, 1, 5, '256', 'Main', 'Maintenance', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 23:40:04'),
(187, 1, 5, '257', 'Main', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 05:45:38'),
(188, 1, 5, '258', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(189, 1, 5, '259', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(190, 1, 5, '260', 'Main', 'Maintenance', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 23:37:33'),
(191, 1, 5, '261', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(192, 1, 5, '262', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(193, 1, 5, '263', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 00:28:22'),
(194, 1, 5, '264', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(195, 1, 5, '265', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(196, 1, 5, '266', 'Main', 'Maintenance', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 23:37:56'),
(197, 1, 5, '267', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(198, 1, 5, '268', 'Main', 'Maintenance', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 23:39:44'),
(199, 1, 5, '269', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 01:36:39'),
(200, 1, 5, '270', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 02:02:14'),
(201, 1, 5, '271', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(202, 1, 5, '272', 'Main', 'Uncleaned', 2, 1, '2026-03-09 01:11:20', '2026-03-09 05:01:32', NULL, '2026-03-09 05:01:32', '2026-03-09 08:01:32', NULL, '2026-03-08 20:01:59', '2026-03-09 05:01:32'),
(203, 1, 5, '273', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(204, 1, 5, '274', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 02:59:56'),
(205, 1, 5, '275', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(206, 1, 5, '276', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 03:01:19'),
(207, 1, 5, '277', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 03:48:51'),
(208, 1, 5, '278', 'Main', 'Cleaning', 2, 1, '2026-03-09 03:58:44', '2026-03-09 04:57:55', NULL, '2026-03-09 04:57:55', '2026-03-09 07:57:55', '2026-03-09 06:18:19', '2026-03-08 20:01:59', '2026-03-09 06:18:19'),
(209, 1, 5, '279', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 21:02:16'),
(210, 1, 5, '280', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(211, 1, 5, '281', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 23:58:11'),
(212, 1, 5, '282', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(213, 1, 5, '283', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(214, 1, 5, '284', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(215, 1, 5, '285', 'Main', 'Occupied', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 00:26:57'),
(216, 1, 5, '286', 'Main', 'Occupied', 2, 1, '2026-03-08 21:25:45', '2026-03-09 02:39:40', NULL, '2026-03-09 02:39:40', NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 03:49:00'),
(217, 1, 5, '287', 'Main', 'Maintenance', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 00:28:25'),
(218, 1, 5, '288', 'Main', 'Available', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 02:15:19'),
(219, 1, 5, '289', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(220, 1, 5, '290', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(221, 1, 5, '291', 'Main', 'Available', 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(222, 1, 5, '292', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(223, 1, 5, '293', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(224, 1, 5, '294', 'Main', 'Maintenance', 2, 1, NULL, NULL, NULL, NULL, '2026-03-09 04:51:51', NULL, '2026-03-08 20:01:59', '2026-03-09 01:52:22'),
(225, 1, 5, '5A', 'Main', 'Occupied', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-09 03:39:04'),
(226, 1, 5, '5B', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(227, 1, 5, '5C', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(228, 1, 5, '5D', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(229, 1, 5, '5E', 'Main', 'Uncleaned', 1, 1, '2026-03-08 22:45:11', '2026-03-09 05:56:02', NULL, '2026-03-09 05:56:02', '2026-03-09 08:56:02', NULL, '2026-03-08 20:01:59', '2026-03-09 05:56:02'),
(230, 1, 5, '5F', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(231, 1, 5, '5G', 'Main', 'Available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59');

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('3kHfG7CiFZajfwwybirZ9klvdNxvlksz2a1NNqBp', NULL, '170.64.221.200', 'Mozilla/5.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRG9NQnhUeWdrcEtSYlFra1VRbUd2MFcyNkl0ZUc5Qkd2SEJJclUzRSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773005685);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('8V2kkyv8qPyjL23VSCpn8Yjw0vPyLbFmzvb9nWh9', 6, '180.190.44.109', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSExRWXluRW40cVpwbmIyYXJNakp5WWpNaURMSlh6c1p5WEltODN5cyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9iYWNrLW9mZmljZS9yZXBvcnQtaHViIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NjtzOjIxOiJwYXNzd29yZF9oYXNoX3NhbmN0dW0iO3M6NjA6IiQyeSQxMCRaenJzRjY4dldRU2FxSlFNaE94VkYud1EvWUZabmJLRDBFemZVRTIySXJZT0kuN2l2R0FUTyI7fQ==', 1773004510);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('BlYN4SIB9zWdbbJbIpsqeqRhWTN3a1RV77TtH8T0', 2, '49.145.211.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicGRLYXI1UkVwbDVpZG40MnYzY1JuOUJjZ3BkdTBnQ2hXZ2VVbTlCMSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9hZG1pbi91c2VycyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoyMToicGFzc3dvcmRfaGFzaF9zYW5jdHVtIjtzOjYwOiIkMnkkMTAkMTdFNGF4V3JuMUJvYi9wVjN4enEwT3V5c0cuNlpPMzBhZy9NNWIvZVMyTmZhVTRBa0syREsiO30=', 1773007140);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('csB7ugPpjSJKDmzN30CM03k6vj5Z6xOk8mmBo7GO', 6, '180.190.46.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiS04waUc4M3M3NmgxWTB5N0lnSVJzcjRYdXZlbWtIb2hGMVFjQjM2UyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9iYWNrLW9mZmljZS9yZXBvcnQtaHViIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NjtzOjIxOiJwYXNzd29yZF9oYXNoX3NhbmN0dW0iO3M6NjA6IiQyeSQxMCRaenJzRjY4dldRU2FxSlFNaE94VkYud1EvWUZabmJLRDBFemZVRTIySXJZT0kuN2l2R0FUTyI7fQ==', 1773008437),
('CxrjZb0t1vyaanLK1LNm8iGLpg83JbH2oQ4ehrsf', NULL, '103.40.61.98', 'libredtail-http', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieXlrOVhya2NEc2k3a01mM21WNFRZM3A0aDhuVUlrQ1B3a0xXdGVpYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjAwOiJodHRwOi8vMTM5LjU5LjI1Mi4xMDAvaW5kZXgucGhwPyUyRiUzQyUzRmVjaG8lMjhtZDUlMjglMjJoaSUyMiUyOSUyOSUzQiUzRiUzRSUyMCUyRnRtcCUyRmluZGV4MS5waHA9JmNvbmZpZy1jcmVhdGUlMjAlMkY9Jmxhbmc9Li4lMkYuLiUyRi4uJTJGLi4lMkYuLiUyRi4uJTJGLi4lMkYuLiUyRnVzciUyRmxvY2FsJTJGbGliJTJGcGhwJTJGcGVhcmNtZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773005289),
('DTbdLV8xAsu1AYibM5LvPXWatl9TtkrRtKZPiyqA', NULL, '143.44.185.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibGpmaTBQdHNrMFVJZmlJZVo1bHZ5NkhhdFVqSHlLRmd6Vjl3UUt0WSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9sb2dpbiI7fX0=', 1773008864),
('DxkDAbslK7MFaxizjHTvxZD2UyqUWkAJLiDGD43E', 2, '180.190.46.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiN3M4azFSZnk5ZHU4TmdNQnlUSzY1Vm5yYjJpa3Y5cFpnTklMcVZRbyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9hZG1pbi9yb29tcyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoyMToicGFzc3dvcmRfaGFzaF9zYW5jdHVtIjtzOjYwOiIkMnkkMTAkMTdFNGF4V3JuMUJvYi9wVjN4enEwT3V5c0cuNlpPMzBhZy9NNWIvZVMyTmZhVTRBa0syREsiO30=', 1773007877),
('f1FnD5j0ZftDJmTvBPUJHmrOAWwlrqTchjztrCdc', 4, '143.44.185.170', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVnNqQkh6aDgyQWRvcThPSWhPUnBIaEswdE54a1M1ZUdTZ3hTbXNiQSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTM5LjU5LjI1Mi4xMDAva2lvc2svZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDt9', 1773006395),
('fvvM153toEi9r9n0WETmB0X7xmOhfLPDfHFydDY0', NULL, '170.64.221.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiak5PNmxVcUdocHczWnZ4R05TeWIwZWUyNFlTdloyTmJZcHRQZmFNdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773005678),
('h3jLOXyQwgLKnBgHVyKD7rb0B07N0oKljNscFSDy', 4, '49.145.211.174', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoieHVKWThQSnFHNHh5WVpHNjJXT1pKMHRYazQ2YWJzWHlHbzF5WWR5UCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ1OiJodHRwOi8vMTM5LjU5LjI1Mi4xMDAva2lvc2svY2hlY2stb3V0L3N1Y2Nlc3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O30=', 1773008722),
('HzG6XkAbtBsXx8s4wJoUHXkAGhwF8IEeOyh2Dpw0', NULL, '170.64.221.200', 'Mozilla/5.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFRTQUd0MXdxUWZoQ1F4dVF0ZUtsdk9ubEJlSzNpMFg4R25ZMTlQdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQ0OiJodHRwOi8vMTM5LjU5LjI1Mi4xMDAvP2Z1bmN0aW9uPWNhbGxfdXNlcl9mdW5jX2FycmF5JnM9JTJGSW5kZXglMkYlNUN0aGluayU1Q2FwcCUyRmludm9rZWZ1bmN0aW9uJnZhcnMlNUIwJTVEPXN5c3RlbSZ2YXJzJTVCMSU1RCU1QjAlNUQ9cHJpbnRlbnYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773005697),
('Kz7ke1Rxougux2e8zmOhSWBwy6nQmhAr3bxdR76t', NULL, '103.40.61.98', 'libredtail-http', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSUI3dlBrSFVRRzVuNTlyaHVyQlZyMUc3Rm8yNFQ5UDZ1QlQ5SnFqRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQ3OiJodHRwOi8vMTM5LjU5LjI1Mi4xMDAvaW5kZXgucGhwP2Z1bmN0aW9uPWNhbGxfdXNlcl9mdW5jX2FycmF5JnM9JTJGaW5kZXglMkYlNUN0aGluayU1Q2FwcCUyRmludm9rZWZ1bmN0aW9uJnZhcnMlNUIwJTVEPW1kNSZ2YXJzJTVCMSU1RCU1QjAlNUQ9SGVsbG8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773005289),
('Lpt29l4DAruuiY51HOm7OvwsHBR461hoB32QtntL', 14, '143.44.185.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSGpsQnhoU0VaYlRVODBJZjZMbUI4MVRkN1JoRW1FQndZdjJqSFNEMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9yb29tYm95L2Rhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE0O3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2MDoiJDJ5JDEwJG1iUi9NMFNuRDN6ZDg2akpEbUYwSy5hdW9vWFZOTm5jazRmTU1SQ3VET2E5bW9GcERZbmt1Ijt9', 1773008862),
('lXuADd7abhbOcZtLMaP51CEv8w8uKE1qfzxQmKCk', NULL, '20.65.194.61', 'Mozilla/5.0 zgrab/0.x', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiekYyUmxhakNETWpoR3VlT3l1R25OcmpOOWVzNlg4MUNwVHVkblU0MSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773006913),
('mSFwESOji7vfa8kZExnB08hkF1UdxWCtC0URxpbE', 13, '143.44.185.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMzE5T0VKNmJtQTRITjRqTUpReTIxSVVHdzRaeDhrQzEwVURHSjJMZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9yb29tYm95L2Rhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEzO3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2MDoiJDJ5JDEwJDFidC5ROW9MaVpkU0FvUnhiamsxM2VPLlNOVVFPanRyNlRpUnlvTWh2UHVjWmx0THlTSFdhIjt9', 1773008863),
('Mxpjql0l8sUsr98EwZ9CQqdEqrdzlC4iAmKYxUw9', NULL, '40.77.167.58', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR3ZWcjhVUnBHMDAwaXVUVHQ4Q1RGMk0xTEp1WXFBU1I3Z2hRSldjYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773004265),
('PMIExJtjcM0bXWXHebe79tWmiERMK51MGTICMRmd', NULL, '121.199.163.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibUhidlNFQjh5OWFRd3paaGwzS0hWdXJIWjhTOXJQU1VxdlFLZlZPSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773003298),
('Rd8porcyOytYPWfZVCnPJZJdJi5H8W6GpLuMkqDX', 9, '49.145.211.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiV2NReHo2QzhJSUthQkZiNFNUMjVhNWFCNDdPaTc4bE1qR2U1R0ZhdCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ3OiJodHRwOi8vMTM5LjU5LjI1Mi4xMDAvZnJvbnRkZXNrL3Jvb20tbW9uaXRvcmluZyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjk7fQ==', 1773008863),
('smr1exFbVzywGR7bG1ze8SRa6vynCcUq6SACQ4wG', 6, '103.216.221.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidmRqdmd5amVjbGN1RU05UlNWcHBSbFJ5NjU4dFBwUXFjRWtiNnRKRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9iYWNrLW9mZmljZS9yZXBvcnQtaHViIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NjtzOjIxOiJwYXNzd29yZF9oYXNoX3NhbmN0dW0iO3M6NjA6IiQyeSQxMCRaenJzRjY4dldRU2FxSlFNaE94VkYud1EvWUZabmJLRDBFemZVRTIySXJZT0kuN2l2R0FUTyI7fQ==', 1773007846),
('sV7SUZmK7HQPFl0ck2rHsYjzlnnE4qaQmfszVBZm', NULL, '170.64.221.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRXdGUFpVRGJ6M3U1WG8yTmxwdjhyU0xQMGNROTZxaG1FVVFBbVFyUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773005675),
('uhfWTisqotYekufkOjQZ0435UKnmYYpmKKFN1Hnk', NULL, '204.76.203.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.46', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiRW5DVEhuZ1RsTXRXQlAzUnhxcXk1NnREV05zUmVxZ3JMR255WFNUeCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773005233),
('USsX8bWSwfFUyvt9QLWWWXQ78Wpfn1nOt81K97X8', NULL, '170.64.221.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicXVwNXZDbnU2WW5XWGt4SkttSWJKcmd3VUxTRkR0c2NsWTh4ZzZpbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773005665),
('vKzwLCr4WFkHYKQi9RCwoWtjctsXmCutH5Gy8NlZ', NULL, '103.40.61.98', 'libredtail-http', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibWc3WG1Rb2liNkVHS3BmaXBIdnVrNldxN1FCeXNnWDlZaTdCQmF6QyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6ODk6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9pbmRleC5waHA/bGFuZz0uLiUyRi4uJTJGLi4lMkYuLiUyRi4uJTJGLi4lMkYuLiUyRi4uJTJGdG1wJTJGaW5kZXgxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773005289),
('vTALnB2fE4P0UKLkNuWr90ruI0G4iRsg6fhNoDga', NULL, '52.58.20.11', 'Go-http-client/1.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid0I4YjFYUTAwOHROQVQ0MGJCMEFQRm1kemtiaUJ3SEZ1bThMWk1SeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773002826),
('w52CmaF4Pz1kfgyMDhtjkmCx4Mf7jzDtYhpPs1l9', NULL, '170.64.221.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib2pVSjhwSjFYVEUyQXlBTXV0Wk11dGU0NGtYUWtPSlRxcTVHQXBOMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773005667),
('X1UhRxNdjwHZIqw8Ezdmp5hU4W9otw22D39pjTKq', 13, '143.44.185.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicmFHd2htbUsyMFJDMDFqdk9OMlJoMDhxNUp6QU9hOFBpYkRlTkpVeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMC9yb29tYm95L2Rhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEzO3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2MDoiJDJ5JDEwJDFidC5ROW9MaVpkU0FvUnhiamsxM2VPLlNOVVFPanRyNlRpUnlvTWh2UHVjWmx0THlTSFdhIjt9', 1773008863),
('YdzqrFULwe3hbgZKdnata6CnOwhG026qIBBzIBwZ', NULL, '52.23.239.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic09YS2QxSUFQeDNkYnV6dDF2QTdtcEluc1NVc01SQVY0ck9ONkVJWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMzkuNTkuMjUyLjEwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773008274);

INSERT INTO `shift_logs` (`id`, `frontdesk_id`, `cash_drawer_id`, `beginning_cash`, `end_cash`, `description`, `total_expenses`, `total_remittances`, `time_in`, `time_out`, `frontdesk_ids`, `shift`, `created_at`, `updated_at`) VALUES
(1, 9, 1, '1.00', '0.00', NULL, '0.00', '0.00', '2026-03-08 20:03:41', NULL, '[2, \"N/A\"]', 'PM', '2026-03-08 20:03:41', '2026-03-08 20:03:47');


INSERT INTO `stay_extensions` (`id`, `guest_id`, `extension_id`, `hours`, `amount`, `frontdesk_ids`, `created_at`, `updated_at`) VALUES
(1, 26, 1, '6', '112', '\"[2,\\\"N\\\\/A\\\"]\"', '2026-03-09 02:34:21', '2026-03-09 02:34:21');
INSERT INTO `stay_extensions` (`id`, `guest_id`, `extension_id`, `hours`, `amount`, `frontdesk_ids`, `created_at`, `updated_at`) VALUES
(2, 48, 1, '6', '112', '\"[2,\\\"N\\\\/A\\\"]\"', '2026-03-09 04:10:26', '2026-03-09 04:10:26');
INSERT INTO `stay_extensions` (`id`, `guest_id`, `extension_id`, `hours`, `amount`, `frontdesk_ids`, `created_at`, `updated_at`) VALUES
(3, 49, 1, '6', '112', '\"[2,\\\"N\\\\/A\\\"]\"', '2026-03-09 04:11:03', '2026-03-09 04:11:03');
INSERT INTO `stay_extensions` (`id`, `guest_id`, `extension_id`, `hours`, `amount`, `frontdesk_ids`, `created_at`, `updated_at`) VALUES
(4, 67, 1, '6', '112', '\"[2,\\\"N\\\\/A\\\"]\"', '2026-03-09 05:45:56', '2026-03-09 05:45:56'),
(5, 62, 1, '6', '112', '\"[2,\\\"N\\\\/A\\\"]\"', '2026-03-09 06:16:07', '2026-03-09 06:16:07');

INSERT INTO `staying_hours` (`id`, `branch_id`, `number`, `created_at`, `updated_at`) VALUES
(1, 1, 6, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `staying_hours` (`id`, `branch_id`, `number`, `created_at`, `updated_at`) VALUES
(2, 1, 12, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `staying_hours` (`id`, `branch_id`, `number`, `created_at`, `updated_at`) VALUES
(3, 1, 18, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `staying_hours` (`id`, `branch_id`, `number`, `created_at`, `updated_at`) VALUES
(4, 1, 24, '2026-03-08 20:01:59', '2026-03-08 20:01:59');





INSERT INTO `transaction_types` (`id`, `name`, `position`, `created_at`, `updated_at`) VALUES
(1, 'Check In', '1', '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `transaction_types` (`id`, `name`, `position`, `created_at`, `updated_at`) VALUES
(2, 'Deposit', '2', '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `transaction_types` (`id`, `name`, `position`, `created_at`, `updated_at`) VALUES
(3, 'Kitchen Order', '3', '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `transaction_types` (`id`, `name`, `position`, `created_at`, `updated_at`) VALUES
(4, 'Damage Charges', '4', '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(5, 'Cashout', '5', '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(6, 'Extend', '6', '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(7, 'Transfer Room', '7', '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(8, 'Amenities', '8', '2026-03-08 20:02:00', '2026-03-08 20:02:00'),
(9, 'Food and Beverages', '8', '2026-03-08 20:02:00', '2026-03-08 20:02:00');

INSERT INTO `transactions` (`id`, `branch_id`, `shift_log_id`, `checkin_detail_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 84, 1, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 592, 0, 0, '2026-03-08 20:05:49', NULL, 'Guest Checked In at room #101', NULL, 0, 'PM', 0, '2026-03-08 20:05:49', '2026-03-08 20:05:49');
INSERT INTO `transactions` (`id`, `branch_id`, `shift_log_id`, `checkin_detail_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(2, 1, 1, 1, 1, 84, 1, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 592, 0, 200, '2026-03-08 20:05:49', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:05:49', '2026-03-08 20:05:49');
INSERT INTO `transactions` (`id`, `branch_id`, `shift_log_id`, `checkin_detail_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(3, 1, 1, 2, 1, 14, 2, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 672, 875, 3, 0, '2026-03-08 20:11:33', NULL, 'Guest Checked In at room #15', NULL, 0, 'PM', 0, '2026-03-08 20:11:33', '2026-03-08 20:11:33');
INSERT INTO `transactions` (`id`, `branch_id`, `shift_log_id`, `checkin_detail_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(4, 1, 1, 2, 1, 14, 2, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 875, 3, 200, '2026-03-08 20:11:33', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:11:33', '2026-03-08 20:11:33'),
(5, 1, 1, 2, 1, 14, 2, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 3, 875, 0, 3, '2026-03-08 20:11:33', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:11:33', '2026-03-08 20:11:33'),
(6, 1, 1, 3, 1, 178, 3, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 336, 550, 14, 0, '2026-03-08 20:14:57', NULL, 'Guest Checked In at room #4C', NULL, 0, 'PM', 0, '2026-03-08 20:14:57', '2026-03-08 20:14:57'),
(7, 1, 1, 3, 1, 178, 3, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 550, 14, 200, '2026-03-08 20:14:57', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:14:57', '2026-03-08 20:14:57'),
(8, 1, 1, 3, 1, 178, 3, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 14, 550, 0, 14, '2026-03-08 20:14:57', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:14:57', '2026-03-08 20:14:57'),
(9, 1, 1, 4, 1, 132, 4, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 336, 550, 14, 0, '2026-03-08 20:23:04', NULL, 'Guest Checked In at room #3F', NULL, 0, 'PM', 0, '2026-03-08 20:23:04', '2026-03-08 20:23:04'),
(10, 1, 1, 4, 1, 132, 4, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 550, 14, 200, '2026-03-08 20:23:04', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:23:04', '2026-03-08 20:23:04'),
(11, 1, 1, 4, 1, 132, 4, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 14, 550, 0, 14, '2026-03-08 20:23:04', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:23:04', '2026-03-08 20:23:04'),
(12, 1, 1, 5, 1, 167, 5, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 20:24:16', NULL, 'Guest Checked In at room #234', NULL, 0, 'PM', 0, '2026-03-08 20:24:16', '2026-03-08 20:24:16'),
(13, 1, 1, 5, 1, 167, 5, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 20:24:16', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:24:16', '2026-03-08 20:24:16'),
(14, 1, 1, 5, 1, 167, 5, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 20:24:16', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:24:16', '2026-03-08 20:24:16'),
(15, 1, 1, 6, 1, 43, 6, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 20:24:48', NULL, 'Guest Checked In at room #60', NULL, 0, 'PM', 0, '2026-03-08 20:24:48', '2026-03-08 20:24:48'),
(16, 1, 1, 6, 1, 43, 6, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 20:24:48', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:24:48', '2026-03-08 20:24:48'),
(17, 1, 1, 6, 1, 43, 6, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 20:24:48', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:24:48', '2026-03-08 20:24:48'),
(18, 1, 1, 7, 1, 1, 7, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 598, 6, 0, '2026-03-08 20:28:17', NULL, 'Guest Checked In at room #1', NULL, 0, 'PM', 0, '2026-03-08 20:28:17', '2026-03-08 20:28:17'),
(19, 1, 1, 7, 1, 1, 7, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 598, 6, 200, '2026-03-08 20:28:17', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:28:17', '2026-03-08 20:28:17'),
(20, 1, 1, 7, 1, 1, 7, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 6, 598, 0, 6, '2026-03-08 20:28:17', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:28:17', '2026-03-08 20:28:17'),
(21, 1, 1, 8, 1, 19, 8, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 20:30:40', NULL, 'Guest Checked In at room #20', NULL, 0, 'PM', 0, '2026-03-08 20:30:40', '2026-03-08 20:30:40'),
(22, 1, 1, 8, 1, 19, 8, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 20:30:40', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:30:40', '2026-03-08 20:30:40'),
(23, 1, 1, 8, 1, 19, 8, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 20:30:40', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:30:40', '2026-03-08 20:30:40'),
(24, 1, 1, 9, 1, 12, 9, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 20:31:17', NULL, 'Guest Checked In at room #12', NULL, 0, 'PM', 0, '2026-03-08 20:31:17', '2026-03-08 20:31:17'),
(25, 1, 1, 9, 1, 12, 9, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 20:31:17', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:31:17', '2026-03-08 20:31:17'),
(26, 1, 1, 9, 1, 12, 9, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 20:31:17', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:31:17', '2026-03-08 20:31:17'),
(27, 1, 1, 10, 1, 58, 10, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 616, 1000, 184, 0, '2026-03-08 20:32:05', NULL, 'Guest Checked In at room #75', NULL, 0, 'PM', 0, '2026-03-08 20:32:05', '2026-03-08 20:32:05'),
(28, 1, 1, 10, 1, 58, 10, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 1000, 184, 200, '2026-03-08 20:32:05', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:32:05', '2026-03-08 20:32:05'),
(29, 1, 1, 10, 1, 58, 10, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 184, 1000, 0, 184, '2026-03-08 20:32:05', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:32:05', '2026-03-08 20:32:05'),
(30, 1, 1, 11, 1, 3, 11, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 20:36:12', NULL, 'Guest Checked In at room #3', NULL, 0, 'PM', 0, '2026-03-08 20:36:12', '2026-03-08 20:36:12'),
(31, 1, 1, 11, 1, 3, 11, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 20:36:12', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:36:12', '2026-03-08 20:36:12'),
(32, 1, 1, 11, 1, 3, 11, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 20:36:12', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:36:12', '2026-03-08 20:36:12'),
(33, 1, 1, 12, 1, 8, 14, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 1000, 408, 0, '2026-03-08 20:46:55', NULL, 'Guest Checked In at room #8', NULL, 0, 'PM', 0, '2026-03-08 20:46:55', '2026-03-08 20:46:55'),
(34, 1, 1, 12, 1, 8, 14, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 1000, 408, 200, '2026-03-08 20:46:55', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:46:55', '2026-03-08 20:46:55'),
(35, 1, 1, 12, 1, 8, 14, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 408, 1000, 0, 408, '2026-03-08 20:46:55', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:46:55', '2026-03-08 20:46:55'),
(36, 1, 1, 13, 1, 162, 15, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 20:50:17', NULL, 'Guest Checked In at room #229', NULL, 0, 'PM', 0, '2026-03-08 20:50:17', '2026-03-08 20:50:17'),
(37, 1, 1, 13, 1, 162, 15, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 20:50:17', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:50:17', '2026-03-08 20:50:17'),
(38, 1, 1, 13, 1, 162, 15, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 20:50:17', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 20:50:17', '2026-03-08 20:50:17'),
(39, 1, 1, 14, 1, 121, 13, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 616, 816, 0, 0, '2026-03-08 20:50:45', NULL, 'Guest Checked In at room #166', NULL, 0, 'PM', 0, '2026-03-08 20:50:45', '2026-03-08 20:50:45'),
(40, 1, 1, 14, 1, 121, 13, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 816, 0, 200, '2026-03-08 20:50:45', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 20:50:45', '2026-03-08 20:50:45'),
(41, 1, 1, 15, 1, 17, 16, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 542, 0, 0, '2026-03-08 21:01:09', NULL, 'Guest Checked In at room #18', NULL, 0, 'PM', 0, '2026-03-08 21:01:09', '2026-03-08 21:01:09'),
(42, 1, 1, 15, 1, 17, 16, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 542, 0, 200, '2026-03-08 21:01:09', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:01:09', '2026-03-08 21:01:09'),
(43, 1, 1, 16, 1, 209, 17, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 1000, 408, 0, '2026-03-08 21:02:16', NULL, 'Guest Checked In at room #279', NULL, 0, 'PM', 0, '2026-03-08 21:02:16', '2026-03-08 21:02:16');
INSERT INTO `transactions` (`id`, `branch_id`, `shift_log_id`, `checkin_detail_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(44, 1, 1, 16, 1, 209, 17, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 1000, 408, 200, '2026-03-08 21:02:16', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:02:16', '2026-03-08 21:02:16'),
(45, 1, 1, 16, 1, 209, 17, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 408, 1000, 0, 408, '2026-03-08 21:02:16', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:02:16', '2026-03-08 21:02:16'),
(46, 1, 1, 17, 1, 92, 18, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 21:04:26', NULL, 'Guest Checked In at room #127', NULL, 0, 'PM', 0, '2026-03-08 21:04:26', '2026-03-08 21:04:26'),
(47, 1, 1, 17, 1, 92, 18, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 21:04:26', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:04:26', '2026-03-08 21:04:26'),
(48, 1, 1, 17, 1, 92, 18, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 21:04:26', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:04:26', '2026-03-08 21:04:26'),
(49, 1, 1, 14, 1, 121, 13, 3, 9, '\"[2,\\\"N\\\\/A\\\"]\"', 'Food and Beverages', 45, 45, 0, 0, '2026-03-08 21:05:41', NULL, 'Guest Added Food and Beverages: (Front Desk) (1) 1 LITER MINERAL', NULL, 0, 'PM', 0, '2026-03-08 21:05:35', '2026-03-08 21:05:41'),
(50, 1, 1, 18, 1, 34, 19, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 21:07:23', NULL, 'Guest Checked In at room #35', NULL, 0, 'PM', 0, '2026-03-08 21:07:23', '2026-03-08 21:07:23'),
(51, 1, 1, 18, 1, 34, 19, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 21:07:23', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:07:23', '2026-03-08 21:07:23'),
(52, 1, 1, 18, 1, 34, 19, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 0, 0, 8, '2026-03-08 21:07:44', NULL, 'Guest Deposit: change', NULL, 0, 'PM', 0, '2026-03-08 21:07:44', '2026-03-08 21:07:44'),
(53, 1, 1, 19, 1, 62, 20, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 21:09:41', NULL, 'Guest Checked In at room #79', NULL, 0, 'PM', 0, '2026-03-08 21:09:41', '2026-03-08 21:09:41'),
(54, 1, 1, 19, 1, 62, 20, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 21:09:41', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:09:41', '2026-03-08 21:09:41'),
(55, 1, 1, 19, 1, 62, 20, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 21:09:41', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:09:41', '2026-03-08 21:09:41'),
(56, 1, 1, 20, 1, 70, 21, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 21:10:38', NULL, 'Guest Checked In at room #87', NULL, 0, 'PM', 0, '2026-03-08 21:10:38', '2026-03-08 21:10:38'),
(57, 1, 1, 20, 1, 70, 21, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 21:10:38', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:10:38', '2026-03-08 21:10:38'),
(58, 1, 1, 20, 1, 70, 21, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 21:10:38', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:10:38', '2026-03-08 21:10:38'),
(59, 1, 1, 21, 1, 83, 22, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 595, 3, 0, '2026-03-08 21:20:13', NULL, 'Guest Checked In at room #100', NULL, 0, 'PM', 0, '2026-03-08 21:20:13', '2026-03-08 21:20:13'),
(60, 1, 1, 21, 1, 83, 22, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 595, 3, 200, '2026-03-08 21:20:13', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:20:13', '2026-03-08 21:20:13'),
(61, 1, 1, 21, 1, 83, 22, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 3, 595, 0, 3, '2026-03-08 21:20:13', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:20:13', '2026-03-08 21:20:13'),
(62, 1, 1, 22, 1, 75, 23, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 21:24:27', NULL, 'Guest Checked In at room #92', NULL, 0, 'PM', 0, '2026-03-08 21:24:27', '2026-03-08 21:24:27'),
(63, 1, 1, 22, 1, 75, 23, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 21:24:27', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:24:27', '2026-03-08 21:24:27'),
(64, 1, 1, 22, 1, 75, 23, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 21:24:27', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:24:27', '2026-03-08 21:24:27'),
(65, 1, 1, 23, 1, 85, 24, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 21:25:00', NULL, 'Guest Checked In at room #120', NULL, 0, 'PM', 0, '2026-03-08 21:25:00', '2026-03-08 21:25:00'),
(66, 1, 1, 23, 1, 85, 24, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 21:25:00', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:25:00', '2026-03-08 21:25:00'),
(67, 1, 1, 24, 1, 216, 25, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 21:25:45', NULL, 'Guest Checked In at room #286', NULL, 0, 'PM', 0, '2026-03-08 21:25:45', '2026-03-08 21:25:45'),
(68, 1, 1, 24, 1, 216, 25, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 21:25:45', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:25:45', '2026-03-08 21:25:45'),
(69, 1, 1, 25, 1, 120, 26, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 21:30:12', NULL, 'Guest Checked In at room #165', NULL, 0, 'PM', 0, '2026-03-08 21:30:12', '2026-03-08 21:30:12'),
(70, 1, 1, 25, 1, 120, 26, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 21:30:12', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:30:12', '2026-03-08 21:30:12'),
(71, 1, 1, 26, 1, 174, 27, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 21:32:28', NULL, 'Guest Checked In at room #251', NULL, 0, 'PM', 0, '2026-03-08 21:32:28', '2026-03-08 21:32:28'),
(72, 1, 1, 26, 1, 174, 27, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 21:32:28', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:32:28', '2026-03-08 21:32:28'),
(73, 1, 1, 26, 1, 174, 27, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 21:32:28', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:32:28', '2026-03-08 21:32:28'),
(74, 1, 1, 27, 1, 13, 28, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 616, 616, 0, 0, '2026-03-08 23:46:56', NULL, 'Guest Checked In at room #28', NULL, 0, 'PM', 0, '2026-03-08 21:33:18', '2026-03-08 23:46:56'),
(75, 1, 1, 27, 1, 13, 28, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 1000, 184, 200, '2026-03-08 21:33:18', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:33:18', '2026-03-08 21:33:18'),
(76, 1, 1, 27, 1, 13, 28, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 184, 1000, 0, 184, '2026-03-08 21:33:18', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:33:18', '2026-03-08 21:33:18'),
(77, 1, 1, 28, 1, 139, 29, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 21:34:20', NULL, 'Guest Checked In at room #205', NULL, 0, 'PM', 0, '2026-03-08 21:34:20', '2026-03-08 21:34:20'),
(78, 1, 1, 28, 1, 139, 29, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 21:34:20', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:34:20', '2026-03-08 21:34:20'),
(79, 1, 1, 28, 1, 139, 29, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 21:34:20', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:34:20', '2026-03-08 21:34:20'),
(80, 1, 1, 29, 1, 103, 30, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 21:35:18', NULL, 'Guest Checked In at room #138', NULL, 0, 'PM', 0, '2026-03-08 21:35:18', '2026-03-08 21:35:18'),
(81, 1, 1, 29, 1, 103, 30, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 21:35:18', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:35:18', '2026-03-08 21:35:18'),
(82, 1, 1, 29, 1, 103, 30, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 21:35:18', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:35:18', '2026-03-08 21:35:18'),
(83, 1, 1, 30, 1, 55, 31, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 21:36:45', NULL, 'Guest Checked In at room #72', NULL, 0, 'PM', 0, '2026-03-08 21:36:45', '2026-03-08 21:36:45'),
(84, 1, 1, 30, 1, 55, 31, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 21:36:45', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:36:45', '2026-03-08 21:36:45'),
(85, 1, 1, 12, 1, 8, 14, 1, 9, '\"[2,\\\"N\\\\/A\\\"]\"', 'Food and Beverages', 55, 55, 0, 0, '2026-03-08 21:41:03', NULL, 'Guest Added Food and Beverages: (Front Desk) (1) SPRITE', NULL, 0, 'PM', 0, '2026-03-08 21:40:48', '2026-03-08 21:41:03');
INSERT INTO `transactions` (`id`, `branch_id`, `shift_log_id`, `checkin_detail_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(86, 1, 1, 12, 1, 8, 14, 1, 5, '\"[2,\\\"N\\\\/A\\\"]\"', 'Cashout', 55, 55, 0, 0, '2026-03-08 21:41:03', NULL, 'Cashout from paying deposit (Food and Beverages)', NULL, 0, 'PM', 0, '2026-03-08 21:41:03', '2026-03-08 21:41:03');
INSERT INTO `transactions` (`id`, `branch_id`, `shift_log_id`, `checkin_detail_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(87, 1, 1, 17, 1, 92, 18, 3, 9, '\"[2,\\\"N\\\\/A\\\"]\"', 'Food and Beverages', 6, 6, 0, 0, '2026-03-08 21:41:55', NULL, 'Guest Added Food and Beverages: (Front Desk) (2) SOAP', NULL, 0, 'PM', 0, '2026-03-08 21:41:48', '2026-03-08 21:41:55');
INSERT INTO `transactions` (`id`, `branch_id`, `shift_log_id`, `checkin_detail_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(88, 1, 1, 17, 1, 92, 18, 3, 5, '\"[2,\\\"N\\\\/A\\\"]\"', 'Cashout', 6, 6, 0, 0, '2026-03-08 21:41:56', NULL, 'Cashout from paying deposit (Food and Beverages)', NULL, 0, 'PM', 0, '2026-03-08 21:41:56', '2026-03-08 21:41:56'),
(89, 1, 1, 21, 1, 83, 22, 2, 9, '\"[2,\\\"N\\\\/A\\\"]\"', 'Food and Beverages', 3, 3, 0, 0, '2026-03-08 21:44:50', NULL, 'Guest Added Food and Beverages: (Front Desk) (1) SOAP', NULL, 0, 'PM', 0, '2026-03-08 21:44:42', '2026-03-08 21:44:50'),
(90, 1, 1, 21, 1, 83, 22, 2, 9, '\"[2,\\\"N\\\\/A\\\"]\"', 'Food and Beverages', 2, 2, 0, 0, '2026-03-08 21:45:13', NULL, 'Guest Added Food and Beverages: (Front Desk) (1) SHAMPOO', NULL, 0, 'PM', 0, '2026-03-08 21:45:08', '2026-03-08 21:45:13'),
(91, 1, 1, 31, 1, 32, 32, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 21:46:36', NULL, 'Guest Checked In at room #33', NULL, 0, 'PM', 0, '2026-03-08 21:46:36', '2026-03-08 21:46:36'),
(92, 1, 1, 31, 1, 32, 32, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 21:46:36', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:46:36', '2026-03-08 21:46:36'),
(93, 1, 1, 31, 1, 32, 32, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 21:46:36', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:46:36', '2026-03-08 21:46:36'),
(94, 1, 1, 32, 1, 147, 33, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 448, 650, 2, 0, '2026-03-08 21:49:21', NULL, 'Guest Checked In at room #214', NULL, 0, 'PM', 0, '2026-03-08 21:49:21', '2026-03-08 21:49:21'),
(95, 1, 1, 32, 1, 147, 33, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 650, 2, 200, '2026-03-08 21:49:21', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:49:21', '2026-03-08 21:49:21'),
(96, 1, 1, 32, 1, 147, 33, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 2, 650, 0, 2, '2026-03-08 21:49:21', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:49:21', '2026-03-08 21:49:21'),
(97, 1, 1, 33, 1, 53, 34, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 448, 1000, 352, 0, '2026-03-08 21:50:38', NULL, 'Guest Checked In at room #70', NULL, 0, 'PM', 0, '2026-03-08 21:50:38', '2026-03-08 21:50:38'),
(98, 1, 1, 33, 1, 53, 34, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 1000, 352, 200, '2026-03-08 21:50:38', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:50:38', '2026-03-08 21:50:38'),
(99, 1, 1, 33, 1, 53, 34, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 352, 1000, 0, 352, '2026-03-08 21:50:38', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:50:38', '2026-03-08 21:50:38'),
(100, 1, 1, 34, 1, 97, 35, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 21:51:35', NULL, 'Guest Checked In at room #132', NULL, 0, 'PM', 0, '2026-03-08 21:51:35', '2026-03-08 21:51:35'),
(101, 1, 1, 34, 1, 97, 35, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 21:51:35', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:51:35', '2026-03-08 21:51:35'),
(102, 1, 1, 35, 1, 16, 36, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 21:55:36', NULL, 'Guest Checked In at room #17', NULL, 0, 'PM', 0, '2026-03-08 21:55:36', '2026-03-08 21:55:36'),
(103, 1, 1, 35, 1, 16, 36, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 21:55:36', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:55:36', '2026-03-08 21:55:36'),
(104, 1, 1, 35, 1, 16, 36, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 21:55:36', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:55:36', '2026-03-08 21:55:36'),
(105, 1, 1, 36, 1, 9, 37, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 21:58:45', NULL, 'Guest Checked In at room #9', NULL, 0, 'PM', 0, '2026-03-08 21:58:45', '2026-03-08 21:58:45'),
(106, 1, 1, 36, 1, 9, 37, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 21:58:45', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 21:58:45', '2026-03-08 21:58:45'),
(107, 1, 1, 36, 1, 9, 37, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 21:58:45', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 21:58:45', '2026-03-08 21:58:45'),
(108, 1, 1, 37, 1, 7, 38, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 22:02:57', NULL, 'Guest Checked In at room #7', NULL, 0, 'PM', 0, '2026-03-08 22:02:57', '2026-03-08 22:02:57'),
(109, 1, 1, 37, 1, 7, 38, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 22:02:57', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:02:57', '2026-03-08 22:02:57'),
(110, 1, 1, 37, 1, 7, 38, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 22:02:57', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 22:02:57', '2026-03-08 22:02:57'),
(111, 1, 1, 38, 1, 110, 39, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 22:06:05', NULL, 'Guest Checked In at room #155', NULL, 0, 'PM', 0, '2026-03-08 22:06:05', '2026-03-08 22:06:05'),
(112, 1, 1, 38, 1, 110, 39, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 22:06:05', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:06:05', '2026-03-08 22:06:05'),
(113, 1, 1, 39, 1, 6, 40, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 22:09:36', NULL, 'Guest Checked In at room #6', NULL, 0, 'PM', 0, '2026-03-08 22:09:36', '2026-03-08 22:09:36'),
(114, 1, 1, 39, 1, 6, 40, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 22:09:36', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:09:36', '2026-03-08 22:09:36'),
(115, 1, 1, 39, 1, 6, 40, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 22:09:36', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 22:09:36', '2026-03-08 22:09:36'),
(116, 1, 1, 27, 1, 13, 28, 1, 8, '\"[2,\\\"N\\\\/A\\\"]\"', 'Amenities', 20, 20, 0, 0, '2026-03-08 22:24:25', NULL, 'Guest Added Amenities: (1) EXTRA TOWEL', NULL, 0, 'PM', 0, '2026-03-08 22:24:23', '2026-03-08 22:24:25'),
(117, 1, 1, 27, 1, 13, 28, 1, 5, '\"[2,\\\"N\\\\/A\\\"]\"', 'Cashout', 20, 20, 0, 0, '2026-03-08 22:24:25', NULL, 'Cashout from paying deposit (Amenities)', NULL, 0, 'PM', 0, '2026-03-08 22:24:25', '2026-03-08 22:24:25'),
(118, 1, 1, 40, 1, 126, 41, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 1000, 408, 0, '2026-03-08 22:29:20', NULL, 'Guest Checked In at room #171', NULL, 0, 'PM', 0, '2026-03-08 22:29:20', '2026-03-08 22:29:20'),
(119, 1, 1, 40, 1, 126, 41, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 1000, 408, 200, '2026-03-08 22:29:20', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:29:20', '2026-03-08 22:29:20'),
(120, 1, 1, 40, 1, 126, 41, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 408, 1000, 0, 408, '2026-03-08 22:29:20', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 22:29:20', '2026-03-08 22:29:20'),
(121, 1, 1, 40, 1, 126, 41, 3, 9, '\"[2,\\\"N\\\\/A\\\"]\"', 'Food and Beverages', 78, 78, 0, 0, '2026-03-08 22:32:14', NULL, 'Guest Added Food and Beverages: (Front Desk) (2) TOOTHBRUSH', NULL, 0, 'PM', 0, '2026-03-08 22:32:08', '2026-03-08 22:32:14'),
(122, 1, 1, 40, 1, 126, 41, 3, 5, '\"[2,\\\"N\\\\/A\\\"]\"', 'Cashout', 78, 78, 0, 0, '2026-03-08 22:32:14', NULL, 'Cashout from paying deposit (Food and Beverages)', NULL, 0, 'PM', 0, '2026-03-08 22:32:14', '2026-03-08 22:32:14'),
(123, 1, 1, 40, 1, 126, 41, 3, 9, '\"[2,\\\"N\\\\/A\\\"]\"', 'Food and Beverages', 15, 15, 0, 0, '2026-03-08 22:32:39', NULL, 'Guest Added Food and Beverages: (Front Desk) (1) CONDITIONER', NULL, 0, 'PM', 0, '2026-03-08 22:32:29', '2026-03-08 22:32:39'),
(124, 1, 1, 40, 1, 126, 41, 3, 5, '\"[2,\\\"N\\\\/A\\\"]\"', 'Cashout', 15, 15, 0, 0, '2026-03-08 22:32:39', NULL, 'Cashout from paying deposit (Food and Beverages)', NULL, 0, 'PM', 0, '2026-03-08 22:32:39', '2026-03-08 22:32:39'),
(125, 1, 1, 40, 1, 126, 41, 3, 9, '\"[2,\\\"N\\\\/A\\\"]\"', 'Food and Beverages', 12, 0, 0, 0, '2026-03-08 22:33:28', NULL, 'Guest Added Food and Beverages: (Front Desk) (4) SOAP', NULL, 0, 'PM', 0, '2026-03-08 22:33:16', '2026-03-08 22:33:28'),
(126, 1, 1, 40, 1, 126, 41, 3, 5, '\"[2,\\\"N\\\\/A\\\"]\"', 'Cashout', 12, 12, 0, 0, '2026-03-08 22:33:28', NULL, 'Cashout from paying all unpaid balances', NULL, 0, 'PM', 0, '2026-03-08 22:33:28', '2026-03-08 22:33:28'),
(127, 1, 1, 40, 1, 126, 41, 3, 9, '\"[2,\\\"N\\\\/A\\\"]\"', 'Food and Beverages', 15, 0, 0, 0, '2026-03-08 22:34:39', NULL, 'Guest Added Food and Beverages: (Front Desk) (.5) TOOTHPASTE', NULL, 0, 'PM', 0, '2026-03-08 22:34:32', '2026-03-08 22:34:39'),
(128, 1, 1, 40, 1, 126, 41, 3, 5, '\"[2,\\\"N\\\\/A\\\"]\"', 'Cashout', 15, 15, 0, 0, '2026-03-08 22:34:39', NULL, 'Cashout from paying all unpaid balances', NULL, 0, 'PM', 0, '2026-03-08 22:34:39', '2026-03-08 22:34:39'),
(129, 1, 1, 41, 1, 107, 42, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 542, 0, 0, '2026-03-08 22:36:57', NULL, 'Guest Checked In at room #152', NULL, 0, 'PM', 0, '2026-03-08 22:36:57', '2026-03-08 22:36:57'),
(130, 1, 1, 41, 1, 107, 42, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 542, 0, 200, '2026-03-08 22:36:57', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:36:57', '2026-03-08 22:36:57'),
(131, 1, 1, 42, 1, 108, 43, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 392, 0, 0, '2026-03-08 23:30:14', NULL, 'Guest Checked In at room #131', NULL, 0, 'PM', 0, '2026-03-08 22:37:28', '2026-03-08 23:30:14'),
(132, 1, 1, 42, 1, 108, 43, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 592, 0, 200, '2026-03-08 22:37:28', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:37:28', '2026-03-08 22:37:28'),
(133, 1, 1, 43, 1, 112, 44, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 22:40:11', NULL, 'Guest Checked In at room #157', NULL, 0, 'PM', 0, '2026-03-08 22:40:11', '2026-03-08 22:40:11'),
(134, 1, 1, 43, 1, 112, 44, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 22:40:11', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:40:11', '2026-03-08 22:40:11'),
(135, 1, 1, 44, 1, 179, 45, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 336, 536, 0, 0, '2026-03-08 22:41:04', NULL, 'Guest Checked In at room #4D', NULL, 0, 'PM', 0, '2026-03-08 22:41:04', '2026-03-08 22:41:04'),
(136, 1, 1, 44, 1, 179, 45, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 536, 0, 200, '2026-03-08 22:41:04', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:41:04', '2026-03-08 22:41:04'),
(137, 1, 1, 45, 1, 49, 46, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 22:41:49', NULL, 'Guest Checked In at room #66', NULL, 0, 'PM', 0, '2026-03-08 22:41:49', '2026-03-08 22:41:49'),
(138, 1, 1, 45, 1, 49, 46, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 22:41:49', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:41:49', '2026-03-08 22:41:49'),
(139, 1, 1, 46, 1, 45, 47, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 616, 949, 133, 0, '2026-03-08 22:43:25', NULL, 'Guest Checked In at room #62', NULL, 0, 'PM', 0, '2026-03-08 22:43:25', '2026-03-08 22:43:25'),
(140, 1, 1, 46, 1, 45, 47, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 949, 133, 200, '2026-03-08 22:43:25', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:43:25', '2026-03-08 22:43:25'),
(141, 1, 1, 46, 1, 45, 47, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 133, 949, 0, 133, '2026-03-08 22:43:25', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 22:43:25', '2026-03-08 22:43:25'),
(142, 1, 1, 47, 1, 18, 48, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 336, 536, 0, 0, '2026-03-08 22:45:03', NULL, 'Guest Checked In at room #19', NULL, 0, 'PM', 0, '2026-03-08 22:45:03', '2026-03-08 22:45:03'),
(143, 1, 1, 47, 1, 18, 48, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 536, 0, 200, '2026-03-08 22:45:03', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:45:03', '2026-03-08 22:45:03'),
(144, 1, 1, 48, 1, 229, 49, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 224, 424, 0, 0, '2026-03-08 22:45:11', NULL, 'Guest Checked In at room #5E', NULL, 0, 'PM', 0, '2026-03-08 22:45:11', '2026-03-08 22:45:11'),
(145, 1, 1, 48, 1, 229, 49, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 424, 0, 200, '2026-03-08 22:45:11', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:45:11', '2026-03-08 22:45:11'),
(146, 1, 1, 49, 1, 116, 50, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 22:58:28', NULL, 'Guest Checked In at room #161', NULL, 0, 'PM', 0, '2026-03-08 22:58:28', '2026-03-08 22:58:28'),
(147, 1, 1, 49, 1, 116, 50, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 22:58:28', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:58:28', '2026-03-08 22:58:28'),
(148, 1, 1, 49, 1, 116, 50, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 22:58:28', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 22:58:28', '2026-03-08 22:58:28'),
(149, 1, 1, 50, 1, 117, 52, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 22:59:16', NULL, 'Guest Checked In at room #162', NULL, 0, 'PM', 0, '2026-03-08 22:59:16', '2026-03-08 22:59:16'),
(150, 1, 1, 50, 1, 117, 52, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 22:59:16', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:59:16', '2026-03-08 22:59:16'),
(151, 1, 1, 51, 1, 26, 51, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 22:59:59', NULL, 'Guest Checked In at room #27', NULL, 0, 'PM', 0, '2026-03-08 22:59:59', '2026-03-08 22:59:59'),
(152, 1, 1, 51, 1, 26, 51, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 22:59:59', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 22:59:59', '2026-03-08 22:59:59'),
(153, 1, 1, 51, 1, 26, 51, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 22:59:59', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 22:59:59', '2026-03-08 22:59:59'),
(154, 1, 1, 51, 1, 26, 51, 1, 9, '\"[2,\\\"N\\\\/A\\\"]\"', 'Food and Beverages', 45, 45, 0, 0, '2026-03-08 23:00:44', NULL, 'Guest Added Food and Beverages: (Front Desk) (1) 1 LITER MINERAL', NULL, 0, 'PM', 0, '2026-03-08 23:00:34', '2026-03-08 23:00:44'),
(155, 1, 1, 52, 1, 89, 53, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 224, 424, 0, 0, '2026-03-08 23:07:43', NULL, 'Guest Checked In at room #124', NULL, 0, 'PM', 0, '2026-03-08 23:07:43', '2026-03-08 23:07:43'),
(156, 1, 1, 52, 1, 89, 53, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 424, 0, 200, '2026-03-08 23:07:43', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 23:07:43', '2026-03-08 23:07:43'),
(157, 1, 1, 53, 1, 10, 54, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 23:21:14', NULL, 'Guest Checked In at room #10', NULL, 0, 'PM', 0, '2026-03-08 23:21:14', '2026-03-08 23:21:14'),
(158, 1, 1, 53, 1, 10, 54, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 23:21:14', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 23:21:14', '2026-03-08 23:21:14'),
(159, 1, 1, 54, 1, 20, 55, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 23:22:09', NULL, 'Guest Checked In at room #21', NULL, 0, 'PM', 0, '2026-03-08 23:22:09', '2026-03-08 23:22:09'),
(160, 1, 1, 54, 1, 20, 55, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 23:22:09', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 23:22:09', '2026-03-08 23:22:09'),
(161, 1, 1, 42, 1, 96, 43, 3, 7, '\"[2,\\\"N\\\\/A\\\"]\"', 'Room Transfer', 0, 0, 0, 0, '2026-03-08 00:00:00', NULL, 'Guest Transfered from Room #153 ( Double size Bed) to Room #131 ( Double size Bed) - Reason: LEAKING', 1, 0, 'PM', 0, '2026-03-08 23:30:14', '2026-03-08 23:30:14'),
(162, 1, 1, 55, 1, 175, 56, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 23:32:12', NULL, 'Guest Checked In at room #252', NULL, 0, 'PM', 0, '2026-03-08 23:32:12', '2026-03-08 23:32:12'),
(163, 1, 1, 55, 1, 175, 56, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 23:32:12', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 23:32:12', '2026-03-08 23:32:12'),
(164, 1, 1, 56, 1, 143, 57, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 23:42:26', NULL, 'Guest Checked In at room #209', NULL, 0, 'PM', 0, '2026-03-08 23:42:26', '2026-03-08 23:42:26'),
(165, 1, 1, 56, 1, 143, 57, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 23:42:26', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 23:42:26', '2026-03-08 23:42:26'),
(166, 1, 1, 57, 1, 61, 59, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 23:44:18', NULL, 'Guest Checked In at room #78', NULL, 0, 'PM', 0, '2026-03-08 23:44:18', '2026-03-08 23:44:18'),
(167, 1, 1, 57, 1, 61, 59, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 23:44:18', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 23:44:18', '2026-03-08 23:44:18'),
(168, 1, 1, 58, 1, 113, 60, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 23:46:02', NULL, 'Guest Checked In at room #158', NULL, 0, 'PM', 0, '2026-03-08 23:46:02', '2026-03-08 23:46:02'),
(169, 1, 1, 58, 1, 113, 60, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 23:46:02', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 23:46:02', '2026-03-08 23:46:02'),
(170, 1, 1, 27, 1, 27, 28, 1, 7, '\"[2,\\\"N\\\\/A\\\"]\"', 'Room Transfer', 0, 0, 0, 0, '2026-03-08 00:00:00', NULL, 'Guest Transfered from Room #14 ( Double size Bed) to Room #28 ( Double size Bed) - Reason: LEAKING', 1, 0, 'PM', 0, '2026-03-08 23:46:56', '2026-03-08 23:46:56'),
(171, 1, 1, 59, 1, 118, 61, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 23:53:45', NULL, 'Guest Checked In at room #163', NULL, 0, 'PM', 0, '2026-03-08 23:53:45', '2026-03-08 23:53:45'),
(172, 1, 1, 59, 1, 118, 61, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 23:53:45', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 23:53:45', '2026-03-08 23:53:45'),
(173, 1, 1, 60, 1, 123, 62, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-08 23:57:35', NULL, 'Guest Checked In at room #168', NULL, 0, 'PM', 0, '2026-03-08 23:57:35', '2026-03-08 23:57:35'),
(174, 1, 1, 60, 1, 123, 62, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-08 23:57:35', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 23:57:35', '2026-03-08 23:57:35'),
(175, 1, 1, 61, 1, 211, 63, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-08 23:58:11', NULL, 'Guest Checked In at room #281', NULL, 0, 'PM', 0, '2026-03-08 23:58:11', '2026-03-08 23:58:11'),
(176, 1, 1, 61, 1, 211, 63, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-08 23:58:11', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-08 23:58:11', '2026-03-08 23:58:11'),
(177, 1, 1, 61, 1, 211, 63, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-08 23:58:11', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-08 23:58:11', '2026-03-08 23:58:11');
INSERT INTO `transactions` (`id`, `branch_id`, `shift_log_id`, `checkin_detail_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(178, 1, 1, 17, 1, 92, 18, 3, 8, '\"[2,\\\"N\\\\/A\\\"]\"', 'Amenities', 20, 20, 0, 0, '2026-03-09 00:06:06', NULL, 'Guest Added Amenities: (1) EXTRA TOWEL', NULL, 0, 'PM', 0, '2026-03-09 00:05:56', '2026-03-09 00:06:06');
INSERT INTO `transactions` (`id`, `branch_id`, `shift_log_id`, `checkin_detail_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(179, 1, 1, 62, 1, 136, 64, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 00:12:11', NULL, 'Guest Checked In at room #202', NULL, 0, 'PM', 0, '2026-03-09 00:12:11', '2026-03-09 00:12:11');
INSERT INTO `transactions` (`id`, `branch_id`, `shift_log_id`, `checkin_detail_id`, `cash_drawer_id`, `room_id`, `guest_id`, `floor_id`, `transaction_type_id`, `assigned_frontdesk_id`, `description`, `payable_amount`, `paid_amount`, `change_amount`, `deposit_amount`, `paid_at`, `override_at`, `remarks`, `transfer_reason_id`, `is_co`, `shift`, `is_override`, `created_at`, `updated_at`) VALUES
(180, 1, 1, 62, 1, 136, 64, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 00:12:11', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 00:12:11', '2026-03-09 00:12:11'),
(181, 1, 1, 63, 1, 124, 65, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-09 00:14:57', NULL, 'Guest Checked In at room #169', NULL, 0, 'PM', 0, '2026-03-09 00:14:57', '2026-03-09 00:14:57'),
(182, 1, 1, 63, 1, 124, 65, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-09 00:14:57', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 00:14:57', '2026-03-09 00:14:57'),
(183, 1, 1, 63, 1, 124, 65, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-09 00:14:57', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 00:14:57', '2026-03-09 00:14:57'),
(184, 1, 1, 64, 1, 125, 66, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 00:16:53', NULL, 'Guest Checked In at room #170', NULL, 0, 'PM', 0, '2026-03-09 00:16:53', '2026-03-09 00:16:53'),
(185, 1, 1, 64, 1, 125, 66, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 00:16:53', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 00:16:53', '2026-03-09 00:16:53'),
(186, 1, 1, 65, 1, 185, 67, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 00:26:31', NULL, 'Guest Checked In at room #255', NULL, 0, 'PM', 0, '2026-03-09 00:26:31', '2026-03-09 00:26:31'),
(187, 1, 1, 65, 1, 185, 67, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 00:26:31', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 00:26:31', '2026-03-09 00:26:31'),
(188, 1, 1, 66, 1, 215, 68, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-09 00:26:56', NULL, 'Guest Checked In at room #285', NULL, 0, 'PM', 0, '2026-03-09 00:26:56', '2026-03-09 00:26:56'),
(189, 1, 1, 66, 1, 215, 68, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-09 00:26:57', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 00:26:57', '2026-03-09 00:26:57'),
(190, 1, 1, 66, 1, 215, 68, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-09 00:26:57', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 00:26:57', '2026-03-09 00:26:57'),
(191, 1, 1, 67, 1, 193, 70, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-09 00:28:22', NULL, 'Guest Checked In at room #263', NULL, 0, 'PM', 0, '2026-03-09 00:28:22', '2026-03-09 00:28:22'),
(192, 1, 1, 67, 1, 193, 70, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-09 00:28:22', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 00:28:22', '2026-03-09 00:28:22'),
(193, 1, 1, 67, 1, 193, 70, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-09 00:28:22', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 00:28:22', '2026-03-09 00:28:22'),
(194, 1, 1, 67, 1, 193, 70, 5, 9, '\"[2,\\\"N\\\\/A\\\"]\"', 'Food and Beverages', 6, 6, 0, 0, '2026-03-09 00:32:45', NULL, 'Guest Added Food and Beverages: (Front Desk) (2) SOAP', NULL, 0, 'PM', 0, '2026-03-09 00:32:40', '2026-03-09 00:32:45'),
(195, 1, 1, 67, 1, 193, 70, 5, 5, '\"[2,\\\"N\\\\/A\\\"]\"', 'Cashout', 6, 6, 0, 0, '2026-03-09 00:32:45', NULL, 'Cashout from paying deposit (Food and Beverages)', NULL, 0, 'PM', 0, '2026-03-09 00:32:45', '2026-03-09 00:32:45'),
(196, 1, 1, 68, 1, 50, 71, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-09 01:03:52', NULL, 'Guest Checked In at room #67', NULL, 0, 'PM', 0, '2026-03-09 01:03:52', '2026-03-09 01:03:52'),
(197, 1, 1, 68, 1, 50, 71, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-09 01:03:52', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 01:03:52', '2026-03-09 01:03:52'),
(198, 1, 1, 68, 1, 50, 71, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-09 01:03:52', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 01:03:52', '2026-03-09 01:03:52'),
(199, 1, 1, 69, 1, 2, 72, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 280, 0, 0, '2026-03-09 01:19:28', NULL, 'Guest Checked In at room #272', NULL, 0, 'PM', 0, '2026-03-09 01:11:20', '2026-03-09 01:19:28'),
(200, 1, 1, 69, 1, 2, 72, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 01:11:20', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 01:11:20', '2026-03-09 01:11:20'),
(201, 1, 1, 69, 1, 202, 72, 5, 7, '\"[2,\\\"N\\\\/A\\\"]\"', 'Room Transfer', 0, 0, 0, 0, '2026-03-09 00:00:00', NULL, 'Guest Transfered from Room #2 ( Double size Bed) to Room #272 ( Double size Bed) - Reason: LEAKING', 1, 0, 'PM', 0, '2026-03-09 01:19:28', '2026-03-09 01:19:28'),
(202, 1, 1, 70, 1, 122, 73, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-09 01:22:10', NULL, 'Guest Checked In at room #167', NULL, 0, 'PM', 0, '2026-03-09 01:22:10', '2026-03-09 01:22:10'),
(203, 1, 1, 70, 1, 122, 73, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-09 01:22:10', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 01:22:10', '2026-03-09 01:22:10'),
(204, 1, 1, 70, 1, 122, 73, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-09 01:22:10', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 01:22:10', '2026-03-09 01:22:10'),
(205, 1, 1, 71, 1, 199, 74, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-09 01:36:39', NULL, 'Guest Checked In at room #269', NULL, 0, 'PM', 0, '2026-03-09 01:36:39', '2026-03-09 01:36:39'),
(206, 1, 1, 71, 1, 199, 74, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-09 01:36:39', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 01:36:39', '2026-03-09 01:36:39'),
(207, 1, 1, 71, 1, 199, 74, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-09 01:36:39', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 01:36:39', '2026-03-09 01:36:39'),
(208, 1, 1, 72, 1, 224, 75, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 280, 0, 0, '2026-03-09 01:51:51', NULL, 'Guest Checked In at room #136', NULL, 0, 'PM', 0, '2026-03-09 01:44:43', '2026-03-09 01:51:51'),
(209, 1, 1, 72, 1, 224, 75, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 01:44:43', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 01:44:43', '2026-03-09 01:44:43'),
(210, 1, 1, 72, 1, 101, 75, 3, 7, '\"[2,\\\"N\\\\/A\\\"]\"', 'Room Transfer', 0, 0, 0, 0, '2026-03-09 00:00:00', NULL, 'Guest Transfered from Room #294 ( Double size Bed) to Room #136 ( Double size Bed) - Reason: LEAKING', 1, 0, 'PM', 0, '2026-03-09 01:51:51', '2026-03-09 01:51:51'),
(211, 1, 1, 73, 1, 200, 76, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 02:02:14', NULL, 'Guest Checked In at room #270', NULL, 0, 'PM', 0, '2026-03-09 02:02:14', '2026-03-09 02:02:14'),
(212, 1, 1, 73, 1, 200, 76, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 02:02:14', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 02:02:14', '2026-03-09 02:02:14'),
(213, 1, 1, 25, 1, 120, 26, 3, 6, '\"[2,\\\"N\\\\/A\\\"]\"', 'Extension', 112, 112, 0, 0, '2026-03-09 02:34:41', NULL, 'Guest Extension : 6 hours', NULL, 0, 'PM', 0, '2026-03-09 02:34:21', '2026-03-09 02:34:41'),
(214, 1, 1, 74, 1, 157, 77, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 616, 884, 68, 0, '2026-03-09 02:35:29', NULL, 'Guest Checked In at room #224', NULL, 0, 'PM', 0, '2026-03-09 02:35:29', '2026-03-09 02:35:29'),
(215, 1, 1, 74, 1, 157, 77, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 884, 68, 200, '2026-03-09 02:35:30', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 02:35:30', '2026-03-09 02:35:30'),
(216, 1, 1, 74, 1, 157, 77, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 68, 884, 0, 68, '2026-03-09 02:35:30', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 02:35:30', '2026-03-09 02:35:30'),
(217, 1, 1, 75, 1, 97, 78, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 02:49:25', NULL, 'Guest Checked In at room #132', NULL, 0, 'PM', 0, '2026-03-09 02:49:25', '2026-03-09 02:49:25'),
(218, 1, 1, 75, 1, 97, 78, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 02:49:25', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 02:49:25', '2026-03-09 02:49:25'),
(219, 1, 1, 76, 1, 2, 79, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 02:56:04', NULL, 'Guest Checked In at room #2', NULL, 0, 'PM', 0, '2026-03-09 02:56:04', '2026-03-09 02:56:04'),
(220, 1, 1, 76, 1, 2, 79, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 02:56:04', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 02:56:04', '2026-03-09 02:56:04'),
(221, 1, 1, 77, 1, 204, 80, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-09 02:59:56', NULL, 'Guest Checked In at room #274', NULL, 0, 'PM', 0, '2026-03-09 02:59:56', '2026-03-09 02:59:56'),
(222, 1, 1, 77, 1, 204, 80, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-09 02:59:56', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 02:59:56', '2026-03-09 02:59:56'),
(223, 1, 1, 77, 1, 204, 80, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-09 02:59:56', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 02:59:56', '2026-03-09 02:59:56'),
(224, 1, 1, 78, 1, 206, 81, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-09 03:01:19', NULL, 'Guest Checked In at room #276', NULL, 0, 'PM', 0, '2026-03-09 03:01:19', '2026-03-09 03:01:19'),
(225, 1, 1, 78, 1, 206, 81, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-09 03:01:19', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 03:01:19', '2026-03-09 03:01:19'),
(226, 1, 1, 78, 1, 206, 81, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-09 03:01:19', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 03:01:19', '2026-03-09 03:01:19'),
(227, 1, 1, 79, 1, 225, 82, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 336, 1000, 464, 0, '2026-03-09 03:39:04', NULL, 'Guest Checked In at room #5A', NULL, 0, 'PM', 0, '2026-03-09 03:39:04', '2026-03-09 03:39:04'),
(228, 1, 1, 79, 1, 225, 82, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 1000, 464, 200, '2026-03-09 03:39:04', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 03:39:04', '2026-03-09 03:39:04'),
(229, 1, 1, 79, 1, 225, 82, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 464, 1000, 0, 464, '2026-03-09 03:39:04', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 03:39:04', '2026-03-09 03:39:04'),
(230, 1, 1, 15, 1, 17, 16, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 0, 0, 8, '2026-03-09 03:43:41', NULL, 'Guest Deposit: change', NULL, 0, 'PM', 0, '2026-03-09 03:43:41', '2026-03-09 03:43:41'),
(231, 1, 1, 15, 1, 17, 16, 1, 5, '\"[2,\\\"N\\\\/A\\\"]\"', 'Cashout', 8, 0, 0, 8, '2026-03-09 03:43:57', NULL, 'Guest Deduction of Deposit: ₱8 deducted.', NULL, 0, 'PM', 0, '2026-03-09 03:43:57', '2026-03-09 03:43:57'),
(232, 1, 1, 80, 1, 85, 83, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 03:48:04', NULL, 'Guest Checked In at room #120', NULL, 0, 'PM', 0, '2026-03-09 03:48:04', '2026-03-09 03:48:04'),
(233, 1, 1, 80, 1, 85, 83, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 03:48:04', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 03:48:04', '2026-03-09 03:48:04'),
(234, 1, 1, 81, 1, 207, 84, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 03:48:51', NULL, 'Guest Checked In at room #277', NULL, 0, 'PM', 0, '2026-03-09 03:48:51', '2026-03-09 03:48:51'),
(235, 1, 1, 81, 1, 207, 84, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 03:48:51', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 03:48:51', '2026-03-09 03:48:51'),
(236, 1, 1, 82, 1, 216, 85, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 03:49:00', NULL, 'Guest Checked In at room #286', NULL, 0, 'PM', 0, '2026-03-09 03:49:00', '2026-03-09 03:49:00'),
(237, 1, 1, 82, 1, 216, 85, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 03:49:00', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 03:49:00', '2026-03-09 03:49:00'),
(238, 1, 1, 83, 1, 208, 86, 5, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 03:58:44', NULL, 'Guest Checked In at room #278', NULL, 0, 'PM', 0, '2026-03-09 03:58:44', '2026-03-09 03:58:44'),
(239, 1, 1, 83, 1, 208, 86, 5, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 03:58:44', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 03:58:44', '2026-03-09 03:58:44'),
(240, 1, 1, 84, 1, 55, 87, 2, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 04:00:08', NULL, 'Guest Checked In at room #72', NULL, 0, 'PM', 0, '2026-03-09 04:00:08', '2026-03-09 04:00:08'),
(241, 1, 1, 84, 1, 55, 87, 2, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 04:00:08', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 04:00:08', '2026-03-09 04:00:08'),
(242, 1, 1, 85, 1, 141, 89, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 04:09:57', NULL, 'Guest Checked In at room #207', NULL, 0, 'PM', 0, '2026-03-09 04:09:57', '2026-03-09 04:09:57'),
(243, 1, 1, 85, 1, 141, 89, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 04:09:57', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 04:09:57', '2026-03-09 04:09:57'),
(244, 1, 1, 47, 1, 18, 48, 1, 6, '\"[2,\\\"N\\\\/A\\\"]\"', 'Extension', 112, 112, 0, 0, '2026-03-09 04:10:43', NULL, 'Guest Extension : 6 hours', NULL, 0, 'PM', 0, '2026-03-09 04:10:26', '2026-03-09 04:10:43'),
(245, 1, 1, 48, 1, 229, 49, 5, 6, '\"[2,\\\"N\\\\/A\\\"]\"', 'Extension', 112, 112, 0, 0, '2026-03-09 04:11:15', NULL, 'Guest Extension : 6 hours', NULL, 0, 'PM', 0, '2026-03-09 04:11:03', '2026-03-09 04:11:15'),
(246, 1, 1, 86, 1, 135, 90, 4, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 616, 1000, 184, 0, '2026-03-09 04:12:30', NULL, 'Guest Checked In at room #201', NULL, 0, 'PM', 0, '2026-03-09 04:12:30', '2026-03-09 04:12:30'),
(247, 1, 1, 86, 1, 135, 90, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 1000, 184, 200, '2026-03-09 04:12:30', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 04:12:30', '2026-03-09 04:12:30'),
(248, 1, 1, 86, 1, 135, 90, 4, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 184, 1000, 0, 184, '2026-03-09 04:12:30', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 04:12:30', '2026-03-09 04:12:30'),
(249, 1, 1, 87, 1, 10, 92, 1, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 280, 480, 0, 0, '2026-03-09 04:32:59', NULL, 'Guest Checked In at room #10', NULL, 0, 'PM', 0, '2026-03-09 04:32:59', '2026-03-09 04:32:59'),
(250, 1, 1, 87, 1, 10, 92, 1, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 480, 0, 200, '2026-03-09 04:32:59', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 04:32:59', '2026-03-09 04:32:59'),
(251, 1, 1, 83, 1, 208, 86, 5, 4, '\"[2,\\\"N\\\\/A\\\"]\"', 'Damage Charges', 100, 100, 0, 0, '2026-03-09 04:54:33', NULL, 'Guest Charged for Damage: (1) BLOOD STAIN LARGE', NULL, 0, 'PM', 0, '2026-03-09 04:51:55', '2026-03-09 04:54:33'),
(252, 1, 1, 83, 1, 208, 86, 5, 4, '\"[2,\\\"N\\\\/A\\\"]\"', 'Damage Charges', 100, 100, 0, 0, '2026-03-09 04:54:21', NULL, 'Guest Charged for Damage: (1) BLOOD STAIN LARGE', NULL, 0, 'PM', 0, '2026-03-09 04:54:02', '2026-03-09 04:54:21'),
(253, 1, 1, 83, 1, 208, 86, 5, 4, '\"[2,\\\"N\\\\/A\\\"]\"', 'Damage Charges', 100, 200, 100, 100, '2026-03-09 04:55:22', NULL, 'Guest Charged for Damage: (1) BLOOD STAIN LARGE', NULL, 0, 'PM', 0, '2026-03-09 04:55:07', '2026-03-09 04:55:22'),
(254, 1, 1, 88, 1, 130, 93, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 224, 424, 0, 0, '2026-03-09 05:07:25', NULL, 'Guest Checked In at room #3D', NULL, 0, 'PM', 0, '2026-03-09 05:07:25', '2026-03-09 05:07:25'),
(255, 1, 1, 88, 1, 130, 93, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 424, 0, 200, '2026-03-09 05:07:25', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 05:07:25', '2026-03-09 05:07:25'),
(256, 1, 1, 89, 1, 131, 94, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 336, 1000, 464, 0, '2026-03-09 05:42:14', NULL, 'Guest Checked In at room #3E', NULL, 0, 'PM', 0, '2026-03-09 05:42:14', '2026-03-09 05:42:14'),
(257, 1, 1, 89, 1, 131, 94, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 1000, 464, 200, '2026-03-09 05:42:14', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 05:42:14', '2026-03-09 05:42:14'),
(258, 1, 1, 89, 1, 131, 94, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 464, 1000, 0, 464, '2026-03-09 05:42:14', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 05:42:14', '2026-03-09 05:42:14'),
(259, 1, 1, 65, 1, 185, 67, 5, 6, '\"[2,\\\"N\\\\/A\\\"]\"', 'Extension', 112, 112, 0, 0, '2026-03-09 05:46:13', NULL, 'Guest Extension : 6 hours', NULL, 0, 'PM', 0, '2026-03-09 05:45:56', '2026-03-09 05:46:13'),
(260, 1, 1, 60, 1, 123, 62, 3, 6, '\"[2,\\\"N\\\\/A\\\"]\"', 'Extension', 112, 112, 0, 0, '2026-03-09 06:16:18', NULL, 'Guest Extension : 6 hours', NULL, 0, 'PM', 0, '2026-03-09 06:16:07', '2026-03-09 06:16:18'),
(261, 1, 1, 90, 1, 115, 95, 3, 1, '\"[2,\\\"N\\\\/A\\\"]\"', 'Guest Check In', 392, 600, 8, 0, '2026-03-09 06:17:42', NULL, 'Guest Checked In at room #160', NULL, 0, 'PM', 0, '2026-03-09 06:17:42', '2026-03-09 06:17:42'),
(262, 1, 1, 90, 1, 115, 95, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 200, 600, 8, 200, '2026-03-09 06:17:42', NULL, 'Deposit From Check In (Room Key & TV Remote)', NULL, 0, 'PM', 0, '2026-03-09 06:17:42', '2026-03-09 06:17:42'),
(263, 1, 1, 90, 1, 115, 95, 3, 2, '\"[2,\\\"N\\\\/A\\\"]\"', 'Deposit', 8, 600, 0, 8, '2026-03-09 06:17:42', NULL, 'Deposit From Check In (Excess Amount)', NULL, 0, 'PM', 0, '2026-03-09 06:17:42', '2026-03-09 06:17:42');

INSERT INTO `transfer_reasons` (`id`, `branch_id`, `reason`, `created_at`, `updated_at`) VALUES
(1, 1, 'LEAKING', '2026-03-08 23:23:27', '2026-03-08 23:23:27');
INSERT INTO `transfer_reasons` (`id`, `branch_id`, `reason`, `created_at`, `updated_at`) VALUES
(2, 1, 'small room', '2026-03-09 00:25:35', '2026-03-09 00:25:35');
INSERT INTO `transfer_reasons` (`id`, `branch_id`, `reason`, `created_at`, `updated_at`) VALUES
(3, 1, 'Big Space', '2026-03-09 00:25:52', '2026-03-09 00:25:52');
INSERT INTO `transfer_reasons` (`id`, `branch_id`, `reason`, `created_at`, `updated_at`) VALUES
(4, 1, 'window side', '2026-03-09 00:26:18', '2026-03-09 00:26:18'),
(5, 1, 'wiith table', '2026-03-09 00:26:33', '2026-03-09 00:26:33'),
(6, 1, 'nagasingaw(baho)', '2026-03-09 00:29:37', '2026-03-09 00:29:37'),
(7, 1, 'barado bowl', '2026-03-09 00:29:51', '2026-03-09 00:29:51'),
(8, 1, 'No water', '2026-03-09 00:30:02', '2026-03-09 00:30:02'),
(9, 1, 'Not Comfortable', '2026-03-09 00:30:50', '2026-03-09 00:30:50');

INSERT INTO `transfered_guest_reports` (`id`, `checkin_detail_id`, `previous_room_id`, `new_room_id`, `rate_id`, `previous_amount`, `new_amount`, `original_check_in_time`, `created_at`, `updated_at`) VALUES
(1, 42, 108, 96, 5, '392.00', '392.00', '2026-03-08 22:37:28', '2026-03-08 23:30:14', '2026-03-08 23:30:14');
INSERT INTO `transfered_guest_reports` (`id`, `checkin_detail_id`, `previous_room_id`, `new_room_id`, `rate_id`, `previous_amount`, `new_amount`, `original_check_in_time`, `created_at`, `updated_at`) VALUES
(2, 27, 13, 27, 6, '616.00', '616.00', '2026-03-08 21:33:18', '2026-03-08 23:46:56', '2026-03-08 23:46:56');
INSERT INTO `transfered_guest_reports` (`id`, `checkin_detail_id`, `previous_room_id`, `new_room_id`, `rate_id`, `previous_amount`, `new_amount`, `original_check_in_time`, `created_at`, `updated_at`) VALUES
(3, 69, 2, 202, 4, '280.00', '280.00', '2026-03-09 01:11:20', '2026-03-09 01:19:28', '2026-03-09 01:19:28');
INSERT INTO `transfered_guest_reports` (`id`, `checkin_detail_id`, `previous_room_id`, `new_room_id`, `rate_id`, `previous_amount`, `new_amount`, `original_check_in_time`, `created_at`, `updated_at`) VALUES
(4, 72, 224, 101, 4, '280.00', '280.00', '2026-03-09 01:44:43', '2026-03-09 01:51:51', '2026-03-09 01:51:51');

INSERT INTO `types` (`id`, `branch_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Single size Bed', NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `types` (`id`, `branch_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(2, 1, ' Double size Bed', NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59');
INSERT INTO `types` (`id`, `branch_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(3, 1, 'Twin size Bed', NULL, '2026-03-08 20:01:59', '2026-03-08 20:01:59');



INSERT INTO `users` (`id`, `branch_id`, `cash_drawer_id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `branch_name`, `remember_token`, `current_team_id`, `roomboy_assigned_floor_id`, `roomboy_cleaning_room_id`, `profile_photo_path`, `assigned_frontdesks`, `time_in`, `shift`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Superadmin', 'superadmin@gmail.com', NULL, '$2y$10$yu3pI089mhSasJUAsC/.BuhWnKkgyPbTcTDCVJfjaprzp3yTe.tLq', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 20:01:58', '2026-03-08 20:01:58');
INSERT INTO `users` (`id`, `branch_id`, `cash_drawer_id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `branch_name`, `remember_token`, `current_team_id`, `roomboy_assigned_floor_id`, `roomboy_cleaning_room_id`, `profile_photo_path`, `assigned_frontdesks`, `time_in`, `shift`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 1, NULL, 'Admin', 'admin@gmail.com', NULL, '$2y$10$17E4axWrn1Bob/pV3xzq0OuysG.6ZO30ag/M5b/eS2NfaU4AkK2DK', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 20:01:58', '2026-03-08 20:01:58');
INSERT INTO `users` (`id`, `branch_id`, `cash_drawer_id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `branch_name`, `remember_token`, `current_team_id`, `roomboy_assigned_floor_id`, `roomboy_cleaning_room_id`, `profile_photo_path`, `assigned_frontdesks`, `time_in`, `shift`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 1, NULL, 'Frontdesk', 'frontdesk@gmail.com', NULL, '$2y$10$BHTRJ3poG7h7Gr9fRsNAHO.hn200YI/v1AuKg7itlhHiC3RUcV0iu', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-08 20:01:58', '2026-03-08 22:10:52');
INSERT INTO `users` (`id`, `branch_id`, `cash_drawer_id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `branch_name`, `remember_token`, `current_team_id`, `roomboy_assigned_floor_id`, `roomboy_cleaning_room_id`, `profile_photo_path`, `assigned_frontdesks`, `time_in`, `shift`, `is_active`, `created_at`, `updated_at`) VALUES
(4, 1, NULL, 'Kiosk', 'kiosk@gmail.com', NULL, '$2y$10$NFaBydRG8ltW8VEUNsVohu5LOO4E69R0Bt5bTx01zr1ilFp9Mric6', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(5, 1, NULL, 'Kitchen', 'kitchen@gmail.com', NULL, '$2y$10$3ODhdmD7uO1mXpLYoslAPu2IhqbgYQJcFr1ekRw0uWxdg.UyALS0G', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(6, 1, NULL, 'Back Office', 'back-office@gmail.com', NULL, '$2y$10$ZzrsF68vWQSaqJQMhOxVF.wQ/YFZnbKD0EzfUE22IrYOI.7ivGATO', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(7, 1, NULL, 'Roomboy', 'roomboy@gmail.com', NULL, '$2y$10$weVHdRFr7U1R0JgYFD0YH.3aSKf/KrcG.A/nws9KgapNNaay6A71C', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 20:01:59', '2026-03-08 20:01:59'),
(8, 1, NULL, 'PUB Kitchen', 'pub-kitchen@gmail.com', NULL, '$2y$10$OHqrbxsbtSO22V1cLVT2befxnLTma4E5oX1roGArc035enktu2A/.', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 20:02:00', '2026-03-08 20:02:00'),
(9, 1, 1, 'Hannah', 'hannah@gmail.com', NULL, '$2y$10$EyVd.dYo9ufB.RNETYXZ4.UUlHE8GA2s0UWUw3s67mZ5mj3yK65ce', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, '\"[2,\\\"N\\\\/A\\\"]\"', '2026-03-08 20:03:41', 'PM', 1, '2026-03-08 20:03:25', '2026-03-08 20:03:41'),
(10, 1, NULL, 'GEORGE MENDOZA', 'george@gmail.com', NULL, '$2y$10$WLqUhnBg8xNp5iG/EnVd.OiuLineCuduHB7SHYozKkUtWtCNMgzhG', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 22:23:09', '2026-03-08 22:24:10'),
(11, 1, NULL, 'LEO ESTILLORE', 'leo@gmail.com', NULL, '$2y$10$.974ol04pwzd4SUrrhWKeuBSpJ0YUdrlw0vHkd4ziYFhi/KV7KHKS', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 22:27:37', '2026-03-08 22:28:04'),
(12, 1, NULL, 'FRANCISCO', 'pepoy@gmail.com', NULL, '$2y$10$un8x9.X//Xlbiq6fYXba.e2wZulsHhTNwdXEax3vLfka2lq97mHce', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 22:30:49', '2026-03-08 22:31:05'),
(13, 1, NULL, 'JOHN KARL', 'karl@gmail.com', NULL, '$2y$10$1bt.Q9oLiZdSAoRxbjk13eO.SNUQOjtr6TiRyoMhvPucZltLySHWa', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, 1, 6, NULL, NULL, NULL, NULL, 1, '2026-03-08 22:33:51', '2026-03-09 06:26:50'),
(14, 1, NULL, 'REYMART', 'reymart@gmail.com', NULL, '$2y$10$mbR/M0SnD3zd86jJDmF0K.auooXVNNnck4fMMRCuDOa9moFpDYnku', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, 3, 208, NULL, NULL, NULL, NULL, 1, '2026-03-08 22:36:17', '2026-03-09 06:18:19'),
(15, 1, NULL, 'RAMILL MILLADO', 'ramill@gmail.com', NULL, '$2y$10$qG2BoP/BM8koO3iG0tqyUemLgFmefkYRqvSa7slgpMvT56SQhsbsy', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 22:39:46', '2026-03-08 22:47:47'),
(16, 1, NULL, 'EDDIE SENAJON', 'eddie@gmail.com', NULL, '$2y$10$bIrzqYH.ACKhjArND1az3e3SV5eOdmJyVADegv2z06dKoD8o44dL2', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 22:41:07', '2026-03-08 22:48:00'),
(17, 1, NULL, 'CHRIS BARAN', 'chris@gmail.com', NULL, '$2y$10$5RkbXzRPEDK.Lpi2PCsEleyg1qgYlgAPE5K8CYJto7YV4ISHDWpQW', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 22:42:32', '2026-03-08 22:48:14'),
(18, 1, NULL, 'MOISES TUAZON', 'moises@gmail.com', NULL, '$2y$10$gPKqFv699TOUmvW.cEsx6.Gc7cR5QH0PWOuDywri1iC9khdDle.pW', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 22:43:49', '2026-03-08 22:48:28'),
(19, 1, NULL, 'REY SUMIL', 'rey@gmail.com', NULL, '$2y$10$vsTu8WygN2EMZIXlt05AVeh5QftKF.hcbDFsVFyYNHlp3o20WOYWu', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 22:46:03', '2026-03-08 22:48:43'),
(20, 1, NULL, 'RICKY MENDEZEBAL', 'ricky@gmail.com', NULL, '$2y$10$yNAD3Rax3cIOJLDcYG3dp.aN26JZtzK3mrJc2i8oTTFDI/pNHDxr2', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 22:47:23', '2026-03-08 22:49:02'),
(21, 1, NULL, 'JENEATH LECIAS', 'lecias@gmail.com', NULL, '$2y$10$yFkJKSiluaJotvFErH4FeOUWKQHIkHG1oEum4Y4QnfrMtRl7N9d52', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 23:07:11', '2026-03-08 23:07:11'),
(22, 1, NULL, 'RUBY GOLD', 'ruby@gmail.com', NULL, '$2y$10$dUgcFHSm7ky4eN6vD4p8be.gOhGj2x3APKczTZ8A3yk/jDuc5TgNW', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 23:08:44', '2026-03-08 23:08:44'),
(23, 1, NULL, 'KATHLEEN DREW', 'kath@gmail.com', NULL, '$2y$10$5wRT27kJx1isZ6e/eWVbje5H1Ene.HOWTOzb8UkBgwy1/QY71/RZW', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 23:10:10', '2026-03-08 23:10:10'),
(24, 1, NULL, 'SEANNE KARYLLE', 'seanne@gmail.com', NULL, '$2y$10$dCqiKDtNjC0bj5.WUu810eklbshYVJX1mVDicrW6YIsOrsjse/iqe', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 23:12:02', '2026-03-08 23:12:02'),
(25, 1, NULL, 'JINKY OBAG', 'jinky@gmail.com', NULL, '$2y$10$vYUgGhJK9gAt9p31uugMfezaJYP7U8s.oealqnq1Wd489sjl99ABq', NULL, NULL, NULL, 'ALMA RESIDENCES GENSAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-08 23:13:37', '2026-03-08 23:13:37');


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;