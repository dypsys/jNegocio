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

class jFWHelperBase extends JObject {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Returns a reference to the a Helper object, only creating it if it doesn't already exist
     *
     * @param type 		$type 	 The helper type to instantiate
     * @param string 	$prefix	 A prefix for the helper class name. Optional.
     * @return helper The Helper Object	 
     */
    function &getInstance($type = 'Base', $prefix = 'Helper') {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        $type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);

        // The Base helper is in _base.php, but it's named TiendaHelperBase
        if (strtolower($type) == 'Base') {
            $helperClass = $prefix . ucfirst($type);
            $type = '_Base';
        }

        $helperClass = $prefix . ucfirst($type);

        if (empty($instances[$helperClass])) {
            if (!class_exists($helperClass)) {
                jimport('joomla.filesystem.path');
                if ($path = JPath::find(nHelperDefault::addIncludePath(), strtolower($type) . '.php')) {
                    require_once $path;

                    if (!class_exists($helperClass)) {
                        JError::raiseWarning(0, 'Helper class ' . $helperClass . ' not found in file.');
                        echo 'Helper class ' . $helperClass . ' not found in file.';
                        return false;
                    }
                } else {
                    JError::raiseWarning(0, 'Helper ' . $type . ' not supported. File not found.' . $helperClass);
                    echo 'Helper ' . $type . ' not supported. File not found.' . $helperClass;
                    return false;
                }
            }

            $instance = new $helperClass();

            $instances[$helperClass] = & $instance;
        }

        return $instances[$helperClass];
    }

    /**
     * Add a directory where jFWHelper should search for helper types. You may
     * either pass a string or an array of directories.
     *
     * @access	public
     * @param	string	A path to search.
     * @return	array	An array with directory elements
     * @since 1.5
     */
    function addIncludePath($path = null) {
        static $nHelperPaths;

        if (!isset($nHelperPaths)) {
            $nHelperPaths = array(dirname(__FILE__));
        }

        // just force path to array
        settype($nHelperPath, 'array');

        if (!empty($nHelperPath) && !in_array($nHelperPath, $nHelperPaths)) {
            // loop through the path directories
            foreach ($nHelperPath as $dir) {
                // no surrounding spaces allowed!
                $dir = trim($dir);

                // add to the top of the search dirs
                // so that custom paths are searched before core paths
                array_unshift($nHelperPaths, $dir);
            }
        }
        return $nHelperPaths;
    }

// 	/**
// 	 * Check if the path exists, and if not, tries to create it
// 	 * @param string $dir
// 	 * @param bool $create
// 	 */
// 	function checkDirectory($dir, $create = true)
// 	{
// 		$return = true;
// 		if (!$exists = &JFolder::exists( $dir ) ) 
// 		{
// 			if($create)
// 				$return = &JFolder::create( $dir );
// 			else
// 				$return = false;
// 		} 
// 		$change = &JPath::setPermissions( $dir );	
// 		return ($return && $change);
// 	}
// 	/**
// 	 * Extracts a column from an array of arrays or objects
// 	 *
// 	 * @static
// 	 * @param	array	$array	The source array
// 	 * @param	string	$index	The index of the column or name of object property
// 	 * @return	array	Column of values from the source array
// 	 * @since	1.5
// 	 */
// 	function getColumn(&$array, $index)
// 	{
// 		$result = array();
// 		if (is_array($array))
// 		{
// 			foreach (@$array as $item)
// 			{
// 				if (is_array($item) && isset($item[$index]))
// 				{
// 					$result[] = $item[$index];
// 				}
// 					elseif (is_object($item) && isset($item->$index))
// 				{
// 					$result[] = $item->$index;
// 				}
// 			}
// 		}
// 		return $result;
// 	}
// 	/**
// 	 * Takes an elements object and converts it to an array that can be binded to a JTable object
// 	 *
// 	 * @param $elements is an array of objects with ->name and ->value properties, all posted from a form
// 	 * @return array[name] = value
// 	 */
// 	function elementsToArray( $elements )
// 	{
// 		$return = array();
//         $names = array();
//         $checked_items = array();
//         if (empty($elements))
//         {
//             $elements = array();
//         }
// 		foreach (@$elements as $element)
// 		{
// 			$isarray = false;
// 			$name = $element->name;
// 			$value = (isset($element->value) ? $element->value : null);
//             $checked = (isset($element->checked) ? $element->checked : null);
// 			// if the name is an array,
// 			// attempt to recreate it 
// 			// using the array's name
// 			if (strpos($name, '['))
// 			{
// 				$isarray = true;
// 				$search = array( '[', ']' );
// 				$exploded = explode( '[', $name, '2' );
// 				$index = str_replace( $search, '', $exploded[0]);
// 				$name = str_replace( $search, '', $exploded[1]);
// 				if (!empty($index))
// 				{
//                     // track the name of the array
// 	                if (!in_array($index, $names))
// 	                {
//                         $names[] = $index;	
// 	                }
// 	                if (empty(${$index}))
// 	                {
// 	                    ${$index} = array(); 
// 	                }
// 	                if (!empty($name))
// 	                {
// 	                	${$index}[$name] = $value;
// 	                }
// 	                else
// 	                {
//                         ${$index}[] = $value;	
// 	                }
// 				    if ($checked)
//                     {
//                     	if (empty($checked_items[$index]))
//                     	{
//                     		$checked_items[$index] = array();
//                     	}
//                         $checked_items[$index][] = $value; 
//                     }
// 				}
// 			}
//             elseif (!empty($name))
// 			{
// 				$return[$name] = $value;
// 			    if ($checked)
//                 {
//                     if (empty($checked_items[$name]))
//                     {
//                         $checked_items[$name] = array();
//                     }
//                     $checked_items[$name] = $value; 
//                 }
// 			}
// 		}
// 		foreach ($names as $extra)
// 		{
// 			$return[$extra] = ${$extra};
// 		}
//         $return['_checked'] = $checked_items;
// 		return $return;
// 	}
// 	/**
//      * Generate an html message
//      * used for validation errors
//      * 
//      * @param string message
//      * @return html message
//      */
//     function generateMessage($msg, $include_li=true)
//     {
//         $html = '<dl id="system-message"><dt class="notice">'.JText::_( "Notice" ).'</dt>
//                  <dd class="notice message fade"><ul>';
//     	if ($include_li) {
// 			$html .= "<li>".$msg."</li>";
// 		} else {
// 			$html .= $msg;
// 		}
//         $html .= "</ul></dd></dl>";
//         return $html;
//     }    
}