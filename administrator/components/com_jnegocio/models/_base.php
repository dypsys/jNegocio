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

// Importamos el model de Joomla
jimport('joomla.model.database');

$options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
jFWBase::load('HelperLanguages', 'helpers.languages', $options);

/**
 * Base class for a FrameWork Model
 *
 * @extends     in Joomla Framework is AbstractDatabaseModel
 *              in Joomla 3.x is JModelDatabase
 *
 * @package		jFWModel
 * @subpackage	FrameWork
 * @since		3.0.1
 */
class jFWModel extends JModelDatabase
{
    /**
     * Database Connector
     *
     * @var    object
     */
    protected $_db;

    /**
     * The model (base) name
     *
     * @var    string
     */
    protected $name;

    var $_data          = null;
    var $_id            = null;
    var $_query         = null;
    var $_total         = null;
    var $_pagination    = null;
    var $_item          = null;
    var $_lang          = null;
    var $_filterinput   = null; // instance of JFilterInput

    /**
     * Returns a Model object, always creating it
     *
     * @param   string  $type    The model type to instantiate
     * @param   string  $prefix  Prefix for the model class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  mixed   A model object or false on failure
     */
    public static function getInstance($type, $prefix = '', $config = array())
    {
        $type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
        $modelClass = $prefix . ucfirst($type);

        if (!class_exists($modelClass))
        {
            jimport('joomla.filesystem.path');
            $path = JPath::find(self::addIncludePath(null, $prefix), self::_createFileName('model', array('name' => $type)));
            if (!$path)
            {
                $path = JPath::find(self::addIncludePath(null, ''), self::_createFileName('model', array('name' => $type)));
            }
            if ($path)
            {
                require_once $path;

                if (!class_exists($modelClass))
                {
                    JLog::add(JText::sprintf('JLIB_APPLICATION_ERROR_MODELCLASS_NOT_FOUND', $modelClass), JLog::WARNING, 'jerror');
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        return new $modelClass($config);
    }

    /**
     * Constructor
     */
    function __construct() {
        $this->_lang = HelperLanguages::getlang();
        parent::__construct();
        // $this->_filterinput = &JFilterInput::getInstance();

        // Set the view name
        if (empty($this->name)) {
            $this->name = $this->getName();
        }

        $this->_db = JFactory::getDbo();
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

        // Populate the state
        $this->loadState();
    }

    /**
     * Method to get the database driver object
     *
     * @return  JDatabaseDriver
     */
    public function getDbo()
    {
        return $this->_db;
    }

    /**
     * Method to get the model name
     *
     * The model name. By default parsed using the classname or it can be set
     * by passing a $config['name'] in the class constructor
     *
     * @return  string  The name of the model
     *
     * @throws  Exception
     */
    public function getName()
    {
        if (empty($this->name))
        {
            $r = null;
            if (!preg_match('/Model(.*)/i', get_class($this), $r))
            {
                throw new Exception(JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
            }
            $this->name = strtolower($r[1]);
        }

        return $this->name;
    }

    /**
     * Add a directory where JModelLegacy should search for models. You may
     * either pass a string or an array of directories.
     *
     * @param   mixed   $path    A path or array[sting] of paths to search.
     * @param   string  $prefix  A prefix for models.
     *
     * @return  array  An array with directory elements. If prefix is equal to '', all directories are returned.
     */
    public static function addIncludePath($path = '', $prefix = '')
    {
        static $paths;

        if (!isset($paths)) {
            $paths = array();
        }

        if (!isset($paths[$prefix])) {
            $paths[$prefix] = array();
        }

        if (!isset($paths[''])) {
            $paths[''] = array();
        }

        if (!empty($path)) {
            jimport('joomla.filesystem.path');

            if (!in_array($path, $paths[$prefix])) {
                array_unshift($paths[$prefix], JPath::clean($path));
            }

            if (!in_array($path, $paths[''])) {
                array_unshift($paths[''], JPath::clean($path));
            }
        }

        return $paths[$prefix];
    }

    /**
     * Method to get a table object, load it if necessary.
     *
     * @access  public
     *
     * @param   string  $name    table name. Optional.
     * @param   string  $prefix
     * @param   array   $options Configuration array for model. Optional.
     *
     * @throws InvalidArgumentException
     * @internal param \The $string table name. Optional.
     * @internal param \The $string class prefix. Optional.
     * @return  object  The table
     * @since   3.0.1
     */
    function getTable($name = '', $prefix = '', $options = array()) {
        if (empty($name)) {
            $name = $this->getName();
        }

        if (empty($prefix)) {
            $prefix = jFWBase::getTablePrefix();
        }

        JTable::addIncludePath(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . jFWBase::getComponentName() . DIRECTORY_SEPARATOR . 'tables');
        if ($table = $this->_createTable($name, $prefix, $options)) {
            return $table;
        } else {
            throw new InvalidArgumentException(JText::sprintf('COM_JNEGOCIO_ERROR_INVALID_TABLE', $name));
        }

        return null;
    }

    /**
     * Adds to the stack of model table paths in LIFO order.
     *
     * @param   mixed  $path  The directory as a string or directories as an array to add.
     *
     * @return  void
     *
     * @since   12.2
     */
    public static function addTablePath($path)
    {
        JTable::addIncludePath($path);
    }

    /**
     * Method to load and return a model object.
     *
     * @param   string  $name    The name of the view
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration settings to pass to JTable::getInstance
     *
     * @return  mixed  Model object or boolean false if failed
     *
     * @since   12.2
     * @see     JTable::getInstance()
     */
    protected function _createTable($name, $prefix = 'Table', $config = array())
    {
        // Clean the model name
        $name = preg_replace('/[^A-Z0-9_]/i', '', $name);
        $prefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);

        // Make sure we are returning a DBO object
        if (!array_key_exists('dbo', $config))
        {
            $config['dbo'] = $this->_db;
        }

        return JTable::getInstance($name, $prefix, $config);
    }

    /**
     * Create the filename for a resource
     *
     * @param   string  $type   The resource type to create the filename for.
     * @param   array   $parts  An associative array of filename information.
     *
     * @return  string  The filename
     *
     * @since   12.2
     */
    protected static function _createFileName($type, $parts = array())
    {
        $filename = '';

        switch ($type)
        {
            case 'model':
                $filename = strtolower($parts['name']) . '.php';
                break;

        }
        return $filename;
    }

    /**
     * Empties the state
     *
     * @return unknown_type
     */
    public function emptyState() {
        // Set State as new JRegistry;
        $this->setState($this->loadState());

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
     * Metodo para assignar el Identificador
     *
     * @access	public
     * @param	int ID
     **/
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
            $jinput = JFactory::getApplication()->input;
            $id = $jinput->getInt('id', '0');
            $array  = $jinput->get('cid', array($id), 'array');
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
     * @access  public
     * @param   bool    $refresh
     * @return  array
     */
    function getData($refresh = false) {
        if (empty($this->_data) || $refresh) {
            $query = $this->getQuery();
            // $strQuery = (string) $query;
            // echo "query:".$query."<br/>";

            $limitstart = $this->getState()->get('limitstart', 0);
            $limit = $this->getState()->get('limit', 0);

            $this->_db->setQuery($query, $limitstart, $limit);
            $this->_data = $this->_db->loadObjectList();
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
     * Returns a record count for the query.
     *
     * @param   JDatabaseQuery|string  $query  The query.
     *
     * @return  integer  Number of rows for query.
     *
     * @since   12.2
     */
    protected function _getListCount($query)
    {
        // Use fast COUNT(*) on JDatabaseQuery objects if there no GROUP BY or HAVING clause:
        if ($query instanceof JDatabaseQuery
            && $query->type == 'select'
            && $query->group === null
            && $query->having === null)
        {

            $query = clone $query;
            $query->clear('select')->clear('order')->select('COUNT(*)');

        } else {

            $query = $this->getDbo()->getQuery(true);

            $query->select('COUNT(*)');
            $this->_buildQueryFrom($query);
            $this->_buildQueryJoins($query);
            $this->_buildQueryWhere($query);
            $this->_buildQueryGroup($query);
        }

        $this->_db->setQuery($query);
        return (int) $this->_db->loadResult();
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
            // echo "get pagination: Total:".$this->getTotal()." limitstart:".$this->getState()->get('limitstart')." limit:".$this->getState()->get('limit')."<br/>";
            $this->_pagination = new JPagination($this->getTotal(), $this->getState()->get('limitstart'), $this->getState()->get('limit'));
        }

        return $this->_pagination;
    }

    /**
     * Builds a generic SELECT query
     *
     * @param   bool $refresh
     * @return  string  SELECT query
     */
    public function _buildQuery($refresh = false) {
        if (!empty($this->_query) && !$refresh) {
            return $this->_query;
        }

        $query = $this->_db->getQuery(true);

        $this->_buildQueryFields($query);
        $this->_buildQueryFrom($query);
        $this->_buildQueryJoins($query);
        $this->_buildQueryWhere($query);
        $this->_buildQueryGroup($query);
        $this->_buildQueryHaving($query);
        $this->_buildQueryOrder($query);

        return $query;
    }

    /**
     * Builds SELECT fields list for the query
     */
    public function _buildQueryFields($query) {
        $query->select($this->getState()->get('select', 'tbl.*'));
        if ($this->getTable()->getKeyName() != 'id') {
            $query->select('tbl.' . $this->getTable()->getKeyName() . ' AS id');
        }
    }

    /**
     * Builds FROM tables list for the query
     */
    public function _buildQueryFrom($query) {
        $name = $this->getTable()->getTableName();
        $query->from($name . ' AS tbl');
    }

    /**
     * Builds JOINS clauses for the query
     */
    public function _buildQueryJoins($query) {
    }

    /**
     * Builds WHERE clause for the query
     */
    public function _buildQueryWhere($query) {
    }

    /**
     * Builds a GROUP BY clause for the query
     */
    public function _buildQueryGroup($query) {
    }

    /**
     * Builds a HAVING clause for the query
     */
    public function _buildQueryHaving($query) {
    }

    /**
     * Builds a generic ORDER BY clause based on the model's state
     */
    public function _buildQueryOrder($query) {
        $order = $this->_db->getEscaped($this->getState()->get('filter_order'));
        $direction = $this->_db->getEscaped(strtoupper($this->getState()->get('filter_order_Dir')));

        if ($order) {
            $query->order($order, $direction);
        }

        // if (in_array('ordering', $this->getTable()->getColumns())) {
        if (in_array('ordering', $this->getTable()->getFields())) {
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

    function _initData() {
        $user = JFactory::getUser();
        $idkey = $this->getTable()->getKeyName();

        // Lets load the content if it doesn't already exist
        $item = new stdClass();
        $item->$idkey       = 0;
        $item->published    = 1;
        $item->created_by   = null;
        $item->created      = null;
        $item->modified_by  = null;
        $item->modified     = 0;
        $item->checked_out  = 0;
        $item->checked_out_time = 0;

        $this->_item = $item;

        return (boolean) $this->_item;
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
}