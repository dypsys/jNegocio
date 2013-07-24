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

class jNegocioControllerDashboard extends jFWFrontController {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->set('suffix', 'dashboard');
    }

}