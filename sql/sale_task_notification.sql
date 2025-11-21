USE `trussmate`;

DROP TABLE IF EXISTS `sale_task_notification`;
CREATE TABLE `sale_task_notification`
(
    `message_id`        VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci          NOT NULL,
    `notification_type` ENUM ('notification', 'reminder', 'escalation', 'escalation_reminder') NOT NULL DEFAULT 'notification',
    `sale_task_id`      VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci          NOT NULL,
    `created`           timestamp                                                              NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`          timestamp                                                              NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`message_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
