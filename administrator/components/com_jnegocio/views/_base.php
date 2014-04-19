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

jimport('joomla.view.html');

class jFWView extends JViewHtml
{
    /**
     * The output of the template script.
     *
     * @var    string
     * @since  3.2
     */
    protected $_output = null;

    /**
     * The name of the default template source file.
     *
     * @var    string
     * @since  3.2
     */
    protected $_template = null;

    /**
     * The set of search directories for resources (templates)
     *
     * @var    array
     * @since  3.2
     */
    protected $_path = array('template' => array(), 'helper' => array());

    /**
     * Layout extension
     *
     * @var    string
     * @since  3.2
     */
    protected $_layoutExt = 'php';

    /**
     * Method to instantiate the view.
     *
     * @param   jFWModel          $model  The model object.
     * @param   SplPriorityQueue  $paths  The paths queue.
     */
    public function __construct(jFWModel $model, SplPriorityQueue $paths = null)
    {
        $app = JFactory::getApplication();

        if (isset($paths)) {
            $paths->insert(JPATH_THEMES . '/' . $app->getTemplate() . '/html/' . jFWBase::getComponentName() . '/' . $this->getName(), 2);
        }

        parent::__construct($model, $paths);
    }

    /**
     * Method to get the view name
     *
     * The model name by default parsed using the classname, or it can be set
     * by passing a $config['name'] in the class constructor
     *
     * @return  string  The name of the model
     *
     * @throws  Exception
     */
    public function getName()
    {
        if (empty($this->_name)) {
            $classname = get_class($this);
            $viewpos = strpos($classname, 'View');

            if ($viewpos === false) {
                throw new Exception(JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME'), 500);
            }

            $lastPart = substr($classname, $viewpos + 4);
            $pathParts = explode(' ', JStringNormalise::fromCamelCase($lastPart));

            if (!empty($pathParts[1])) {
                $this->_name = strtolower($pathParts[0]);
            } else {
                $this->_name = strtolower($lastPart);
            }
        }

        return $this->_name;
    }

    /**
     * Returns the model
     *
     * @return  jFWModel  The model object.
     */
    public function getModel() {
        return $this->model;
    }


    /**
     * Display the view
     *
     * @return  string  The rendered view.
     */
    public function render()
    {
        $this->getLayoutVars();

        return parent::render();
    }

    /**
     * Gets layout vars for the view
     */
    function getLayoutVars() {
        $layout = $this->getLayout();
        switch (strtolower($layout)) {
            case "view":
                $this->_form();
                break;
            case "modal":
                JFactory::getApplication()->input->set('hidemainmenu', true);
                $this->_modal();
                break;
            case "form":
                JFactory::getApplication()->input->set('hidemainmenu', true);
                $this->_form();
                break;
            case "default":
            default:
                $this->_default();
                break;
        }
    }

    /**
     * Basic commands for displaying a list
     *
     * @return unknown_type
     */
    function _default() {
        $rows       = null;
        $pageNav    = null;

        $this->displayTitle();
        $this->defaultToolbar();

        try {
            $rows = $this->model->getData();
            $pageNav = $this->model->getPagination();
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        JHtml::_('jquery.framework');
        JHtml::_('script', jFWBase::getUrl('js', false) . 'jquery.searchtools.js',false, true);
        JHtml::_('stylesheet', jFWBase::getUrl('css', false) . 'jquery.searchtools.css', false, true);

        $this->state    = $this->model->getState();

        $selector = "#adminForm";
        $defaultLimit = $this->state->get('limit');
        $doc = JFactory::getDocument();
        $script = "
				(function($){
					$(document).ready(function() {
						$('" . $selector . "').negsearchtools(
							{\"defaultLimit\":\"".$defaultLimit."\",\"formSelector\":\"#adminForm\"}
						);
					});
				})(jQuery);
			";
        $doc->addScriptDeclaration($script);

        $this->items        = &$rows;
        $this->pagination   = &$pageNav;
        $this->action   = 'index.php?option=' . jFWBase::getComponentName() . '&controller=' . $this->_name . '&view=' . $this->_name;
        $this->tmpl	    = JFactory::getApplication()->input->get('tmpl', 'index');
        $this->idkey    = $this->model->getTable()->getKeyName();
    }

    /**
     * Basic methods for displaying an item from a list
     *
     * @param   bool $clearstate
     * @return  bool
     */
    function _form($clearstate = true) {

        $row        = null;
        $document   = JFactory::getDocument();

        if (JFactory::getApplication()->input->get('hidemainmenu', true)) {
            $this->formToolbar();
        }

        try {
            $row = $this->model->getItem($clearstate);
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        $table  = $this->model->getTable();
        $idkey  = $this->model->getTable()->getKeyName();

        // Build the page title string
        $title = $row->$idkey ? JText::_('Edit') : JText::_('New');

        // Set page title
        $document->setTitle($title);
        $this->displayTitle();

        if ($row->$idkey) {
            JFilterOutput::objectHTMLSafe($row);
        }

        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $languages = jFWBase::getClass('HelperLanguages', 'helpers.language', $options)->getAllLanguages();

        $multilang = count($languages) > 1;

        $this->languages = $languages;
        $this->multilang = $multilang;

        $this->row      = &$row;
        $this->state    = $this->model->getState();
        $this->action   = 'index.php';
        $this->tmpl	    = JFactory::getApplication()->input->get('tmpl', 'index');
        $this->idkey    = $idkey;
    }

    /**
     * Displays text as the title of the page
     *
     * @param   string  $text
     * @param   string  $classTitle
     *
     * @return unknown_type
     */
    function displayTitle($text = '', $classTitle = null)
    {
        $list = array('menuoptions');

        $title = $text ? JText::_($text) : JText::_('COM_JNEGOCIO_' . strtoupper($this->getName()));
        if ($classTitle === null) {
            $classTitle = 'COM_JNEGOCIO_' . strtoupper($this->_name);
        }
        $html_title = $title;
        JToolBarHelper::title($html_title, $classTitle);
    }

    /**
     * The default toolbar for a list
     */
    function defaultToolbar() {
        JToolBarHelper::addnew();
        JToolbarHelper::divider();
        JToolBarHelper::editList();
        JToolBarHelper::deleteList();
    }

    /**
     * The default toolbar for editing an item
     */
    function formToolbar() {
        JToolBarHelper::save('save');
        JToolBarHelper::apply('apply');
        JToolBarHelper::cancel();
    }
}