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

// set the options array
$options = array('site' => 'site', 'type' => 'components', 'ext' => 'com_jnegocio');
jFWBase::load('jFWFrontModel', 'models._base', $options);

class jNegocioModelProduct extends jFWFrontModel {

    function getTable() {
        $table = JTable::getInstance('products', jFWBase::getTablePrefix());
        return $table;
    }

    /**
     * Method to load content event data
     *
     * @access	private
     * @return	boolean	True on success
     * @since	0.9
     */
    function _loadData() {
        // Lets load the content if it doesn't already exist
        if (empty($this->_item)) {
            $query = 'SELECT tbl.product_id as id, tbl.*';
            $query .= ',tbl.`' . $this->getLang()->getField('name') . '` AS name';
            $query .= ',tbl.`' . $this->getLang()->getField('shortdesc') . '` AS shortdesc';
            $query .= ',tbl.`' . $this->getLang()->getField('description') . '` AS description';
            
            $query .= ' FROM ' . $this->getTable()->getTableName() . ' AS tbl';

            $query .= ' WHERE ' . $this->getTable()->getKeyName() . ' = ' . $this->getId();

            $this->_db->setQuery($query);
            $this->_item = $this->_db->loadObject();

            return (boolean) $this->_item;
        }
        return true;
    }

}