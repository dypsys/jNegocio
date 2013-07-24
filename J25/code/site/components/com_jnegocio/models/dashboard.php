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

// set the options array
$options = array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_jnegocio' );
jFWBase::load( 'jFWFrontModel', 'models._base', $options );

class jNegocioModelDashboard extends jFWFrontModel 
{
    function getTable() {
        $table = JTable::getInstance('Config', jFWBase::getTablePrefix());
        return $table;
    }

}