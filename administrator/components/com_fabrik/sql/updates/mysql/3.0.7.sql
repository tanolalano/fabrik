CREATE TABLE IF NOT EXISTS `#__fabrik_schema_updates` (
	`id` int( 6 ) NOT NULL AUTO_INCREMENT ,
	`filename` varchar( 150 ) NOT NULL ,
	`applied` TINYINT( 1 ) NOT NULL DEFAULT '0'
	`applied_by` int( 6 ) NOT NULL ,
	`applied_date` DATETIME,
	`remote_site` varchar( 150 ) NOT NULL ,
	`remote_user` varchar( 100 ) NOT NULL ,
	PRIMARY KEY ( `id` )
)DEFAULT CHARSET = utf8;