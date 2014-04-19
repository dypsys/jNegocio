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

class jFWHelperBase
{
    protected $app;

    /**
     * Constructor
     */
    function __construct() {
        // parent::__construct();
        $this->app = isset($app) ? $app : JFactory::getApplication();
    }

    /**
     * Get the application object.
     *
     * @return  JApplicationBase  The application object.
     *
     * @since   12.1
     */
    public function getApplication()
    {
        return $this->app;
    }
}