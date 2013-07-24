<?php
/**
* @version	$Id$
* @package	Joomla
* @subpackage	jNegocio
* @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
* @license	Comercial License
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if ( !class_exists('jFWBase') ) {
    JLoader::register( "jFWBase", JPATH_ADMINISTRATOR.DS."components".DS."com_jnegocio".DS."defines.php" );
}

$options = array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_jnegocio' );
jFWBase::load( 'jFWConfig', 'defines', $options );

// include lang files
$element = jFWBase::getComponentName();
$lang = &JFactory::getLanguage();
$lang->load( $element, JPATH_BASE );
$lang->load( $element, JPATH_ADMINISTRATOR );

$options = array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_jnegocio' );
jFWBase::load( 'jFWFrontController', 'controllers._base', $options );

JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jnegocio'.DS.'tables' );

$ajax		= JRequest::getBool('no_html');
$necConfig 	= &fwConfig::getInstance();
$necConfig->current_lang = $lang->gettag();

// Require specific controller if requested
$controller = JRequest::getWord('controller', JRequest::getVar( 'view' ) );
if (!jFWBase::load( 'jNegocioController'.$controller, "controllers.$controller" , $options )) {
    $controller = '';
}

// Creamos nuestro propio controlador 
$classname  = 'jNegocioController'.$controller;
$controller = jFWBase::getClass( $classname );

$controller->execute( JRequest::getWord('task','display') );
$controller->redirect();
?>