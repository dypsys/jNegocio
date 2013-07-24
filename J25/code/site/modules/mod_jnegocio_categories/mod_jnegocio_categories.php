<?php
/**
* @version	$Id$
* @package	Joomla
* @subpackage	jInmo
* @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
* @license	Comercial License
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the syndicate functions only once
require_once ( dirname(__FILE__) .DS. 'helper.php' );

if ( !class_exists('jFWBase') ) {
    JLoader::register( "jFWBase", JPATH_ADMINISTRATOR.DS."components".DS."com_jnegocio".DS."defines.php" );
}

// load the config class
jFWBase::load( 'jFWConfig', 'defines' );

// include lang files
$element = 'com_jnegocio';
$lang =& JFactory::getLanguage();
$lang->load( $element, JPATH_BASE );
$lang->load( $element, JPATH_ADMINISTRATOR );

$uid = uniqid();

$necConfig = fwConfig::getInstance();
if ($necConfig->get('loadjquey_frontend')) {
    if ($necConfig->get('debug_mode')) {
        JHtml::_('script', 'jquery/jquery.js', jFWBase::getUrl('js', false));
    } else {
        JHtml::_('script', 'jquery/jquery.min.js', jFWBase::getUrl('js', false));
    }
    JHtml::_('script', 'jquery/jquery-noconflict.js', jFWBase::getUrl('js', false));
}

$list = &modJNegocioCategoriesHelper::getList($params);

require JModuleHelper::getLayoutPath('mod_jnegocio_categories', $params->get('layout', 'default'));