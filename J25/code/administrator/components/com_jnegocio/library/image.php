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

jFWBase::load('fwFile', 'library.file');

class fwImage extends fwFile {

    private $_image;
    private $_type;
    private $_is_archive = false;

    function fwImage($filename = "") {
        parent::__construct();

        if (!empty($filename)) {
            if (!JFile::exists($filename)) {
                $this->setError("Image does not exist");
                return;
            }
            $this->setFullPath($filename);
            $this->setDirectory(substr($this->full_path, 0, strrpos($this->full_path, DS)));
            $this->setFileName(JFile::getName($filename));

            if (!empty($filename)) {
                $image_info = getimagesize($filename);
                $this->_type = $image_info[2];
            }
        }
    }

    /**
     * Get if files is a compressed file
     */    
    public function get_is_archive() {
        return $this->_is_archive;
    }

    /**
     * Get the image width
     */
    function getWidth() {
        return imagesx($this->_image);
    }

    /**
     * Get the image height
     */
    function getHeight() {
        return imagesy($this->_image);
    }

    function getType() {
        return $this->_type;
    }

    /**
     * Resize the image to a defined height
     * @param $height
     */
    function resizeToHeight($height) {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * Resize the image to a defined width
     * @param $width
     */
    function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * Scale the image to the defined proportion in %
     * @param unknown_type $scale
     */
    function scale($scale) {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }

    /**
     * Load the image!
     */
    function load() {
        $filename = $this->getFullPath();
        $image_info = getimagesize($filename);
        $this->_type = $image_info[2];

        if ($this->_type == IMAGETYPE_JPEG) {
            $this->_image = imagecreatefromjpeg($filename);
        } elseif ($this->_type == IMAGETYPE_GIF) {
            $this->_image = imagecreatefromgif($filename);
        } elseif ($this->_type == IMAGETYPE_PNG) {
            $this->_image = imagecreatefrompng($filename);
        }
    }

    /**
     * Save the image and chmods
     * @param $filename
     * @param $image_type image type: png, gif, jpeg
     * @param $compression
     * @param $permissions
     */
    function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null) {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->_image, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->_image, $filename);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->_image, $filename);
        }

        if ($permissions != null) {
            chmod($filename, $permissions);
        }
        unset($this->_image);
        return true;
    }

    /**
     * Resize the image
     * Based heavily on http://github.com/maxim/smart_resize_image
     * by maxim - thanks man!
     * @param $width
     * @param $height
     */
    function resize($width, $height) {

        $image_resized = imagecreatetruecolor($width, $height);

        if ($this->_type == IMAGETYPE_PNG) {
            $transparency_index = imagecolortransparent($this->_image);
            // If we have a specific transparent color
            if ($transparency_index >= 0) {
                // Get the original image's transparent color's RGB values
                $transparent_color = imagecolorsforindex($this->_image, $transparency_index);

                // Allocate the same color in the new image resource
                $transparency_index = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);

                // Completely fill the background of the new image with allocated color.
                imagefill($image_resized, 0, 0, $transparency_index);

                // Set the background color for new image to transparent
                imagecolortransparent($image_resized, $transparency_index);
            } elseif ($this->_type == IMAGETYPE_PNG) {
                // Always make a transparent background color for PNGs that don't have one allocated already
                // Turn off transparency blending (temporarily)
                imagealphablending($image_resized, false);

                // Create a new transparent color for image
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);

                // Completely fill the background of the new image with allocated color.
                imagefill($image_resized, 0, 0, $color);

                // Restore transparency blending
                imagesavealpha($image_resized, true);
            }
        }

        imagecopyresampled($image_resized, $this->_image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->_image = $image_resized;
    }

    /**
     * Support Zip files for image galleries 
     * @see fwFile::upload()
     */
    function upload() {
        if ($result = parent::upload()) {
            // Check if it's a supported archive
            $allowed_archives = array('zip', 'tar', 'tgz', 'gz', 'gzip', 'tbz2', 'bz2', 'bzip2');

            if (in_array(strtolower($this->getExtension()), $allowed_archives)) {
                $dir = $this->getDirectory();
                jimport('joomla.filesystem.archive');
                JArchive::extract($this->full_path, $dir);
                JFile::delete($this->full_path);

                $this->_is_archive = true;

                $files = JFolder::files($dir);

                // Thumbnails support
                if (count($files)) {
                    // Name correction
                    foreach ($files as &$file) {
                        $file = new fwImage($dir . DS . $file);
                    }

                    $this->archive_files = $files;
                    $this->physicalname = $files[0]->getPhysicalname();
                }
            }
        }
        return $result;
    }

}