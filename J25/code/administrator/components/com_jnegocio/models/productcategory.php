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

jFWBase::load('jFWModel', 'models._base');

class jNegocioModelProductcategory extends jFWModel {

    function getTable() {
        $table = JTable::getInstance('Productcategory', jFWBase::getTablePrefix());
        return $table;
    }

    function _buildQueryWhere(&$query) {
        $filter_id = $this->getState('filter_id');
        $filter_productid = $this->getState('filter_productid');
        $filter_categoryid = $this->getState('filter_geozoneid');

        if ($filter_id) {
            $query->where('tbl.productcategory_id =' . $filter_id, 'AND');
        }
        
        if ($filter_productid) {
            $query->where('tbl.product_id =' . $filter_productid, 'AND');
        }

        if ($filter_categoryid) {
            $query->where('tbl.category_id =' . $filter_categoryid, 'AND');
        }
    }
}