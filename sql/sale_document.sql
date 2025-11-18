USE `trussmate`;

DROP TABLE IF EXISTS `sale_document`;
CREATE TABLE `sale_document`
(
    `user_id`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `sale_id`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `sale_task_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                DEFAULT NULL,
    `doc_id`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `doc_type`     enum ('floor_plan', 'estimate', 'proforma', 'invoice', 'quotation', 'other') DEFAULT 'other' NOT NULL,
    `doc_src`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `doc_name`     varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                DEFAULT NULL,
    `created`      timestamp                                                     NOT NULL       DEFAULT CURRENT_TIMESTAMP,
    `modified`     timestamp                                                     NOT NULL       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`doc_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
