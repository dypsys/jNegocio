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

class jNegocioViewAttributesValues extends jFWView {

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
        parent::_form($tpl, false);
    }

    /**
     * Displays text as the title of the page
     * 
     * @param $text
     * @return unknown_type
     */
    function displayTitle($text = '', $classTitle = null) {

        $html_menu = '';
        if (!JRequest::getInt('hidemainmenu') && empty($this->hidemenu)) {
            // $this->displaySubMenu();
        }
        $model = $this->getModel();

        $state = $model->getState();
        $attrid = @$state->filter_attrid;
        
        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        jFWBase::load('HelperDisplayElement', 'helpers.displayelement', $options);
        jFWBase::load('HelperLanguages', 'helpers.languages', $options);

        $_lang = &HelperLanguages::getlang();
        $name_lang = $_lang->getField('name');
        $attr_item = HelperDisplayElement::getAttrbyId($attrid);
        if ($attr_item != false) {
            $attr_name = $attr_item->$name_lang;
            $title = JText::sprintf('COM_JNEGOCIO_ATTRIBUTESVALUES_TITLE', $attr_name);
        } else {
            $title = JText::_('COM_JNEGOCIO_ATTRIBUTESVALUES');
        }

        if ($classTitle === null) {
            $classTitle = 'COM_JNEGOCIO_' . $this->_name;
        }

        $html_title = $title;
        JToolBarHelper::title($html_title, $classTitle);
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

        // set the model state
        $this->assign('state', $model->getState());

        if (empty($this->hidemenu)) {
            // add toolbar buttons
            $this->_defaultToolbar();
        }

        $action = 'index.php';
        $this->displayTitle($this->get('title'));
        // page-navigation
        $this->assignRef('pageNav', $model->getPagination());

        // list of items
        $this->assignRef('action', $action);
        $this->assignRef('rows', $model->getData());
        $this->assignRef('user', $user);
        $this->assignRef('tmpl', $tmpl);
        $this->assignRef('layout', $layout);
        $this->assignRef('idkey', $model->getTable()->getKeyName());
    }

    /**
     * The default toolbar for a list
     * @return unknown_type
     */
    function _defaultToolbar() {
        JToolBarHelper::addnew();
        JToolBarHelper::spacer();
        JToolBarHelper::deleteList();
        JToolBarHelper::spacer();
        JToolBarHelper::editList();
        JToolBarHelper::spacer();

        $tmpl = JRequest::getCmd('tmpl');

        $PC = JText::_('COM_JNEGOCIO_ATTRIBUTES_PANEL');
        JToolBarHelper::divider();
        JToolBarHelper::custom('backattr', 'back.png', 'back.png', $PC, false);
    }

}