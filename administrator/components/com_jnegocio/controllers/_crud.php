<?php
/**
 * @version	    3.0.1
 * @package	    Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Require the base controller
jFWBase::load('jFWController', 'controllers._base');

/**
 * Base class for a Controller
 *
 * @abstract
 * @package		Joomla
 * @subpackage	jNegocio
 */
class jFWControllerCRUD extends jFWController
{
    protected $_itemtable = null;

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();

        $this->_registerTask('add', 'edit');
        $this->_registerTask('apply', 'save');
        $this->_registerTask('savenew', 'save');
        $this->_registerTask('remove', 'delete');
        $this->_registerTask('resethits', 'save');
    }

    /**
     * logic for cancel an action
     *
     * @access public
     * @return void
     */
    function cancel() {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app        = JFactory::getApplication();
        $tmpl       = $this->input->getCmd('tmpl', 'index');
        $viewName   = $this->input->getWord('view', 'dashboard');
        $model      = $this->_getModel($viewName);
        $table      = $model->getTable();
        $key        = $table->getKeyName();
        $recordId   = $app->input->getInt($key);

        if ($table->load($recordId)) {
            $table->checkin();
        }

        $this->messagetype = 'notice';
        $this->message = JText::_('COM_JNEGOCIO_OPERATION_CANCELED');
        $redirect = 'index.php?option=' . jFWBase::getComponentName() . '&view=' . $viewName;
        if ($tmpl) {
            $redirect .= '&tmpl=' . $tmpl;
        }
        $redirect = JRoute::_($redirect, false);
        $this->Redirect($redirect, $this->message, $this->messagetype);
    }

    /**
     * Saves an item and redirects based on task
     *
     * @param   bool $lforceRedirect
     *
     * @return  void
     */
    function save($lforceRedirect = true) {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app        = JFactory::getApplication();
        $tmpl       = $this->input->getCmd('tmpl', 'index');
        $viewName   = $this->input->getWord('view', 'dashboard');
        $task       = $this->input->getCmd('task', 'Display');
        $model      = $this->_getModel($viewName);
        $table      = $model->getTable();
        $key        = $table->getKeyName();
        $bError     = false;

        $post  = $this->input->post->get('jform', array(), 'array');

        if ($task == 'save_as') {
            $post[$key] = 0;
        }

        if ($task == 'resethits') {
            $post['hits'] = 0;
        }

        $redirect = "index.php?option=" . jFWBase::getComponentName();
        $returnid = $model->store($post);
        if ($returnid) {
            $this->_itemtable = $model->getTable();
            $this->_itemtable->load($returnid);
            $dispatcher = JDispatcher::getInstance();
            $dispatcher->trigger('onAfterSave' . $this->getName(), array($this->_itemtable));

            switch ($task) {
                case 'savenew' :
                    $redirect .= '&view=' . $this->getName() . '&task=edit&cid[]=0';
                    break;

                case 'apply' :
                    $redirect .= '&view=' . $this->getName() . '&task=edit&cid[]=' . $returnid;
                    break;

                default :
                    $redirect .= '&view=' . $this->getName();
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

        if ($lforceRedirect) {
            $this->Redirect($redirect, $this->message, $this->messagetype);
        }
        return $bError;
    }

    /**
     * Checks if an item is checked out, and if so, redirects to layout for viewing item
     * Otherwise, displays a form for editing item
     *
     * @return void
     */
    function edit() {
        $user       = JFactory::getUser();
        $viewName   = $this->input->getWord('view', 'dashboard');
        $this->_itemtable = null;

        $model  = $this->_getModel($viewName);
        $view   = $this->_getView($model, $viewName);

        $this->_itemtable = $model->getTable();
        $this->_itemtable->load($model->getId());

        // Error if checkedout by another administrator
        if ($model->isCheckedOut($user->get('id'))) {
            JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_JNEGOCIO_EDITED_BY_ANOTHER_ADMIN'));
            $view->setLayout('default');
        } else {
            if ($model->checkout()) {
                JRequest::setVar('hidemainmenu', 1);
                $view->setLayout('form');
            } else {
                JError::raiseWarning('SOME_ERROR_CODE', JText::sprintf('COM_JNEGOCIO_ERROR_CHECKOUT_FAILED', $model->getError()));
                $view->setLayout('default');
            }
        }

        $model->emptyState();
        $this->_setModelState($model);

        // Render view.
        echo $view->render();
    }
}