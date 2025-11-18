USE `trussmate`;

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message`
(
    `account_no`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                                                                           NOT NULL,
    `user_id`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                                                                           NOT NULL,
    `record_id`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                                                                                    DEFAULT NULL,
    `message_id`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                                                                           NOT NULL,
    `sp_message_id`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                                                                                    DEFAULT NULL,
    `message_type`     enum ('quotation', 'order_confirmation', 'proforma_invoice', 'invoice', 'account_statement', 'password_reset', 'one_time_pin', 'other') NOT NULL DEFAULT 'other',
    `message_priority` enum ('low', 'medium', 'high')                                                                                                          NOT NULL DEFAULT 'medium',
    `subject`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                                                                           NOT NULL,
    `message`          LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                                                                               NOT NULL,
    `channel`          enum ('push_notification', 'instant_messaging', 'sms', 'email')                                                                         NOT NULL DEFAULT 'email',
    `created`          timestamp                                                                                                                               NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`         timestamp                                                                                                                               NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`message_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
