<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license		Comercial License
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jFWBase::load('jFWModelCRUD', 'models._crud');

class jNegocioModelTaxrates extends jFWModelCRUD {

    function _buildQueryWhere(&$query) {
        $filter = $this->getState('search');
        $filter_state = $this->getState('filter_state');
        $filter_id = $this->getState('filter_id');
        $filter_geozoneid = $this->getState('filter_geozoneid');
        $filter_typetaxid = $this->getState('filter_typetaxid');
        $filter_zoneid = $this->getState('filter_zoneid');

        if ($filter_state) {
            if ($filter_state == 'P') {
                $where = '1';
            } else if ($filter_state == 'U') {
                $where = '0';
            }
            $query->where('tbl.published =' . $where, 'AND');
        }

        if ($filter) {
            $key = '%' . $this->_db->getEscaped(trim(strtolower($filter))) . '%';
            $where = array();
            $where[] = '(LOWER(tbl.taxrate_id) LIKE ' . $this->_db->Quote($key) . ')';
            $where[] = '(LOWER(tbl.name) LIKE ' . $this->_db->Quote($key) . ')';
            $strwhere = '(' . implode(' OR ', $where) . ')';

            $query->where($strwhere, 'AND');
        }

        if ($filter_geozoneid) {
            $query->where('tbl.geozone_id =' . $filter_geozoneid, 'AND');
        }

        if ($filter_typetaxid) {
            $query->where('tbl.typetax_id =' . $filter_typetaxid, 'AND');
        }
        
        if ($filter_zoneid) {
            $query->where('zr.zone_id =' . $filter_zoneid, 'AND');
        }

        if ($filter_id) {
            $query->where('tbl.taxrate_id =' . $filter_id, 'AND');
        }
    }

    /**
     * Builds SELECT fields list for the query
     */
    public function _buildQueryFields(&$query) {
        parent::_buildQueryFields(&$query);
        $query->select('gz.name AS geozone_name');
        $query->select('tt.name AS typetax_name');
    }

    /**
     * Builds JOINS clauses for the query
     */
    function _buildQueryJoins(&$query) {
        $query->join('LEFT', '#__' . jFWBase::getTablePrefix() . 'typetaxes AS tt ON tt.typetax_id = tbl.typetax_id');
        $query->join('LEFT', '#__' . jFWBase::getTablePrefix() . 'geozones AS gz ON gz.geozone_id = tbl.geozone_id');
        
        $filter_zoneid = $this->getState('filter_zoneid');
        if ($filter_zoneid) {
            $query->join('LEFT', '#__' . jFWBase::getTablePrefix() . 'geozonerelations AS zr ON zr.geozone_id=tbl.geozone_id');
        }
    }

    /**
     * Builds a generic ORDER BY clause based on the model's state
     */
    public function _buildQueryOrder(&$query) {
        $order = $this->_db->getEscaped($this->getState('filter_order'));
        $direction = $this->_db->getEscaped(strtoupper($this->getState('filter_order_Dir')));

//		if (strtolower($order)=='tbl.name' || strtolower($order)=='name') {
//			$order = 'tbl.`'.$this->_lang->getField('name').'`';
//		}

        if ($order == 'tbl.ordering') {
            $query->order('tbl.ordering ASC');
        } else {
            if ($order) {
                $query->order($order . ' ' . $direction);
            }

            if (in_array('ordering', $this->getTable()->getColumns())) {
                $query->order('ordering ASC');
            }
        }
    }
}