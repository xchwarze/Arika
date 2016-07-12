<?php
/**
* Arika Subtitle Engine
* by DSR! 
* https://github.com/xchwarze/Arika
*/
class Installer
{
	public static function install() {
		//por ahi cambio el charset a utf8mb4_unicode_ci
		//agregar las fk de framerates y idiomas

		$tables = array(
			"CREATE TABLE IF NOT EXISTS `{{PREFIX}}arika_languages` (
			  `langID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `lang_name` varchar(255) NOT NULL DEFAULT '',
			  `sub_lang_code` varchar(11) NOT NULL DEFAULT '',
			  `is_utf` tinyint(1) NOT NULL DEFAULT '0',
			  `enabled` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`langID`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
			
			"CREATE TABLE IF NOT EXISTS `{{PREFIX}}arika_framerate` (
			  `frID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `fr_name` varchar(255) NOT NULL DEFAULT '',
			  `enabled` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`frID`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",

			"CREATE TABLE IF NOT EXISTS `{{PREFIX}}arika_status` (
			  `statusID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `status_name` varchar(255) NOT NULL DEFAULT '',
			  `enabled` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`statusID`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",

			"CREATE TABLE IF NOT EXISTS `{{PREFIX}}arika_subtitles` (
			  `subID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `userID` bigint(20) unsigned NOT NULL DEFAULT '0',
			  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, -- O DATETIME
			  `title` varchar(255) NOT NULL,
			  `season` smallint(5) unsigned NOT NULL DEFAULT '0',
			  `episode` smallint(5) unsigned NOT NULL DEFAULT '0',
			  `language` int(255) NOT NULL,
			  `original_language` int(255) NOT NULL,
			  `comment` varchar(255) NOT NULL DEFAULT '',
			  `user_request` bigint(20) unsigned NOT NULL DEFAULT '0',
			  `duration` varchar(50) NOT NULL DEFAULT '',
			  `total_lines` int(11) unsigned DEFAULT '0',
			  `framerate` smallint(5) DEFAULT '0',
			  `cloned_from` bigint(20) unsigned DEFAULT '0',
			  `downloads` bigint(20) unsigned DEFAULT '0',
			  `status` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`subID`),
			  KEY `userID` (`userID`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
			
			"CREATE TABLE IF NOT EXISTS `{{PREFIX}}arika_subtitle_content` (
			  `subcontID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `subID` bigint(20) unsigned NOT NULL,
			  `userID` bigint(20) unsigned NOT NULL DEFAULT '0',
			  `start` varchar(50) NOT NULL,
			  `end` varchar(50) NOT NULL,
			  `original_text` text,
			  `translated_text` text,
			  `is_bk` tinyint(1) NOT NULL DEFAULT '0',
			  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
			  `done` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`subcontID`),
			  KEY `subID` (`subID`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		// tables
		foreach ($tables as $value) {
			Integration::query($value);
		}

		// content
		if (Integration::queryVar("select lang_name from `{{PREFIX}}arika_languages` where langID = 1") == NULL) {
			$sql = "INSERT INTO `{{PREFIX}}arika_languages` VALUES (1,'English',0,0,1),(2,'English (UK)',0,0,1),(3,'English (US)',0,0,1),(4,'Español',0,0,1),(5,'Español (España)',0,0,1),(6,'Español (Latinoamérica)',0,0,1),(7,'Italian',0,0,1),(8,'French',0,0,1),(9,'Portuguese',0,0,1),(10,'Brazilian',0,0,1),(11,'German',0,0,1),(12,'Català',0,0,1),(13,'Euskera',0,0,1),(14,'Czech',0,0,1),(15,'Galego',0,0,1),(16,'Turkish',0,1,1),(17,'Nederlandse',0,0,1),(18,'Swedish',0,0,1),(19,'Russian',0,1,1),(20,'Hungarian',0,1,1),(21,'Polish',0,1,1),(22,'Slovenian',0,1,1),(23,'Hebrew',0,1,1),(24,'Chinese',0,0,1),(25,'Slovak',0,0,1);";
			Integration::query($sql);
		}

		if (Integration::queryVar("select fr_name from `{{PREFIX}}arika_framerate` where frID = 1") == NULL) {
			$sql = "INSERT INTO `{{PREFIX}}arika_framerate` VALUES (1,'Dont know',1),(2,'23.976',1),(3,'23.980',1),(4,'24.000',1),(5,'25.000',1),(6,'29.970',1),(7,'30.000',1);";
			Integration::query($sql);
		}


		if (Integration::queryVar("select status_name from `{{PREFIX}}arika_status` where statusID = 1") == NULL) {
			$sql = "INSERT INTO `{{PREFIX}}arika_status` VALUES (1,'Complete',1),(2,'Incomplete',1),(3,'In Progress',1);";
			Integration::query($sql);
		}
	}

	public static function uninstall() {
		$tables = array(
			"DROP TABLE {{PREFIX}}arika_languages",
			"DROP TABLE {{PREFIX}}arika_framerate",
			"DROP TABLE {{PREFIX}}arika_status",
			"DROP TABLE {{PREFIX}}arika_subtitles",
			"DROP TABLE {{PREFIX}}arika_subtitle_content"
		);

		// tables
		foreach ($tables as $value) {
			Integration::query($value);
		}
	}
}
