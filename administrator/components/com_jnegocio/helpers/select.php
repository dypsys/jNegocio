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

jFWBase::load('jFWSelect', 'library.select');

class HelperSelect extends jFWSelect
{
    /**
     * Generate list for select position
     *
     */
    public static function symbolalign($selected, $name = 'currency_symbol_align', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false) {
        $options = array();
        if ($allowAny) {
            $options[] = self::option('0', '- ' . JText::_('COM_JNEGOCIO_SELECT') . ' -');
        }
        $options[] = self::option('1', '- ' . JText::_('COM_JNEGOCIO_OPTION_POSITION_LEFT') . ' -');
        $options[] = self::option('2', '- ' . JText::_('COM_JNEGOCIO_OPTION_POSITION_RIGHT') . ' -');

        return self::genericlist($options, $name, $attribs, 'value', 'text', $selected, $idtag);
    }
}