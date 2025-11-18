USE `trussmate`;

DROP TABLE IF EXISTS `user_account`;
CREATE TABLE `user_account`(
    `account_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_status` enum('active', 'inactive', 'locked') DEFAULT 'active' NOT NULL,
    `user_role` enum('super_admin', 'admin', 'estimator', 'production', 'customer', 'user', 'system') DEFAULT 'user' NOT NULL,
    `user_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
