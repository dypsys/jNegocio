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

// Require the base controller
jFWBase::load( 'jFWControllerCRUD', 'controllers._crud' );

class jNegocioControllerTaxrates extends jFWControllerCRUD
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->set('suffix', 'taxrates');
	}
	
	/**
	 * Sets the model's default state based on value in the request
	 *
	 * @return unknown_type
	 */
	function _setModelState()
	{
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();
	
		$state['filter_geozoneid'] 		= $app->getUserStateFromRequest( $ns.'.filter_geozoneid', 'filter_geozoneid', '', '');
		$state['filter_typetaxid'] 		= $app->getUserStateFromRequest( $ns.'.filter_typetaxid', 'filter_typetaxid', '', '');
	
		foreach (@$state as $key=>$value) {
			$model->setState( $key, $value );
		}
	
		return $state;
	}
}