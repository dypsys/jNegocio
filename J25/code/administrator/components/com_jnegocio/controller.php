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

// Require the base controller
jFWBase::load('jFWControllerCRUD', 'controllers._crud');

/**
 * Base class for a Controller
 * 
 * @abstract
 * @package	Joomla
 * @since	1.5
 */
class jNegocioController extends jFWControllerCRUD {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
    }

}