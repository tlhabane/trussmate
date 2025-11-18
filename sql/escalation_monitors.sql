USE `trussmate`;

DROP TABLE IF EXISTS `escalation_monitor`;
CREATE TABLE `escalation_monitor`
(
    `account_no`      VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `escalation_id`   VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `escalation_type` ENUM ('progress', 'overdue')                                  NOT NULL DEFAULT 'overdue',
    `escalation_days` INT                                                           NOT NULL DEFAULT 0,
    `created`         timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`        timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`escalation_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
