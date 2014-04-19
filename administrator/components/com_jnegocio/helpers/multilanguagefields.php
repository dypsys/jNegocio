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

jFWBase::load('jFWHelperBase', 'helpers._base');

class HelperMultiLanguageFields extends jFWHelperBase
{
    protected $table = "";
    protected $lang = "";
    protected $tableFields = array();

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->_LoadTableFields();
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function getTable($table)
    {
        return $this->table;
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function getLang($lang)
    {
        return $this->lang;
    }

    public function getField($field) {
        return $field . "_" . $this->lang;
    }

    function addNewFieldLandInTables($lang, $defaultLang = "")
    {

        if (isset($this) && is_a($this, 'HelperMultiLanguageFields'))
        {
            $helper = &$this;
        } else {
            $helper = jFWBase::getClass('HelperMultiLanguageFields', 'helpers.multilanguage', array('site' => 'admin', 'type' => 'components', 'ext' => jFWBase::getComponentName() ));
        }

        $finish	= 1;
        $db		= JFactory::getDbo();
        $query	= $db->getQuery(true);

        foreach ($helper->tableFields as $table_name_end => $table)
        {
            $table_name = "#__" . jFWBase::getTablePrefix() . $table_name_end;

            $list_name_field = array();

            $db->setQuery('SHOW FIELDS FROM '. $table_name );
            $fields = $db->loadObjectList();
            foreach ($fields as $field)
            {
                $list_name_field[] = $field->Field;
            }

            //filter existent field
            foreach ($table as $k => $field)
            {
                if (in_array($field[0] . "_" . $lang, $list_name_field))
                {
                    unset($table[$k]);
                }
            }

            $sql_array_add_field = array();
            foreach ($table as $field)
            {
                $name = $field[0] . "_" . $lang;
                $sql_array_add_field[] = "ADD `" . $name . "` " . $field[1];
            }

            $sql_array_update_field = array();
            foreach ($table as $field) {
                $name = $field[0] . "_" . $lang;
                $name2 = $field[0] . "_" . $defaultLang;
                if (in_array($name2, $list_name_field)) {
                    $sql_array_update_field[] = " " . $db->Quote($name) . " = " . $db->Quote($name2);
                }
            }

            if (count($sql_array_add_field))
            {
                $query = "ALTER TABLE " . $table_name . " " . implode(", ", $sql_array_add_field);
                $db->setQuery($query);
                if (!$db->execute())
                {
                    $this->getApplication()->enqueueMessage( "Error install new language:<br>" . $db->getErrorMsg() );
                    $finish = 0;
                }

                //copy information
                if ($defaultLang != "" && count($sql_array_update_field))
                {
                    $query = "UPDATE " . $table_name . " SET " . implode(", ", $sql_array_update_field);
                    $db->setQuery($query);
                    if (!$db->execute())
                    {
                        $this->getApplication()->enqueueMessage("Error copy new language:<br>" . $db->getErrorMsg());
                        $finish = 0;
                    }
                }
            }
        }
        return $finish;
    }

    public function _LoadTableFields()
    {
        $f = array();
        $f[] = array("name", "varchar(255) NOT NULL");
        $this->tableFields["countries"] = $f;

    }
}