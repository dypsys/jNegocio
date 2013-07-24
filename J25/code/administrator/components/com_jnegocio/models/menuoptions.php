<?php
/**
* @version		$Id$
* @package		Joomla
* @subpackage	jNegocio
* @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
* @license		Comercial License
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jFWBase::load( 'jFWModel', 'models._base' );

class jNegocioModelMenuoptions extends jFWModel 
{
	function getTable() {
		$table = JTable::getInstance( 'Config', jFWBase::getTablePrefix() );
		return $table;
	}
}