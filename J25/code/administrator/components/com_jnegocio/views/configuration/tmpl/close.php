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

// close a modal window
JFactory::getDocument()->addScriptDeclaration('
	window.parent.location.href=window.parent.location.href;
	window.parent.SqueezeBox.close();
');