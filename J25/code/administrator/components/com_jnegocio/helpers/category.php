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

class HelperCategory extends jFWHelperBase {

    static $_categories = array();

    /**
     * Returns a formatted path for the category
     * @param $id
     * @param $format
     * @return unknown_type
     */
    function getPathName($CategoryId, $format = 'flat', $linkSelf = false) {
        $name = '';
        if (empty($CategoryId)) {
            return $name;
        }

        if (isset($this) && is_a($this, 'HelperCategory')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperCategory', 'helpers.category', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }

        if (empty($helper->_categories[$CategoryId])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
            $helper->_categories[$CategoryId] = JTable::getInstance('categories', jFWBase::getTablePrefix());
            $helper->_categories[$CategoryId]->load($CategoryId);
        }

        $item = $helper->_categories[$CategoryId];

        if (empty($item->category_id)) {
            return $name;
        }

        $path = $item->getPath();

        return $path;
    }

    function getbyId($categoryid) {
        $item = null;

        if (isset($this) && is_a($this, 'HelperCategory')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperCategory', 'helpers.category', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }

        if (empty($helper->_categories[$categoryid])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
            $helper->_categories[$categoryid] = JTable::getInstance('categories', jFWBase::getTablePrefix());
            $helper->_categories[$categoryid]->load(array('category_id' => $categoryid));
        }

        $item = $helper->_categories[$categoryid];

        if (empty($item->category_id)) {
            return false;
        }
        return $item;
    }

}