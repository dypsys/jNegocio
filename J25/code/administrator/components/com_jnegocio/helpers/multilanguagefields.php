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

class HelperMultiLanguageFields extends jFWHelperBase {

    static $table = "";
    static $lang = "";
    static $tableFields = array();

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->_LoadTableFields();
    }

    function setTable($table) {
        $this->table = $table;
    }

    function getTable($table) {
        return $this->table;
    }

    function setLang($lang) {
        $this->lang = $lang;
    }

    function getLang($lang) {
        return $this->lang;
    }

    function getField($field) {
        return $field . "_" . $this->lang;
    }

    function addNewFieldLandInTables($lang, $defaultLang = "") {

        if (isset($this) && is_a($this, 'HelperMultiLanguageFields')) {
            $helper = & $this;
        } else {
            $helper = &jFWBase::getClass('HelperMultiLanguageFields', 'helpers.multilanguage', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }

        // echo "llega:".$lang." ".$defaultLang."<br/>";
        $finish = 1;
        $db = & JFactory::getDBO();
        foreach ($helper->tableFields as $table_name_end => $table) {
            $table_name = "#__" . jFWBase::getTablePrefix() . $table_name_end;

            $list_name_field = array();
            $query = 'SHOW FIELDS FROM `' . $table_name . '`';
            // echo "query:".$query."<br/>";
            $db->setQuery($query);
            $fields = $db->loadObjectList();
            foreach ($fields as $field) {
                $list_name_field[] = $field->Field;
            }

            //filter existent field
            foreach ($table as $k => $field) {
                if (in_array($field[0] . "_" . $lang, $list_name_field)) {
                    unset($table[$k]);
                }
            }

            $sql_array_add_field = array();
            foreach ($table as $field) {
                $name = $field[0] . "_" . $lang;
                $sql_array_add_field[] = "ADD `" . $name . "` " . $field[1];
            }

            $sql_array_update_field = array();
            foreach ($table as $field) {
                $name = $field[0] . "_" . $lang;
                $name2 = $field[0] . "_" . $defaultLang;
                if (in_array($name2, $list_name_field)) {
                    $sql_array_update_field[] = " `" . $name . "` = `" . $name2 . "`";
                }
            }

            if (count($sql_array_add_field)) {
                $query = "ALTER TABLE `" . $table_name . "` " . implode(", ", $sql_array_add_field);
                $db->setQuery($query);
                if (!$db->query()) {
                    JError::raiseWarning(500, "Error install new language:<br>" . $db->getErrorMsg());
                    $finish = 0;
                }

                //copy information
                if ($defaultLang != "" && count($sql_array_update_field)) {
                    $query = "update `" . $table_name . "` set " . implode(", ", $sql_array_update_field);
                    $db->setQuery($query);
                    if (!$db->query()) {
                        JError::raiseWarning(500, "Error copy new language:<br>" . $db->getErrorMsg());
                        $finish = 0;
                    }
                }
            }
        }
        return $finish;
    }

    function _LoadTableFields() {

        $f = array();
        $f[] = array("name", "varchar(255) NOT NULL");
        $this->tableFields["countries"] = $f;

//        $f = array();
//        $f[] = array("name", "varchar(255) NOT NULL");
//        $this->tableFields["states"] = $f;
        
        $f = array();
        $f[] = array("name", "varchar(255) NOT NULL");
        $f[] = array("alias", "varchar(255) NOT NULL");
        $f[] = array("description", "text NOT NULL");
        $f[] = array("meta_title", "varchar(255) NOT NULL");
        $f[] = array("meta_description", "text NOT NULL");
        $f[] = array("meta_keyword", "text NOT NULL");
        $this->tableFields["manufacturers"] = $f;

        $f = array();
        $f[] = array("name", "varchar(255) NOT NULL");
        $f[] = array("alias", "varchar(255) NOT NULL");
        $f[] = array("description", "text NOT NULL");
        $f[] = array("meta_title", "varchar(255) NOT NULL");
        $f[] = array("meta_description", "text NOT NULL");
        $f[] = array("meta_keyword", "text NOT NULL");
        $this->tableFields["categories"] = $f;
        
        $f = array();
        $f[] = array("name", "varchar(255) NOT NULL");
        $f[] = array("alias", "varchar(255) NOT NULL");
        $f[] = array("shortdesc", "text NOT NULL");
        $f[] = array("description", "text NOT NULL");
        $f[] = array("meta_title", "varchar(255) NOT NULL");
        $f[] = array("meta_description", "text NOT NULL");
        $f[] = array("meta_keyword", "text NOT NULL");
        $this->tableFields["products"] = $f;
 
        $f = array();
        $f[] = array("alt", "varchar(255) NOT NULL");
        $this->tableFields["productimages"] = $f;
        
        $f = array();
        $f[] = array("name", "varchar(255) NOT NULL");
        $f[] = array("label", "varchar(255) NOT NULL");
        $this->tableFields["attributes"] = $f;
        
        $f = array();
        $f[] = array("name", "varchar(255) NOT NULL");
        $this->tableFields["attributes_values"] = $f;        
    }

}