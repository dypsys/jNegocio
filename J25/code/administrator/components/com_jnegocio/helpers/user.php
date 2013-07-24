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

class HelperUsers extends jFWHelperBase {
    static $_users = array();
    static $_groups = array();
    
    function getUserInfo( $user_id ) {
	$objreturn = false;
	if (empty($user_id) || (is_numeric($user_id) && ($user_id==0))) {
            return $objreturn;
	}
        
        if (isset($this) && is_a($this, 'HelperUsers')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperUsers', 'helpers.product', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }
        
        if (!isset($helper->_users[$user_id])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
            $helper->_users[$user_id] = JTable::getInstance('userinfo', jFWBase::getTablePrefix());
            $helper->_users[$user_id]->load($user_id);
        }
        
        $returnObject = $this->_users[$user_id];
	return $returnObject;
    }
}