DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
	first_name	VARCHAR(255),
	last_name	VARCHAR(255),
	email_address	VARCHAR(255),
	phone_number	VARCHAR(255)
)ENGINE=MyISAM;

INSERT INTO `user` (`first_name`, `last_name`, `email_address`, `phone_number`)
VALUES ('first name 1', 'last name 1', 'email 1', '001'), ('first name 2', 'last name 2', 'email 2', '222244444');


DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
	`id`	VARCHAR(255),
	`email`	VARCHAR(255),
	`blog`	VARCHAR(255)
)ENGINE=MyISAM;

INSERT INTO `post` (`id`, `email`, `blog`) VALUES('001', 'asdf@b.com', 'blog 1'),
('002', 'aaa@bbb.com', 'blog2'), ('003', 'a@c.com', 'blog 3');

