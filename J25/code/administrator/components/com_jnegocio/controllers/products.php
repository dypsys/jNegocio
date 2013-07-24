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

class jNegocioControllerProducts extends jFWControllerCRUD {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->set('suffix', 'products');

        $this->registerTask('uploadmedia', 'uploadmedia');

        $this->registerTask('imageorderup', 'imageorderup');
        $this->registerTask('imageorderdown', 'imageorderdown');
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
        $state['filter_manufacturerid'] = $app->getUserStateFromRequest($ns . '.filter_manufacturerid', 'filter_manufacturerid', '', '');
        $state['filter_groupid'] = $app->getUserStateFromRequest($ns . '.filter_groupid', 'filter_groupid', '', '');

        foreach (@$state as $key => $value) {
            $model->setState($key, $value);
        }

        return $state;
    }
    
    /**
     * Logic to orderup a gallery
     *
     * @access public
     * @return void
     */
    function imageorderup() {
        $error = false;
        $this->messagetype = '';
        $this->message = '';

        $tmpl = JRequest::getCmd('tmpl', 'index');
        $aGalley_id = JRequest::getVar('productimage_id', array(0), '', 'array');
        $returnid = JRequest::getVar('product_id', JRequest::getVar('product_id', '0', 'post', 'int'), 'get', 'int');
        $redirect = 'index.php?option=' . jFWBase::getComponentName() . '&view=' . $this->get('suffix') . '&task=edit&cid[]=' . $returnid;
        if ($tmpl) {
            $redirect .= '&tmpl=' . $tmpl;
        }
        $redirect = JRoute::_($redirect, false);

        $modelimages = JModel::getInstance('Productimages', 'jNegocioModel');
        $modelimages->setId((int) $aGalley_id[0]);

        if (!$modelimages->move(-1)) {
            $this->messagetype = 'notice';
            $this->message = JText::_('Ordering Failed') . " - " . $modelimages->getError();
        }

        $this->setRedirect($redirect, $this->message, $this->messagetype);
    }

    /**
     * Logic to orderdown a category
     *
     * @access public
     * @return void
     */
    function imageorderdown() {
        $error = false;
        $this->messagetype = '';
        $this->message = '';
        $tmpl = JRequest::getCmd('tmpl', 'index');
        $aGalley_id = JRequest::getVar('productimage_id', array(0), '', 'array');
        $returnid = JRequest::getVar('product_id', JRequest::getVar('product_id', '0', 'post', 'int'), 'get', 'int');
        $redirect = 'index.php?option=' . jFWBase::getComponentName() . '&view=' . $this->get('suffix') . '&task=edit&cid[]=' . $returnid;
        if ($tmpl) {
            $redirect .= '&tmpl=' . $tmpl;
        }
        $redirect = JRoute::_($redirect, false);

        $modelimages = JModel::getInstance('Productimages', 'jNegocioModel');
        $modelimages->setId((int) $aGalley_id[0]);

        if (!$modelimages->move(1)) {
            $this->messagetype = 'notice';
            $this->message = JText::_('Ordering Failed') . " - " . $modelimages->getError();
        }

        $this->setRedirect($redirect, $this->message, $this->messagetype);
    }

    function uploadmedia() {
        if (!JRequest::checkToken('get')) {
            $this->_setResponse(400, JText::_('JINVALID_TOKEN'));
        }

        $session = JFactory::getSession();
        $user = JFactory::getUser();
        $config = fwConfig::getInstance();
        $itemid = JRequest::getVar('itemid', JRequest::getVar('itemid', '0', 'post', 'int'), 'get', 'int');
        $baseDirImg = JPATH_SITE . DS . $config->get('upload_target_dir') . DS . 'products' . DS . 'p' . $itemid;

        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jinmo');
        jFWBase::load('fwImage', 'library.image', $options);

        $fileNameFromReq = JRequest::getVar('name', '', 'request');

        $upload = new fwImage();

        $upload->handleUpload('file');
        $upload->checkDirectory(JPATH_SITE . DS . $config->get('upload_target_dir'));
        $upload->checkDirectory(JPATH_SITE . DS . $config->get('upload_target_dir') . DS . 'products');
        $upload->checkDirectory(JPATH_SITE . DS . $config->get('upload_target_dir') . DS . 'products' . DS . 'p' . $itemid);

        $upload->setDirectory(JPATH_SITE . DS . $config->get('upload_target_dir') . DS . 'products' . DS . 'p' . $itemid);

        $upload->setFileName($fileNameFromReq);
        $upload->plupload();

        $aErrors = $upload->getErrors();
        if ($aErrors) {
            $error_msg = '';
            foreach ($aErrors as $error) {
                $error_msg .= $error;
            }
            $this->_setResponse(101, $error_msg);
        }

        if (!$upload->getPartesTotal() || $upload->getPartesNum() == ($upload->getPartesTotal() - 1)) {
            // Ya se han subido todas las partes

            jFWBase::load('HelperImage', 'helpers.image', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
            $imgHelper = HelperImage::getInstance('Image', 'Helper');
            $imgHelper->resizeImage($upload, 'product', array(
                'width' => fwConfig::getInstance()->get('product_img_width'),
                'height' => fwConfig::getInstance()->get('product_img_height'),
                'quality' => fwConfig::getInstance()->get('product_img_quality'),
                'thumb_path' => $upload->getDirectory() . DS . 'full'
                    )
            );

            $aErrors = $imgHelper->getErrors();
            if ($aErrors) {
                $error_msg = '';
                foreach ($aErrors as $error) {
                    $error_msg .= $error;
                }
                $this->_setResponse(101, $error_msg);
            }

            $imgHelper->resizeImage($upload, 'product', array(
                'width' => fwConfig::getInstance()->get('product_list_width'),
                'height' => fwConfig::getInstance()->get('product_list_height'),
                'quality' => fwConfig::getInstance()->get('product_list_quality'),
                'thumb_path' => $upload->getDirectory() . DS . 'list'
                    )
            );

            $aErrors = $imgHelper->getErrors();
            if ($aErrors) {
                $error_msg = '';
                foreach ($aErrors as $error) {
                    $error_msg .= $error;
                }
                $this->_setResponse(101, $error_msg);
            }

            $imgHelper->resizeImage($upload, 'product', array(
                'width' => fwConfig::getInstance()->get('product_thumb_width'),
                'height' => fwConfig::getInstance()->get('product_thumb_height'),
                'quality' => fwConfig::getInstance()->get('product_thumb_quality'),
                'thumb_path' => $upload->getDirectory() . DS . 'thumb'
                    )
            );

            $aErrors = $imgHelper->getErrors();
            if ($aErrors) {
                $error_msg = '';
                foreach ($aErrors as $error) {
                    $error_msg .= $error;
                }
                $this->_setResponse(101, $error_msg);
            }

            $tblimage = JTable::getInstance('productimages', jFWBase::getTablePrefix());
            $tblimage->product_id = $itemid;
            $tblimage->locationuri = $config->get('upload_target_dir') . DS . 'products' . DS . 'p' . $itemid;
            $tblimage->locationurl = $config->get('upload_target_dir') . '/products/p' . $itemid;
            $tblimage->attachment = $upload->getPhysicalName();

            $where = 'product_id = ' . (int) $itemid;
            $tblimage->ordering = $tblimage->getNextOrder($where);

            // 	Make sure the data is valid
            if (!$tblimage->check()) {
                $this->_setResponse(101, $tblimage->getError());
            }

            if (!$tblimage->store()) {
                $this->_setResponse(101, $tblimage->getError());
            }

            $this->_setResponse(0, null, false);
        } else {
            // Aun quedan partes por subir
            $this->_setResponse(0, null, false);
        }
    }

    /**
     * Saves an item and redirects based on task
     * @return void
     */
    function save() {
        // Check for request forgeries
        JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jinmo');
        $languages = jFWBase::getClass('HelperLanguages', 'helpers.language', $options)->getAllLanguages();
        
        $app    = JFactory::getApplication();
        $db     = & JFactory::getDBO();
        $tmpl   = JRequest::getCmd('tmpl', 'index');
        $task   = JRequest::getVar('task');
        $bError = false;
        $this->_itemtable = null;

        //Sanitize
        $post = JRequest::get('post');
        foreach ($languages as $lang) {
            $text_name = "description_" . $lang->language;
            $post[$text_name] = JRequest::getVar($text_name, '', 'post', 'string', JREQUEST_ALLOWRAW);
        }

        $model = $this->getModel($this->get('suffix'));

        if ($task == 'save_as') {
            $pk = $model->getTable()->getKeyName();
            $post[$pk] = 0;
        }

        if ($task == 'resethits') {
            $post['hits'] = 0;
        }

        $redirect = "index.php?option=" . jFWBase::getComponentName();

        // echo var_dump($post)."<hr/>";
        $returnid = $model->store($post);
        // echo "returnid:".$returnid."<br/>";

        if ($returnid) {
            $this->_itemtable = $model->getTable();
            $this->_itemtable->load($returnid);
            $dispatcher = JDispatcher::getInstance();
            $dispatcher->trigger('onAfterSave' . $this->get('suffix'), array($this->_itemtable));

            if (!$bError) {
                $post_categories = JRequest::getVar('categories', array(), '', 'array');
                foreach ($post_categories as $key => $categoria) {
                    if (!$this->_itemtable->InCategory($returnid, $categoria)) {
                        $query = "INSERT INTO `#__nec_productcategory` SET " .
                                "`product_id` = '" . $db->getEscaped($returnid) . "', " .
                                "`category_id` = '" . $db->getEscaped($categoria) . "' "
                        ;
                        $db->setQuery($query);
                        $db->query();
                    }
                }

                //delete other cat for product        
                $query = "select `category_id` from `#__nec_productcategory` where `product_id` = '" . $db->getEscaped($returnid) . "'";
                $db->setQuery($query);
                $listcat = $db->loadObjectList();
                foreach ($listcat as $val) {
                    if (!in_array($val->category_id, $post_categories)) {
                        $query = "delete from `#__nec_productcategory` where `product_id` = '" . $db->getEscaped($returnid) . "' and `category_id` = '" . $db->getEscaped($val->category_id) . "'";
                        $db->setQuery($query);
                        $db->query();
                    }
                }
            }

            // Prices
            if (!$bError) {
                $post_prdprices = JRequest::getVar('prdprices', array(), '', 'array');
                
                // echo var_dump($post_prdprices) . "<hr/>";
                // echo "count:" . count($post_prdprices) . "<hr/>";
                
                foreach ($post_prdprices as $key => $prdprice) {
                    
                    // echo var_dump($prdprice) . "<hr/>";
                    
                    $productprice_id = $prdprice['productprice_id'];
                    $product_price = $prdprice['price'];
                    $product_priceincltax = $prdprice['priceincltax'];
                    $product_discount = $prdprice['discount'];
                    // $product_price_startdate = $prdprice['startdate'];
                    // $product_price_enddate = $prdprice['enddate'];
                    $group_id = $prdprice['group_id'];
                    $price_quantity_start = $prdprice['qntstart'];
                    $price_quantity_end = $prdprice['qntend'];
                    $delete = $prdprice['deleted'];

                    unset($tblprice);
                    $tblprice = JTable::getInstance('Productprices', jFWBase::getTablePrefix());
                    $tblprice->load($productprice_id);
                    
                    if (!$tblprice->productprice_id) {
                        // Es Nuevo
                        if (!$delete) {
                            $tblprice->productprice_id = 0;
                            $tblprice->product_id = $returnid;
                            $tblprice->product_price = $product_price;
                            $tblprice->product_priceincltax = $product_priceincltax;
                            $tblprice->product_discount = $product_discount;
                            // $tblprice->product_price_startdate = $product_price_startdate;
                            // $tblprice->product_price_enddate = $product_price_enddate;
                            $tblprice->group_id = $group_id;
                            $tblprice->price_quantity_start = $price_quantity_start;
                            $tblprice->price_quantity_end = $price_quantity_end;

                            $where = 'product_id = ' . (int) $returnid . ' AND group_id = '.$group_id;
                            $tblprice->ordering = $tblprice->getNextOrder($where);

                            // 	Make sure the data is valid
                            if (!$tblprice->check()) {
                                $bError = true;
                                $app->enqueueMessage($tblprice->getError(), 'warning');
                            }

                            if (!$tblprice->store()) {
                                $bError = true;
                                $app->enqueueMessage($tblprice->getError(), 'warning');
                            }
                            $tblprice->reorder($where);
                        }
                    } else {
                        // Es Modificacion
                        if ($delete) {
                            $tblprice->delete($productprice_id);
                        } else {
                            $tblprice->product_id = $returnid;
                            $tblprice->product_price = $product_price;
                            $tblprice->product_priceincltax = $product_priceincltax;
                            $tblprice->product_discount = $product_discount;
                            // $tblprice->product_price_startdate = $product_price_startdate;
                            // $tblprice->product_price_enddate = $product_price_enddate;
                            $tblprice->group_id = $group_id;
                            $tblprice->price_quantity_start = $price_quantity_start;
                            $tblprice->price_quantity_end = $price_quantity_end;

                            // 	Make sure the data is valid
                            if (!$tblprice->check()) {
                                $bError = true;
                                $app->enqueueMessage($tblprice->getError(), 'warning');
                            }

                            if (!$tblprice->store()) {
                                $bError = true;
                                $app->enqueueMessage($tblprice->getError(), 'warning');
                            }
                            $where = 'product_id = ' . (int) $returnid . ' AND group_id = '.$group_id;
                            // $tblprice->ordering = $tblprice->getNextOrder($where);
                            $tblprice->reorder($where);
                        }
                    }
                }
                
                // die();
            }
            
            if (!$bError) {
                $bError = $this->saveAttributes($returnid);
            }
            
            // Image Gallery
            if (!$bError) {
                $galley_total = JRequest::getVar('image_total', 0, 'post', 'int');
                $post_array = JRequest::getVar('gallery', array(), '', 'array');

                for ($i = 0; $i <= ($galley_total - 1); $i++) {
                    unset($tblgallery);
                    $tblgallery = JTable::getInstance('Productimages', jFWBase::getTablePrefix());
                    $id_gallery = $post_array[$i]['productimage_id'];
                    $tblgallery->load($id_gallery);

                    if ($tblgallery->productimage_id) {
                        if (isset($post_array[$i]['delete'])) {
                            // echo "Se debe borrar la imagen<br/>\n";
                            $this->deleteAllImages($tblgallery->locationuri, $tblgallery->attachment);
                            $tblgallery->delete($id_gallery);
                        } else {
                            $tblgallery->product_id = $returnid;

                            foreach ($languages as $lang) {
                                $namefield = 'alt_' . $lang->language;
                                $tblgallery->$namefield = $post_array[$i][$namefield];
                            }

                            //$tblgallery->alt 		= $post_array[$i]['alt'];
                            $tblgallery->store();
                        }
                    } else {
                        // echo "Not load Gallery id:".$id_gallery."<br/>\n";
                    }
                }
            }
            
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
        // die();
        $this->setRedirect($redirect, $this->message, $this->messagetype);
    }

    function saveAttributes($idProduct) {
        $lreturn = true;
        
        $post_prdattr = JRequest::getVar('prdattr', array(), '', 'array');
        
        $Attr_id = array();
        $AttrValues_id = array();
        $nTotalAttr = JRequest::getInt('pa_tmp_total_attr', 0);
        $post_AddPrdAttr = JRequest::getVar('pa_tmp_attr_id', array(), '', 'array');
        for ($i = 0; $i <= ($nTotalAttr - 1); $i++) {
            $Attr_id[] = $post_AddPrdAttr[$i]['pa_tmp_attr_id'];
            $strAttrValueid = 'pa_tmp_attrvalue_'.$post_AddPrdAttr[$i]['pa_tmp_attr_id'];
            $AttrValues_id = JRequest::getInt( $strAttrValueid, 0);
        }
        
        return $lreturn;
    }
    
    function deleteAllImages($localpath , $filename) {
        // $localpath = $dir . DS . 'm' . $itemid;
        $original = JPATH_SITE .DS.  $localpath . DS . $filename;
        $this->deleteImage($original);

        $fullimage = JPATH_SITE .DS.  $localpath . DS . 'full' . DS . $filename;
        $this->deleteImage($fullimage);
        
        $medianimage = JPATH_SITE .DS.  $localpath . DS . 'list' . DS . $filename;
        $this->deleteImage($medianimage);
        
        $thumbs = JPATH_SITE .DS. $localpath . DS . 'thumb' . DS . $filename;
        $this->deleteImage($thumbs);
    }

    protected function deleteImage($name) {
        if (JFile::exists($name)) {
            JFile::delete($name);
        }
    }
    
    /**
     * Deletes record(s) and redirects to default layout
     */
    function delete() {
        $error = false;
        $db = & JFactory::getDBO();
        
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
            
            // Delete Images
            foreach( $this->_itemtable->getImages($cid) as $itemimage) {
                $tblgallery = JTable::getInstance('Productimages', jFWBase::getTablePrefix());
                $tblgallery->load($itemimage->productimage_id);

                if ($tblgallery->productimage_id) {
                    $this->deleteAllImages($tblgallery->locationuri, $tblgallery->attachment);
                    $tblgallery->delete($itemimage->productimage_id);
                }
            }
            
            // Delete Categories asociates
            $query = "delete from `#__nec_productcategory` where `product_id` = '" . $db->getEscaped($cid) . "'";
            $db->setQuery($query);
            $db->query();
            
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
    
    private function _setResponse($code, $msg = null, $error = true) {
        JResponse::setHeader('Content-Type', 'application/json; charset=utf-8');

        if ($error) {
            $jsonrpc = array(
                "error" => 1,
                "code" => $code,
                "msg" => $msg
            );
        } else {
            $jsonrpc = array(
                "error" => 0,
                "code" => $code,
                "msg" => "File uploaded!"
            );
        }

        if (function_exists('json_encode')) {
            echo json_encode($jsonrpc);
        } else {
            //--seems we are in PHP < 5.2... or json_encode() is disabled
            require JPATH_COMPONENT_ADMINISTRATOR . DS . 'library' . DS . 'json.php';
            $json = new Services_JSON();
            echo $json->encode($jsonrpc);
        }

        // Close the application.
        $app = JFactory::getApplication();
        $app->close();
    }

}