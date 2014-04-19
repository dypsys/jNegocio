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

// Require the base controller
jFWBase::load( 'jFWController', 'controllers._base' );

class jNegocioControllerDashboard extends jFWController
{
    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
        // $this->set('suffix', 'dashboard');
        // $this->setSuffix('dashboard');
    }
}