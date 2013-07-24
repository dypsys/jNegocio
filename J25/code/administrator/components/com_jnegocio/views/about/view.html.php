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

class jNegocioViewAbout extends jFWView {
	
	function __construct($config = array()) {
		parent::__construct($config);
		$this->_hidesubmenu = true;
	}
	
	/**
	 * Displays a layout file
	 *
	 * @param unknown_type $tpl
	 * @return unknown_type
	 */
	function display($tpl=null) {
		jFWBase::load( 'jFWManifest', 'library.manifest' );
		$manifest = new jFWManifest();
		
		$this->assignRef( 'manifest', $manifest);
		
		parent::display($tpl);		
	}
	
	/**
	 * Gets layout vars for the view
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function getLayoutVars($tpl=null) {
		$layout = $this->getLayout();
		$this->_default($tpl);
	}
}