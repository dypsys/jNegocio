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

// require_once( JPATH_SITE.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'select.php' );

require_once( JPATH_SITE .DIRECTORY_SEPARATOR. 'libraries' .DIRECTORY_SEPARATOR. 'cms' .DIRECTORY_SEPARATOR. 'html' .DIRECTORY_SEPARATOR. 'select.php' );

Class jFWSelect extends JHTMLSelect
{
    /**
     * Generates a yes/no radio list
     *
     * @param $selected
     * @param string $name
     * @param array $attribs
     * @param null $idtag
     * @param bool $allowAny
     * @param string $title
     * @param string $yes
     * @param string $no
     *
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
}