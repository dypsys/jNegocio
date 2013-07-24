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

// Importamos el modelo de Joomla
jimport('joomla.application.component.model');
$options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
jFWBase::load('HelperLanguages', 'helpers.languages', $options);

/**
 * Base class for a FrameWork Model
 * 
 * @abstract
 * @package		jFWModel
 * @subpackage	FrameWork
 * @since		1.5
 */
class jFWModel extends JModel {

    var $_data = null;
    var $_id = null;
    var $_query = null;
    var $_total = null;
    var $_pagination = null;
    var $_item = null;
    var $_lang = null;
    var $_filterinput = null; // instance of JFilterInput

    /**
     * Constructor
     */

    function __construct() {
        $this->_lang = &HelperLanguages::getlang();
        parent::__construct();

        $this->_filterinput = &JFilterInput::getInstance();
    }

    /**
     * Method to get a table object, load it if necessary.
     *
     * @access  public
     * @param   string The table name. Optional.
     * @param   string The class prefix. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  object  The table
     * @since   1.5
     */
    function &getTable($name = '', $prefix = '', $options = array()) {
        if (empty($name)) {
            $name = $this->getName();
        }

        if (empty($prefix)) {
            $prefix = jFWBase::getTablePrefix();
        }

        JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . jFWBase::getComponentName() . DS . 'tables');
        if ($table = $this->_createTable($name, $prefix, $options)) {
            return $table;
        }

        JError::raiseError(0, 'Table ' . $name . ' not supported. File not found.');
        $null = null;
        return $null;
    }

    /**
     * Empties the state
     *
     * @return unknown_type
     */
    public function emptyState() {
        $state = JArrayHelper::fromObject($this->getState());
        foreach ($state as $key => $value) {
            if (substr($key, '0', '1') != '_') {
                $this->setState($key, '');
//                echo "borrar state clave:".$key."<br/>";
            }
        }

        $this->_data = null;
        $this->_id = null;
        $this->_query = null;
        $this->_total = null;
        $this->_pagination = null;
        $this->_languages = null;
        $this->_langcontent = null;
        $this->_item = null;

        return $this->getState();
    }

    /**
     * Gets a property from the model's state, or the entire state if no property specified
     * @param $property
     * @param $default
     * @param string The variable type {@see JFilterInput::clean()}.
     * 
     * @return unknown_type
     */
    public function getState($property = null, $default = null, $return_type = 'default') {
        $return = parent::getState($property, $default);
        return $this->_filterinput->clean($return, $return_type);
    }

    /**
     * Metodo para assignar el Identificador 
     *
     * @access	public
     * @param	int ID
     */
    function setId($id) {
        $this->_id = $id;
        $this->_data = null;
    }

    /**
     * Metodo que nos devuelve el Identificador
     * 
     * @return int ID
     */
    public function getId() {
        if (empty($this->_id)) {
            $id = JRequest::getVar('id', JRequest::getVar('id', '0', 'post', 'int'), 'get', 'int');
            $array = JRequest::getVar('cid', array($id), '', 'array');
            $this->setId((int) $array[0]);
        }

        return $this->_id;
    }

    /**
     * Gets the model's query, building it if it doesn't exist
     * @return valid query object
     */
    public function getQuery() {
        if (empty($this->_query)) {
            $this->_query = $this->_buildQuery();
        }
        return $this->_query;
    }

    /**
     * Sets the model's query
     * @param $query	A valid query object
     * @return valid query object
     */
    public function setQuery($query) {
        $this->_query = $query;
        return $this->_query;
    }

    /**
     * Metodo para coger los tipos de los datos
     *
     * @access public
     * @return array
     */
    function getData($refresh = false) {
        if (empty($this->_data) || $refresh) {
            $query = $this->getQuery();
            // $strQuery = (string) $query;
            // echo "query:".$query."<br/>";

            $limitstart = $this->getState('limitstart', 0);
            $limit = $this->getState('limit', 0);

            $this->_db->setQuery($query, $limitstart, $limit);
            $this->_data = $this->_db->loadObjectList();
            // $this->_data = $this->_getList($query, $this->getState('limitstart', 0), $this->getState('limit', 0));
            // echo "return<hr/>";
            // var_dump($this->_data);
            // echo "limits:start:".$this->getState('limitstart') . " limit:" .$this->getState('limit')."<br/>";
        }

        return $this->_data;
    }

    /**
     * Metodo que devuelve el numero total de tipos
     *
     * @access public
     * @return integer
     */
    function getTotal() {
        // Lets load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->getQuery();
            $this->_total = $this->_getListCount((string) $query);
        }

        return $this->_total;
    }

    /**
     * Metodo que devuelve el objeto paginacion de los tipos
     *
     * @access public
     * @return integer
     */
    function getPagination() {
        // Lets load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_pagination;
    }

    /**
     * Builds a generic SELECT query
     *
     * @return  string  SELECT query
     */
    public function _buildQuery($refresh = false) {
        if (!empty($this->_query) && !$refresh) {
            return $this->_query;
        }

        $query = $this->getDbo()->getQuery(true);
        // $query = new fwQuery();  
        // nQuery::getInstance();

        $this->_buildQueryFields($query);
        $this->_buildQueryFrom($query);
        $this->_buildQueryJoins($query);
        $this->_buildQueryWhere($query);
        $this->_buildQueryGroup($query);
        $this->_buildQueryHaving($query);
        $this->_buildQueryHaving($query);
        $this->_buildQueryOrder($query);

        return $query;
    }

    /**
     * Builds SELECT fields list for the query
     */
    public function _buildQueryFields(&$query) {
        $query->select($this->getState('select', 'tbl.*'));
        if ($this->getTable()->getKeyName() != 'id') {
            $query->select('tbl.' . $this->getTable()->getKeyName() . ' AS id');
        }

//		$field = array();
//		$field[] = 'LEFT(tbl.title, 1)  AS letter_name';
//		$query->select( $field );		
    }

    /**
     * Builds FROM tables list for the query
     */
    public function _buildQueryFrom(&$query) {
        $name = $this->getTable()->getTableName();
        $query->from($name . ' AS tbl');
    }

    /**
     * Builds JOINS clauses for the query
     */
    public function _buildQueryJoins(&$query) {
        
    }

    /**
     * Builds WHERE clause for the query
     */
    public function _buildQueryWhere(&$query) {
        
    }

    /**
     * Builds a GROUP BY clause for the query
     */
    public function _buildQueryGroup(&$query) {
        
    }

    /**
     * Builds a HAVING clause for the query
     */
    public function _buildQueryHaving(&$query) {
        
    }

    /**
     * Builds a generic ORDER BY clause based on the model's state
     */
    public function _buildQueryOrder(&$query) {
        $order = $this->_db->getEscaped($this->getState('filter_order'));
        $direction = $this->_db->getEscaped(strtoupper($this->getState('filter_order_Dir')));

        if ($order) {
            $query->order($order, $direction);
        }

        // TODO Find an abstract way to determine if order is a valid field in query
        // if (in_array($order, $this->getTable()->getColumns())) does not work
        // because you could be ordering by a field from one of the JOINed tables
        if (in_array('ordering', $this->getTable()->getColumns())) {
            $query->order('ordering', 'ASC');
        }
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
            $query = 'SELECT *'
                    . ' FROM ' . $this->getTable()->getTableName()
                    . ' WHERE ' . $this->getTable()->getKeyName() . ' = ' . $this->getId()
            ;
            $this->_db->setQuery($query);
            $this->_item = $this->_db->loadObject();

            return (boolean) $this->_item;
        }
        return true;
    }

    function getItem($emptyState = true) {
        if (empty($this->_item)) {
            if ($emptyState) {
                $this->emptyState();
            }

            if ($this->_loadData()) {
                //			$user	= & JFactory::getUser();
            } else {
                $this->_initData();
            }
        }

        return $this->_item;
    }

    function _initData() {
        $user = & JFactory::getUser();
        $row = $this->getTable();
        $idkey = $row->getKeyName();
        // Lets load the content if it doesn't already exist
        $item = new stdClass();
        $item->$idkey = 0;

        $item->published = 1;
        $item->created_by = null;
        $item->created = null;
        $item->modified_by = null;
        $item->modified = 0;
        $item->checked_out = 0;
        $item->checked_out_time = 0;
        $this->_item = $item;

        return (boolean) $this->_item;
    }

}