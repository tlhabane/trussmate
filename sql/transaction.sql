USE `trussmate`;

DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction`
(
    `invoice_no`            INT                                                                      NOT NULL,
    `account_no`            varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci            NOT NULL,
    `user_id`               varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci            NOT NULL,
    `transaction_id`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci            NOT NULL,
    `transaction_cancelled` TINYINT                                                                  NOT NULL DEFAULT 0,
    `transaction_amount`    DECIMAL(19, 2)                                                           NOT NULL DEFAULT 0,
    `transaction_type`      ENUM ('credit_memo', 'debit_memo', 'payment')                            NOT NULL DEFAULT 'payment',
    `transaction_date`      DATE                                                                     NOT NULL DEFAULT (CURRENT_DATE),
    `transaction_method`    ENUM ('bank_transfer', 'cash', 'credit_card', 'mobile_payment', 'other') NOT NULL DEFAULT 'bank_transfer',
    `transaction_desc`      TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                             DEFAULT NULL,
    `created`               timestamp                                                                NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`              timestamp                                                                NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`transaction_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
