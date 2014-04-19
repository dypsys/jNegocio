-- -----------------------------------------------------
-- HOW TO USE THIS FILE:
-- Replace all instances of #_ with your prefix
-- In PHPMYADMIN or the equiv, run the entire SQL
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `#__neg_config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__neg_config` (
    `config_id`     int(11) NOT NULL AUTO_INCREMENT ,
    `settingname`   varchar(255) NOT NULL ,
    `value`         TEXT NOT NULL ,
    PRIMARY KEY (`config_id`)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table `#__neg_languages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__neg_languages` (
    `id`          int(11) NOT NULL auto_increment,
    `language` 		varchar(32) default NULL,
    `name`        varchar(255) NOT NULL,
    `published` 	int(11) NOT NULL,
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------
-- Table structure for table `#__neg_countries`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__neg_countries` (
    `country_id` 	  int(11) unsigned NOT NULL AUTO_INCREMENT,
    `isocode_2` 	  varchar(2) DEFAULT NULL,
    `isocode_3` 	  varchar(3) DEFAULT NULL,
    `published` 	  tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created` 		  datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	  int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		  datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		  int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`country_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;