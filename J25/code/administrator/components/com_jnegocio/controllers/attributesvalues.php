<?php

/**
 * @version     $Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

// Require the base controller
jFWBase::load('jFWControllerCRUD', 'controllers._crud');

class jNegocioControllerAttributesValues extends jFWControllerCRUD {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->set('suffix', 'attributesvalues');

        // Register Extra task
        $this->registerTask('backattr', 'backattr');
    }

    /**
     * Logic para volver al panel principal
     *
     * @access public
     * @return void
     */
    function backattr() {
        $link = 'index.php?option=' . jFWBase::getComponentName() . '&view=attributes';
        $this->setRedirect($link);
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

        $state['filter_attrid'] = $app->getUserStateFromRequest($ns . '.filter_attrid', 'filter_attrid', '', '');

//        echo "setModelState filter_attrid :".$state['filter_attrid']."<br/>";
        foreach (@$state as $key => $value) {
            $model->setState($key, $value);
        }

        return $state;
    }
}