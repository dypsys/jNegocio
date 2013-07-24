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

jFWBase::load('jFWHelperBase', 'helpers._base');

class HelperImage extends jFWHelperBase {

    private $category_img_width = 0;
    private $category_img_height = 0;
    private $category_img_quality = 0;
    private $category_img_path = '';
    private $category_thumb_path = '';
    
    private $manufacturer_img_width = 0;
    private $manufacturer_img_height = 0;
    private $manufacturer_img_quality = 0;
    private $manufacturer_img_path = '';
    private $manufacturer_thumb_path = '';

    private $product_img_width = 0;
    private $product_img_height = 0;
    private $product_img_quality = 0;
    private $product_img_path = '';
    private $product_thumb_path = '';
    
    private $_types = array();

    /**
     * Protected! Use the getInstance
     */
    protected function HelperImage() {
        // Parent Helper Construction
        parent::__construct();

        $config = fwConfig::getInstance();

        $this->_types = array('category', 'manufacturer', 'product');

        $this->category_img_width = $config->get('category_img_width');
        $this->category_img_height = $config->get('category_img_height');
        $this->category_img_quality = $config->get('category_img_quality');
        $this->category_img_path = JPATH_SITE .DS. $config->get('upload_target_dir') .DS. 'category';
        $this->category_thumb_path = JPATH_SITE .DS. $config->get('upload_target_dir') .DS. 'category' .DS. 'thumbs';

        $this->manufacturer_img_width = $config->get('manufacturer_img_width');
        $this->manufacturer_img_height = $config->get('manufacturer_img_height');
        $this->manufacturer_img_quality = $config->get('manufacturer_img_quality');
        $this->manufacturer_img_path = JPATH_SITE .DS. $config->get('upload_target_dir') .DS. 'manufacturers';
        $this->manufacturer_thumb_path = JPATH_SITE .DS. $config->get('upload_target_dir') .DS. 'manufacturers' .DS. 'thumbs';

        $this->product_img_width = $config->get('product_img_width');
        $this->product_img_height = $config->get('product_img_height');
        $this->product_img_quality = $config->get('product_img_quality');
        $this->product_img_path = JPATH_SITE .DS. $config->get('upload_target_dir') .DS. 'products';
        $this->product_thumb_path = JPATH_SITE .DS. $config->get('upload_target_dir') .DS. 'products' .DS. 'thumbs';
    }

    /**
     * Resize Image
     * 
     * @param image	jFWImage filename of the image
     * @param type	string	what kind of image: product, category
     * @param options	array	array of options: width, height, thumb_path
     * @return thumb full path
     */
    function resizeImage(&$img, $type = 'manufacturer', $options = array()) {
        if (!$img->get_is_archive()) {
            if (!in_array($type, $this->_types)) {
                $type = 'manufacturer';
            }

            $thumb_path  = $img->getDirectory() . DS . 'thumbs';
            $img_width   = $type . '_img_width';
            $img_height  = $type . '_img_height';
            $img_quality = $type . '_img_quality';

            $img->load();

            // Default width or options width?
            if (!empty($options['width']) && is_numeric($options['width'])) {
                $width = $options['width'];
            } else {
                $width = $this->$img_width;
            }

            // Default height or options height?
            if (!empty($options['height']) && is_numeric($options['height'])) {
                $height = $options['height'];
            } else {
                $height = $this->$img_height;
            }

            // Default height or options height?
            if (!empty($options['quality']) && is_numeric($options['quality'])) {
                $quality = $options['quality'];
            } else {
                $quality = $this->$img_quality;
            }

            // Default thumb path or options thumb path?
            if (!empty($options['thumb_path'])) {
                $dest_dir = $options['thumb_path'];
            } else {
                $dest_dir = $thumb_path;
            }

            $img->checkDirectory($dest_dir);

            if ($width >= $height) {
                $img->resizeToWidth($width);
            } else {
                $img->resizeToHeight($height);
            }

            $dest_path = $dest_dir .DS. $img->getPhysicalName();
            if (!$img->save($dest_path, $img->getType(), $quality)) {
                $this->setError($img->getError());
                return false;
            }
        } else {
            foreach ($img->archive_files as $file) {
                $dest_path = self::resizeImage($file, $type, $options);
            }
        }
        
        return $dest_path;
    }

}