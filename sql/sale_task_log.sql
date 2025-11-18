USE `trussmate`;

DROP TABLE IF EXISTS `sale_task_log`;
CREATE TABLE `sale_task_log`
(
    `user_id`              varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `sale_task_id`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_id`              varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_no`              INT(11)                                                       NOT NULL DEFAULT 1,
    `task_status`          enum ('pending', 'started', 'cancelled', 'completed')         NOT NULL DEFAULT 'pending',
    `task_payment`         DECIMAL(19, 2)                                                NOT NULL DEFAULT 0 COMMENT 'Amount required for to enable task execution',
    `task_payment_type`    ENUM ('fixed', 'percentage', '0')                             NOT NULL DEFAULT '0',
    `task_days`            INT                                                           NOT NULL DEFAULT 0,
    `task_frequency`       INT                                                           NOT NULL DEFAULT 0 COMMENT 'Number of days between each task repetition',
    `task_completion_date` DATE                                                          NOT NULL DEFAULT (CURRENT_DATE),
    `comments`             TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NOT NULL,
    `created`              timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`             timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sale_task_id`, `created`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
