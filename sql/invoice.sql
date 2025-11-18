USE `trussmate`;

DROP TABLE IF EXISTS `invoice`;
CREATE TABLE `invoice`
(
    `sale_task_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `invoice_no`   INT                                                           NOT NULL AUTO_INCREMENT UNIQUE,
    `invoice_type` ENUM ('invoice', 'proforma_invoice')                          NOT NULL DEFAULT 'invoice',
    `created`      timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`     timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`invoice_no`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
