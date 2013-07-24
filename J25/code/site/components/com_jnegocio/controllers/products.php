<?php
/**
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2013 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// set the options array
$options = array('site' => 'site', 'type' => 'components', 'ext' => 'com_jnegocio');
jFWBase::load('jFWFrontController', 'controllers._base', $options);

class jNegocioControllerProducts extends jFWFrontController {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->set('suffix', 'products');
    }

    /**
     * Sets the model's default state based on value in the request
     * 
     * @return unknown_type
     */
    function _setModelState() {
        $state = parent::_setModelState();
        $app = JFactory::getApplication();
        $model = $this->getModel($this->get('suffix'));
        $ns = $this->getNamespace();
        $necConfig = fwConfig::getInstance();

        $products_page = $necConfig->get('productlist_products_x_page', 9 );
        
        $state['limit'] = $app->getUserStateFromRequest($ns . '.limit', 'limit', $products_page, 'int');
        $state['limitstart'] = $app->getUserStateFromRequest($ns . '.limitstart', 'limitstart', 0, 'int');
        
        $state['filter_categoryid'] = $app->getUserStateFromRequest($ns . '.filter_categoryid', 'filter_categoryid', '', '');
        $state['filter_manufacturerid'] = $app->getUserStateFromRequest($ns . '.filter_manufacturerid', 'filter_manufacturerid', '', '');
        $state['filter_groupid'] = $app->getUserStateFromRequest($ns . '.filter_groupid', 'filter_groupid', '', '');
        $state['filter_displaystyle'] = $app->getUserStateFromRequest($ns . '.filter_displaystyle', 'filter_displaystyle', '', '');
        
        if (empty($state['filter_displaystyle'])) {
            $state['filter_displaystyle'] = $necConfig->get('productlist_displaystyle','list');
        }
        
        foreach (@$state as $key => $value) {
            $model->setState($key, $value);
        }

        return $state;
    }    
}