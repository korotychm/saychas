DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
	first_name	VARCHAR(255),
	last_name	VARCHAR(255),
	email_address	VARCHAR(255),
	phone_number	VARCHAR(255)
)ENGINE=MyISAM;

INSERT INTO `user` (`first_name`, `last_name`, `email_address`, `phone_number`)
VALUES ('first name 1', 'last name 1', 'email 1', '12341324'), ('first name 2', 'last name 2', 'email 2', '222244444');

