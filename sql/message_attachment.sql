USE `trussmate`;

DROP TABLE IF EXISTS `message_attachment`;
CREATE TABLE `message_attachment`
(
    `message_id`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `attachment_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `filename`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `file_source`   TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `created`       timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`      timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`attachment_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
