<?php
/**
 * @version	    3.0.1
 * @package	    Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Abstract Table class
 *
 * Parent classes to all tables.
 *
 * @abstract
 * @since		3.0
 */
class jFWTable extends JTable {

    /**
     * constructor
     */
    function __construct($tbl_name, $tbl_key, &$db) {
        parent::__construct($tbl_name, $tbl_key, $db);
        // set table properties based on table's fields
        // $this->setTableProperties();
    }


    function checkFieldinColumns($fieldName) {
        $lreturn = false;
        $aFields = $this->getFields();
        foreach (@$aFields as $name => $type) {
            if ($name == $fieldName) {
                $lreturn = true;
            }
        }
        return $lreturn;
    }
}