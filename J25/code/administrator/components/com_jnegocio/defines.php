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

// Component Helper
jimport('joomla.application.component.helper');

class jFWBase extends JObject {
    // Product name.

    const PRODUCT = 'jnegocio';
    // Release version.	
    const VERSION = '2.0';
    // Maintenance version.
    const MAINTENANCE = '1';
    // Development STATUS.
    const STATUS = 'developement';
    // Build number.
    const BUILD = 0;
    // Release date.
    const RELEASE_DATE = '21-Ago-2012';
    const TABLEPREFIX = 'nec';

    public static function getTablePrefix() {
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
    public static function getLongVersion() {
        return self::PRODUCT . ' ' . self::VERSION . '.' . self::MAINTENANCE . ' '
                . self::STATUS . ' ' . self::RELEASE_DATE;
    }

    /**
     * Get the Name
     */
    public static function getName() {
        return self::PRODUCT;
    }

    public static function getComponentName() {
        return 'com_' . self::PRODUCT;
    }

    /**
     * Get the URL to the folder containing all media assets
     *
     * @param string	$type	The type of URL to return, default 'media'
     * @return 	string	URL
     */
    public static function getURL($type = 'media', $pathonly = true) {
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
     * @return 	string	Path
     */
    public static function getPath($type = 'media') {
        $path = '';

        switch ($type) {
            case 'media' :
                $path = JPATH_SITE . DS . 'media' . DS . self::getComponentName();
                break;
            case 'css' :
                $path = JPATH_SITE . DS . 'media' . DS . self::getComponentName() . DS . 'css';
                break;
            case 'images' :
                $path = JPATH_SITE . DS . 'media' . DS . self::getComponentName() . DS . 'images';
                break;
            case 'js' :
                $path = JPATH_SITE . DS . 'media' . DS . self::getComponentName() . DS . 'js';
                break;
        }

        return $path;
    }

    /**
     * Loads a class from specified directories.
     *
     * @param string $classname 	The class name to look for
     * @param string $filePath	Search this directory for the class.( dot notation ).
     * @param string $options	.
     * @return void
     * @since 1.5
     */
    function load($classname, $filepath, $options = array('site' => 'admin', 'type' => 'components', 'ext' => '')) {
        $classname = strtolower($classname);
        $classes = JLoader::getClassList();
        // echo "classes:".var_dump($classes)."<hr/>";
        // echo "buscamos:".$classname."<hr/>";
        if (class_exists($classname) || array_key_exists($classname, $classes)) {
            return true;
        }

        static $paths;

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
                        $base = JPATH_SITE . DS;
                        break;
                    default:
                        $base = JPATH_ADMINISTRATOR . DS;
                        break;
                }

                $base .= (!empty($options['type'])) ? $options['type'] . DS : '';
                $base .= self::getComponentName() . DS;
                // $option .DS;
                // $base .= (!empty($options['ext'])) ? $options['ext'].DS : '';
            }

            $paths[$classname] = $base . str_replace('.', DS, $filepath) . '.php';
        }

        // if invalid path, return false
        if (!is_file($paths[$classname])) {
            // echo "file does not exist, class $classname file $paths[$classname]<br/>";
            return false;
        }

        // if not registered, register it
        if (!array_key_exists($classname, $classes)) {
            // echo "jloader register:".$classname." classes:".$paths[$classname]."<br/>";
            // echo "$classname not registered, so registering it<br/>";
            JLoader::register($classname, $paths[$classname]);
            return true;
        }
        return false;
    }

    /**
     * Intelligently loads instances of classes in framework
     * 
     * @param string $classname   The class name
     * @param string $filepath    The filepath ( dot notation )
     * @param array  $options
     * @return object of requested class (if possible), else a new JObject
     */
    public function getClass($classname, $filepath = 'controller', $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jinmo')) {
        if (jFWBase::load($classname, $filepath, $options)) {
            $instance = new $classname();
            return $instance;
        }

        $instance = new JObject();
        return $instance;
    }

    public static function dump(&$var, $htmlSafe = true) {
        $result = print_r($var, true);
        return '<pre>' . ( $htmlSafe ? htmlspecialchars($result) : $result) . '</pre>';
    }

}

// extends JComponentHelper
class fwConfig extends JObject {

    var $_baseappname = '';
    var $_tblname = '';
    var $_db = null;
    
    var $less_admin                 = true;
    var $less_frontend              = true;
    var $debug_mode                 = false;
    
    var $loadjquey_admin            = true;
    var $loadjquey_frontend         = true;
    
    var $frontend_lang              = '';
    var $backend_lang               = '';
    var $current_lang               = '';
    var $default_lang               = 'es-ES';
    
    var $manufacturer_img_width     = 50;
    var $manufacturer_img_height    = 50;
    var $manufacturer_img_quality   = 100;
    
    var $category_img_width         = 128;
    var $category_img_height        = 96;
    var $category_img_quality       = 100;

    var $product_img_width          = 640;
    var $product_img_height         = 480;
    var $product_img_quality        = 90;
    
    var $product_list_width         = 200;
    var $product_list_height        = 150;
    var $product_list_quality       = 90;
    var $product_thumb_width        = 100;
    var $product_thumb_height       = 75;
    var $product_thumb_quality      = 100;
    
    var $plupload_runtime		= 'gears,html5,flash,silverlight,browserplus';
    var $plupload_max_file_size		= 2048;
    var $plupload_max_file_size_unit	= 'kb';
    var $plupload_chunk_size		= 524;	// 524Kb*2 = 1024 kb = 1Mb 
    var $plupload_chunk_unit		= 'kb';
    var $plupload_image_file_extensions	= 'jpg,png,gif';    
    var $plupload_enable_image_resizing = 1;
    var $plupload_resize_width		= '800';
    var $plupload_resize_height		= '600';
    var $plupload_resize_quality	= '90';
    
    var $upload_target_dir          = 'images/jnegocio';
    
    var $company_name       = '';
    var $company_adress     = '';
    var $company_city       = '';
    var $company_country    = 195;
    var $company_zone       = 1;
    var $company_codpostal  = '';
    var $company_phone      = '';
    
    var $default_currencyid = 1;
    var $default_usergroup  = 1;
    var $work_pricewithtax  = 1;
    
    var $productlist_displaystyle = 'list';
    var $productlist_products_x_row = 3;
    var $productlist_products_x_page = 9;
    
    /**
     * A hack to support __construct()
     *
     * @access	public
     * @return	Object
     * @since	1.5
     */
    function fwConfig() {
        $args = func_get_args();
        call_user_func_array(array(&$this, '__construct'), $args);
    }

    /**
     * Object constructor
     *
     * Can be overloaded/supplemented by the child class
     *
     * @param 	object 	An optional Config object with configuration options.
     */
    public function __construct($name = null) {
        static $instances;

        $this->_baseappname = 'com_' . jFWBase::getName();
        $this->_tblname = '#__nec_config';
        $this->_db = & JFactory::getDBO();
        $this->loadconfig();
        parent::__construct();
    }

    /**
     * Returns a reference to a global Editor object, only creating it
     * if it doesn't already exist.
     *
     * @access	public
     * @return	fwQuery	The jMultiPartner Menu object.
     */
    function & getInstance($name = null) {
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
    function loadconfig() {

        $arr = get_object_vars($this);
        $ids = array();
        while (list($prop, $val) = each($arr))
            $ids[] = "'" . $prop . "'";
        $olderr = error_reporting(0);
        $query = "select settingname, value " .
                "from " . $this->_db->nameQuote($this->_tblname) .
                "where settingname in(" . implode(',', $ids) . ")"
        ;

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
                    . " FROM " . $this->_db->nameQuote($this->_tblname)
                    . " WHERE settingname = " . $this->_db->Quote($prop)
            ;

            $this->_db->setQuery($query, 0, 1);

            $row = null;
            $row = (int) $this->_db->loadResult();
            if ($row >= 1) {
                $query = "UPDATE " . $this->_db->nameQuote($this->_tblname)
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
                $query = "INSERT INTO " . $this->_db->nameQuote($this->_tblname)
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

    /**
     * Binds a named array/hash to this object
     * 
     * @access	public
     * @param 	$request mixed	An associative array or object
     * @return 	boolean
     */
    function bindRequest($request) {
        $fromArray = is_array($request);
        $fromObject = is_object($request);

        if (!$fromArray && !$fromObject) {
            $this->setError(get_class($this) . '::bind failed. Invalid from argument');
            return false;
        }

        foreach ($this->getProperties() as $k => $v) {
            if ($fromArray && isset($from[$k])) {
                $this->$k = $from[$k];
            } else if ($fromObject && isset($from->$k)) {
                $this->$k = $from->$k;
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