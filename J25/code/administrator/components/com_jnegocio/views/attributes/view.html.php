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

class jNegocioViewAttributes extends jFWView {

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

        $array_categories = array();
        if ($row->$idkey >= 1) {
            $array_categories = explode(",", $row->attribute_cats);
        }

        if (count($array_categories) <= 0) {
            $array_categories[] = -1;
        }

        $this->assignRef('categories_select', $array_categories);
    }

    /**
     * Basic commands for displaying a list
     *
     * @param $tpl
     * @return unknown_type
     */
    function _default($tpl = '') {
        
        jFWBase::load( 'jFWArrays', 'library.arrays' );
        
        $user = &JFactory::getUser();
        $tmpl = JRequest::getCmd('tmpl', 'index');
        $model = $this->getModel();
        $layout = $this->getLayout();

        parent::_default($tpl);
        
        $table = $model->getTable();
        $rows = $model->getData();
        $idkey = $table->getKeyName();

        $modelAttrValues = JModel::getInstance('AttributesValues', 'jNegocioModel');

        foreach ($rows as $key => $value) {
            $modelAttrValues->emptyState();
            $modelAttrValues->setState('limit', 0);
            $modelAttrValues->setState('filter_attrid', $rows[$key]->$idkey);
            $rows[$key]->values = jFWArrays::splitValuesArrayObject($modelAttrValues->getData(), 'name');
            $rows[$key]->count_values = count($rows[$key]->values);
        }

//        $this->assignRef('action', $action);
    }

}