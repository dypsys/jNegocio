<?php
/**
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jFWBase::load('jFWHelperBase', 'helpers._base');

class HelperZones extends jFWHelperBase {

    static $_GeoZones = array();
    static $_zones = array();

    function getGeoZonebyId($idGeoZone) {
        $item = null;

        if (isset($this) && is_a($this, 'HelperZones')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperZones', 'helpers.zones', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }

        if (empty($helper->_GeoZones[$idGeoZone])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
            $helper->_GeoZones[$idGeoZone] = JTable::getInstance('geozones', jFWBase::getTablePrefix());
            $helper->_GeoZones[$idGeoZone]->load(array('geozone_id' => $idGeoZone));
        }

        $item = $helper->_GeoZones[$idGeoZone];

        if (empty($item->geozone_id)) {
            return false;
        }
        return $item;
    }

    function getGeoZonebyZoneID($idZone) {
        $item = null;
        
        if (empty($idZone)) {
            $idZone = fwConfig::getInstance()->get('company_zone', 1);
        }
        
        if (isset($this) && is_a($this, 'HelperZones')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperZones', 'helpers.zones', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }
        
        if (empty($helper->_zones[$idZone])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
            $helper->_zones[$idZone] = JTable::getInstance('geozonerelations', jFWBase::getTablePrefix());
            $helper->_zones[$idZone]->load(array('zone_id' => $idZone));
        }
        
        $item = $helper->_zones[$idZone];
        if ($item->geozone_id) {
            return $item->geozone_id;
        } else {
            return null;
        }
    }
}