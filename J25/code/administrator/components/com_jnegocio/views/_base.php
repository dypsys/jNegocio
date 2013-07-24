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

require_once( JPATH_ADMINISTRATOR . DS . 'includes' . DS . 'toolbar.php' );

jimport('joomla.filter.filteroutput');
jimport('joomla.application.component.view');

jFWBase::load('jFWHelperBase', 'helpers._base');

class jFWView extends JView {

    protected $editor;

    /**
     * Method to get a JEditor object based on the form field.
     *
     * @return  JEditor  The JEditor object.
     *
     * @since   11.1
     */
    protected function &getEditor() {
        // Only create the editor if it is not already created.
        if (empty($this->editor)) {
            // Initialize variables.
            $editor = null;

            // Get the editor type attribute. Can be in the form of: editor="desired|alternative".
            $type = 'Editor';
            // trim((string) $this->element['editor']);

            if ($type) {
                // Get the list of editor types.
                $types = explode('|', $type);

                // Get the database object.
                $db = JFactory::getDBO();

                // Iterate over teh types looking for an existing editor.
                foreach ($types as $element) {
                    // Build the query.
                    $query = $db->getQuery(true);
                    $query->select('element');
                    $query->from('#__extensions');
                    $query->where('element = ' . $db->quote($element));
                    $query->where('folder = ' . $db->quote('editors'));
                    $query->where('enabled = 1');

                    // Check of the editor exists.
                    $db->setQuery($query, 0, 1);
                    $editor = $db->loadResult();

                    // If an editor was found stop looking.
                    if ($editor) {
                        break;
                    }
                }
            }

            // Create the JEditor instance based on the given editor.
            $this->editor = JFactory::getEditor($editor ? $editor : null);
        }

        return $this->editor;
    }

    /**
     * Displays a layout file 
     * 
     * @param unknown_type $tpl
     * @return unknown_type
     */
    function display($tpl = null) {
        jFWBase::load('jFWSelect', 'library.select');
        jFWBase::load('jFWGrid', 'library.grid');

        //initialise variables
        $user = & JFactory::getUser();
        $db = & JFactory::getDBO();
        $document = & JFactory::getDocument();
        $tmpl = JRequest::getCmd('tmpl', 'index');
        $necConfig = fwConfig::getInstance();

        JHtml::_('behavior.mootools');
        if ($necConfig->get('less_admin')) {
            jFWBase::load('jFWLess', 'library.less');
            jFWLess::autoCompile(
                    jFWBase::getPath('css') . DS . 'admin' . DS . 'template.less', jFWBase::getPath('css') . DS . 'nec.backend.css'
            );
        }

        JHTML::_('stylesheet', 'nec.backend.css', jFWBase::getUrl('css', false));
        if ($necConfig->get('loadjquey_admin')) {
            if ($necConfig->get('debug_mode')) {
                JHtml::_('script', 'jquery/jquery.js', jFWBase::getUrl('js', false)); 
            } else {
                JHtml::_('script', 'jquery/jquery.min.js', jFWBase::getUrl('js', false)); 
            }
            JHtml::_('script', 'jquery/jquery-noconflict.js', jFWBase::getUrl('js', false));
        }
        
        if ($necConfig->get('debug_mode')) {
            JHtml::_('script', 'nec.backend.all.js', jFWBase::getUrl('js', false));
        } else {
            JHtml::_('script', 'nec.backend.all.min.js', jFWBase::getUrl('js', false));
        }

        $this->getLayoutVars($tpl);
        // $this->displayTitle($this->get('title'));

        jimport('joomla.application.module.helper');

        $this->assignRef('config'   , $necConfig);

        if (!JRequest::getInt('hidemainmenu') && empty($this->hidemenu) && $tmpl != 'component') {
            jFWBase::load('jFWManifest', 'library.manifest');
            $manifest = new jFWManifest();
            echo '<div style="float:left;width:19%; margin-right:1%;">';
            // echo "mi menu";
            echo $manifest->menuadmin();
            echo '</div>';
            echo '<div style="float:left;width:80%;">';
        }
        parent::display($tpl);

        if (!JRequest::getInt('hidemainmenu') && empty($this->hidemenu) && $tmpl != 'component') {
            echo '</div>';
        }
    }

    /**
     * Displays text as the title of the page
     * 
     * @param $text
     * @return unknown_type
     */
    function displayTitle($text = '', $classTitle = null) {
        $list = array('menuoptions');

        $tmpl = JRequest::getCmd('tmpl', 'index');
//		$html_menu = ''; 
// 		if (!JRequest::getInt('hidemainmenu') && empty($this->hidemenu)) {
// 			$this->displaySubMenu();
// 		}		
        $title = $text ? JText::_($text) : JText::_('COM_JNEGOCIO_' . strtoupper(ucfirst(JRequest::getVar('view'))));
        if ($classTitle === null) {
            $classTitle = 'COM_JNEGOCIO_' . strtoupper($this->_name);
        }
        $html_title = $title;
        JToolBarHelper::title($html_title, $classTitle);
    }

// 	/**
// 	 * Display SubMenu
// 	 * 
//  	 * @param $text
// 	 * @return unknown_type
// 	 */
// 	function displaySubMenu() {
// 		$list = array( 'productos', 'categories', 'pedidos', 'facturas', 'menuoptions');
// 		$tmpl		= JRequest::getCmd( 'tmpl' , 'index' );
// 		if ($tmpl != 'component') {
// 			$admin = JFactory::getApplication()->isAdmin();
// 			if ($admin) {
// 				foreach($list as $type) {
// 					$texto = JText::_('COM_JNEGOCIO_SUBMENU_'.strtoupper($type) );
// 					$link = 'index.php?option='.jFWBase::getComponentName().'&view='.$type;
// 					$def = ($this->_name == $type) ? true:false;
// 					JSubMenuHelper::addEntry( $texto, $link,$def);
// 				}
// 			}
// 		}
// 	}

    /**
     * Gets layout vars for the view
     * 
     * @return unknown_type
     */
    function getLayoutVars($tpl = null) {
        $layout = $this->getLayout();
        switch (strtolower($layout)) {
            case "view":
                $this->_form($tpl);
                break;

            case "modal":
                JRequest::setVar('hidemainmenu', '1');
                $this->_modal($tpl);
                break;

            case "form":
                JRequest::setVar('hidemainmenu', '1');
                $this->_form($tpl);
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
        $user = &JFactory::getUser();
        $tmpl = JRequest::getCmd('tmpl', 'index');
        $model = $this->getModel();
        $layout = $this->getLayout();

        // set the model state
        $this->assign('state', $model->getState());

        if (empty($this->hidemenu)) {
            // add toolbar buttons
            $this->_defaultToolbar();
        }

        $action = 'index.php?option=' . jFWBase::getComponentName() . '&controller=' . $this->_name . '&view=' . $this->_name;

        $this->displayTitle($this->get('title'));
        
        // page-navigation
        $this->assignRef('pageNav', $model->getPagination());

        // list of items
        $this->assignRef('action'   , $action);
        $this->assignRef('rows'     , $model->getData());
        $this->assignRef('user'     , $user);
        $this->assignRef('tmpl'     , $tmpl);
        $this->assignRef('layout'   , $layout);
        $this->assignRef('idkey'    , $model->getTable()->getKeyName());
    }

    /**
     * Basic commands for displaying a list
     *
     * @param $tpl
     * @return unknown_type
     */
    function _modal($tpl = '') {
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

        // page-navigation
        $this->assignRef('pageNav', $model->getPagination());

        // list of items
        $this->assignRef('action'   , $action);
        $this->assignRef('rows'     , $model->getData());
        $this->assignRef('user'     , $user);
        $this->assignRef('tmpl'     , $tmpl);
        $this->assignRef('layout'   , $layout);
        $this->assignRef('idkey'    , $model->getTable()->getKeyName());
    }

    /**
     * Basic methods for displaying an item from a list
     * @param $tpl
     * @return unknown_type
     */
    function _form($tpl = '', $clearstate = true) {
        global $mainframe;

        // Initialize variables
        $document = & JFactory::getDocument();
        $uri = & JFactory::getURI();
        $user = & JFactory::getUser();
        $tmpl = JRequest::getCmd('tmpl', 'index');
        $model = $this->getModel();

        if (empty($this->hidemenu)) {
            // add toolbar buttons
            $this->_formToolbar();
            if ($tmpl == 'component') {
                // Si es tmpl component mostramos la barra que tengamos
                $bar = & JToolBar::getInstance('toolbar');
                echo $bar->render('toolbar');
                echo "<div class='clr'></div>";
            }
        }

        $row = $model->getItem($clearstate);
        $table = $model->getTable();
        $idkey = $table->getKeyName();

        // fail if checked out not by 'me'
        if ($row->$idkey) {
//			if ($model->isCheckedOut( $user->get('id') )) {
//				JError::raiseWarning( 'SOME_ERROR_CODE', JText::_( 'EDITED BY ANOTHER ADMIN' ));
//				$url = 'index.php?option='.$this->_baseappname.'&view=' . $this->_name;
//				$mainframe->redirect( $url );
//			}
            JFilterOutput::objectHTMLSafe($row);
        }

        // Build the page title string
        $title = $row->$idkey ? JText::_('Edit') : JText::_('New');

        // Set page title
        $document->setTitle($title);
        $this->displayTitle($this->get('title'));

        // Load the JEditor object
        $editor = & JFactory::getEditor();
        $session = JFactory::getSession();
        $action = 'index.php';
        // $action = $uri->toString();

        $this->assignRef('state'    , $model->getState());
        $this->assignRef('action'   , $action);
        $this->assignRef('editor'   , $editor);
        $this->assignRef('session'  , $session);
        $this->assignRef('user'     , $user);
        $this->assignRef('row'      , $row);
        $this->assignRef('tmpl'     , $tmpl);
        $this->assignRef('idkey'    , $idkey);
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

        if ($tmpl != 'component') {
            $PC = JText::_('COM_JNEGOCIO_CONTROL_PANEL');
            JToolBarHelper::divider();
            JToolBarHelper::custom('cpanel', 'back.png', 'back.png', $PC, false);
        }
    }

    /**
     * The default toolbar for editing an item
     * @param $isNew
     * @return unknown_type
     */
    function _formToolbar() {
        JToolBarHelper::custom('savenew', "savenew", "savenew", JText::_('COM_JNEGOCIO_TOOLBAR_SAVE_AND_NEW'), false);
        JToolBarHelper::save('save');
        JToolBarHelper::spacer();
        JToolBarHelper::apply('apply');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
    }

}