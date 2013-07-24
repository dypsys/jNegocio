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

Class jFWArrays {

    function parseObjecttoArray($object) {
        $returnArray = array();
        foreach ($object as $key => $value) {
            $returnArray[$key] = $value;
        }
        return $returnArray;
    }

    function parseArrayToParams($array) {
        $str = '';
        foreach ($array as $key => $value) {
            $str .= $key . "=" . $value . "\n";
        }
        return $str;
    }

    function parseParamsToArray($string) {
        $temp = explode("\n", $string);
        $array = array();
        foreach ($temp as $key => $value) {
            if (!$value)
                continue;
            $temp2 = explode("=", $value);
            $array[$temp2[0]] = $temp2[1];
        }
        return $array;
    }

    function splitValuesArrayObject($array_object, $property_name) {
        $return = '';
        if (is_array($array_object)) {
            foreach ($array_object as $key => $value) {
                $return .= $array_object[$key]->$property_name . ', ';
            }
            $return = "( " . substr($return, 0, strlen($return) - 2) . " )";
        }
        return $return;
    }

}
