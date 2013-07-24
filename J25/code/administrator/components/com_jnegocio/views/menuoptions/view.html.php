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

class jNegocioViewMenuoptions extends jFWView
{
	
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->_hidesubmenu = true;
	}
	
	/**
	 * Gets layout vars for the view
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function getLayoutVars($tpl=null) 
	{
		$layout = $this->getLayout();
		$this->_default($tpl);
	}

	/**
	 * The default toolbar for a list
	 * @return unknown_type
	 */
	function _defaultToolbar()
	{
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Popup', 'options', 'Preferences', 'index.php?option='.jFWBase::getComponentName().'&view=configuration&tmpl=component' );
		$bar->appendButton( 'Popup', 'about', 'About', 'index.php?option=com_'.jFWBase::getName().'&view=about&tmpl=component' );
	}	
}