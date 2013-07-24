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

// set the options array
$options = array('site' => 'site', 'type' => 'components', 'ext' => 'com_jnegocio');
jFWBase::load('jFWFrontController', 'controllers._base', $options);

$options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
jFWBase::load('HelperProduct', 'helpers.product', $options);
jFWBase::load('HelperCurrency', 'helpers.currency', $options);

class jNegocioControllerProduct extends jFWFrontController {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->set('suffix', 'product');
    }
    
    /**
     * Displays a single product
     * (non-PHPdoc)
     */
    function display($cachable = false) {
        $app = JFactory::getApplication();

        JRequest::setVar('view', $this->get('suffix'));
        $model = $this->getModel($this->get('suffix'));
        $id = $model->getId();
        // echo "id:".$id."<br/>";

        $row = $model->getItem(false); // use the state
        $row = HelperProduct::setProduct($row);
        $table = $model->getTable();
        $idkey = $table->getKeyName();
	$state	= $model->getState();

        // echo "controller:".var_dump($row)."<hr/>";
        
        $this->_setModelState();

        if (empty($row->published)) {
            $redirect = "index.php?option=com_jnegocio&view=products&task=display";
            $redirect = JRoute::_($redirect, false);
            $this->message = JText::_("COM_JNEGOCIO_CANNOT_VIEW_DISABLED_PRODUCTS");
            $this->messagetype = 'notice';
            $this->setRedirect($redirect, $this->message, $this->messagetype);
            return;
        }
        
//        if (empty($this->row->name)) {
//            $pageTitle = $this->escape($this->row->name) ;
//	}
        
        $qnt = 1;
        $pageTitle = $row->name;
        
        $modelgalleries = null;
        $images = null;
        if ($row->$idkey) {
            // Load galleries
            JFilterOutput::objectHTMLSafe( $row );
            
            $categories_select = $table->getCategories($row->$idkey);
            
            $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
            $modelimages = jFWBase::getClass('jNegocioModelProductimages', 'models.productimages', $options);
            $modelimages->emptyState();
            $modelimages->setState( 'filter_productid', $row->$idkey );
            $modelimages->setState( 'filter_order', 'tbl.ordering' );
            $modelimages->setState( 'limit', 0);
            $images = $modelimages->getData();
        }
        
        $view = $this->getView( $this->get( 'suffix' ), JFactory::getDocument( )->getType( ) );
        
        $product = HelperProduct::prepareProductforAddCar($row->$idkey, $row);
        
	$view->setModel( $model, true );
	// $view->setModel( $modelgalleries , false );
        
	$view->assign( 'idkey',         $idkey );
        $view->assign( 'row',           $row );
        $view->assign( 'state',         $state );
        $view->assign( 'images',        $images );
        $view->assign( 'page_title',    $pageTitle );
        
	$view->display( );
	return;        
    }
}
