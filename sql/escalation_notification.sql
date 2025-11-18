USE `trussmate`;

DROP TABLE IF EXISTS `escalation_notification`;
CREATE TABLE `escalation_notification`
(
    `escalation_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `username`      VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `created`       timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`      timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`escalation_id`, `username`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
