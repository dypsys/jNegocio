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

class jNegocioControllerManufacturers extends jFWControllerCRUD {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->set('suffix', 'manufacturers');
    }

    /**
     * Saves an item and redirects based on task
     * @return void
     */
    function save() {
        $post = JRequest::get('post');
        $config = fwConfig::getInstance();
        $fieldname = 'manufacturer_image_new';
        $userfile = JRequest::getVar($fieldname, '', 'files', 'array');
        $bError = false;

        if (isset($post['delete_image'])) {
            $itemid = $post['manufacturer_id'];
            $File_OldName = $post['attachment'];
            $File_OldUri = $post['locationuri'];
            
            $this->deleteAllImages($File_OldUri, $File_OldName );

            JRequest::setVar('attachment', '');
            JRequest::setVar('locationuri', '');
            JRequest::setVar('locationurl', '');
        }
        
        if (!empty($userfile['size'])) {
            $itemid = $post['manufacturer_id'];
            $File_OldName = $post['attachment'];
            $File_OldUri = $post['locationuri'];

            if ($upload = $this->addfile($fieldname, $itemid)) {
                $cartype_image = $upload->getPhysicalName();
                JRequest::setVar('image', $cartype_image, 'post');
            } else {
                $bError = true;
            }

            if ($bError) {
                $this->messagetype = 'notice';
                $this->message = JText::_('COM_JNEGOCIO_ERROR_SAVE_FAILED') . " on save file";
                $redirect .= '&view=' . $this->get('suffix');
            } else {
                JRequest::setVar('attachment', $upload->getPhysicalName());
                JRequest::setVar('locationuri', $config->get('upload_target_dir') . DS . 'manufacturers' . DS . 'm' . $itemid);
                JRequest::setVar('locationurl', $config->get('upload_target_dir') . '/manufacturers/m' . $itemid );
                $this->deleteAllImages($File_OldUri, $File_OldName );
            }
        }

        if (!$bError) {
            parent::save();
        } else {
            $this->setRedirect($redirect, $this->message, $this->messagetype);
        }
    }

    /**
     * Adds a thumbnail image to item
     * @return unknown_type
     */
    function addfile($fieldname = 'image_upload', $itemid) {
        $config = fwConfig::getInstance();
        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        jFWBase::load('fwImage', 'library.image', $options);

        $upload = new fwImage();
        // handle upload creates upload object properties
        $upload->handleUpload($fieldname);

        $aErrors = $upload->getErrors();
        if ($aErrors) {
            $error_msg = '';
            foreach ($aErrors as $error) {
                $error_msg .= $error;
            }

            $this->messagetype = 'notice';
            $this->message = JText::_('COM_JNEGOCIO_ERROR_SAVE_FAILED') . " - " . $error_msg;
            return false;
        }

        $upload->checkDirectory(JPATH_SITE . DS . $config->get('upload_target_dir'));
        $upload->checkDirectory(JPATH_SITE . DS . $config->get('upload_target_dir') . DS . 'manufacturers');
        $upload->checkDirectory(JPATH_SITE . DS . $config->get('upload_target_dir') . DS . 'manufacturers' . DS . 'm' . $itemid);

        // then save image to appropriate folder
        $upload->setDirectory(JPATH_SITE . DS . $config->get('upload_target_dir') . DS . 'manufacturers' . DS . 'm' . $itemid);

        // Do the real upload!
        $upload->upload();

        $aErrors = $upload->getErrors();
        if ($aErrors) {
            $error_msg = '';
            foreach ($aErrors as $error) {
                $error_msg .= $error;
            }
            $this->messagetype = 'notice';
            $this->message = JText::_('COM_JNEGOCIO_ERROR_SAVE_FAILED') . " - " . $error_msg;
            return false;
        }

        // Thumb
        jFWBase::load('HelperImage', 'helpers.image', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        $imgHelper = HelperImage::getInstance('Image', 'Helper');
        $imgHelper->resizeImage($upload, 'manufacturer', array(
            'width' => fwConfig::getInstance()->get('manufacturer_img_width'),
            'height' => fwConfig::getInstance()->get('manufacturer_img_height'),
            'quality' => fwConfig::getInstance()->get('manufacturer_img_quality'),
            'thumb_path' => $upload->getDirectory() . DS . 'thumbs'
                )
        );

        return $upload;
    }

    function deleteAllImages($localpath , $filename) {
        // $localpath = $dir . DS . 'm' . $itemid;
        $original = JPATH_SITE .DS.  $localpath . DS . $filename;
        $this->deleteImage($original);

        $thumbs = JPATH_SITE .DS. $localpath . DS . 'thumbs' . DS . $filename;
        $this->deleteImage($thumbs);
    }

    protected function deleteImage($name) {
        if (JFile::exists($name)) {
            JFile::delete($name);
        }
    }

}