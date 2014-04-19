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

// Require the base controller
jFWBase::load('jFWModel', 'models._base');

/**
 * Base class for a FrameWork Model
 *
 * @abstract
 * @package		jFWModel
 * @subpackage	FrameWork
 * @since		1.5
 */
class jFWModelCRUD extends jFWModel {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Tests if table is checked out
     *
     * @access	public
     * @param	int	A user id
     * @return	boolean	True if checked out
     * @since	1.5
     */
    function isCheckedOut($uid = 0) {
        if ($this->_loadData()) {
            if ($uid) {
                return ($this->_item->checked_out && $this->_item->checked_out != $uid);
            } else {
                return $this->_item->checked_out;
            }
        } elseif ($this->getId() < 1) {
            return false;
        } else {
            JError::raiseWarning(0, 'Unable to Load Data');
            return false;
        }
    }

    /**
     * Method to checkin/unlock the item
     *
     * @access	public
     * @return	boolean	True on success
     */
    function checkin() {
        if ($this->getId()) {
            $table = $this->getTable();
            return $table->checkin($this->getId());
        }
        return true;
    }

    /**
     * Method to checkout/lock the item
     *
     * @access	public
     * @param	int	$uid	User ID of the user checking the item out
     * @return	boolean	True on success
     */
    function checkout($uid = null) {
        if ($this->getId()) {
            // Make sure we have a user id to checkout the group with
            if (is_null($uid)) {
                $user = JFactory::getUser();
                $uid = $user->get('id');
            }
            // Lets get to it and checkout the thing...
            $table = $this->getTable();
            return $table->checkout($uid, $this->getId());
        }
        return true;
    }

    /**
     * Method to store the category
     *
     * @access  public
     * @param   $data
     * @return  boolean    True on success
     */
    function store($data) {
        global $mainframe;

        jimport('joomla.utilities.date');

        $user       = JFactory::getUser();
        $nullDate   = $this->getDbo()->getNullDate();
        $config     = JFactory::getConfig();
        $tzoffset   = $config->get('offset');

        $row    = $this->getTable();
        $idkey  = $row->getKeyName();

        // bind it to the table
        if (!$row->bind($data)) {
            JError::raiseError(501, "error bind:" . $this->_db->getErrorMsg());
            return false;
        }

        if ($row->checkFieldinColumns('ordering')) {
            // if (isset($row->ordering) || is_null($row->ordering) ) {

            if (!$row->$idkey) {
                $row->ordering = $row->getNextOrder();
                //				echo "entra ordering:".$row->ordering."<br/>";
            }
        } else {
            //			echo "no existe el row ordering<br/>";
            //			echo "<hr/>".var_dump($row)."<hr/>";
        }

        // Are we saving from an item edit?
        if (!$row->$idkey) {
            // Es nuevo
            $date = new JDate($row->created, $tzoffset);
            $row->modified = $nullDate;
            $row->modified_by = '';

            $row->created = $date->toSql();
            $row->created_by = $user->get('id');
        } else {
            // es Modificacion
            $date = new JDate($row->modified, $tzoffset);
            $row->modified = $date->toSql();
            $row->modified_by = $user->get('id');
        }

        // Make sure the data is valid
        if (!$row->check()) {
            JError::raiseError(502, "error check:" . $row->getError());
            return false;
        }

        // Store it in the db
        if (!$row->store()) {
            JError::raiseError(503, "error store:" . $this->_db->getErrorMsg());
            return false;
        }

        return $row->$idkey;
    }
}