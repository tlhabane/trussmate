USE `trussmate`;

DROP TABLE IF EXISTS `workflow_task`;
CREATE TABLE `workflow_task`
(
    `workflow_id`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `workflow_task_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_id`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_no`          INT(11)                                                       NOT NULL                 DEFAULT 1,
    `task_optional`    BOOLEAN                                                                                DEFAULT FALSE,
    `trigger_type`     ENUM ('manual', 'automatic')                                  NOT NULL                 DEFAULT 'manual',
    `assigned_to`      enum ('super_admin', 'admin', 'estimator', 'production', 'customer', 'user', 'system') DEFAULT 'user' NOT NULL,
    `assignment_note`  MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci                            DEFAULT NULL,
    `created`          timestamp                                                     NOT NULL                 DEFAULT CURRENT_TIMESTAMP,
    `modified`         timestamp                                                     NOT NULL                 DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`workflow_task_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
