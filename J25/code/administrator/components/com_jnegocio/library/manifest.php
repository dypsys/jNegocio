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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.path');

Class jFWManifest
{
	private	$_manifest	= null;
	private $_xmlpath	= null;	
	
	/**
	 * A hack to support __construct()
	 *
	 * @access	public
	 * @return	Object
	 * @since	1.5
	 */
	function jFWManifest() {
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}
	
	/**
	 * Returns a reference to a global Editor object, only creating it
	 * if it doesn't already exist.
	 *
	 * @access	public
	 * @return	jFWManifest	class.
	 */
	function &getInstance($options) {
		static $instance;
		if (!isset($instance)){
			$instance = new jFWManifest($options);
		}
		return $instance;
	}
	
	function __construct($xmlpath='') {
		if (strlen($xmlpath)) {
			$this->_xmlpath = $xmlpath;
		} else {
			$this->_xmlpath = JPATH_ADMINISTRATOR .DS. 'components' .DS. jFWBase::getComponentName() .DS. jFWBase::getName() . '.xml';  
		}
	
		if (JFile::exists($this->_xmlpath)) {
			//load the file, and save it to our object
			$this->_manifest = simplexml_load_file($this->_xmlpath);
		} else {
			$this->_manifest = null;
		}
	}
	
	function getManifest() {
		return $this->_manifest;
	}
	
	/**
	 * Get the changelog from our manifest
	 */
	public function changelogs()
	{
		//Get the changelogs
		$changelogs = ($xml = @$this->_manifest->changelogs) ? $xml : array();
		
		jimport('joomla.utilities.date');
		
		$html = array();
		
		//Render the changelogs
		foreach ( $changelogs->children() as $name => $changelog) {
			if ($name == 'version')	$html[] = '<div class="changelog">';
			$html[] = '<div><strong>' . JText::_($name) . ':</strong>';
			if ($name == 'date') {
				$date = new JDate($changelog);
				$html[] = $date->toFormat('%d-%m-%Y');
				unset($date);
			} else {
				$html[] = JText::_($changelog);
			}
			$html[] = '</div>';
			
			if ($states = $name == 'state') $html[] = '<ul>';
			foreach ($changelog->children() as $type => $state) {
				$title 	= $state['title'] ? ' title="' . JText::_($state['title']) . '"' : null;
				$html[] = '<li class="' . JText::_($type) . '" rel="' . $type . '"' . $title . '>';
				$html[] = JText::_($state);
				$html[] = '</li>';
			}
			if ($states) {
				$html[] = '</ul>';
			}
				
			if($name == 'state') {
				$html[] = '</div>';
			}
		}
		
		return implode("\n", $html);
	}
	
	/**
	 * Get the menu admin from our manifest
	 */
	public function menuadmin()
	{
		//Get the changelogs
		$iMenu = ($xml = @$this->_manifest->menuadmin) ? $xml : array();
		
		if(!$iMenu) return false;
		
		jimport('joomla.html.pane');
		
		$html = array();
		
		$html[] = JHtml::_('sliders.start', 'jec-menuadmin', array('useCookie'=>1));
		foreach($iMenu->children() as $keymenu => $menu) {
						
			$htmlPanel = "";
			$htmlitems = "";
			$titulo = "";
			
			if ($keymenu == 'submenu') {
				
				if (isset($menu['name'])) {
					$titulo = JText::_($menu['name']);
				}
				
				foreach($menu->children() as $keysubmenu => $submenu) {
					if ($keysubmenu == 'menu') {
						$htmlitems .= $this->checkMenuItem($submenu);
					}
				}								
			}
			
			$html[] = JHtml::_('sliders.panel', $titulo, 'jec-menuadmin-'.$menu['name'] );
			$html[] = $htmlitems;
		}
		$html[] = JHtml::_('sliders.end');
		
		return implode("\n", $html);
	}
	
	function checkMenuItem($submenu) {
		// Set the sub menu link
		if ($submenu["link"]) {
			$admin_menu_link = str_replace('&amp;', '&', $submenu["link"]);
		} else {
			$request = array();
			if ($submenu['act']) {
				$request[] = 'act='.(string)$submenu['act'];
			}
			if ($submenu['task']) {
				$request[] = 'task='.(string)$submenu['task'];
			}
			if ($submenu['controller']) {
				$request[] = 'controller='.(string)$submenu['controller'];
			}
			if ($submenu['view']) {
				$request[] = 'view='.(string)$submenu['view'];
			}
			if ($submenu['layout']) {
				$request[] = 'layout='.(string)$submenu['layout'];
			}
			$qstring = (count($request)) ? '&'.implode('&',$request) : '';
			$admin_menu_link = "index.php?option=".jFWBase::getComponentName().$qstring;
		}
		
		// Set the sub menu image
		if ($submenu["img"]) {
			$admin_menu_img = (string)$submenu["img"];
		} else {
			$admin_menu_img = null;
		}
		
		$admin_text = JText::_( (string)$submenu );
		
		return $this->renderMenuItem($admin_text, $admin_menu_img, $admin_menu_link);
	}
	
	function renderMenuItem($text, $img, $link) {
		//initialise variables
		$html = array ();
		$html[] = '<div class="jecMenuItem">';		
		$properties = "";
		$html[] = '<a href="' . $link . '" ' . $properties .'>';
		if ($img && ($img!=null)) {
			$html[] = '<img src="'.jFWBase::getURL('icons'). '16/'. $img .'" border="0" alt="'. $text .'" />';
		}
		
		$html[] = '<span>' . $text . '</span>';
		$html[] = '</a>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
}