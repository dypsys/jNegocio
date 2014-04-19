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

/**
 * Base Controller
 *
 * @package     Joomla.Site
 * @subpackage  com_jnegocio
 * @since       3.2
 */
class jFWController extends JControllerBase
{
    /**
     * Application object - Redeclared for proper typehinting
     *
     * @var    JApplicationCms
     */
    protected $app;

    /**
     * Array of class methods
     *
     * @var    array
     */
    protected $methods;

    /**
     * Array of class methods to call for a given task.
     *
     * @var    array
     */
    protected $taskMap;

    /**
     * The set of search directories for resources (views).
     *
     * @var    array
     */
    protected $paths;

    /**
     * Prefix for the view and model classes
     *
     * @var    string
     * @since  3.2
     */
    protected $prefix = 'jNegocio';

    /**
     * Name of controller
     *
     * @var    string
     */
    protected $name;

    /**
     * Models to be used by the controller
     *
     * @var array() instances of Models to be used by the controller
     */
    protected $_models = array();

    /**
     * Redirect message.
     *
     * @var    string
     */
    protected $message;

    /**
     * Redirect message type.
     *
     * @var    string
     */
    protected $messageType;

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();

        // Initialise variables.
        $this->methods  = array();
        $this->taskMap  = array();
        $this->paths    = array();

        // Determine the methods to exclude from the base class.
        $xMethods = get_class_methods('jFWController');

        // Get the public methods in this class using reflection.
        $r = new ReflectionClass($this);
        $rMethods = $r->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($rMethods as $rMethod) {
            $mName = $rMethod->getName();
            if ('_' != substr($mName, 0, 1)) {
                // Add default display method if not explicitly declared.
                if (!in_array($mName, $xMethods) || $mName == 'display') {
                    $this->methods[] = strtolower($mName);
                    // Auto register the methods as tasks.
                    $this->taskMap[strtolower($mName)] = $mName;
                }
            }
        }

        $this->_registerDefaultTask('display');

        if ($this->app->isAdmin())  {
            $this->basePath = JPATH_ADMINISTRATOR . '/components/' . jFWBase::getComponentName();
        } else {
            $this->basePath = JPATH_BASE . '/components/' . jFWBase::getComponentName();
        }

        $this->_setPath('view', $this->basePath . '/views');
        $this->_setPath('model', $this->basePath . '/models');

        $viewName   = $this->input->getWord('view', 'dashboard');
        $this->setName($viewName);
    }

    /**
     * Gets the view's namespace for state variables
     * @return string
     */
    function _getNamespace() {
        $model = $this->_getModel($this->getName());

        $ns = $this->app->getName() . '::com.' . jFWBase::getName() . '.model.' . $model->getTable()->get('_suffix');
        return $ns;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets an entire array of search paths for resources.
     *
     * @param   string  $type  The type of path to set, typically 'view' or 'model'.
     * @param   string  $path  The new set of search paths. If null or false, resets to the current directory only.
     *
     * @return  void
     */
    protected function _setPath($type, $path)
    {
        // Clear out the prior search dirs
        $this->paths[$type] = array();

        // Actually add the user-specified directories
        $this->_addPath($type, $path);
    }

    /**
     * Adds to the search path for templates and resources.
     *
     * @param   string  $type  The path type (e.g. 'model', 'view').
     * @param   mixed   $path  The directory string  or stream array to search.
     *
     * @return  jFWController  A jFWController object to support chaining.
     */
    protected function _addPath($type, $path)
    {
        // Just force path to array
        settype($path, 'array');

        if (!isset($this->paths[$type])) {
            $this->paths[$type] = array();
        }

        // Loop through the path directories
        foreach ($path as $dir) {
            // No surrounding spaces allowed!
            $dir = rtrim(JPath::check($dir, '/'), '/') . '/';

            // Add to the top of the search dirs
            array_unshift($this->paths[$type], $dir);
        }

        return $this;
    }

    /**
     * Register the default task to perform if a mapping is not found.
     *
     * @param   string  $method  The name of the method in the derived class to perform if a named task is not found.
     *
     * @return  jFWController  A jFWController object to support chaining.
     */
    public function _registerDefaultTask($method)
    {
        $this->_registerTask('__default', $method);

        return $this;
    }

    /**
     * Register (map) a task to a method in the class.
     *
     * @param   string  $task    The task.
     * @param   string  $method  The name of the method in the derived class to perform for this task.
     *
     * @return  jFWController  A jFWController object to support chaining.
     */
    public function _registerTask($task, $method)
    {
        if (in_array(strtolower($method), $this->methods))
        {
            $this->taskMap[strtolower($task)] = $method;
        }

        return $this;
    }

    /**
     * Unregister (unmap) a task in the class.
     *
     * @param   string  $task  The task.
     *
     * @return  jFWController  This object to support chaining.
     */
    public function _unregisterTask($task)
    {
        unset($this->taskMap[strtolower($task)]);

        return $this;
    }

    /**
     * Execute the controller.
     *
     * This is a generic method to execute and register task to render a view.
     *
     * @return  mixed   The value returned by the called method, false in error case.
     *
     * @throws  Exception
     */
    public function execute()
    {
        $task = $this->getInput()->getCmd('task', 'Display');
        return $this->doexecute($task);
    }

    /**
     * Execute a task by triggering a method in the derived class.
     *
     * @param   string  $task  The task to perform. If no matching task is found, the '__default' task is executed, if defined.
     *
     * @return  mixed   The value returned by the called method, false in error case.
     *
     * @throws  Exception
     */
    public function doexecute($task)
    {
        $this->task = $task;

        $task = strtolower($task);
        if (isset($this->taskMap[$task])) {
            $doTask = $this->taskMap[$task];
        } elseif (isset($this->taskMap['__default'])) {
            $doTask = $this->taskMap['__default'];
        } else {
            throw new Exception(JText::sprintf('COM_JNEGOCIO_ERROR_TASK_NOT_FOUND', $task) . " task name : " . $task, 404);
        }

        // Record the actual task being fired
        $this->doTask = $doTask;

        return $this->$doTask();
    }

    /**
     * Create the filename for a resource.
     *
     * @param   string  $type   The resource type to create the filename for.
     * @param   array   $parts  An associative array of filename information. Optional.
     *
     * @return  string  The filename.
     */
    protected static function _createFileName($type, $parts = array())
    {
        $filename = '';
        switch ($type) {
            case 'view':
                if (!empty($parts['type'])) {
                    $parts['type'] = '.' . $parts['type'];
                } else {
                    $parts['type'] = '';
                }

                $filename = strtolower($parts['name'] . '/view' . $parts['type'] . '.php');
                break;
        }

        return $filename;
    }

    /**
     * Typical view method for MVC based architecture
     *
     * This function is provide as a default implementation, in most cases
     * you will need to override it in your own controllers.
     *
     * @access  public
     * @param   bool $cachable
     * @param   bool $urlparams
     *
     * @throws  Exception
     * @return  bool
     */
    function display($cachable = false, $urlparams = false) {
        // Get the document object.
        $document   = JFactory::getDocument();
        $viewName   = $this->input->getWord('view', 'dashboard');
        $layoutName = $this->input->getWord('layout', 'default');
        $viewType   = $document->getType();

        $model = $this->_getModel($viewName);
        $this->_setModelState($model);
            // Access check.
//            if (!JFactory::getUser()->authorise('core.admin', $model->getState()->get('component.option')))
//            {
//                $this->app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
//
//                return;
//            }

        $view = $this->_getView($model, $viewName);
            // new $viewClass($model, $paths);
        $view->setLayout($layoutName);

        // Push document object into the view.
        $view->document = $document;

        // Reply for service requests
        if ($viewType == 'json') {
            return $view->renderJson();
        }

        // Render view.
        echo $view->render();

        return true;
    }

    /**
     * Gets the view
     *
     * @param   jFWModel    $model
     * @param   string      $name
     *
     * @throws  Exception
     *
     * @return null
     */
    function _getView(jFWModel $model, $name = '') {
        if (empty($name)) {
            $name = $this->getName();
        }

        $viewType   = JFactory::getDocument()->getType();

        // Register the layout paths for the view
        $paths = new SplPriorityQueue;
        if ($this->app->isAdmin()) {
            $paths->insert(JPATH_ADMINISTRATOR . '/components/' . jFWBase::getComponentName() . '/views/' . $name . '/tmpl', 1);
        } else {
            $paths->insert(JPATH_BASE . '/components/' . jFWBase::getComponentName() . '/views/' . $name . '/tmpl', 1);
        }

        $viewClass  = $this->prefix . 'View' . ucfirst($name);

        if (!class_exists($viewClass)) {
            jimport('joomla.filesystem.path');
            $path = JPath::find($this->paths['view'], $this->_createFileName('view', array('name' => $name, 'type' => $viewType)));

            if ($path) {
                require_once $path;

                if (!class_exists($viewClass)) {
                    throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_VIEW_CLASS_NOT_FOUND', $name, $path), 500);
                }
            } else {
                return null;
            }
        }

        return new $viewClass($model, $paths);
    }

    /**
     * Gets the model
     *
     * @param string $name
     * @param string $prefix
     *
     * @return null
     * @throws Exception
     */
    function _getModel($name = '', $prefix = '') {
        if (empty($name)) {
            $name = $this->getName();
        }

        if (empty($prefix)) {
            $prefix = $this->prefix . 'Model';
        }

        $modelClass = $prefix . ucfirst($name);
        if (empty($this->_models[$modelClass])) {
            if (!class_exists($modelClass)) {
                jimport('joomla.filesystem.path');
                $path = JPath::find($this->paths['model'], $name . '.php' );

                if ($path) {
                    require_once $path;

                    if (!class_exists($modelClass)) {
                        throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_VIEW_CLASS_NOT_FOUND', $modelClass, $path), 500);
                    }
                } else {
                    return null;
                }
            }
            $this->_models[$modelClass] = new $modelClass;
        }

        return $this->_models[$modelClass];
    }

    /**
     * Sets the model's default state based on value in the request
     *
     * @param   jFWModel $model
     *
     * @return  unknown_type
     */
    function _setModelState(jFWModel $model) {
        $ns = $this->_getNamespace();

        $state = array();

        $state['limit'] = $this->app->getUserStateFromRequest($ns . '.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
        $state['limitstart'] = $this->app->getUserStateFromRequest($ns . '.limitstart', 'limitstart', 0, 'int');
        $state['filter_order'] = $this->app->getUserStateFromRequest($ns . '.filter_order', 'filter_order', 'tbl.' . $model->getTable()->getKeyName(), 'cmd');
        $state['filter_order_Dir'] = $this->app->getUserStateFromRequest($ns . '.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
        $state['filter_state'] = $this->app->getUserStateFromRequest($ns . '.filter_state', 'filter_state', '', 'word');
        $state['filter_id'] = $this->app->getUserStateFromRequest($ns . '.filter_id', 'filter_id', '', 'int');
        $state['search'] = $this->app->getUserStateFromRequest($ns . '.search', 'search', '', 'string');
        $state['id'] = $this->input->getInt('id');

        foreach (@$state as $key => $value) {
            $model->getState()->set($key, $value);
        }

        return $state;
    }


    /**
     * Sets the internal message that is passed with a redirect
     *
     * @param   string  $text  Message to display on redirect.
     * @param   string  $type  Message type. Optional, defaults to 'message'.
     *
     * @return  string  Previous message
     *
     * @since   12.2
     */
    public function setMessage($text, $type = 'message')
    {
        $previous = $this->message;
        $this->message = $text;
        $this->messageType = $type;

        return $previous;
    }

    /**
     * Set a URL for browser redirection.
     *
     * @param   string  $url   URL to redirect to.
     * @param   string  $msg   Message to display on redirect. Optional, defaults to value set internally by controller, if any.
     * @param   string  $type  Message type. Optional, defaults to 'message' or the type set by a previous call to setMessage.
     */
    public function Redirect($url, $msg = null, $type = null)
    {
        if ($msg !== null) {
            // Controller may have set this directly
            $this->message = $msg;
        }

        // Ensure the type is not overwritten by a previous call to setMessage.
        if (empty($type)) {
            if (empty($this->messageType)) {
                $this->messageType = 'message';
            }
        } else {
            // If the type is explicitly set, set it.
            $this->messageType = $type;
        }

        $app = JFactory::getApplication();

        // Enqueue the redirect message
        $app->enqueueMessage($this->message, $this->messageType);

        // Execute the redirect
        $app->redirect($url);
    }
}