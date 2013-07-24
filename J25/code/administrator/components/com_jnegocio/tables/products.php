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

jFWBase::load('jFWTable', 'tables._base');

class nec_Products extends jFWTable {

    /**
     * @param database A database connector object
     */
    function nec_Products(&$db) {

        $tbl_key = 'product_id';
        $tbl_suffix = 'products';
        $this->set('_suffix', $tbl_suffix);
        $name = jFWBase::getTablePrefix();

        parent::__construct("#__{$name}{$tbl_suffix}", $tbl_key, $db);
    }

    // overloaded check function
    function check() {
        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $languages = jFWBase::getClass('HelperLanguages', 'helpers.language', $options)->getAllLanguages();

        foreach ($languages as $lang) {
            $name = 'name_' . $lang->language;
            $alias = 'alias_' . $lang->language;

            if ((isset($this->$name)) && (trim($this->$name) == '')) {
                $this->_error = JText::_('COM_JNEGOCIO_ADD_NAME');
                JError::raiseWarning('SOME_ERROR_CODE', $this->_error);
                return false;
            } else {
                $this->$name = trim($this->$name);
            }

            $tempalias = JFilterOutput::stringURLSafe($this->$name);
            if (empty($this->$alias) || $this->$alias === $tempalias) {
                $this->$alias = $tempalias;
            }

            $keyName = $this->getKeyName();

            /** check for existing name */
            $query = 'SELECT ' . $keyName . ' FROM ' . $this->getTableName() . ' WHERE `' . $name . '` = ' . $this->_db->Quote($this->$name);
            $this->_db->setQuery($query);

            $xid = intval($this->_db->loadResult());
            if ($xid && $xid != intval($this->$keyName)) {
                $this->setError(JText::sprintf('COM_JNEGOCIO_ALREADY_EXIST_ITEM', $this->$name));
                return false;
            }
        }

        return true;
    }

    function getCategories($productid = NULL) {
        if (!is_numeric($productid)) {
            $productid = $this->product_id;
        }
        $query = "SELECT * FROM `#__nec_productcategory` WHERE product_id = '".$this->_db->getEscaped($productid)."'";
        // echo "query:".$query."<br/>";
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }
    
    function InCategory($productid = NULL, $categoryid) {
        if (!is_numeric($productid)) {
            $productid = $this->product_id;
        }
        $query = "SELECT prod_cat.category_id FROM `#__nec_productcategory` AS prod_cat" .
                " WHERE prod_cat.product_id = '".$this->_db->getEscaped($productid)."'".
                " AND prod_cat.category_id = '".$this->_db->getEscaped($categoryid)."'";
        $this->_db->setQuery($query);
        $res = $this->_db->query();
        return $this->_db->getNumRows($res);
    }
    
    function getImages($productid = NULL) {
        if (!is_numeric($productid)) {
            $productid = $this->product_id;
        }
        $query = "SELECT * FROM `#__nec_productimages` WHERE product_id = '".$this->_db->getEscaped($productid)."' ORDER BY ordering";
        // echo "query:".$query."<br/>";
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }
}