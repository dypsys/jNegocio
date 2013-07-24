<?php
/**
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2013 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Importamos el controlador de Joomla
jimport('joomla.application.component.controller');

/**
 * Base class for a jFW Controller
 * 
 * @abstract
 * @package	jFWController
 * @subpackage	framework
 * @since	1.5
 */
class jFWFrontController extends JController {

    /**
     * @var array() instances of Models to be used by the controller
     */
    protected $_models = array();

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();

        $this->set('suffix', 'dashboard');
    }

    /**
     * Gets the view's namespace for state variables
     * @return string
     */
    function getNamespace() {
        $app = JFactory::getApplication();
        $model = $this->getModel($this->get('suffix'));

        $ns = $app->getName() . '::com.' . jFWBase::getName() . '.model.' . $model->getTable()->get('_suffix');
        return $ns;
    }

    /**
     * Typical view method for MVC based architecture
     *
     * This function is provide as a default implementation, in most cases
     * you will need to override it in your own controllers.
     *
     * @access	public
     * @param	string	$cachable	If true, the view output will be cached
     * @since	1.5
     */
    function display($cachable = false) {

        // this sets the default view
        JRequest::setVar('view', JRequest::getVar('view', 'dashboard'));

        $document = JFactory::getDocument();

        $viewType = $document->getType();
        $viewName = JRequest::getCmd('view', $this->getName());
        $viewLayout = JRequest::getCmd('layout', 'default');
        $ns = $this->getNamespace();

        $view = $this->getView($viewName, $viewType);

        // Get/Create the model
        if ($model = & $this->getModel($viewName)) {
            // El controlador assigna las variables de estado al modelo
            $this->_setModelState();

            // Push the model into the view (as default)
            $view->setModel($model, true);
        }

        // Set the layout
        $view->setLayout($viewLayout);

        // Display the view
        if ($cachable && $viewType != 'feed') {
            $cache = & JFactory::getCache(jFWBase::getComponentName(), 'view');
            $cache->get($view, 'display');
        } else {
            $view->display();
        }
    }

    /**
     * 
     * Gets the model
     * We override parent::getModel because parent::getModel always creates a new Model instance
     */
    function getModel($name = '', $prefix = '', $config = array()) {
        if (empty($name)) {
            $name = $this->getName();
        }

        if (empty($prefix)) {
            $prefix = $this->getName() . 'Model';
        }

        $fullname = strtolower($prefix . $name);
        if (empty($this->_models[$fullname])) {
            $this->_models[$fullname] = parent::getModel($name, $prefix, $config);
        }

        return $this->_models[$fullname];
    }

    /**
     * Sets the model's default state based on value in the request
     * 
     * @return unknown_type
     */
    function _setModelState() {
        $app = JFactory::getApplication();
        $model = $this->getModel($this->get('suffix'));
        $ns = $this->getNamespace();

        $state = array();

        $state['limit'] = $app->getUserStateFromRequest($ns . '.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $state['limitstart'] = $app->getUserStateFromRequest($ns . '.limitstart', 'limitstart', 0, 'int');
        // Si no se hace asi, cuando es el salto a la 1er pag, no salta;
        // $state['limitstart'] = JRequest::getInt('limitstart', 0);        
        $state['filter_order'] = $app->getUserStateFromRequest($ns . '.filter_order', 'filter_order', 'tbl.'.$model->getTable()->getKeyName(), 'cmd');
        $state['filter_order_Dir'] = $app->getUserStateFromRequest($ns . '.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
        $state['filter_state'] = $app->getUserStateFromRequest($ns . '.filter_state', 'filter_state', '', 'word');
        $state['filter_id'] = $app->getUserStateFromRequest($ns . '.filter_id', 'filter_id', '', 'int');
        $state['search'] = $app->getUserStateFromRequest($ns . '.search', 'search', '', 'string');
        $state['id'] = JRequest::getVar('id', JRequest::getVar('id', '', 'get', 'int'), 'post', 'int');

        foreach (@$state as $key => $value) {
            $model->setState($key, $value);
        }
        return $state;
    }
}