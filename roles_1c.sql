DROP TABLE IF EXISTS `role_hierarchy`;
DROP TABLE IF EXISTS `role_permission`;
DROP TABLE IF EXISTS `permission`;
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
	`id`	int(11) NOT NULL auto_increment,
	`name`	VARCHAR(255) NOT NULL,
	`role`  VARCHAR(9) NOT NULL,
	`description` TEXT,
	`date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE INDEX `name_idx` (`name`),
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `role` (`role`, `name`, `description`) values 
    ('000000001', 'administrator', 'Администратор'),
    ('000000002', 'developer', 'Разработчик'),
    ('000000003', 'analyst', 'Аналитик'),
    ('000000004', 'brand_manager', 'Бренд менеджер'),
    ('000000005', 'store_manager', 'Менеджер магазина'),
    ('000000006', 'guest', 'Гость');

-- DROP TABLE IF EXISTS `role_hierarchy`;

-- CREATE TABLE `role_hierarchy` (
-- 	`id`	int(11) auto_increment,
-- 	`parent_role_id` int(11) NOT NULL,
-- 	`child_role_id`  int(11) NOT NULL,
-- 	INDEX `par_ind` (`parent_role_id`),
-- 	INDEX `chld_ind` (`child_role_id`),
-- 	FOREIGN KEY `role_role_parent_role_id_fk` (`parent_role_id`) REFERENCES `role` (`id`)
-- 	ON UPDATE CASCADE
-- 	ON DELETE CASCADE,
-- 	FOREIGN KEY `role_role_child_role_id_fk` (`child_role_id`) REFERENCES `role` (`id`)
-- 	ON UPDATE CASCADE
-- 	ON DELETE CASCADE,
-- 	PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

CREATE TABLE `role_hierarchy` (
        `id`    int(11) NOT NULL auto_increment,
        `parent_role_id` int(11) NOT NULL,
        `child_role_id`  int(11) NOT NULL,
        `terminal` int(1) NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `role_hierarchy` (`parent_role_id`, `child_role_id`, `terminal`)
-- VALUES(0,1,1),(0,2,1),(0,3,1),(0,4,1),(0,5,1),(0,6,1);
VALUES(0,1,1),(0,2,1),(2,3,1),(3,4,1),(0,5,1),(0,6,1);

CREATE TABLE `permission` (
	`id`	int(11) NOT NULL auto_increment,
	`name` VARCHAR(255),
	`description` TEXT,
	`date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE INDEX `name_idx` (`name`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `permission` (`name`) VALUES ('administrator');
INSERT INTO `permission` (`name`) VALUES ('developer');
INSERT INTO `permission` (`name`) VALUES ('analyst');
INSERT INTO `permission` (`name`) VALUES ('brand.manager');
INSERT INTO `permission` (`name`) VALUES ('store.manager');
INSERT INTO `permission` (`name`) VALUES ('guest');

CREATE TABLE `role_permission` (
	`id`		int(11) NOT NULL auto_increment,
	`role_id`	int(11) NOT NULL,
	`permission_id`	int(11)	NOT NULL,
        FOREIGN KEY `role_permission_role_id_fk` (`role_id`) REFERENCES `role` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

	FOREIGN KEY `role_permission_permission_id_fk` (`permission_id`) REFERENCES `permission` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- Администратор
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 1);

-- Разработчик
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 2);
-- Аналитик
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 3);

-- Бренд менеджер
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 4);

-- Менеджер магазина
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 5);

-- Гость
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (6, 6);

CREATE OR REPLACE VIEW `role_permissions` (`role_id`, `role`, `role_name`, `permission_name`)
AS
	SELECT r.id AS role_id, r.role, r.name AS role_name, p.name AS permission_name FROM role r
	LEFT JOIN role_permission rp ON rp.role_id=r.id
	RIGHT JOIN permission p ON rp.permission_id = p.id ORDER BY role_id;



