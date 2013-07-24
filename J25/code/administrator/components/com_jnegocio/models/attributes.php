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

class jNegocioModelAttributes extends jFWModelCRUD {

    function _buildQueryWhere(&$query) {
        $filter             = $this->getState('search');
        $filter_state       = $this->getState('filter_state');
        $filter_id          = $this->getState('filter_id');
        $filter_categoryid  = $this->getState('filter_categoryid');

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
            $where[] = '(LOWER(tbl.attribute_id) LIKE ' . $this->_db->Quote($key) . ')';
            $where[] = '(LOWER(tbl.`' . $this->_lang->getField('name') . '`) LIKE ' . $this->_db->Quote($key) . ')';
            $strwhere = '(' . implode(' OR ', $where) . ')';

            $query->where($strwhere, 'AND');
        }

        if ($filter_id) {
            $query->where('tbl.attribute_id =' . $filter_id, 'AND');
        }
        
        if ( $filter_categoryid ) {
            if (is_array($filter_categoryid)) {
                $where = array();
                foreach($filter_categoryid as $cat) {
                    // echo $cat->category_id."<br/>";
                    $where[] = '(FIND_IN_SET(' .$this->_db->Quote($cat->category_id) . ',tbl.attribute_cats)!=0)';
                }
                $where[] = 'tbl.attribute_cats=-1';
                $strwhere = '(' . implode(' OR ', $where) . ')';
                $query->where($strwhere, 'AND');
            } else {
                $query->where( '(( FIND_IN_SET(' .$this->_db->Quote($filter_categoryid) . ',tbl.attribute_cats)!=0) OR (tbl.attribute_cats=-1))', 'AND' );
            }
        }
    }

    /**
     * Builds SELECT fields list for the query
     */
    public function _buildQueryFields(&$query) {
        parent::_buildQueryFields(&$query);
        $query->select('tbl.`' . $this->_lang->getField('name') . '` AS name');
        $query->select('tbl.`' . $this->_lang->getField('label') . '` AS label');
    }

    /**
     * Builds a generic ORDER BY clause based on the model's state
     */
    public function _buildQueryOrder(&$query) {
        $order = $this->_db->getEscaped($this->getState('filter_order'));
        $direction = $this->_db->getEscaped(strtoupper($this->getState('filter_order_Dir')));

        if (strtolower($order) == 'tbl.name' || strtolower($order) == 'name') {
            $order = 'tbl.`' . $this->_lang->getField('name') . '`';
        }

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