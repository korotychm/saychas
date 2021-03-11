DROP TABLE IF EXISTS `provider`;
CREATE TABLE IF NOT EXISTS `provider` (
  `id`	        int(11) NOT NULL,
  `title` 	text,
  `description` text,
  `icon`        text,
  PRIMARY KEY(`id`)
) ENGINE=MyISAM;


