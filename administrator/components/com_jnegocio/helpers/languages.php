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

class HelperLanguages extends jFWHelperBase
{
    /**
     * get system language
     *
     * @param int $client (0 - site, 1 - admin)
     *
     * @return array
     */
    public static function getAllJoomlaLanguages($client = 0)
    {
        jimport('joomla.filesystem.folder');

        $pattern = '#(.*?)\(#is';
        $client = JApplicationHelper::getClientInfo($client);
        $rows = array();
        $path = JLanguage::getLanguagePath($client->path);
        $dirs = JFolder::folders($path);
        foreach ($dirs as $dir) {
            $files = JFolder::files($path . DIRECTORY_SEPARATOR . $dir, '^([-_A-Za-z]*)\.xml$');
            foreach ($files as $file) {
                $data = JApplicationHelper::parseXMLLangMetaFile($path . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $file);
                $row = new StdClass();
                $row->descr = $data['name'];
                $row->language = substr($file, 0, -4);
                $row->lang = substr($row->language, 0, 2);
                $row->name = $data['name'];
                preg_match($pattern, $row->name, $matches);
                if (isset($matches[1]))
                {
                    $row->name = trim($matches[1]);
                }

                if (!is_array($data))
                {
                    continue;
                }
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * Metodo para coger los lenguajes operativos para el componente
     *
     * @param   int $publish
     *
     * @return  array
     */
    public static function getAllLanguages($publish = 1) {
        $db = JFactory::getDBO();
        $negConfig = fwConfig::getInstance();
        $query = "SELECT * FROM #__" . jFWBase::getTablePrefix() . "languages";
        if ($publish) {
            $query .= " WHERE published = 1";
        }
        $query .= " ORDER BY ordering";

        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $rowssort = array();

        foreach ($rows as $k => $v) {
            $rows[$k]->lang = substr($v->language, 0, 2);
            if ($negConfig->current_lang == $v->language)
                $rowssort[] = $rows[$k];
        }
        foreach ($rows as $k => $v) {
            if (isset($rowssort[0]) && $rowssort[0]->language == $v->language)
                continue;
            $rowssort[] = $v;
        }
        unset($rows);
        return $rowssort;
    }

    public static function getlang($langtag = "")
    {
        static $hMultiLang;

        if (!is_object($hMultiLang)) {
            $negConfig = fwConfig::getInstance();
            $hMultiLang = jFWBase::getClass('HelperMultiLanguageFields', 'helpers.multilanguagefields', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
            if ($langtag == "") {
                $langtag = $negConfig->current_lang;
            }
            $hMultiLang->setLang($negConfig->current_lang);
        }
        return $hMultiLang;
    }

    public static function installNewLanguages($defaultLanguage = "", $show_message = 1) {
        $db         = JFactory::getDBO();
        $negConfig  = fwConfig::getInstance();
        $session    = JFactory::getSession();
        $joomlaLangs = self::getAllJoomlaLanguages();

        if (JFactory::getApplication()->input->getWord('task') == "checklanguage") {
            $force = true;
        } else {
            $force = false;
        }

        $checkedlanguage = $session->get(jFWBase::getTablePrefix() . 'checked_language');
        if (!$force) {
            if (is_array($checkedlanguage)) {
                $newlanguages = 0;
                foreach ($joomlaLangs as $lang) {
                    if (!in_array($lang->language, $checkedlanguage))
                        $newlanguages++;
                }
                if ($newlanguages == 0) {
                    return false;
                }
            }
        }

        $dbLangsTag = array();
        if (!$force) {
            $query = "select * from #__" . jFWBase::getTablePrefix() . "languages";
            $db->setQuery($query);

            $dbLangs = $db->loadObjectList();
            foreach ($dbLangs as $lang) {
                $dbLangsTag[] = $lang->language;
            }
        }
        if (!$defaultLanguage)
            $defaultLanguage = $negConfig->default_lang;

        $checkedlanguage = array();
        $installed_new_lang = 0;

        $helpMultiLang = self::getlang();

        foreach ($joomlaLangs as $lang) {
            $checkedlanguage[] = $lang->language;
            // echo "check language:".$lang->language."<br/>";
            if (!in_array($lang->language, $dbLangsTag)) {
                if ($helpMultiLang->addNewFieldLandInTables($lang->language, $defaultLanguage)) {

                    $query = "SELECT id FROM #__" . jFWBase::getTablePrefix() . "languages";
                    $query .= " WHERE `language`='" . $lang->language . "'";
                    $db->setQuery($query);
                    // echo "query:".$query."<br/>";
                    $items = $db->loadObjectList('id');
                    // echo "items:<br/><pre>".var_dump($items)."</pre><hr/>";
                    // echo "count:".count($items)."<br/>";
                    if (!count($items)) {
                        $installed_new_lang = 1;
                        $query = "INSERT INTO #__" . jFWBase::getTablePrefix() . "languages SET `language`='" . $lang->language . "', `name`='" . $lang->name . "', `published`='1'";
                        $db->setQuery($query);
                        if (!$db->query()) {
                            JError::raiseNotice("", "Error:" . $db->getErrorMsg() . ": " . $lang->name);
                        }

                        if ($show_message) {
                            JError::raiseNotice("", JText::sprintf('COM_NEGOCIO_INSTALLED_NEW_LANGUAGES', $lang->name));
                        }
                    }
                }
            }
        }
        $session->set(jFWBase::getTablePrefix() . "checked_language", $checkedlanguage);
        return true;
    }
}