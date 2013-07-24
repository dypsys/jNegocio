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

// set the options array
$options = array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_jnegocio' );
jFWBase::load( 'jFWFrontModel', 'models._base', $options );

class jNegocioModelProducts extends jFWFrontModel 
{
    function _buildQueryWhere(&$query) {
        $filter             = $this->getState('search');
        $filter_state       = $this->getState('filter_state');
        $filter_id          = $this->getState('filter_id');
        $filter_categoryid  = $this->getState('filter_categoryid');
        $filter_manufacturerid  = $this->getState('filter_manufacturerid');
        $field_groupid      = $this->getState('filter_groupid');

//        if ($filter_state) {
//            if ($filter_state == 'P') {
                $where = '1';
//            } else if ($filter_state == 'U') {
//                $where = '0';
//            }
            $query->where('tbl.published =' . $where, 'AND');
//        }

        if ($filter) {
            $key = '%' . $this->_db->getEscaped(trim(strtolower($filter))) . '%';
            $where = array();
            $where[] = '(LOWER(tbl.product_id) LIKE ' . $this->_db->Quote($key) . ')';
            $where[] = '(LOWER(tbl.`' . $this->_lang->getField('name') . '`) LIKE ' . $this->_db->Quote($key) . ')';
            $strwhere = '(' . implode(' OR ', $where) . ')';

            $query->where($strwhere, 'AND');
        }

        if ($filter_id) {
            $query->where('tbl.product_id =' . $filter_id, 'AND');
        }
        
        if ($filter_manufacturerid) {
            $query->where('tbl.manufacturer_id =' . $filter_manufacturerid, 'AND');
        }

        if ($filter_categoryid) {
            $query->where('p2c.category_id =' . $filter_categoryid, 'AND');
        }
    }
    
    /**
     * Builds SELECT fields list for the query
     */
    public function _buildQueryFields(&$query) {
        parent::_buildQueryFields(&$query);
        $query->select('tbl.`' . $this->getLang()->getField('name') . '` AS name');
        $query->select('tbl.`' . $this->getLang()->getField('shortdesc') . '` AS shortdesc');
        $query->select('pimg.locationuri AS gl_location_uri');
	$query->select('pimg.locationurl AS gl_location_url');
        $query->select('pimg.attachment AS gl_attachment');
        
        $field_groupid  = $this->getState('filter_groupid');
        if ( empty( $field_groupid ) ) {
            $field_groupid = (int)fwConfig::getInstance()->get('default_usergroup',1);
	}
        
//        $fieldPrice = "( SELECT ";
//        if (fwConfig::getInstance()->get('work_pricewithtax',1) == 1) {
//            $fieldPrice .= "prices.product_priceincltax";
//        } else {
//            $fieldPrice .= "prices.product_price";
//        }
//        
//        $fieldPrice .= " FROM #__nec_productprices AS prices ";
//	$fieldPrice .= " WHERE ";
//	$fieldPrice .= " prices.product_id = tbl.product_id";
//	$fieldPrice .= " AND prices.group_id = '".$field_groupid."'";
//        $fieldPrice .= " ORDER BY prices.price_quantity_start ASC ";
//	$fieldPrice .= " LIMIT 1 ";
//	$fieldPrice .= " ) AS price";      
//        $query->select($fieldPrice);
        
        $query->select('prices.product_priceincltax AS p_priceincltax');
        $query->select('prices.product_price AS p_price');
        $query->select('prices.product_discount AS p_discount');
    }

    /**
     * Builds JOINS clauses for the query
     */
    function _buildQueryJoins(&$query) {
        parent::_buildQueryJoins($query);
        
        $field_groupid  = $this->getState('filter_groupid');
        if ( empty( $field_groupid ) ) {
            $field_groupid = (int)fwConfig::getInstance()->get('default_usergroup',1);
	}
        
        $filter_categoryid  = $this->getState('filter_categoryid');
        if ($filter_categoryid) {
            $query->join('LEFT', '#__' . jFWBase::getTablePrefix() . 'productcategory AS p2c ON p2c.product_id = tbl.product_id');
        }
        
        $query->join('LEFT', '#__' . jFWBase::getTablePrefix() .'productprices AS prices ON (prices.product_id = tbl.product_id) AND (prices.group_id = \''.$field_groupid. '\') AND (prices.ordering<=1)' );
        
        $query->join('LEFT', '#__' . jFWBase::getTablePrefix() .'productimages AS pimg ON (pimg.product_id = tbl.product_id) AND (pimg.ordering<=1)' );
    }
    
    /**
     * Builds a generic ORDER BY clause based on the model's state
     */
    public function _buildQueryOrder(&$query) {
        $order = $this->_db->getEscaped($this->getState('filter_order'));
        $direction = $this->_db->getEscaped(strtoupper($this->getState('filter_order_Dir')));

        if (strtolower($order) == 'tbl.name' || strtolower($order) == 'name') {
            $order = 'tbl.`' . $this->getLang()->getField('name') . '`';
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
     * Metodo para coger los tipos de los datos
     *
     * @access public
     * @return array
     */
    function getData($refresh = false) {
        if (empty($this->_data) || $refresh) {
            $query = $this->getQuery();
            // $strQuery = (string) $query;
            // echo "query:".$query."<br/>";

            $limitstart = $this->getState('limitstart', 0);
            $limit = $this->getState('limit', 0);

            $this->_db->setQuery($query, $limitstart, $limit);
            $this->_data = $this->_db->loadObjectList();
            // $this->_data = $this->_getList($query, $this->getState('limitstart', 0), $this->getState('limit', 0));
            // echo "return<hr/>";
            // var_dump($this->_data);
            // echo "limits:start:".$this->getState('limitstart') . " limit:" .$this->getState('limit')."<br/>";
        }

        return $this->_data;
    }
}