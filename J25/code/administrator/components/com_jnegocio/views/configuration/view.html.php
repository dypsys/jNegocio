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

class jNegocioViewConfiguration extends jFWView {

    function __construct($config = array()) {
        parent::__construct($config);
        $this->_hidesubmenu = true;
    }

    /**
     * Gets layout vars for the view
     *
     * @param $tpl
     * @return unknown_type
     */
    function getLayoutVars($tpl = null) {
        $layout = $this->getLayout();
        switch (strtolower($layout)) {
            case "close":
                $this->_displayclose($tpl);
                break;
            case "default":
            default:
                $this->_form($tpl);
                break;
        }
    }

    /**
     * Basic commands for displaying a list
     *
     * @param $tpl
     * @return unknown_type
     */
    function _displayclose($tpl = '') {
        $user = & JFactory::getUser();
        $tmpl = JRequest::getCmd('tmpl', 'index');

        $this->assignRef('user', $user);
        $this->assignRef('tmpl', $tmpl);
    }

    /**
     * The default toolbar for editing an item
     * @param $isNew
     * @return unknown_type
     */
    function _formToolbar() {
        $divider = false;

        JToolBarHelper::save('save');
        JToolBarHelper::cancel();
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

        $row = fwConfig::getInstance();
        // echo "row:".var_dump($row)."<br/>";
        // Load the JEditor object
        $editor = & JFactory::getEditor();
        $action = 'index.php';
        // $action = $uri->toString();

        $this->assignRef('action', $action);
        $this->assignRef('editor', $editor);
        $this->assignRef('user', $user);
        $this->assignRef('row', $row);
        $this->assignRef('tmpl', $tmpl);
    }

}