-- ------------------------------------------------------
-- Data for table `#__nec_typetaxes`
-- ------------------------------------------------------
INSERT IGNORE INTO `#__nec_typetaxes` (`typetax_id`, `name`, `description`, `published`) VALUES
    (1, 'Tipo General', '', 1),
    (2, 'Tipo Reducido', '', 1),
    (3, 'Tipo SuperReducido', '', 1);

-- ------------------------------------------------------
-- Data for table `#__nec_currencies`
-- ------------------------------------------------------
INSERT IGNORE INTO `#__nec_currencies` (`currency_id`, `currency_name`, `currency_code`, `currency_symbol`, `currency_symbol_position`, `currency_decimals`, `currency_decimals_separator`, `currency_thousands_separator`, `currency_flag`, `currency_exchange_rate`, `currency_updated_date`, `published`, `ordering`) VALUES
    (1, 'Euros', 'EUR', ' €', 2, 2, '.', ',', '', '0.00000000', '0000-00-00 00:00:00', 1, 1),
    (2, 'US Dollar', 'USD', '$ ', 1, 2, '.', ',', '', '0.00000000', '0000-00-00 00:00:00', 1, 2);

-- ------------------------------------------------------
-- Data for table `#__nec_usergrousp`
-- ------------------------------------------------------
INSERT IGNORE INTO `#__nec_usergroups` (`usergroup_id`, `name`, `published`, `ordering`) VALUES
    (1, 'Default', 1, 1);


INSERT INTO negocio_j25.jos_nec_countries 
SELECT * FROM negocio_j25_datos.jos_nec_countries;

INSERT INTO negocio_j25.jos_nec_zones 
SELECT * FROM negocio_j25_datos.jos_nec_zones;

INSERT INTO negocio_j25.jos_nec_geozones 
SELECT * FROM negocio_j25_datos.jos_nec_geozones;

INSERT INTO negocio_j25.jos_nec_geozonerelations
SELECT * FROM negocio_j25_datos.jos_nec_geozonerelations;

INSERT INTO negocio_j25.jos_nec_taxrates
SELECT * FROM negocio_j25_datos.jos_nec_taxrates;