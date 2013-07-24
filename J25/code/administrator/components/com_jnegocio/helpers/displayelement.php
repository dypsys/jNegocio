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

class HelperDisplayElement extends jFWHelperBase {

    protected static $_Attr = array();

    function getAttrbyId($Attrid) {
        $item = null;

        if (isset($this) && is_a($this, 'HelperDisplayElement')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperDisplayElement', 'helpers.displayelement', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }

        if (empty($helper->_Attr[$Attrid])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
            $helper->_Attr[$Attrid] = JTable::getInstance('Attributes', jFWBase::getTablePrefix());
            $helper->_Attr[$Attrid]->load(array('attribute_id' => $Attrid));
        }

        $item = $helper->_Attr[$Attrid];

        if (empty($item->attribute_id)) {
            // Add UserId In table _vt_person
            // if($helper->addPersonId($PersonId, 'Personage Generado automaticamente')) {
            //	$helper->persons[$PersonId] = JTable::getInstance( 'person', 'vt_' );
            //	$helper->persons[$PersonId]->load( array('person_id'=>$PersonId)  );
            //	$item = $helper->persons[$PersonId];
            // } else {
            //	// Error to add userid to table
            //	return false;
            // }
            return false;
        }
        return $item;
    }

}