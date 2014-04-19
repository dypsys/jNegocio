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

if (!class_exists('jFWBase')) {
    JLoader::register("jFWBase", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_jnegocio" . DIRECTORY_SEPARATOR . "defines.php");
}

// load the config class
jFWBase::load('jFWConfig', 'defines');
JTable::addIncludePath(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . jFWBase::getComponentName() . DIRECTORY_SEPARATOR . 'tables');

// Require the base controller
jFWBase::load('jFWController', 'controllers._base');

$ajax       = JFactory::getApplication()->input->getBool('no_html', false);
$adminlang  = JFactory::getLanguage();
$negConfig  = fwConfig::getInstance();
$negConfig->current_lang = $negConfig->default_lang;

if ($negConfig->backend_lang != $adminlang->getTag()) {
    $negConfig->backend_lang = $adminlang->getTag();
    $negConfig->save();
}

if (!$ajax) {
    jFWBase::load('HelperLanguages', 'helpers.languages', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
    HelperLanguages::installNewLanguages();
} else {
    //header for ajax
    header('Content-Type: text/html;charset=UTF-8');
}

// Require specific controller if requested
$controller = JFactory::getApplication()->input->get('controller', JFactory::getApplication()->input->get('view', 'dashboard'));
if (!jFWBase::load('jNegocioController' . $controller, "controllers.$controller")) {
    $controller = '';
}

// Creamos nuestro propio controlador
$classname = 'jNegocioController' . $controller;
$controller = jFWBase::getClass($classname);

// Ejecutamos la tarea
$controller->execute();
// $controller->redirect();