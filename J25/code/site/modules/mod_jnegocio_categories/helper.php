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

class modJNegocioCategoriesHelper {
    
    static function &getList(&$params) {
        
        $options = array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_jnegocio');
	jFWBase::load( 'HelperLanguages', 'helpers.languages', $options );
	$lang = &HelperLanguages::getlang();
        
	JTable::addIncludePath( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jnegocio' .DS. 'tables' );
	$table = JTable::getInstance( 'Categories', jFWBase::getTablePrefix() );
	$items = $table->getTreeList();
		
        // $tree = array();
	$list = array();
	$fieldname = $lang->getField('name');
        foreach (@$items as $item) {
            $item->name = $item->$fieldname;
            $list[] = $item;
        }
        return $list;
    }
}