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

$options = array('site' => 'site', 'type' => 'components', 'ext' => 'com_jnegocio');
jFWBase::load('jFWFrontView', 'views._base', $options);

$options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
jFWBase::load('HelperSelect', 'helpers.select', $options);
jFWBase::load('HelperProduct', 'helpers.product', $options);
jFWBase::load('HelperCurrency', 'helpers.currency', $options);

class jNegocioViewProducts extends jFWFrontView {

    /**
     * 
     * @param $tpl
     * @return unknown_type
     */
    function getLayoutVars($tpl = null) {
        $layout = $this->getLayout();
        switch (strtolower($layout)) {
            case "category":
            case "default":
            default:
                $this->_default($tpl);
                break;
        }
    }

    /**
     * Basic commands for displaying a list
     *
     * @param $tpl
     * @return unknown_type
     */
    function _default($tpl = '') {
        $user = & JFactory::getUser();
        $tmpl = JRequest::getCmd('tmpl', 'index');
        $model = $this->getModel();
        $layout = $this->getLayout();
        $app = JFactory::getApplication();

        $this->params = $app->getParams();
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // set the model state
        $this->assign('state', $model->getState());

        $rows = $model->getData();
        if (count($rows)) {
            foreach ( $rows as $key=>$row ) {
                
                $pp_price           = (float)$row->p_price;
                $pp_priceincltax    = (float)$row->p_priceincltax;
                $pp_discount        = (float)$row->p_discount;                    
                $taxrate            = HelperProduct::gettaxrate( @$row );
            
                if ($this->necConfig->get('work_pricewithtax',1) == 1) {
                    $pp_bruto       = $pp_priceincltax / ((100 + (float)$taxrate )/100); 
                    $pp_totaldto    = ($pp_bruto * $pp_discount)/100;
                    $pp_neto        = $pp_bruto - $pp_totaldto;
                    $pp_total       = $pp_neto * ((100 + (float)$taxrate )/100);
                    $pp_ratetotal   = $pp_total - $pp_neto;
                } else {
                    $pp_bruto       = $pp_price;
                    $pp_totaldto    = ($pp_bruto * $pp_discount)/100;
                    $pp_neto        = $pp_bruto - $pp_totaldto;
                    $pp_total       = $pp_neto * ((100 + (float)$taxrate )/100);
                    $pp_ratetotal   = $pp_total - $pp_neto;
                }
                
                $rows[$key]->pp_bruto = $pp_bruto;
                $rows[$key]->pp_totaldto = $pp_totaldto;
                $rows[$key]->pp_neto = $pp_neto;
                $rows[$key]->pp_total = $pp_total;
                $rows[$key]->pp_ratetotal = $pp_ratetotal;
            }
        }
        // $action = 'index.php?option='.jFWBase::getComponentName().'&controller='.$this->_name.'&view='.$this->_name;
        // page-navigation
        $this->assignRef('pageNav', $model->getPagination());

        // list of items
        $this->assignRef('params', $this->params);
        $this->assignRef('rows', $rows);

        $this->assignRef('user', $user);
        $this->assignRef('tmpl', $tmpl);
        $this->assignRef('layout', $layout);
        $this->assignRef('idkey', $model->getTable()->getKeyName());

        // form
        $form = array();
        $view = strtolower(JRequest::getVar('view'));
        $form['action'] = 'index.php?option=' . jFWBase::getComponentName() . '&view=' . $this->_name . '&layout=' . $layout;
        $form['validate'] = "<input type='hidden' name='" . JUtility::getToken() . "' value='1' />";
        $this->assign('form', $form);

        $this->_prepareDocument();
    }    
}