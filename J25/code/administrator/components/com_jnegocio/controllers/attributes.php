<?php

/**
 * @version     $Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

// Require the base controller
jFWBase::load('jFWControllerCRUD', 'controllers._crud');

class jNegocioControllerAttributes extends jFWControllerCRUD {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->set('suffix', 'attributes');
    }
    
    /**
     * Sets the model's default state based on value in the request
     * 
     * @return unknown_type
     */
    function _setModelState() {
        $state = parent::_setModelState();
        $app = JFactory::getApplication();
        $model = $this->getModel($this->get('suffix'));
        $ns = $this->getNamespace();

        $state['filter_categoryid'] = $app->getUserStateFromRequest($ns . '.filter_categoryid', 'filter_categoryid', '', '');

        foreach (@$state as $key => $value) {
            $model->setState($key, $value);
        }

        return $state;
    }
    
    /**
     * Saves an item and redirects based on task
     * @return void
     */
    function save() {
        // Check for request forgeries
        JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));
        $tmpl = JRequest::getCmd('tmpl', 'index');
        $task = JRequest::getVar('task');
        $bError = false;
        $this->_itemtable = null;
        
        //Sanitize
        $post = JRequest::get('post');

        $model = $this->getModel($this->get('suffix'));

        if ($task == 'save_as') {
            $pk = $model->getTable()->getKeyName();
            $post[$pk] = 0;
        }

        if ($task == 'resethits') {
            $post['hits'] = 0;
        }
        $categorys = $post['categories'];
        if (!is_array($categorys)) { 
            $categorys = array(); 
            $categorys[] = -1; 
        }
        $post['attribute_cats'] = implode(",", $categorys);
        
        $redirect = "index.php?option=" . jFWBase::getComponentName();
        $returnid = $model->store($post);
        if ($returnid) {
            $this->_itemtable = $model->getTable();
            $this->_itemtable->load($returnid);
            $dispatcher = JDispatcher::getInstance();
            $dispatcher->trigger('onAfterSave' . $this->get('suffix'), array($this->_itemtable));

            switch ($task) {
                case 'savenew' :
                    $redirect .= '&view=' . $this->get('suffix') . '&task=edit&cid[]=0';
                    break;

                case 'apply' :
                    $redirect .= '&view=' . $this->get('suffix') . '&task=edit&cid[]=' . $returnid;
                    break;

                default :
                    $redirect .= '&view=' . $this->get('suffix');
                    break;
            }
            $this->messagetype = 'message';
            $this->message = JText::_('COM_JNEGOCIO_SAVED');
            $bError = false;
        } else {
            $this->messagetype = 'notice';
            $this->message = JText::_('COM_JNEGOCIO_ERROR_SAVE_FAILED') . " - " . JError::getError();
            $redirect .= '&view=' . $this->get('suffix');
            $bError = true;
        }

        $model->checkin();
        if ($tmpl) {
            $redirect .= '&tmpl=' . $tmpl;
        }
        $redirect = JRoute::_($redirect, false);
        $this->_internal_redirect = $redirect;
        
        // echo "this->message:".$this->message."<br/>";
        // echo "this->messagetype:".$this->messagetype."<br/>";
        
        $this->setRedirect($redirect, $this->message, $this->messagetype);
        return $bError;        
    }
}