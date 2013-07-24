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

require_once( JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'select.php' );

Class jFWSelect extends JHTMLSelect
{
	/**
 	 * Generates a yes/no radio list
	 *
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @returns string HTML for the radio list
	 */
	public static function booleans( $selected, $name = 'filter_enabled', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title='Select State', $yes = 'Enabled', $no = 'Disabled' )
	{
	    $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -" );
		}

		$list[] = JHTML::_('select.option',  '0', JText::_( $no ) );
		$list[] = JHTML::_('select.option',  '1', JText::_( $yes ) );

		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
	}
	
	/**
	 * Shows a true/false graphics
	 *
	 * @param	bool	Value
	 * @param 	string	Image for true
	 * @param 	string	Image for false
	 * @param 	string 	Text for true
	 * @param 	string	Text for false
	 * @return 	string	Html img
	 */
	public static function boolean( $bool, $true_img = null, $false_img = null, $true_text = null, $false_text = null)
	{
		$true_img 	= $true_img 	? $true_img 	: 'tick.png';
		$false_img 	= $false_img	? $false_img	: 'publish_x.png';
		$true_text 	= $true_text 	? $true_text 	: 'Yes';
		$false_text = $false_text 	? $false_text 	: 'No';
		
		$imgsrc = ($bool ? $true_img : $false_img);
		$imgalt = JText::_($bool ? $true_text : $false_text);
		$image = JHTML::_('image.administrator',  $imgsrc, '/media/com_jnegocio/images/icons/' , NULL, NULL, $imgalt );
		
		return '<img src="'.jFWBase::getURL('icons'). ($bool ? $true_img : $false_img) .'" border="0" alt="'. JText::_($bool ? $true_text : $false_text) .'" />';
		// return $image;
	}
	
	/**
	 * Generate a state list
	 * @param $filter_state
	 * @param $published
	 * @param $unpublished
	 * @param $archived
	 * @param $trashed
	 * @return unknown_type
	 */
	public static function state( $filter_state='*', $published='COM_JNEGOCIO_OPTION_PUBLISHED', $unpublished='COM_JNEGOCIO_OPTION_UNPUBLISHED', $archived=NULL, $trashed=NULL )
	{
		$state[] = JHTML::_('select.option',  '', '- '. JText::_( 'COM_JNEGOCIO_SELECT_STATUS' ) .' -' );
		//Jinx : Why is this used ?
		//$state[] = JHTML::_('select.option',  '*', JText::_( 'Any' ) );
		$state[] = JHTML::_('select.option',  'P', JText::_( $published ) );
		$state[] = JHTML::_('select.option',  'U', JText::_( $unpublished ) );

		if ($archived) {
			$state[] = JHTML::_('select.option',  'A', JText::_( $archived ) );
		}

		if ($trashed) {
			$state[] = JHTML::_('select.option',  'T', JText::_( $trashed ) );
		}

		return self::genericlist($state, 'filter_state', 'class="inputbox necFilter" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );
	}
}