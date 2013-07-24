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

class jNegocioModelCategories extends jFWModelCRUD {

    function _buildQueryWhere(&$query) {
        $filter = $this->getState('search');
        $filter_state = $this->getState('filter_state');
        $filter_id = $this->getState('filter_id');
        $parentid = $this->getState('filter_parentid');
        $level = $this->getState('filter_level');

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
            $where[] = '(LOWER(tbl.category_id) LIKE ' . $this->_db->Quote($key) . ')';
            $where[] = '(LOWER(tbl.`' . $this->_lang->getField('name') . '`) LIKE ' . $this->_db->Quote($key) . ')';
            $strwhere = '(' . implode(' OR ', $where) . ')';

            $query->where($strwhere, 'AND');
        }

        if ($filter_id) {
            $query->where('tbl.category_id =' . $filter_id, 'AND');
        }
        $query->where('tbl.isroot = 0', 'AND');
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
        $direction = $this->_db->getEscaped(strtoupper($this->getState('filter_order_Dir', 'ASC')));
        $strOrder = array();

        if (strtolower($order) == 'tbl.name' || strtolower($order) == 'name') {
            $order = 'tbl.`' . $this->_lang->getField('name') . '`';
        }

//        if ($order != 'tbl.lft') {
//        	$strOrder[] = 'tbl.lft'.' '. $direction;	
//        }

        if ($order == 'tbl.ordering') {
            $query->order('tbl.lft ' . $direction . ', ordering ' . $direction);
//	       	$strOrder[] = 'ordering ASC';
        } else {
            if ($order) {
                $query->order($order . ' ' . $direction);
//            	$strOrder[] = $order .' '. $direction;
            }

            if (in_array('ordering', $this->getTable()->getColumns())) {
                $query->order('ordering ASC');
//    			$strOrder[] = 'ordering ASC';
            }
        }

//       	echo "Orden:".implode(',', $strOrder);
//       	$query->order( implode(',', $strOrder) );
    }

    /**
     * Method to store the category
     *
     * @access	public
     * @return	boolean	True on success
     * @since	1.5
     */
    function store($data) {
        $user = & JFactory::getUser();
        $nullDate = $this->_db->getNullDate();
        $config = & JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');

        $table = $this->getTable();
        $idkey = $table->getKeyName();
        $isNew = true;
        $pk = (!empty($data[$idkey])) ? $data[$idkey] : $this->getId();

        // Load the row if saving an existing category.
        if ($pk > 0) {
            $table->load($pk);
            $isNew = false;
        }

        // Set the new parent id if parent id not matched OR while New/Save as Copy .
        if ($table->parent_id != $data['parent_id'] || $data[$idkey] == 0) {
            $table->setLocation($data['parent_id'], 'last-child');
        }

        // if (isset($table->ordering)) {
        // if (in_array('ordering', $this->getTable()->getColumns())) {
        if ($this->getTable()->checkFieldinColumns('ordering')) {
            if (!$table->$idkey) {
                $table->ordering = $table->getNextOrder();
            }
        }

        // Bind the data.
        if (!$table->bind($data)) {
            $this->setError($table->getError());
            return false;
        }

        // Are we saving from an item edit?
        if (!$table->$idkey) {
            // Es nuevo
            $date = new JDate($row->created, $tzoffset);
            $table->modified = $nullDate;
            $table->modified_by = '';

            $table->created = $date->toMySQL();
            $table->created_by = $user->get('id');
        } else {
            // es Modificacion
            $date = new JDate($row->modified, $tzoffset);
            $table->modified = $date->toMySQL();
            $table->modified_by = $user->get('id');
        }

        // Check the data.
        if (!$table->check()) {
            $this->setError($table->getError());
            return false;
        }

        // Store the data.
        if (!$table->store()) {
            $this->setError($table->getError());
            return false;
        }

        // Rebuild the path for the category:
        if (!$table->rebuildPath($table->$idkey)) {
            $this->setError($table->getError());
            return false;
        }

        // Rebuild the paths of the category's children:
        if (!$table->rebuild($table->$idkey, $table->lft, $table->level, $table->path)) {
            $this->setError($table->getError());
            return false;
        }

        return $table->$idkey;
    }

    public function saveorder($idArray = null, $lft_array = null) {
        // Get an instance of the table object.
        $table = $this->getTable();

        if (!$table->saveorder($idArray, $lft_array)) {
            $this->setError($table->getError());
            return false;
        }

        // Clear the cache
        $this->cleanCache();

        return true;
    }

}