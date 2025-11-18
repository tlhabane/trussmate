USE `trussmate`;

DROP TABLE IF EXISTS `sale_task`;
CREATE TABLE `sale_task`
(
    `sale_task_id`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `sale_id`              varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_id`              varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_no`              INT(11)                                                       NOT NULL DEFAULT 1,
    `task_status`          enum ('pending', 'started', 'cancelled', 'completed')                  DEFAULT 'pending' NOT NULL,
    `task_payment`         DECIMAL(19, 2)                                                NOT NULL DEFAULT 0 COMMENT 'Amount required for to enable task execution',
    `task_payment_type`    ENUM ('fixed', 'percentage', '0')                             NOT NULL DEFAULT '0',
    `task_days`            INT                                                           NOT NULL DEFAULT 0,
    `task_frequency`       INT                                                           NOT NULL DEFAULT 0 COMMENT 'Number of days between each task repetition',
    `task_completion_date` DATE                                                          NOT NULL DEFAULT (CURRENT_DATE),
    `created`              timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`             timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sale_task_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

/*ALTER TABLE `sale_task`
    ADD `task_payment`      DECIMAL(19, 2)                                                NOT NULL DEFAULT 0 COMMENT 'Amount required for to enable task execution' AFTER `task_status`,
    ADD `task_payment_type` ENUM ('fixed', 'percentage', '0')                             NOT NULL DEFAULT '0' AFTER `task_payment`,
    ADD `task_days`         INT                                                           NOT NULL DEFAULT 0 AFTER `task_payment_type`,
    ADD `task_frequency`    INT                                                           NOT NULL DEFAULT 0 COMMENT 'Number of days between each task repetition' AFTER `task_days`,*/

/*ALTER TABLE `sale_task`
    ADD `task_no`              INT(11) NOT NULL DEFAULT 1 AFTER `task_id`,
    ADD `task_completion_date` DATE    NOT NULL DEFAULT (CURRENT_DATE) AFTER `task_frequency`;*/
