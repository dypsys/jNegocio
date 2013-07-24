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

jFWBase::load('jFWModelCRUD', 'models._crud');

class jNegocioModelGeozonerelations extends jFWModelCRUD {

    function _buildQueryWhere(&$query) {
        $filter = $this->getState('search');
        $filter_id = $this->getState('filter_id');
        $filter_zoneid = $this->getState('filter_zoneid');
        $filter_geozoneid = $this->getState('filter_geozoneid');

        if ($filter) {
            $key = '%' . $this->_db->getEscaped(trim(strtolower($filter))) . '%';
            $where = array();
            $where[] = '(LOWER(tbl.geozonerelation_id) LIKE ' . $this->_db->Quote($key) . ')';
            $strwhere = '(' . implode(' OR ', $where) . ')';

            $query->where($strwhere, 'AND');
        }

        if ($filter_id) {
            $query->where('tbl.geozonerelation_id =' . $filter_id, 'AND');
        }

        if ($filter_zoneid) {
            $query->where('tbl.zone_id =' . $filter_zoneid, 'AND');
        }

        if ($filter_geozoneid) {
            $query->where('tbl.geozone_id =' . $filter_geozoneid, 'AND');
        }
    }

    /**
     * Builds SELECT fields list for the query
     */
    public function _buildQueryFields(&$query) {
        parent::_buildQueryFields(&$query);
        $query->select('gz.name AS geozone_name');
        $query->select('z.name AS zone_name');
    }

    /**
     * Builds JOINS clauses for the query
     */
    function _buildQueryJoins(&$query) {
        $query->join('LEFT', '#__' . jFWBase::getTablePrefix() . 'geozones AS gz ON gz.geozone_id = tbl.geozone_id');
        $query->join('LEFT', '#__' . jFWBase::getTablePrefix() . 'zones AS z ON z.zone_id = tbl.zone_id');
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
            $query->order('ordering ASC');
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