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

jFWBase::load( 'HelperRoute', 'helpers.route', array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_jnegocio' ) );

/**
 * Build the route for the com_content component
 *
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 * @since	1.5
 */
function jNegocioBuildRoute(&$query)
{
    return HelperRoute::build($query);
}

/**
 * Parse the url segments
 * Is just a wrapper for HelperRoute::parse()
 * 
 * @param unknown_type $segments
 * @return unknown_type
 */
function jNegocioParseRoute($segments)
{
    return HelperRoute::parse($segments);
}
