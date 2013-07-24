<?php
/**
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$options = array('site' => 'site', 'type' => 'components', 'ext' => 'com_jnegocio');
jFWBase::load('jFWFrontView', 'views._base', $options);

$options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
jFWBase::load('HelperSelect', 'helpers.select', $options);
jFWBase::load('HelperProduct', 'helpers.product', $options);
jFWBase::load('HelperCurrency', 'helpers.currency', $options);

class jNegocioViewProduct extends jFWFrontView {

    /**
     * 
     * @param $tpl
     * @return unknown_type
     */
    function getLayoutVars($tpl = null) {
        $layout = $this->getLayout();
        switch (strtolower($layout)) {
            case "default":
            default:
                $this->_view($tpl);
                break;
        }
    }

    /**
     * Basic methods for displaying an item from a list
     * @param $tpl
     * @return unknown_type
     */
    function _view($tpl = '', $clearstate = true) {
        $user   = & JFactory::getUser();
        $tmpl   = JRequest::getCmd('tmpl', 'index');
        $app    = JFactory::getApplication();
        
        $this->params = $app->getParams();
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                
        $this->assignRef( 'params', $this->params);
	$this->assignRef( 'user',   $user);
	$this->assignRef( 'tmpl',   $tmpl);
        
        // $this->_prepareDocument();
    }
}