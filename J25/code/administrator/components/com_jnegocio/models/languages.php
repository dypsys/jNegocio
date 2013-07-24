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

jFWBase::load( 'jFWModelCRUD', 'models._crud' );

class jNegocioModelLanguages extends jFWModelCRUD {

    function _buildQueryWhere(&$query) {
        $filter = $this->getState('search');
        $filter_state = $this->getState('filter_state');
        $filter_id = $this->getState('filter_id');

        if ($filter_state) {
            if ($filter_state == 'P') {
                $where = '1';
            } else if ($filter_state == 'U') {
                $where = '0';
            }
            $query->where('tbl.published', '=', $where, 'AND');
        }

        if ($filter) {
            $key = '%' . $this->_db->getEscaped(trim(strtolower($filter))) . '%';
            $where = array();
            $where[] = '(LOWER(tbl.id) LIKE ' . $this->_db->Quote($key) . ')';
            $where[] = '(LOWER(tbl.language) LIKE ' . $this->_db->Quote($key) . ')';
            $strwhere = '(' . implode(' OR ', $where) . ')';

            $query->where($strwhere, NULL, NULL, 'AND');
        }

        if ($filter_id) {
            $query->where('tbl.id', '=', $filter_id, 'AND');
        }
    }

}