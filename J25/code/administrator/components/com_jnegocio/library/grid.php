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

require_once( JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'grid.php' );

Class jFWGrid extends JHTMLGrid
{	
	
	public static function jFWpublished($value, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= $value ? $imgY : $imgX;
		$task 	= $value ? 'unpublish' : 'publish';
		$alt 	= $value ? JText::_( 'COM_JNEGOCIO_OPTION_PUBLISHED' ) : JText::_( 'COM_JNEGOCIO_OPTION_UNPUBLISHED' );
		$action = $value ? JText::_( 'COM_JNEGOCIO_UNPUBLISHED_ITEM' ) : JText::_( 'COM_JNEGOCIO_PUBLISHED_ITEM' );

		$href = '
		<a href="javascript:void(0);" class="hasTip" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'::' . $alt .'">
		<img src="'.jFWBase::getURL('icons'). '16/'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}
	
	public static function jFWenable( $enable, $i, $prefix = '', $imgY = 'tick.png', $imgX = 'publish_x.png' )
	{
		$img 	= $enable ? $imgY : $imgX;
		$task 	= $enable ? 'disable' : 'enable';
		$alt 	= $enable ? JText::_( 'COM_JNEGOCIO_OPTION_ENABLED' ) : JText::_( 'COM_JNEGOCIO_OPTION_DISABLED' );
		$action = $enable ? JText::_( 'COM_JNEGOCIO_OPTION_DISABLE_ITEM' ) : JText::_( 'COM_JNEGOCIO_OPTION_ENABLE_ITEM' );
		
        $href = '
        <a href="javascript:void(0);" class="hasTip" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
        <img src="' . jFWBase::getURL('icons'). '16/'. $img .'" border="0" alt="'. $alt .'" />
        </a>';
        
		return $href;
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
		$image = JHTML::_('image.administrator',  $imgsrc, '/administrator/images/' , NULL, NULL, $imgalt );
		
		return '<img src="'.jFWBase::getURL('icons'). '16/' . ($bool ? $true_img : $false_img) .'" border="0" alt="'. JText::_($bool ? $true_text : $false_text) .'" />';
		// return $image;
	}
	
	function jFWorder( $rows, $image='filesave.png', $task="saveorder" )
	{
		$image = '<img src="'.jFWBase::getURL('icons'). '16/' . $image .'" border="0" alt="'. JText::_( 'COM_JNEGOCIO_SAVE_ORDER' ) .'" />'; 
		$href = '<a class="necsaveorder" href="javascript:saveorder('.(count( $rows )-1).', \''.$task.'\')" title="'.JText::_( 'COM_JNEGOCIO_SAVE_ORDER' ).'">'.$image.'</a>';
		
		return $href;		
	}
	
	/**
	 * @param	string	The link title
	 * @param	string	The order field for the column
	 * @param	string	The current direction
	 * @param	string	The selected ordering
	 * @param	string	An optional task override
	 */
	function jFWsort( $title, $order, $direction = 'asc', $selected = 0, $task=NULL, $new_direction = 'asc')
	{
		$direction	= strtolower( $direction );
		$icon 		= array( 'sort_asc.png', 'sort_desc.png' );
		$index		= intval( $direction == 'desc' );

		if ($order != $selected) {
			$direction = $new_direction;
		} else {
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}		
		
		$html = '<a href="javascript:tableOrdering(\''.$order.'\',\''.$direction.'\',\''.$task.'\');" title="'.JText::_( 'JGLOBAL_CLICK_TO_SORT_THIS_COLUMN' ).'">';
		$html .= JText::_( $title );
		if ($order == $selected ) {
			$html .= '<img src="'.jFWBase::getURL('icons'). '16/' . $icon[$index] .'" border="0" alt="'. JText::_( 'JGLOBAL_CLICK_TO_SORT_THIS_COLUMN' ) .'" />';
		}
		$html .= '</a>';
		return $html;
	}
}