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

class jNegocioModelProductattributes extends jFWModelCRUD {
    
    function getTable() {
        $table = JTable::getInstance('Productattributes', jFWBase::getTablePrefix());
        return $table;
    }
    
    function _buildQueryWhere(&$query) {
        $filter_id          = $this->getState('filter_id');
        $filter_productid   = $this->getState('filter_productid');
        $filter_attributeid = $this->getState('filter_attributeid');

        if ($filter_id) {
            $query->where('tbl.productattribute_id =' . $filter_id, 'AND');
        }

        if ($filter_productid) {
            $query->where('tbl.product_id =' . $filter_productid, 'AND');
        }
        
        if ($filter_attributeid) {
            $query->where('tbl.attribute_id =' . $filter_attributeid, 'AND');
        }
    }
    
    /**
     * Builds a generic ORDER BY clause based on the model's state
     */
    public function _buildQueryOrder(&$query) {
        $db = $this->getDbo();
        $order = $db->getEscaped($this->getState('filter_order'));
        $direction = $db->getEscaped(strtoupper($this->getState('filter_order_Dir')));

        if ($order == 'tbl.ordering') {
            $query->order('tbl.product_id' . ' ' . $direction);
            $query->order('tbl.ordering' . ' ' . $direction);
        } else {
            if ($order) {
                $query->order($order . ' ' . $direction);
            }

            $query->order('tbl.product_id' . ' ' . $direction);
            if (in_array('ordering', $this->getTable()->getColumns())) {
                $query->order('ordering ASC');
            }
        }
    }
}