USE `trussmate`;

DROP TABLE IF EXISTS `task`;
CREATE TABLE `task`
(
    `account_no`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_id`           varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_name`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_description`  MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                           DEFAULT NULL,
    `task_payment`      DECIMAL(19, 2)                                                NOT NULL                DEFAULT 0 COMMENT 'Amount required for to enable task execution',
    `task_payment_type` ENUM ('fixed', 'percentage', '0')                             NOT NULL                DEFAULT '0',
    `task_days`         INT                                                           NOT NULL                DEFAULT 0,
    `task_frequency`    INT                                                           NOT NULL                DEFAULT 0 COMMENT 'Number of days between each task repetition',
    `task_document`     BOOLEAN                                                       NOT NULL                DEFAULT FALSE COMMENT 'Are document(s) required to complete task',
    `task_action`       ENUM ('estimate', 'invoice', 'penalty', 'proforma_invoice', 'quotation', 'task', '0') DEFAULT 'task' NOT NULL,
    `created`           timestamp                                                     NOT NULL                DEFAULT CURRENT_TIMESTAMP,
    `modified`          timestamp                                                     NOT NULL                DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`task_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
