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

class jFWBase extends JObject
{
    // Product name.
    const PRODUCT = 'jnegocio';
    // Release version.
    const VERSION = '3.0';
    // Maintenance version.
    const MAINTENANCE = '1';
    // Development STATUS.
    const STATUS = 'developement';
    // Build number.
    const BUILD = 0;
    // Release date.
    const RELEASE_DATE = '21-Ago-2012';
    // Table prefix
    const TABLEPREFIX = 'neg';

    /**
     * Gets a table prefix
     *
     * @return  string  Version string.
     */
    public static function getTablePrefix()
    {
        return self::TABLEPREFIX . '_';
    }

    /**
     * Gets a "PHP standardized" version string for the current Joomla Platform.
     *
     * @return  string  Version string.
     */
    public static function getShortVersion() {
        return self::VERSION . '.' . self::MAINTENANCE;
    }

    /**
     * Gets a version string for the current Joomla Platform with all release information.
     *
     * @return  string  Complete version string.
     *
     * @since   11.1
     */
    public static function getLongVersion()
    {
        return self::PRODUCT . ' ' . self::VERSION . '.' . self::MAINTENANCE . ' ' . self::STATUS . ' ' . self::RELEASE_DATE;
    }

    /**
     * Get the Simple Component Name
     *
     * @return  string  Simple component name.
     *
     * @since   11.1
     */
    public static function getName()
    {
        return self::PRODUCT;
    }

    /**
     * Get the Complete Component Name
     *
     * @return  string  Complete component name.
     *
     * @since   11.1
     */
    public static function getComponentName()
    {
        return 'com_' . self::PRODUCT;
    }

    /**
     * Get the URL to the folder containing all media assets
     *
     * @param string	$type	    The type of URL to return, default 'media'
     * @param boolean	$pathonly	show '/'
     *
     * @return 	string	URL
     */
    public static function getURL($type = 'media', $pathonly = true)
    {
        $url = '';
        switch ($type) {
            case 'media' :
                $url = JURI::root($pathonly) . ($pathonly ? '/' : '') . 'media/' . self::getComponentName() . '/';
                break;
            case 'css' :
                $url = JURI::root($pathonly) . ($pathonly ? '/' : '') . 'media/' . self::getComponentName() . '/css/';
                break;
            case 'images' :
                $url = JURI::root($pathonly) . ($pathonly ? '/' : '') . 'media/' . self::getComponentName() . '/images/';
                break;
            case 'icons' :
                $url = JURI::root($pathonly) . ($pathonly ? '/' : '') . 'media/' . self::getComponentName() . '/images/icons/';
                break;
            case 'js' :
                $url = JURI::root($pathonly) . ($pathonly ? '/' : '') . 'media/' . self::getComponentName() . '/js/';
                break;
        }
        return $url;
    }

    /**
     * Get the path to the folder containing all media assets
     *
     * @param 	string	$type	The type of path to return, default 'media'
     *
     * @return 	string	Path
     */
    public static function getPath($type = 'media')
    {
        $path = '';
        switch ($type) {
            case 'media' :
                $path = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . self::getComponentName();
                break;
            case 'css' :
                $path = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . self::getComponentName() . DIRECTORY_SEPARATOR . 'css';
                break;
            case 'images' :
                $path = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . self::getComponentName() . DIRECTORY_SEPARATOR . 'images';
                break;
            case 'js' :
                $path = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . self::getComponentName() . DIRECTORY_SEPARATOR . 'js';
                break;
        }
        return $path;
    }

    /**
     * Loads a class from specified directories.
     *
     * @param string $classname The class name to look for
     * @param $filepath
     * @param array $options
     *
     * @internal param string $filePath Search this directory for the class.( dot notation ).
     * @return boolean
     * @since 1.5
     */
    public static function load($classname, $filepath, $options = array('site' => 'admin', 'type' => 'components', 'ext' => ''))
    {
        static $paths;

        $classname = strtolower($classname);
        $classes = JLoader::getClassList();
        if (class_exists($classname) || array_key_exists($classname, $classes)) {
            return true;
        }

        if (empty($paths)) {
            $paths = array();
        }

        if (empty($paths[$classname]) || !is_file($paths[$classname])) {
            // find the file and set the path
            if (!empty($options['base'])) {
                $base = $options['base'];
            } else {
                // recreate base from $options array
                switch ($options['site']) {
                    case "site":
                        $base = JPATH_SITE . DIRECTORY_SEPARATOR;
                        break;
                    default:
                        $base = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR;
                        break;
                }
                $base .= (!empty($options['type'])) ? $options['type'] . DIRECTORY_SEPARATOR : '';
                $base .= self::getComponentName() . DIRECTORY_SEPARATOR;
            }
            $paths[$classname] = $base . str_replace('.', DIRECTORY_SEPARATOR, $filepath) . '.php';
        }

        // if invalid path, return false
        if (!is_file($paths[$classname])) {
            return false;
        }

        // if not registered, register it
        if (!array_key_exists($classname, $classes)) {
            JLoader::register($classname, $paths[$classname]);
            return true;
        }
        return false;
    }

    /**
     * Intelligently loads instances of classes in framework
     *
     * @param string $classname		The class name
     * @param string $filepath		The filepath ( dot notation )
     * @param array  $options
     *
     * @return object of requested class (if possible), else a new JObject
     */
    public static function getClass($classname, $filepath = 'controller', $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jinmo'))
    {
        if (jFWBase::load($classname, $filepath, $options)) {
            $instance = new $classname();
            return $instance;
        }

        $instance = new JObject();
        return $instance;
    }
}

// extends JComponentHelper
class fwConfig extends JObject {

    var $_baseappname				= '';
    var $_tblname					= '';
    var $_db						= null;

    var $less_admin					= true;
    var $less_frontend              = true;
    var $debug_mode                 = false;

    var $frontend_lang              = '';
    var $backend_lang               = '';
    var $current_lang               = '';
    var $default_lang               = 'es-ES';

    /**
     * Object constructor
     *
     * Can be overloaded/supplemented by the child class
     *
     * @param 	object 	An optional Config object with configuration options.
     */
    public function __construct($name = null)
    {
        static $instances;
        $this->_baseappname	= 'com_' . jFWBase::getName();
        $this->_tblname		= '#__' . jFWBase::getTablePrefix() . 'config';
        $this->_db			= JFactory::getDBO();
        $this->loadconfig();
        parent::__construct();
    }

    /**
     * Returns a reference to a global fwConfig object, only creating it
     * if it doesn't already exist.
     *
     * @access    public
     *
     * @param     null $name
     * @return    fwConfig    The Config object.
     */
    public static function getInstance($name = null)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new fwConfig();
        }
        return $instance;
    }

    /**
     * Loads a row from the database and binds the fields to the object properties
     *
     * @return unknown_type
     */
    public function loadconfig()
    {
        $arr = get_object_vars($this);
        $ids = array();
        while (list($prop, $val) = each($arr)){
            if ('_' != substr($prop, 0, 1)) {
                $ids[] = "'" . $prop . "'";
            }
        }


        $olderr = error_reporting(0);
        // $this->_db->nameQuote($this->_tblname) .
        $query = "select settingname, value " .
            "from " . $this->_tblname . " " .
            "where settingname in(" . implode(',', $ids) . ")";

        $this->_db->setQuery($query);
        $rows = $this->_db->loadObjectList();
        error_reporting($olderr);
        if (count($rows)) {
            foreach ($rows as $row) {
                $prop = $row->settingname;
                $this->$prop = stripcslashes($row->value);
            }
        }
    }

    function save() {

        foreach ($this->getPropertiesConfig() as $prop => $val) {
            $query = "SELECT COUNT(config_id) as numitems "
                . " FROM " . $this->_tblname
                . " WHERE settingname = " . $this->_db->Quote($prop)
            ;

            $this->_db->setQuery($query, 0, 1);

            $row = null;
            $row = (int) $this->_db->loadResult();
            if ($row >= 1) {
                $query = "UPDATE " . $this->_tblname
                    . " SET value='" . addcslashes($val, "\0..\37!@\@\177..\377") . "' "
                    . " WHERE settingname = " . $this->_db->Quote($prop)
                ;

                $this->_db->setQuery($query);
                if (!$this->_db->query()) {
                    $err = $this->_db->getErrorMsg();
                    JError::raiseError(500, $err);
                    return false;
                }
            } else {
                $query = "INSERT INTO " . $this->_tblname
                    . " (settingname, value) "
                    . " values ('" . $prop . "', '" . addcslashes($val, "\0..\37!@\@\177..\377") . "')";
                $this->_db->setQuery($query);

                if (!$this->_db->query()) {
                    $err = $this->_db->getErrorMsg();
                    JError::raiseError(500, $err);
                    return false;
                }
            }
        }

        return true;
    }


    function getPropertiesConfig($public = true) {
        $vars = get_object_vars($this);

        if ($public) {
            foreach ($vars as $key => $value) {
                if ('_' == substr($key, 0, 1)) {
                    unset($vars[$key]);
                }
            }
        }

        return $vars;
    }
}