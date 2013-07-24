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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * File Building Class.
 *
 * @package		Joomla.Framework
 * @subpackage	FrameWork
 * @since		1.6
 */
class fwFile extends JObject {

    private $_full_path;
    private $_directory;
    private $_extension;
    private $_physicalname;
    private $_fileName;
    private $_filesize;
    private $_filePath;
    private $_proper_name;
    private $_partes_num = 0;
    private $_partes_total = 0;

    /**
     * A hack to support __construct()
     *
     * @access	public
     * @return	Object
     * @since	1.5
     */
    function fwFile() {
        $args = func_get_args();
        call_user_func_array(array(&$this, '__construct'), $args);
    }

    /**
     * Object constructor
     *
     * Can be overloaded/supplemented by the child class
     *
     * @param 	object 	An optional Config object with configuration options.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns a reference to a global Editor object, only creating it
     * if it doesn't already exist.
     *
     * @access	public
     * @return	fwFile Class object.
     */
    function & getInstance() {
        static $instance;
        if (!isset($instance)) {
            $instance = new fwFile();
        }
        return $instance;
    }

    /**
     * Assign the directory 
     * @param string 
     * @return string
     */
    function setDirectory($dir = null) {
        $success = false;

        // checks to confirm existence of directory
        // then confirms directory is writeable		
        if ($dir === null) {
            $dir = $this->getDirectory();
        }

        $this->checkDirectory($dir);
        // then confirms existence of htaccess file
        $success = $this->createHtAccess($dir);

        $this->_directory = $dir;
        return $this->_directory;
    }

    /**
     * Get the Directory (Path) of file
     * @param string default media directory
     * @return string
     */
    function getDirectory($media = 'images') {
        if (!isset($this->_directory)) {
            $this->_directory = jFWBase::getPath($media);
        }
        return $this->_directory;
    }

    /**
     * Set the name of file
     * @param string 
     */
    function setFileName($filename = '') {
        if (empty($filename)) {
            $filename = $this->_proper_name;
        }
        $this->_fileName = JFile::getName($filename);
    }

    /**
     * Returns the extension of file
     * @param string filename
     * @return string
     */
    function getExtension($filename = '') {
        if (empty($filename)) {
            $filename = $this->_proper_name;
        }

        if (!isset($this->_extension)) {
            $namebits = explode('.', $filename);
            $this->_extension = $namebits[count($namebits) - 1];
        }

        return $this->_extension;
    }

    /**
     * Returns a unique physical name
     * @param string filename
     * @param Boolean ofuscate name hash(time())
     * @return string
     */
    function getPhysicalName($originalfilename = '', $obfuscate = false) {
        if (!empty($this->_physicalname)) {
            return $this->_physicalname;
        }

        if (empty($originalfilename)) {
            $originalfilename = $this->_fileName;
        }

        if ($obfuscate) {
            $dir = $this->getDirectory();
            $extension = $this->getExtension($originalfilename);
            $name = JUtility::getHash(time());
            $physicalname = $name . "." . $extension;

            while ($fileexists = &JFile::exists($dir . DS . $physicalname)) {
                $name = JUtility::getHash(time());
                $physicalname = $name . "." . $extension;
                $tmp_physicalname = $name;
            }
        } else {
            $name = explode('.', $originalfilename);
            $extension = $name[count($name) - 1];
            $tmp_physicalname = $this->cleanTitle($name[0]);
        }

        $this->_physicalname = $tmp_physicalname . '.' . $extension;

        return $this->_physicalname;
    }

    /**
     * Get Full file Path
     */
    function getFullPath() {
        return $this->_full_path;
    }

    /**
     * Set de full Path
     * @param string $strFullName
     */
    function setFullPath($strFullName) {
        $this->_full_path = $strFullName;
    }

    function getPartesNum() {
        return $this->_partes_num;
    }

    function getPartesTotal() {
        return $this->_partes_total;
    }

    /**
     * Check if the path exists, and if not, tries to create it
     * @param string $dir
     * @param bool $create
     */
    function checkDirectory($dir, $create = true) {
        $return = true;
        if (!$exists = &JFolder::exists($dir)) {
            if ($create) {
                $return = &JFolder::create($dir);
            } else {
                $return = false;
            }
        }

        if ($return) {
            // then confirms existence of htaccess file
            $return = $this->createHtAccess($dir);
        }

        $change = &JPath::setPermissions($dir);
        return ($return && $change);
    }

    /**
     * Create a file .htaccess
     * @param string $dir
     * 
     * @return bool $create
     */
    function createHtAccess($dir) {
        $return = true;
        // $htaccess = $dir . DS . '.htaccess';
        $htaccess = $dir . DS . 'index.html';
        if (!$fileexists = &JFile::exists($htaccess)) {
            $destination = $htaccess;
            $text = "<!DOCTYPE html><title></title>";
            // $text = "deny from all";
            if (!JFile::write($destination, $text)) {
                $this->setError(JText::_('JI_LIB_STORAGE_DIRECTORY_IS_UNPROTECTED'));
            } else {
                $return = false;
            }
        }
        return $return;
    }

    /**
     * Returns a cleaned title
     * @param mixed Boolean
     * @param mixed Boolean
     * @return array
     */
    function cleanTitle($title, $length = '64') {
        // trim whitespace
        $trim_title = strtolower(trim(str_replace(" ", "", $title)));

        // strip all html tags
        $wc = strip_tags($trim_title);

        // remove 'words' that don't consist of alphanumerical characters or punctuation
        $pattern = "#[^(\w|\d|\'|\"|\.|\!|\?|;|,|\\|\/|\-|:|\&|@)]+#";
        $wc = trim(preg_replace($pattern, "", $wc));

        // remove one-letter 'words' that consist only of punctuation
        $wc = trim(preg_replace("#\s*[(\'|\"|\.|\!|\?|;|,|\\|\/|\-|:|\&|@)]\s*#", "", $wc));

        // remove superfluous whitespace
        $wc = preg_replace("/\s\s+/", "", $wc);

        // cut title to length
        $cut_title = substr($wc, 0, $length);

        $data = $cut_title;

        return $data;
    }

    /**
     * Returns 
     * @param mixed Boolean
     * @param mixed Boolean
     * @return object
     */
    function handleUpload($fieldfile = 'file', $upload_config = array()) {
        $success = false;
        $upload_config = (array) $upload_config;

        $files_maxsize = isset($upload_config['upload_maxsize']) ? $upload_config['upload_maxsize'] : fwConfig::getInstance()->get('max_file_size', '2097152');

        // Check if file uploads are enabled
        if (!(bool) ini_get('file_uploads')) {
            $this->setError(JText::_('Uploads Disabled'));
            return $success;
        }

        // Check that the zlib is available
        if (!extension_loaded('zlib')) {
            $this->setError(JText::_('ZLib Unavailable'));
            return $success;
        }

        // check that upload exists
        $userfile = JRequest::getVar($fieldfile, '', 'files', 'array');

        if (!$userfile) {
            $this->setError(JText::_('No File'));
            return $success;
        }

        $this->_proper_name = basename($userfile['name']);

        if ($userfile['size'] == 0) {
            $this->setError(JText::_('Invalid File'));
            return $success;
        }

        $this->_filesize = $userfile['size'] / 1024;

        // check size of upload against max set in config
        if ($this->_filesize > $files_maxsize) {
            $this->setError(JText::_('Invalid File Size'));
            return $success;
        }

        $this->_filesize = number_format($this->_filesize, 2) . ' Kb';

        if (!is_uploaded_file($userfile['tmp_name'])) {
            $this->setError(JText::_('Invalid File'));
            return $success;
        } else {
            $this->_filePath = $userfile['tmp_name'];
        }

        // echo "file_path:".$file_path."<br/>";
        $this->getExtension($this->_proper_name);
        $this->setFileName($this->_proper_name);
        $this->uploaded = true;
        $success = true;
        return $success;
    }

    /**
     * Do the real upload
     */
    function upload() {
        // path
        $dest = $this->getDirectory() . DS . $this->getPhysicalName();
        // delete the file if dest exists
        if ($fileexists = JFile::exists($dest)) {
            JFile::delete($dest);
        }
        // save path and filename or just filename
        if (!JFile::upload($this->_filePath, $dest)) {
            $this->setError(sprintf(JText::_("Move failed from %s, to file %s"), $this->_filePath, $dest));
            return false;
        }

        $this->_full_path = $dest;
        return true;
    }

    /**
     * Do the real upload
     */
    function plupload() {
        // path
        $dest = $this->getDirectory() . DS . $this->getPhysicalName();
        // delete the file if dest exists
        if ($fileexists = JFile::exists($dest)) {
            JFile::delete($dest);
        }

        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];
        }

        if (isset($_SERVER["CONTENT_TYPE"])) {
            $contentType = $_SERVER["CONTENT_TYPE"];
        }

        // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        if (strpos($contentType, "multipart") !== false) {
            // echo "tiene multipart<br/>\n";
            // Open temp file
            $out = fopen("{$dest}.part", $this->_partes_num == 0 ? "wb" : "ab");
            // echo "out File: {$dest}.part<br/>\n";
            if ($out) {
                // Read binary input stream and append it to temp file
                // echo "in File: {$this->_filePath}<br/>\n";
                $in = fopen($this->_filePath, "rb");
                if ($in) {
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                } else {
                    // echo "error:Failed to open input stream.\n";
                    $this->setError(JText::_('Failed to open input stream.'));
                    // $this->_setResponse (101, "Failed to open input stream.");
                }
                fclose($in);
                fclose($out);
                JFile::delete($this->_filePath);
            } else {
                // echo "error:Failed to open output stream.\n";
                $this->setError(JText::_('Failed to open output stream.'));
                // $this->_setResponse (102, "Failed to open output stream.");
            }
        } else {
            // echo "NO tiene multipart<br/>";
            // Open temp file
            $out = fopen("{$dest}.part", $this->_partes_num == 0 ? "wb" : "ab");
            // echo "out File: {$dest}.part<br/>\n";   
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");
                if ($in) {
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                } else {
                    // echo "error:Failed to open input stream.\n";
                    $this->setError(JText::_('Failed to open input stream..'));
                    // $this->_setResponse (101, "Failed to open input stream.");
                }
                fclose($in);
                fclose($out);
            } else {
                // echo "error:Failed to open output stream.\n";
                $this->setError(JText::_('Failed to open output stream..'));
                // $this->_setResponse (102, "Failed to open output stream.");
            }
        }

        // Check if file has been uploaded
        if (!$this->_partes_total || $this->_partes_num == ($this->_partes_total - 1)) {
            // Strip the temp .part suffix off
            @rename("{$dest}.part", $dest);
        }
        // save path and filename or just filename
        // if (!JFile::upload($this->file_path, $dest)) {
        //	$this->setError( sprintf( JText::_("Move failed from %s, to file %s"), $this->file_path, $dest) );
        //	return false;			
        // }

        $this->_full_path = $dest;
        return true;
    }

}