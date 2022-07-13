CREATE TABLE IF NOT EXISTS `#__schemaorg` (
	`articleId` int(10) NOT NULL AUTO_INCREMENT,
	`schemaType` varchar(100),
	`schema` text,
	PRIMARY KEY (`articleId`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
