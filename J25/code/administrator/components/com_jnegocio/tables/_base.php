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

/**
 * Abstract Table class
 *
 * Parent classes to all tables.
 *
 * @abstract
 * @package 	FrameWork
 * @subpackage	Admin
 * @since		1.5
 */
class jFWTable extends JTable {

    /**
     * constructor
     */
    function __construct($tbl_name, $tbl_key, &$db) {
        parent::__construct($tbl_name, $tbl_key, $db);
        // set table properties based on table's fields
        $this->setTableProperties();
    }

    /**
     * Set properties of object based on table fields
     *
     * @acces   public
     * @return  object
     */
    function setTableProperties() {
        static $fields;

        if (empty($fields)) {
            $fields = $this->getColumns();
        }

        foreach (@$fields as $name => $type) {
            $this->$name = null;
        }
    }

    /**
     * Get columns from db table
     *
     * @return unknown_type
     */
    function getColumns() {
        static $fields;

        if (empty($fields)) {
            $db = $this->getDBO();
            // $fields = $db->getTableFields($this->getTableName()); // Deprecated
            $fields = $db->getTableColumns($this->getTableName());
        }

        // $return = @$fields[$this->getTableName()];
        return $fields;
    }

    function checkFieldinColumns($fieldName) {
        $lreturn = false;
        $aFields = $this->getColumns();
        foreach (@$aFields as $name => $type) {
            if ($name == $fieldName) {
                $lreturn = true;
            }
        }
        return $lreturn;
    }

    /**
     * Gets the key names
     *
     * returned $keynames array typically looks like:
     * $keynames['pelicula_id']  = 'pelicula_id';
     * $keynames['genero_id'] = 'genero_id';
     *
     * @return array
     * @since 1.5
     */
    public function getKeyNames() {
        $keynames = $this->_tbl_keys;
        if (!is_array($keynames)) {
            // set _tbl_keys using the primary keyname
            $keynames = array();
            $keyName = $this->getKeyName();
            $keynames[$keyName] = $keyName;
            $this->_tbl_keys = $keynames;
        }
        return $this->_tbl_keys;
    }

    /**
     * Sets the keynames
     *
     * $keynames typically looks like:
     * $keynames = array();
     * $keynames['pelicula_id']  = 'pelicula_id';
     * $keynames['genero_id'] = 'genero_id';
     *
     * @param $keynames array
     * @return unknown_type
     */
    public function setKeyNames($keynames) {
        $this->_tbl_keys = $keynames;
        return $this->_tbl_keys;
    }

    /**
     * Delete item from db table
     *
     * @param $oid
     * @return unknown_type
     */
    function delete($oid = null) {
        if ($return = parent::delete($oid)) {
            $dispatcher = JDispatcher::getInstance();
            $dispatcher->trigger('onAfterDelete' . $this->get('_suffix'), array($this));
        }
        return $return;
    }

    /**
     * Generic save function
     *
     * @access	public
     * @returns TRUE if completely successful, FALSE if partially or not successful
     */
    function guardar() {
        if (!$this->check()) {
            return false;
        }

        if (!$this->store()) {
            return false;
        }

        if (!$this->checkin()) {
            $db = $this->getDBO();
            $this->setError($db->stderr());
            return false;
        }

        $this->reorder();
        $this->setError('');

        // TODO Move ALL onAfterSave plugin events here as opposed to in the controllers, duh
        //$dispatcher = JDispatcher::getInstance();
        //$dispatcher->trigger( 'onAfterSave'.$this->get('_suffix'), array( $this ) );
        return true;
    }

    /**
     * Loads a row from the database and binds the fields to the object properties
     *
     * @access	public
     * @param	mixed	Optional primary key.  If not specifed, the value of current key is used
     * @return	boolean	True if successful
     */
    function load($oid = null, $reset = true) {
        if (is_numeric($oid)) {
            // load by primary key if numeric
            $keyName = $this->getKeyName();
            $oid = array($keyName => $oid);
        }

        if (empty($oid)) {
            // if empty, use the value of the current key
            $keyName = $this->getKeyName();
            $oid = $this->$keyName;
            if (empty($oid)) {
                // if still empty, fail
                $this->setError(JText::_("Cannot load with empty key"));
                return false;
            }
        }

        // allow $oid to be an array of key=>values to use when loading
        $oid = (array) $oid;

        if ($reset) {
            $this->reset();
        }

        $db = $this->getDBO();

        // initialize the query
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($this->getTableName());

        foreach ($oid as $key => $value) {
            // Check that $key is field in table
            if (!in_array($key, array_keys($this->getProperties()))) {
                $this->setError(get_class($this) . ' does not have the field ' . $key);
                return false;
            }
            // add the key=>value pair to the query
            // $value = $db->Quote( $db->getEscaped( trim( strtolower( $value ) ) ) );
            // $query->where( $key.' = '.$value);
            $query->where($db->quoteName($key) . ' = ' . $db->quote($db->getEscaped(trim(strtolower($value)))));
        }

        $db->setQuery($query);

        try {
            $result = $db->loadAssoc();
        } catch (RuntimeException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        // Legacy error handling switch based on the JError::$legacy switch.
        // @deprecated  12.1
        if (JError::$legacy && $db->getErrorNum()) {
            $e = new JException($db->getErrorMsg());
            $this->setError($e);
            return false;
        }

        // Check that we have a result.
        if (empty($result)) {
            $e = new JException(JText::_('JLIB_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
            $this->setError($e);
            return false;
        }

        // echo "result:".var_dump($result)."<br/>";
        // Bind the object with the row and return.
        return $this->bind($result);
        //	    if ( $result = $db->loadAssoc() ) {
        //            return $this->bind($result);
        //        } else {
        //            $this->setError( $db->getErrorMsg() );
        //            return false;
        //        }
    }

}