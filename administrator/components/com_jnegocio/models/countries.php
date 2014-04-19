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

jFWBase::load('jFWModelCRUD', 'models._crud');

class jNegocioModelCountries extends jFWModelCRUD {

    function _buildQueryWhere($query)
    {
        $filter = $this->getState()->get('search', '');

        if ($filter)
        {
            $key	= '%'.$this->getDbo()->escape( trim( strtolower( $filter ) ) ).'%';
            $where = array();
            $where[] = '(LOWER(tbl.country_id) LIKE '.$this->getDbo()->Quote($key).')';
            $where[] = '(LOWER(tbl.`'.$this->_lang->getField('name').'`) LIKE '.$this->getDbo()->Quote($key).')';
            $where[] = '(LOWER(tbl.isocode_2) LIKE '.$this->getDbo()->Quote($key).')';
            $where[] = '(LOWER(tbl.isocode_3) LIKE '.$this->getDbo()->Quote($key).')';
            $strwhere = '('.implode(' OR ', $where).')';

            $query->where( $strwhere, 'AND' );
        }
    }

    /**
     * Builds SELECT fields list for the query
     */
    public function _buildQueryFields($query)
    {
        parent::_buildQueryFields($query);
        $query->select( 'tbl.`'.$this->_lang->getField('name') . '` AS name' );
        // $query->select( 'tbl.`name_en-GB` AS name' );
    }

    /**
     * Builds a generic ORDER BY clause based on the model's state
     */
    public function _buildQueryOrder($query)
    {
        $order      = $this->getDbo()->escape( $this->getState()->get('filter_order') );
        $direction  = $this->getDbo()->escape( strtoupper( $this->getState()->get('filter_order_Dir') ) );

        if (strtolower($order)=='tbl.name' || strtolower($order)=='name')
        {
            $order = 'tbl.`'.$this->_lang->getField('name').'`';
        }

        if ($order == 'tbl.ordering')
        {
            $query->order('ordering ASC');
        } else {
            if ($order)
            {
                $query->order($order .' '. $direction);
            }

            if ($this->getTable()->checkFieldinColumns('ordering'))
            {
                $query->order('ordering ASC');
            }
        }
    }
}