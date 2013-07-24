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

jFWBase::load('jFWView', 'views._base');

class jNegocioViewProducts extends jFWView {

    /**
     * Gets layout vars for the view
     * 
     * @param $tpl
     * @return unknown_type
     */
    function getLayoutVars($tpl = null) {
        $layout = $this->getLayout();
        switch (strtolower($layout)) {
            case "form":
                JRequest::setVar('hidemainmenu', '1');
                $this->_form($tpl);
                break;

            case "view":
            case "default":
            default:
                $this->_default($tpl);
                break;
        }
    }

    function _defaultToolbar() {
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();
        JToolBarHelper::divider();
        parent::_defaultToolbar();
    }

    /**
     * Basic methods for displaying an item from a list
     * @param $tpl
     * @return unknown_type
     */
    function _form($tpl = '', $clearstate = true) {
        parent::_form($tpl, $clearstate);

        $model = $this->getModel();

        $row = $model->getItem($clearstate);
        $table = $model->getTable();
        $idkey = $table->getKeyName();
        $necConfig = fwConfig::getInstance();
        
        $categories_select = null;
	$images = null;
	$images_pageNav = null;
        
        $attributes = null;
        $prdattributes = null;
        // $attributes_pageNav = null;
        if ($row->$idkey>=1) {
            JFilterOutput::objectHTMLSafe( $row );
            
            $categories_select = $table->getCategories($row->$idkey);
            
            $modelimages = JModel::getInstance( 'Productimages', 'jNegocioModel' );
            $modelimages->emptyState();
            $modelimages->setState( 'filter_productid', $row->$idkey );
            $modelimages->setState( 'filter_order', 'tbl.ordering' );
            $modelimages->setState( 'limit', 0);
            $images = $modelimages->getData();
            // page-navigation
            $images_pageNav = $modelimages->getPagination();
            
            $modelprices = JModel::getInstance( 'Productprices', 'jNegocioModel' );
            $modelprices->emptyState();
            $modelprices->setState( 'filter_productid', $row->$idkey );
            $modelprices->setState( 'filter_order', 'tbl.group_id ASC, tbl.price_quantity_start' );
            $modelprices->setState( 'filter_order_Dir', 'ASC' );
            $modelprices->setState( 'limit', 0);
            $prices = $modelprices->getData();
            
            $modelprdattributes = JModel::getInstance( 'Productattributes', 'jNegocioModel' );
            $modelprdattributes->emptyState();
            $modelprdattributes->setState( 'filter_productid', $row->$idkey );
            $modelprdattributes->setState( 'filter_order', 'tbl.ordering' );
            $modelprdattributes->setState( 'limit', 0);
            $prdattributes = $modelprdattributes->getData();
            // page-navigation
            // $attributes_pageNav = $modelimages->getPagination();      
            
            // echo var_dump($categories_select)."<hr/>";
            $modelattributes = JModel::getInstance( 'Attributes', 'jNegocioModel' );
            $modelattributes->emptyState();
            $modelattributes->setState( 'filter_categoryid', $categories_select );
            $modelattributes->setState( 'filter_order', 'tbl.ordering' );
            $modelattributes->setState( 'limit', 0);
            $attributes = $modelattributes->getData();
        }
        
        $modelcurrencies = JModel::getInstance( 'Currencies', 'jNegocioModel' );
        $modelcurrencies->emptyState();
        $modelcurrencies->setState( 'filter_order', 'tbl.ordering' );
        $modelcurrencies->setState( 'limit', 0);
        $currencies = $modelcurrencies->getData();
        
        $modeltaxrates = JModel::getInstance( 'Taxrates', 'jNegocioModel' );
        $modeltaxrates->emptyState();
        $modeltaxrates->setState( 'filter_zoneid', $necConfig->get('company_zone', 1) );
        $modeltaxrates->setState( 'filter_order', 'tbl.typetax_id' );
        $modeltaxrates->setState( 'limit', 0);
        $taxrates = $modeltaxrates->getData();
        
        $modelusergroups = JModel::getInstance( 'Usergroups', 'jNegocioModel' );
        $modelusergroups->emptyState();
        $modelusergroups->setState( 'filter_order', 'tbl.usergroup_id' );
        $modelusergroups->setState( 'limit', 0);
        $usergroups = $modelusergroups->getData();
        
        // echo "categories_select:".var_dump($categories_select)."<br/>";
        $this->assignRef( 'categories_select'   , $categories_select);
	$this->assignRef( 'image_rows'          , $images);
	$this->assignRef( 'image_pageNav'	, $images_pageNav);        
        $this->assignRef( 'prices_rows'         , $prices);
        $this->assignRef( 'attributes_rows'     , $prdattributes);
        $this->assignRef( 'attributes'          , $attributes);
        $this->assignRef( 'currecies'           , $currencies);
        $this->assignRef( 'taxrates'            , $taxrates);
        $this->assignRef( 'usergroups'          , $usergroups);
    }
}