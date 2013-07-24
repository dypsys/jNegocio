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

jimport('joomla.filter.filteroutput');
jimport('joomla.application.component.view');

jFWBase::load('jFWHelperBase', 'helpers._base');

class jFWFrontView extends JView {

    public $necConfig = null;

    /**
     * Displays a layout file 
     * 
     * @param unknown_type $tpl
     * @return unknown_type
     */
    function display($tpl = null) {
        // jFWBase::load('HelperRoute', 'helpers.route');
        //initialise variables
        $user = & JFactory::getUser();
        $db = & JFactory::getDBO();
        $document = & JFactory::getDocument();
        $tmpl = JRequest::getCmd('tmpl', 'index');
        $this->necConfig = fwConfig::getInstance();

        JHtml::_('behavior.mootools');
        $this->loadcss();
        if ($this->necConfig->get('loadjquey_frontend')) {
            if ($this->necConfig->get('debug_mode')) {
                JHtml::_('script', 'jquery/jquery.js', jFWBase::getUrl('js', false));
            } else {
                JHtml::_('script', 'jquery/jquery.min.js', jFWBase::getUrl('js', false));
            }
            JHtml::_('script', 'jquery/jquery-noconflict.js', jFWBase::getUrl('js', false));
        }

        $this->getLayoutVars($tpl);

        $this->assignRef('Config', $this->necConfig);
        parent::display($tpl);
    }

    /**
     * Gets layout vars for the view
     * 
     * @return unknown_type
     */
    function getLayoutVars($tpl = null) {
        $layout = $this->getLayout();
        switch (strtolower($layout)) {
            case "view":
                $this->_view($tpl);
                break;
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

        // $action = 'index.php?option='.jFWBase::getComponentName().'&controller='.$this->_name.'&view='.$this->_name;
        // page-navigation
        $this->assignRef('pageNav', $model->getPagination());

        // list of items
        $this->assignRef('params', $this->params);
        $this->assignRef('rows', $model->getData());

        $this->assignRef('user', $user);
        $this->assignRef('tmpl', $tmpl);
        $this->assignRef('layout', $layout);
        $this->assignRef('idkey', $model->getTable()->getKeyName());

        // form
        $form = array();
        $view = strtolower(JRequest::getVar('view'));
        $form['action'] = 'index.php?option=' . jFWBase::getComponentName() . '&controller=' . $this->_name . '&view=' . $this->_name;
        $form['validate'] = "<input type='hidden' name='" . JUtility::getToken() . "' value='1' />";
        $this->assign('form', $form);

        $this->_prepareDocument();
    }
    
    /**
     * Basic methods for displaying an item from a list
     * @param $tpl
     * @return unknown_type
     */
    function _view($tpl = '', $clearstate = true) {
        $user   = & JFactory::getUser();
        $tmpl   = JRequest::getCmd('tmpl', 'index');
        $model  = $this->getModel();
        $app    = JFactory::getApplication();
        
        $this->params = $app->getParams();
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
        
        $row 	= $model->getItem($clearstate);
	$table	= $model->getTable();
	$idkey 	= $table->getKeyName();
        
        if ($row->$idkey) {
            
        }
        
        $this->assignRef( 'state', $model->getState() );
        $this->assignRef( 'params', $this->params);
	$this->assignRef( 'user', $user);
	$this->assignRef( 'row', $row);
	$this->assignRef( 'tmpl', $tmpl);
	$this->assignRef( 'idkey', $idkey);
        
        $this->_prepareDocument();
    }

    function _prepareDocument() {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $document = JFactory::getDocument();

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_NEGOCIO_DEFAULT_PAGE_TITLE'));
        }

        $title = $this->params->get('page_title');
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $document->setMetadata('robots', $this->params->get('robots'));
        }
    }

    function loadcss() {
        $template = JFactory::getApplication()->getTemplate();

        // Create the template file name based on the layout
        $file = 'negocio.css';

        // Clean the file name
        $file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
        $template_url = JURI::root() . 'templates/' . $template;
        $template_uri = JPATH_THEMES . DS . $template;

        if (JFile::exists($template_uri . DS . $file)) {
            JHTML::_('stylesheet', 'negocio.css', $template_url);
        } else {
            if ($this->necConfig->get('less_frontend')) {
                $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
                jFWBase::load('jFWLess', 'library.less', $options);
                jFWLess::autoCompile(
                        jFWBase::getPath('css') . DS . 'frontend' . DS . 'template.less', jFWBase::getPath('css') . DS . $file
                );
            }
            JHTML::_('stylesheet', 'negocio.css', jFWBase::getUrl('css', false));
        }
    }

}