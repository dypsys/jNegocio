<?php
/**
 * @version	    3.0.1
 * @package	    Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_SITE .DIRECTORY_SEPARATOR. 'libraries' .DIRECTORY_SEPARATOR. 'cms' .DIRECTORY_SEPARATOR. 'html' .DIRECTORY_SEPARATOR. 'jgrid.php' );

Class jFWGrid extends JHtmlJGrid
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
}