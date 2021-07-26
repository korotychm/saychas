DROP TABLE IF EXISTS `role_hierarchy`;
DROP TABLE IF EXISTS `role_permission`;
DROP TABLE IF EXISTS `permission`;
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
	`id`	int(11) NOT NULL auto_increment,
	-- `parent_role_id` int(11) DEFAULT 0,
	-- `child_role_id` int(11) DEFAULT 0,
	`name`	VARCHAR(255),
	`description` TEXT,
	`date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE INDEX `name_idx` (`name`),
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `role` (name, description) values ('admin', 'Administrator'), ('editor', 'Editor'), ('author', 'Author'), ('audit', 'Audit'), ('supervisor', 'Supervisor'), ('guest', 'Guest'), ('visitor', 'Visitor');

-- DROP TABLE IF EXISTS `role_hierarchy`;

-- CREATE TABLE `role_hierarchy` (
-- 	`id`	int(11) auto_increment,
-- 	`parent_role_id` int(11) NOT NULL,
-- 	`child_role_id`  int(11) NOT NULL,
-- 	`terminal` int(1) NOT NULL DEFAULT 0,
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

/*
CREATE TABLE `role_hierarchy` (
        `id`    int(11) NOT NULL,
        `parent_role_id` int(11) NOT NULL,
        `child_role_id`  int(11) NOT NULL,
        `terminal` int(1) NOT NULL DEFAULT 0 -- ,
--        PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
*/


-- ALTER TABLE `role` DROP COLUMN `parent_role_id`;
-- INSERT INTO `role_hierarchy` (`parent_role_id`, `child_role_id`) values(1,2),(2,3),(4,2),(1,5),(7,6),(1,7);
INSERT INTO `role_hierarchy` (`parent_role_id`, `child_role_id`, `terminal`) values(0,1,1),(1,2,0),(1,3,0),(2,4,0),(4,5,0),(5,6,0),(3,7,0);

/*
INSERT INTO `role` (name, description) values ('admin', 'Administrator');
select LAST_INSERT_ID() into @l;
INSERT INTO `role_hierarchy` (`id`, `parent_role_id`, `child_role_id`, `terminal`) values(@l,0,1,1);

INSERT INTO `role` (name, description) values ('editor', 'Editor');
select LAST_INSERT_ID() into @l;
INSERT INTO `role_hierarchy` (`id`, `parent_role_id`, `child_role_id`, `terminal`) values(@l,1,2,0);

INSERT INTO `role` (name, description) values ('author', 'Author');
select LAST_INSERT_ID() INTO @l;
INSERT INTO `role_hierarchy` (`id`, `parent_role_id`, `child_role_id`, `terminal`) values(@l,1,3,0);

INSERT INTO `role` (name, description) values ('audit', 'Audit');
select LAST_INSERT_ID() into @l;
INSERT INTO `role_hierarchy` (`id`, `parent_role_id`, `child_role_id`, `terminal`) values(@l,2,4,0);

INSERT INTO `role` (name, description) values ('supervisor', 'Supervisor');
select LAST_INSERT_ID() into @l;
INSERT INTO `role_hierarchy` (`id`, `parent_role_id`, `child_role_id`, `terminal`) values(@l,4,5,0);

INSERT INTO `role` (name, description) values ('guest', 'Guest');
select LAST_INSERT_ID() into @l;
INSERT INTO `role_hierarchy` (`id`, `parent_role_id`, `child_role_id`, `terminal`) values(@l,5,6,0);

INSERT INTO `role` (name, description) values ('visitor', 'Visitor');
select LAST_INSERT_ID() into @l;
INSERT INTO `role_hierarchy` (`id`, `parent_role_id`, `child_role_id`, `terminal`) values(@l,3,7,0);
*/
-- INSERT INTO `role_hierarchy` (`parent_role_id`, `child_role_id`, `terminal`) values(1,2,1),(1,3,0),(1,2,0),(2,5,0),(4,6,0),(5,7,0),(3,4,0);

CREATE TABLE `permission` (
	`id`	int(11) NOT NULL auto_increment,
	`name` VARCHAR(255),
	`description` TEXT,
	`date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE INDEX `name_idx` (`name`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `permission` (`name`) VALUES ('edit.own.profile');
INSERT INTO `permission` (`name`) VALUES ('edit.profile');
INSERT INTO `permission` (`name`) VALUES ('general');
INSERT INTO `permission` (`name`) VALUES ('view.profile');
INSERT INTO `permission` (`name`) VALUES ('add.personal');
INSERT INTO `permission` (`name`) VALUES ('delete.own.personal');
INSERT INTO `permission` (`name`) VALUES ('delete.personal');

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

-- admin
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 1);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 2);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 3);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 4);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 5);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 6);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (1, 7);

-- editor
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 2);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 4);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (2, 3);

-- author
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 1);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 4);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (3, 3);

-- audit
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 4);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (4, 3);

-- supervisor
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 3);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 5);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 6);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (5, 7);

-- guest
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (6, 3);

-- visitor
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (7, 3);
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES (7, 4);


CREATE OR REPLACE VIEW `role_permissions` (`role_id`, `role_name`, `permission_name`)
AS
	SELECT r.id AS role_id, r.name AS role_name, p.name AS permission_name FROM role r
	LEFT JOIN role_permission rp ON rp.role_id=r.id
	RIGHT JOIN permission p ON rp.permission_id = p.id ORDER BY role_id;



