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

jFWBase::load( 'jFWView', 'views._base' );

class jNegocioViewCountries extends jFWView
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
                JFactory::getApplication()->input->set('hidemainmenu', true);
                $this->_form($tpl);
                break;

            case "view":
            case "default":
            default:
                $this->_default($tpl);
                break;
        }
    }
}