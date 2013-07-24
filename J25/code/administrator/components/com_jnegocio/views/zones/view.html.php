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

jFWBase::load( 'jFWView', 'views._base' );

class jNegocioViewZones extends jFWView
{	
	/**
	 * Gets layout vars for the view
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function getLayoutVars($tpl=null) 
	{
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "form":
				JRequest::setVar('hidemainmenu', '1');
				$this->_form($tpl);
			  	break;
			
			case "select":
				$model 	= $this->getModel();
				$state	= $model->getState();
				$filter_geozoneid = $state->filter_geozoneid;
				jFWBase::load( 'HelperZones', 'helpers.zones' );
				$this->geozone = HelperZones::getGeoZonebyId($filter_geozoneid);
				
				$this->_modal($tpl);
				break;
				
			case "view":
			case "default":
			default:
				$this->_default($tpl);
			  	break;
		}
	}

	function _defaultToolbar()
	{
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "select":
				JToolBarHelper::custom('selected_enabled', "publish", "publish", JText::_( 'COM_JNEGOCIO_ZONA_ENABLED_GEOZONA' ), true);
				JToolBarHelper::custom('selected_disabled', "unpublish", "unpublish", JText::_( 'COM_JNEGOCIO_ZONA_DISABLED_GEOZONA' ), true);
				break;
			default:
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::divider();
				parent::_defaultToolbar();
			  	break;
		}
	}
}