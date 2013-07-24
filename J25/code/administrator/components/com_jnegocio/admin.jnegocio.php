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

if (!class_exists('jFWBase')) {
    JLoader::register("jFWBase", JPATH_ADMINISTRATOR . DS . "components" . DS . "com_jnegocio" . DS . "defines.php");
}

// load the config class
jFWBase::load('jFWConfig', 'defines');

// Require the base controller
jFWBase::load('jFWController', 'controllers._base');
JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');

// $ajax 		= JRequest::getInt('ajax');
$ajax = JRequest::getBool('no_html');
$adminlang = &JFactory::getLanguage();
$necConfig = &fwConfig::getInstance();
$necConfig->current_lang = $necConfig->default_lang;

if ($necConfig->backend_lang != $adminlang->getTag()) {
    $necConfig->backend_lang = $adminlang->getTag();
    $necConfig->save();
}

if (!$ajax) {
    jFWBase::load('HelperLanguages', 'helpers.languages', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
    HelperLanguages::installNewLanguages();
} else {
    //header for ajax
    header('Content-Type: text/html;charset=UTF-8');
}

// Require specific controller if requested
$controller = JRequest::getWord('controller', JRequest::getVar('view', 'dashboard'));
if (!jFWBase::load('jNegocioController' . $controller, "controllers.$controller")) {
    $controller = '';
}

// Creamos nuestro propio controlador 
$classname = 'jNegocioController' . $controller;
$controller = jFWBase::getClass($classname);

// Ejecutamos la tarea
$controller->execute(JRequest::getWord('task'));
$controller->redirect();