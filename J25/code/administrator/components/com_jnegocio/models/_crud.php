<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license		Comercial License
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
                $user = & JFactory::getUser();
                $uid = $user->get('id');
            }
            // Lets get to it and checkout the thing...
            $table = $this->getTable();
            return $table->checkout($uid, $this->getId());
        }
        return true;
    }

    /**
     * Method to (un)publish a category
     *
     * @access	public
     * @return	boolean	True on success
     */
    function publish($cid = array(), $publish = 1) {
        $user = & JFactory::getUser();

        if (count($cid)) {
            $cids = implode(',', $cid);

            $query = 'UPDATE ' . $this->getTable()->getTableName()
                    . ' SET published = ' . (int) $publish
                    . ' WHERE ' . $this->getTable()->getKeyName() . ' IN (' . $cids . ')'
                    . ' AND ( checked_out = 0 OR ( checked_out = ' . (int) $user->get('id') . ' ) )'
            ;
            // echo "query:".$query."<br/>";
            $this->_db->setQuery($query);
            if (!$this->_db->query()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }
        }
        return true;
    }

    /**
     * Method to move a category
     *
     * @access	public
     * @return	boolean	True on success
     */
    function move($direction) {
        $user = & JFactory::getUser();
        $row = $this->getTable();

        if (!$row->load($this->getId())) {
            $this->setError("NOT LOADED ITEM " . $this->_db->getErrorMsg());
            return false;
        }

        if (!$row->move($direction)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        return true;
    }

    /**
     * Method to order items
     *
     * @access	public
     * @return	boolean	True on success
     */
    function saveorder($cid = array(), $order) {
        $user = & JFactory::getUser();
        $row = $this->getTable();

        // update ordering values
        for ($i = 0; $i < count($cid); $i++) {
            $row->load((int) $cid[$i]);

            if ($row->ordering != $order[$i]) {
                $row->ordering = $order[$i];
                if (!$row->store()) {
                    $this->setError($this->_db->getErrorMsg());
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Method to store the category
     *
     * @access	public
     * @return	boolean	True on success
     * @since	1.5
     */
    function store($data) {
        global $mainframe;

        jimport('joomla.utilities.date');

        $user = & JFactory::getUser();
        $nullDate = $this->_db->getNullDate();
        $config = & JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');

        $row = $this->getTable();
        $idkey = $row->getKeyName();

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

            $row->created = $date->toMySQL();
            $row->created_by = $user->get('id');
        } else {
            // es Modificacion
            $date = new JDate($row->modified, $tzoffset);
            $row->modified = $date->toMySQL();
            $row->modified_by = $user->get('id');
        }

        if (isset($row->params)) {
            $params = JRequest::getVar('params', null, 'post', 'array');
            // Build parameter INI string
            if (is_array($params)) {
                $txt = array();
                foreach ($params as $k => $v) {
                    if (is_array($v)) {
                        $v = implode('|', $v);
                    }
                    $txt[] = "$k=$v";
                }
                $row->params = implode("\n", $txt);
            }
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