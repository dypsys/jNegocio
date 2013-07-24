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

// Require the base controller
jFWBase::load('jFWController', 'controllers._base');

/**
 * Base class for a Controller
 *
 * @abstract
 * @package		Joomla
 * @subpackage	jNegocio
 */
class jFWControllerCRUD extends jFWController {

    private $_internal_redirect = '';
    private $_itemtable = null;
    
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();

        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        $this->registerTask('savenew', 'save');
        $this->registerTask('remove', 'delete');
        $this->registerTask('resethits', 'save');
    }
    
    public function get_internal_redirect() {
        return $this->_internal_redirect;
    }

    public function get_itemtable() {
        return $this->_itemtable;
    }

    /**
     * Saves an item and redirects based on task
     * @return void
     */
    function save($lforceRedirect = true) {
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
        
        if ($lforceRedirect) {
            $this->setRedirect($redirect, $this->message, $this->messagetype);
        }
        return $bError;
    }

    /**
     * logic for cancel an action
     *
     * @access public
     * @return void
     */
    function cancel() {
        // Check for request forgeries
        JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));
        $tmpl = JRequest::getCmd('tmpl', 'index');
        $model = $this->getModel($this->get('suffix'));
        $this->_itemtable = null;
        $this->_itemtable = $model->getTable();
        $this->_itemtable->bind(JRequest::get('post'));
        $this->_itemtable->checkin();

        $this->messagetype = 'notice';
        $this->message = JText::_('COM_JNEGOCIO_OPERATION_CANCELED');
        $redirect = 'index.php?option=' . jFWBase::getComponentName() . '&view=' . $this->get('suffix');
        if ($tmpl) {
            $redirect .= '&tmpl=' . $tmpl;
        }
        $redirect = JRoute::_($redirect, false);
        $this->setRedirect($redirect, $this->message, $this->messagetype);
    }

    /**
     * Checks if an item is checked out, and if so, redirects to layout for viewing item
     * Otherwise, displays a form for editing item
     *
     * @return void
     */
    function edit() {
        $user = & JFactory::getUser();
        $view = $this->getView($this->get('suffix'), 'html');
        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $languages = jFWBase::getClass('HelperLanguages', 'helpers.language', $options)->getAllLanguages();
        $this->_itemtable = null;
        
        $model = $this->getModel($this->get('suffix'));
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

        $view->setModel($model, true);

        $multilang = count($languages) > 1;

        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);

//        echo "borrar state desde controller editar<br/>";
        $model->emptyState();
        $this->_setModelState();
        $view->display();
    }

    /**
     * Deletes record(s) and redirects to default layout
     */
    function delete() {
        $error = false;
        $this->messagetype = '';
        $this->message = '';
        $this->_itemtable = null;
        
        $tmpl = JRequest::getCmd('tmpl', 'index');
        if (!isset($this->redirect)) {
            $this->redirect = JRequest::getVar('return') ? base64_decode(JRequest::getVar('return')) : 'index.php?option=' . jFWBase::getComponentName() . '&view=' . $this->get('suffix');
            if ($tmpl) {
                $this->redirect .= '&tmpl=' . $tmpl;
            }
            $this->redirect = JRoute::_($this->redirect, false);
        }
        $model = $this->getModel($this->get('suffix'));
        $this->_itemtable = $model->getTable();

        $cids = JRequest::getVar('cid', array(0), 'request', 'array');
        foreach (@$cids as $cid) {
            if (!$this->_itemtable->delete($cid)) {
                $this->message .= $this->_itemtable->getError();
                $this->messagetype = 'notice';
                $error = true;
            }
        }

        if ($error) {
            $this->message = JText::sprintf('COM_JNEGOCIO_ERROR_DELETED_FAILED', $this->message);
        } else {
            $this->message = JText::sprintf('COM_JNEGOCIO_ITEMS_DELETED', count($cids));
        }

        $this->setRedirect($this->redirect, $this->message, $this->messagetype);
    }

}