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

jFWBase::load('jFWHelperBase', 'helpers._base');

class HelperRoute extends jFWHelperBase {

    static $itemids = null;

    /**
     * devuelve un array con todos los items de menu publicados del componente jnegocio
     */
    function getItems($option = 'com_jnegocio') {
        static $items;

        if (empty($option)) {
            $option = 'com_jnegocio';
        }

        $menus = &JApplication::getMenu('site', array());
        if (empty($menus)) {
            return array();
        }

        if (empty($items)) {
            $items = array();
        }

        if (empty($items[$option])) {
            $component = &JComponentHelper::getComponent($option);
            // echo var_dump($menus);
            $items = $menus->getItems('component', 'com_jnegocio');
            foreach ($items as $item) {
                if (!is_object($item)) {
                    continue;
                }

                if ($item->component_id == $component->id || (!empty($item->query['option']) && $item->query['option'] == $option)) {
                    $items[$option][] = $item;
                }
            }
        }

        if (empty($items[$option])) {
            return array();
        }

        return $items[$option];
    }

    /**
     * Finds the itemid for the set of variables provided in $needles
     * Busca el itemid para el conjunto de las variables previstas en $needles
     * 
     * @param array $needles
     * @return unknown_type
     */
    public static function findItemid($needles = array('view' => 'products', 'task' => '', 'id' => '')) {
        // populate the array of menu items for the extension
        if (empty(self::$itemids)) {
            self::$itemids = array();

            $items = self::getItems();

            if (empty($items)) {
                return null;
            }

            foreach ($items as $item) {
                if (!empty($item->query) && !empty($item->query['view'])) {
                    $query = "";

                    $view = $item->query['view'];
                    $query .= "&view=" . $view;

                    if (!empty($item->query['task'])) {
                        $task = $item->query['task'];
                        $query .= "&task=" . $task;
                    }

                    if (!empty($item->query['id'])) {
                        $id = $item->query['id'];
                        $query .= "&id=" . $id;
                    }

                    // set the itemid in the cache array
                    if (empty(self::$itemids[$query])) {
                        self::$itemids[$query] = $item->id;
                    }
                }
            }
        }

        // reconstruct query based on needle
        $query = "";
        if (!empty($needles['view'])) {
            $view = $needles['view'];
            $query .= "&view=" . $view;
        }

        if (!empty($needles['task'])) {
            $task = $needles['task'];
            $query .= "&task=" . $task;
        }

        if (!empty($needles['id'])) {
            $id = $needles['id'];
            $query .= "&id=" . $id;
        }

        // if the query exists in the itemid cache, return it
        if (!empty(self::$itemids[$query])) {
            return self::$itemids[$query];
        }

        return null;
    }

    /**
     * Build the route
     *
     * @param   array   An array of URL arguments
     * @return  array   The URL arguments to use to assemble the URL
     */
    function build(&$query) {
        $segments = array();

        // echo "<b>build route:</b><pre>".var_dump($query)."</pre><br/>\n";
        // get a menu item based on Itemid or currently active
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        $menuItem = null;

        if (empty($query['Itemid'])) {
            $menuItem = $menu->getActive();
        } else {
            // $menuItem = $menu->getItem($query['Itemid']);
        }
        // $menuItem 	= null;
        // if (isset($query['Itemid'])) {
        //  	unset($query['Itemid']);
        // }
        // echo "<b>build route:</b><pre>".var_dump($query)."</pre><br/>\n";

        $menuView = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
        if ($menuView == @$query['view'] && $menuView == "product") {
            unset($query['Itemid']);
            $menuItem = null;
        }

        $menuTask = (empty($menuItem->query['task'])) ? null : $menuItem->query['task'];
        $menuId = (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

        if ($menuView == @$query['view'] &&
                $menuTask == @$query['task'] &&
                $menuId == @$query['id']
        ) {
            // unset all variables and use the menu item's alias set by user 
            unset($query['view']);
            unset($query['layout']);
            unset($query['task']);
            unset($query['id']);
        }

        // otherwise, create the sef url from the query
        if (isset($query['view'])) {
            $view = $query['view'];
            $segments[] = $view;
            unset($query['view']);
        }
        
        if (isset($query['layout'])) {
            $layout = $query['layout'];
            $segments[] = $layout;
            unset($query['layout']);
        }        

        if (isset($query['task'])) {
            $task = $query['task'];
            $segments[] = $task;
            unset($query['task']);
        }

        if (isset($query['id'])) {
            $id = $query['id'];
            $segments[] = $id;
            unset($query['id']);
        }

        return $segments;
    }

    /**
     * Parses the segments of a URL
     *
     * @param   array   The segments of the URL to parse
     * @return  array   The URL attributes
     */
    function parse($segments) {
        $vars = array();

        //Get the active menu item
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        $item = $menu->getActive();

        $count = count($segments);

//        if (!isset($item)) {
            // echo "no item<br/>";
//            $vars['view'] = 'product';
//            $vars['id'] = $segments[$count - 1];
//            return $vars;
//        }

//        echo "count:".$count."<br/>";
//        echo "segments:".var_dump($segments)."<hr/>";
//        if ($count == 1 && is_numeric($segments[0])) {
//            // If there is only one numeric segment, then it points to a property detail
//            if (strpos($segments[0], ':') === false) {
//                $id = (int) $segments[0];
//            } else {
//                $exp = explode(':', $segments[0], 2);
//                $id = (int) $exp[0];
//            }
//
//            $vars['view'] = 'product';
//            $vars['id'] = $id;
//        }

        switch ($segments[0]) {
            case 'products':
                $vars['view'] = 'products';
                $vars['layout'] = $segments[$count - 1];
                break;

            case 'product':
                $vars['view'] = 'product';
                $vars['id'] = $segments[$count - 1];
                break;
        }
        
        if (isset($vars['id'])) {
            if (strpos($vars['id'], ':') === false) {
                $id = (int) $vars['id'];
            } else {
                $exp = explode(':', $vars['id'], 2);
                $id = (int) $exp[0];
            }
            $vars['id'] = $id;
        }
        return $vars;
    }

}
