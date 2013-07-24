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

// Require the base controller
// set the options array
$options = array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_jnegocio' );
jFWBase::load( 'jFWFrontController', 'controllers._base', $options );

/**
 * Base class for a jInmo Controller
 * 
 * @abstract
 * @package	Joomla
 * @subpackage	jNegocio
 */
class jNegocioController extends jFWFrontController {

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
}