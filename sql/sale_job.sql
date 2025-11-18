USE `trussmate`;

DROP TABLE IF EXISTS `sale_job`;
CREATE TABLE `sale_job`
(
    `sale_id`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `job_no`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `job_description` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci   NOT NULL,
    `design_info`     JSON                                                          NOT NULL,
    `line_items`      JSON                                                          NOT NULL,
    `subtotal`        DECIMAL(19, 2)                                                NOT NULL DEFAULT 0,
    `vat`             DECIMAL(19, 2)                                                NOT NULL DEFAULT 0,
    `total`           DECIMAL(19, 2)                                                NOT NULL DEFAULT 0,
    `created`         timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`        timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sale_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
