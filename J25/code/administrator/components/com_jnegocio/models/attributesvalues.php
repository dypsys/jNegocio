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

class jNegocioModelAttributesValues extends jFWModelCRUD {

    function _buildQueryWhere(&$query) {
        $filter = $this->getState('search');
        $filter_id = $this->getState('filter_id');
        $filter_attrid = $this->getState('filter_attrid');

        if ($filter) {
            $key = '%' . $this->_db->getEscaped(trim(strtolower($filter))) . '%';
            $where = array();
            $where[] = '(LOWER(tbl.value_id) LIKE ' . $this->_db->Quote($key) . ')';
            $where[] = '(LOWER(tbl.`' . $this->_lang->getField('name') . '`) LIKE ' . $this->_db->Quote($key) . ')';
            $strwhere = '(' . implode(' OR ', $where) . ')';

            $query->where($strwhere, 'AND');
        }

        if ($filter_attrid) {
            $query->where('tbl.attribute_id =' . (int) $filter_attrid, 'AND');
        }

        if ($filter_id) {
            $query->where('tbl.value_id =' . $filter_id, 'AND');
        }
    }

    /**
     * Builds SELECT fields list for the query
     */
    public function _buildQueryFields(&$query) {
        parent::_buildQueryFields(&$query);
        $query->select('tbl.`' . $this->_lang->getField('name') . '` AS name');
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

    /**
     * Method to move a category
     *
     * @access	public
     * @return	boolean	True on success
     */
    function move($direction) {
        $user = & JFactory::getUser();
        $row = $this->getTable();

        if (!$row->load($this->getId())) {
            $this->setError("NOT LOADED ITEM " . $this->_db->getErrorMsg());
            return false;
        }

        if (!$row->move($direction, ' attribute_id = ' . (int) $row->attribute_id)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        return true;
    }

    /**
     * Method to order items
     *
     * @access	public
     * @return	boolean	True on success
     */
    function saveorder($cid = array(), $order) {
        $user = & JFactory::getUser();
        $row = $this->getTable();
        $groupings = array();

        // update ordering values
        for ($i = 0; $i < count($cid); $i++) {
            $row->load((int) $cid[$i]);

            $groupings[] = (int) $row->attribute_id;

            if ($row->ordering != $order[$i]) {
                $row->ordering = $order[$i];
                if (!$row->store()) {
                    $this->setError($this->_db->getErrorMsg());
                    return true;
                }
            }
        }

        $groupings = array_unique($groupings);
        foreach ($groupings as $group) {
            // list($doctypeid, $fieldtab, $fieldcolumn, $fieldset) = split(":", $group);
            $row->reorder('attribute_id = ' . (int) $group);
            // 'idfieldtab = '.(int) $group);
        }
        return false;
    }

    /**
     * Method to store the category
     *
     * @access	public
     * @return	boolean	True on success
     * @since	1.5
     */
    function store($data) {
        jimport('joomla.utilities.date');

        $user = & JFactory::getUser();
        $nullDate = $this->_db->getNullDate();
        $config = & JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');

        $idtable = parent::store($data);

        if ($idtable) {
            $row = $this->getTable();
            $row->load((int) $idtable);
            $idkey = $row->getKeyName();

            // Check the article and update item order
            $row->checkin();
            $row->reorder('attribute_id = ' . (int) $row->attribute_id);
        }

        return $idtable;
    }

}