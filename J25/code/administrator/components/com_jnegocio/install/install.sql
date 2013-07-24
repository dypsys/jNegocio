-- -----------------------------------------------------
-- HOW TO USE THIS FILE:
-- Replace all instances of #_ with your prefix
-- In PHPMYADMIN or the equiv, run the entire SQL
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `#__nec_config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_config` (
    `config_id`     int(11) NOT NULL AUTO_INCREMENT ,
    `settingname`   varchar(255) NOT NULL ,
    `value`         TEXT NOT NULL ,
    PRIMARY KEY (`config_id`) 
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table `#__nec_languages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_languages` (
    `id` 		int(11) NOT NULL auto_increment,
    `language` 		varchar(32) default NULL,
    `name` 		varchar(255) NOT NULL,
    `published` 	int(11) NOT NULL,
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__nec_currencies`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_currencies` (
    `currency_id` 	int(11) unsigned NOT NULL AUTO_INCREMENT,
    `currency_name` 	varchar(255) NOT NULL,
    `currency_code` 	char(3) DEFAULT NULL,
    `currency_symbol` 	varchar(12) NOT NULL,
    `currency_symbol_position` 	tinyint(1) NOT NULL DEFAULT '2',
    `currency_decimals` tinyint(1) NOT NULL DEFAULT '2',
    `currency_decimals_separator` 	varchar(1) NOT NULL DEFAULT '.',
    `currency_thousands_separator` 	varchar(1) NOT NULL DEFAULT ',',
    `currency_flag` 	varchar(25) NOT NULL,
    `currency_exchange_rate` 	decimal(15,8) NOT NULL DEFAULT '0.00000000' COMMENT 'Value of currency in EUR',
    `currency_updated_date` 	datetime NOT NULL COMMENT 'The last time the currency was updated',	
    `published` 	tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`currency_id`),
    KEY `idx_currency_name` (`currency_name`),
    KEY `idx_currency_code` (`currency_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ------------------------------------------------------
-- Table structure for table `#__nec_countries`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_countries` (
    `country_id` 	int(11) unsigned NOT NULL AUTO_INCREMENT,
    `isocode_2` 	varchar(2) DEFAULT NULL,
    `isocode_3` 	varchar(3) DEFAULT NULL,
    `published` 	tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`country_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------------------
-- Table structure for table `#__nec_zones`
-- -----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_zones` (
    `zone_id` 		int(11) unsigned NOT NULL AUTO_INCREMENT,
    `country_id` 	int(11) unsigned NOT NULL DEFAULT '0',
    `name` 		varchar(128) DEFAULT NULL,
    `code` 		varchar(10) DEFAULT NULL,
    `published` 	tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`zone_id`),
    KEY `fk_countries_zones` (`country_id`),
    KEY `idx_zones_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ------------------------------------------------------
-- Table structure for table `#__nec_geozones`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_geozones` (
    `geozone_id` 	int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` 		varchar(128) NOT NULL DEFAULT '',
    `description` 	text,
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`geozone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------------------
-- Table structure for table `#__nec_zonerelations`
-- -----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_geozonerelations` (
    `geozonerelation_id`    int(11) unsigned NOT NULL AUTO_INCREMENT,
    `zone_id`               int(11) unsigned NOT NULL DEFAULT '0',
    `geozone_id`            int(11) unsigned NOT NULL DEFAULT '0',
    `created`               datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by`            int(11) unsigned NOT NULL DEFAULT '0',
    `modified`              datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by`           int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out`           int(11) NOT NULL DEFAULT '0',
    `checked_out_time`      datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`geozonerelation_id`),
    KEY `fk_geozone_zonerelations` (`geozone_id`),
    KEY `fk_geozone_zones` (`zone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------------------
-- Table structure for table `#__nec_typetaxes`
-- -----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_typetaxes` (
    `typetax_id` 	int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` 		varchar(128) DEFAULT NULL,
    `description` 	text,
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `published` 	tinyint(1) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`typetax_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------------------
-- Table structure for table `#__nec_taxrates`
-- -----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_taxrates` (
    `taxrate_id` 	int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` 		varchar(255) NOT NULL,
    `geozone_id` 	int(11) unsigned NOT NULL DEFAULT '0',
    `typetax_id` 	int(11) unsigned NOT NULL DEFAULT '0',
    `tax_rate` 		decimal(7,4) NOT NULL DEFAULT '0.0000',
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `published` 	tinyint(1) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`taxrate_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ------------------------------------------------------
-- Table structure for table `#__nec_manufacturers`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_manufacturers` (
    `manufacturer_id`   int(11) unsigned NOT NULL AUTO_INCREMENT,
    `published`         tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created`           datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL DEFAULT '0',
    `hits` 		int(11) NOT NULL DEFAULT '0',
    `params` 		text NOT NULL, 
    `locationuri` 	varchar( 255 ) NOT NULL,
    `locationurl` 	varchar( 255 ) NOT NULL,
    `attachment` 	varchar( 255 ) NOT NULL,
    PRIMARY KEY  (`manufacturer_id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

-- ------------------------------------------------------
-- Table structure for table `#__nec_categories`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_categories` (
    `category_id` 	int(11) unsigned NOT NULL AUTO_INCREMENT,
    `parent_id` 	int(10) unsigned NOT NULL DEFAULT '0',
    `lft` 		int(11) NOT NULL,
    `rgt` 		int(11) NOT NULL,
    `level` 		int(10) unsigned NOT NULL DEFAULT '0',
    `path` 		varchar(255) NOT NULL DEFAULT '',
    `isroot` 		tinyint(1) NOT NULL DEFAULT '0',	
    `published` 	tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL DEFAULT '0',
    `hits` 		int(11) NOT NULL DEFAULT '0',
    `params` 		text NOT NULL,
    `locationuri` 	varchar( 255 ) NOT NULL,
    `locationurl` 	varchar( 255 ) NOT NULL,
    `attachment` 	varchar( 255 ) NOT NULL,
    PRIMARY KEY (`category_id`),
    KEY `idx_checkout` (`checked_out`),
    KEY `idx_path` (`path`),
    KEY `idx_left_right` (`lft`,`rgt`),
    KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ------------------------------------------------------
-- Table structure for table `#__nec_products`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_products` (
    `product_id` 	int(11) unsigned NOT NULL AUTO_INCREMENT,
    `sku`               varchar(64) DEFAULT NULL,
    `ean`               varchar(64) DEFAULT NULL,
    `model`             varchar(128) NOT NULL,
    `manufacturer_id` 	int(11) NOT NULL DEFAULT '0',
    `typetax_id` 	int(11) unsigned NOT NULL DEFAULT '0',
    `rating_sum` 	int(11) unsigned NOT NULL DEFAULT '0',
    `rating_count` 	int(11) unsigned NOT NULL DEFAULT '0',
    `weight` 		decimal(10,4) DEFAULT '0.0000',
    `length` 		decimal(10,4) DEFAULT '0.0000',
    `width` 		decimal(10,4) DEFAULT '0.0000',
    `height` 		decimal(10,4) DEFAULT '0.0000',
    `quantity_min` 	int(11) DEFAULT NULL,
    `quantity_max` 	int(11) DEFAULT NULL,
    `quantity_step` 	int(11) DEFAULT NULL,
    `quantity_restriction` tinyint(1) NOT NULL DEFAULT '0',
    `unlimited`         tinyint(1) NOT NULL DEFAULT '0',
    `currency_id` 	int(11) NOT NULL,
    `published`         tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created`           datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL DEFAULT '0',
    `hits` 		int(11) NOT NULL DEFAULT '0',
    `params` 		text NOT NULL, 
    PRIMARY KEY (`product_id`),
    KEY `idx_checkout` (`checked_out`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------------------
-- Table structure for table `#__nec_productcategoryxrefs`
-- -----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_productcategory` (
    `productcategory_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `category_id` 	int(11) NOT NULL DEFAULT '0',
    `product_id` 	int(11) NOT NULL DEFAULT '0',
    `created`           datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`productcategory_id`),
    KEY `idx_product_category_xref_category_id` (`category_id`),
    KEY `idx_product_category_xref_product_id` (`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------------------
-- Table structure for table `#__nec_productimages`
-- -----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_productimages` (
    `productimage_id`   int(11) unsigned NOT NULL AUTO_INCREMENT,
    `product_id` 	int(11) NOT NULL DEFAULT '0',
    `locationuri` 	varchar(255) NOT NULL,
    `locationurl` 	varchar(255) NOT NULL,
    `attachment` 	varchar(255) NOT NULL,
    `created`           datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`productimage_id`),
    KEY `idx_productimage_xref_product_id` (`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------------------
-- Table structure for table `#__nec_productprices`
-- -----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_productprices` (
    `productprice_id` 		int(11) NOT NULL AUTO_INCREMENT,
    `product_id` 		int(11) NOT NULL DEFAULT '0',
    `product_price` 		decimal(12,5) DEFAULT '0.00000',
    `product_priceincltax` 	decimal(12,5) DEFAULT '0.00000',
    `product_discount` 		decimal(12,2) NOT NULL DEFAULT '0.00',
    `product_price_startdate`   datetime NOT NULL COMMENT 'GMT Only',
    `product_price_enddate`     datetime NOT NULL COMMENT 'GMT Only',
    `group_id` 			int(11) DEFAULT '0',
    `price_quantity_start` 	int(11) unsigned NOT NULL DEFAULT '0',
    `price_quantity_end` 	int(11) unsigned NOT NULL DEFAULT '0',
    `created`                   datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by`                int(11) unsigned NOT NULL DEFAULT '0',
    `modified`                  datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by`               int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out`               int(11) NOT NULL DEFAULT '0',
    `checked_out_time`          datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 			int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`productprice_id`),
    KEY `idx_product_price_product_id` (`product_id`),
    KEY `idx_product_price_group_id` (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__nec_usergroups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_usergroups` (
    `usergroup_id` 	int(11) NOT NULL auto_increment,
    `name` 		varchar(255) NOT NULL,
    `description` 	text NOT NULL,
    `published` 	int(11) NOT NULL,
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL,
    PRIMARY KEY  (`usergroup_id`)
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__nec_attributes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_attributes` (
    `attribute_id` 	int(11) NOT NULL AUTO_INCREMENT,
    `attribute_type` 	tinyint(1) NOT NULL,
    `attribute_cats` 	text NOT NULL,
    `dependency`        tinyint(1) NOT NULL,
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL,
    PRIMARY KEY  (`attribute_id`)
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__nec_attributes_values`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_attributes_values` (
    `value_id` 		int(11) unsigned NOT NULL auto_increment,
    `attribute_id` 	int(11) NOT NULL,
    `created` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `modified` 		datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` 	int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out` 	int(11) NOT NULL DEFAULT '0',
    `checked_out_time` 	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering` 		int(11) NOT NULL,
    PRIMARY KEY  (`value_id`),
    KEY `idx_attr_value_id` (`attribute_id`)
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__nec_attributes_values`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__nec_productattributes` (
    `productattribute_id`   int(11) NOT NULL AUTO_INCREMENT,
    `product_id`            int(11) unsigned NOT NULL DEFAULT '0',
    `attribute_id`          int(11) unsigned NOT NULL DEFAULT '0',
    `attributevalue_id`     int(11) unsigned NOT NULL DEFAULT '0',
    `price`                 decimal(12,5) NOT NULL,
    `code`                  varchar(255) NOT NULL,
    `prefix`                varchar(1) NOT NULL,
    `stock`                 int(11) NOT NULL,
    `ean`                   varchar(128) NOT NULL,
    `weight`                decimal(12,4) NOT NULL,
    `weight_volume_units`   decimal(12,2) NOT NULL,	
    `created`               datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `created_by`            int(11) unsigned NOT NULL DEFAULT '0',
    `modified`              datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by`           int(11) unsigned NOT NULL DEFAULT '0',
    `checked_out`           int(11) NOT NULL DEFAULT '0',
    `checked_out_time`      datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering`              int(11) NOT NULL,
    PRIMARY KEY  (`productattribute_id`),
    KEY `idx_prd_attr_value_id` (`product_id`),
    KEY `idx_prd_attr_id` (`attribute_id`)
) ENGINE = MyISAM DEFAULT CHARSET=utf8;
