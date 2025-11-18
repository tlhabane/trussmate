USE `trussmate`;

DROP TABLE IF EXISTS `sale`;
CREATE TABLE `sale`
(
    `account_no`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `sale_id`             varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `sale_no`             INT                                                           NOT NULL AUTO_INCREMENT UNIQUE,
    `sale_status`         enum ('pending', 'started', 'cancelled', 'completed')                  DEFAULT 'pending' NOT NULL,
    `customer_id`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `contact_id`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `billing_address_id`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `delivery_address_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `delivery_required`   BOOLEAN                                                       NOT NULL DEFAULT FALSE,
    `labour_required`     BOOLEAN                                                       NOT NULL DEFAULT FALSE,
    `workflow_id`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `created`             timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`            timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sale_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
