CREATE TABLE IF NOT EXISTS `#__schemaorg` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`item_id` int,
	`context` varchar(100),
	`schema_type` varchar(100),
	`schema_form` text,
	`schema` text,
	PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
