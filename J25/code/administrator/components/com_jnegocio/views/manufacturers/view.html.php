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

class jNegocioViewManufacturers extends jFWView
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
			
			case "view":
			case "default":
			default:
				$this->_default($tpl);
			  	break;
		}
	}

	function _defaultToolbar()
	{
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		parent::_defaultToolbar();
	}	
}