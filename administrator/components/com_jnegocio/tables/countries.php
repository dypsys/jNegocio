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

jFWBase::load( 'jFWTable', 'tables._base' );

class neg_Countries extends jFWTable
{
    /**
     * @param database A database connector object
     */
    function neg_Countries( &$db )
    {
        $tbl_key 	= 'country_id';
        $tbl_suffix = 'countries';
        $this->set( '_suffix', $tbl_suffix );
        $name 		= jFWBase::getTablePrefix();

        parent::__construct( "#__{$name}{$tbl_suffix}", $tbl_key, $db );
    }
}