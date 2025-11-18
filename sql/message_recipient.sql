USE `trussmate`;

DROP TABLE IF EXISTS `message_recipient`;
CREATE TABLE `message_recipient`
(
    `message_id`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `recipient_id`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `recipient_name`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `recipient_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `message_status`    enum ('queued', 'sent', 'failed', 'cancelled')                NOT NULL DEFAULT 'queued',
    `created`           timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`          timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`recipient_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
