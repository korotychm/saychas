DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
	id		INT(11),
	title		TEXT,
	content		TEXT,
	status		TEXT,
	date_created	date
)ENGINE=MyISAM;
