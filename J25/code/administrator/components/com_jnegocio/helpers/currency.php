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

class HelperCurrency extends jFWHelperBase {

    static $currencies = array();
    static $codes = array();

    function getRowbyId($idcurrency) {
        $returnItem = null;
        if (empty($idcurrency)) {
            return $returnItem;
        }
        
        if (isset($this) && is_a($this, 'HelperCurrency')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperCurrency', 'helpers.currency', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }
        
        if (empty($helper->currencies[$idcurrency])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
            $helper->currencies[$idcurrency] = JTable::getInstance('currencies', jFWBase::getTablePrefix());
            $keynames = array();
            $keynames['currency_id'] = (string) $idcurrency;
            $helper->currencies[$idcurrency]->load($keynames);
        }
        
        $returnItem = $helper->currencies[$idcurrency];
        return $returnItem;
    }
    
    function getRowbyCode($currency_code) {
        $returnItem = null;
        if (empty($currency_code)) {
            return $returnItem;
        }
        
        if (isset($this) && is_a($this, 'HelperCurrency')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperCurrency', 'helpers.currency', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }
        
        if (empty($helper->codes[$currency_code])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
            $helper->codes[$currency_code] = JTable::getInstance('currencies', jFWBase::getTablePrefix());
            $keynames = array();
            $keynames['currency_code'] = (string) $currency_code;
            $helper->codes[$currency_code]->load($keynames);
        }
        
        $returnItem = $helper->codes[$currency_code];
        
        if (!empty($returnItem->currency_id)) {
            $idcurrency = $returnItem->currency_id;
            if (empty($helper->currencies[$idcurrency])) {
                $helper->currencies[$idcurrency] = $returnItem;
            }
        }
        
        return $returnItem;
    }
    
    function getcodenamebyid($idcurrency) {
        JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
        $table = &JTable::getInstance('currencies', jFWBase::getTablePrefix());
        $keynames = array();
        $keynames['currency_id'] = (string) $idcurrency;
        $table->load($keynames);
        return $table->currency_code;
    }

    function getidbycodename($currency_code) {
        JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
        $table = &JTable::getInstance('currencies', jFWBase::getTablePrefix());
        $keynames = array();
        $keynames['currency_code'] = (string) $currency_code;
        $table->load($keynames);
        return $table->currency_id;
    }

    /**
     * Converts an amount from one currency to another
     * 
     * @param float $amount
     * @param str $currencyFrom
     * @param str $currencyTo
     * @return boolean
     */
    function convert($currencyFrom, $currencyTo = 'EUR', $amount = '1', $refresh = false) {
        static $rates;

        if (!is_array($rates)) {
            $rates = array();
        }

        if (empty($rates[$currencyFrom]) || !is_array($rates[$currencyFrom])) {
            $rates[$currencyFrom] = array();
        }

        if (empty($rates[$currencyFrom][$currencyTo])) {
            // get the exchange rate, and let the getexchange rate method handle refreshing the cache
            $rates[$currencyFrom][$currencyTo] = HelperCurrency::getExchangeRate($currencyFrom, $currencyTo, $refresh);
        }
        $exchange_rate = $rates[$currencyFrom][$currencyTo];

        // convert the amount        
        $return = $amount * $exchange_rate;
        return $return;
    }

    /**
     * Gets the exchange rate 
     * 
     * @param float $amount
     * @param str $currencyFrom
     * @param str $currencyTo
     * @return boolean
     */
    function getExchangeRate($currencyFrom, $currencyTo = 'EUR', $refresh = false) {
        if ($currencyTo == $currencyFrom) {
            return (float) 1.0;
        }

        JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');

        $date = JFactory::getDate();
        $now = $date->toMySql();
        $database = JFactory::getDBO();
        $database->setQuery("SELECT DATE_SUB( '$now', INTERVAL 1 HOUR )");
        $expire_datetime = $database->loadResult();

        if ($currencyTo == 'EUR') {
            // get from DB table
            $tableFrom = &JTable::getInstance('currencies', jFWBase::getTablePrefix());
            $tableFrom->load(array('currency_code' => $currencyFrom));
            if (!empty($tableFrom->currency_id)) {
                // refresh if it's too old or refresh forced
                if ($tableFrom->currency_updated_date < $expire_datetime || $refresh) {
                    if ($currencyFrom == "EUR") {
                        $tableFrom->currency_exchange_rate = (float) 1.0;
                    } else {
                        $tableFrom->currency_exchange_rate = HelperCurrency::getExchangeRateYahoo($currencyFrom, $currencyTo);
                    }
                    $tableFrom->currency_updated_date = $now;
                    $tableFrom->updatecambio = $now;
                    $tableFrom->guardar();
                }
                return (float) $tableFrom->currency_exchange_rate * 1.0;
            } else {
                // invalid currency, fail
                JError::raiseError('1', JText::_("Invalid Currency Type") . " currency from :" . $currencyFrom);
                return;
            }
        }

        $exchange_rate = HelperCurrency::getExchangeRateYahoo($currencyFrom, $currencyTo);
        return (float) $exchange_rate * 1.0;
    }

    /**
     * Gets the exchange rate 
     * 
     * @param float $amount
     * @param str $currencyFrom
     * @param str $currencyTo
     * @return boolean
     */
    function getExchangeRateYahoo($currencyFrom, $currencyTo = 'EUR') {
        static $has_run;

        // if refresh = true 
        // query yahoo for exchange rate
        if (!empty($has_run)) {
            sleep(1);
        }

        $url = "http://quote.yahoo.com/d/quotes.csv?s={$currencyFrom}{$currencyTo}=X&f=l1&e=.csv";
        // http://quote.yahoo.com/d/quotes.csv?s=USDEUR=X&f=l1&e=.csv
        $handle = @fopen($url, 'r');
        $result = 0;
        if ($handle) {
            $result = fgets($handle, 4096);
            fclose($handle);
        }

        $rate = (float) $result * 1.0;

        $has_run = true;

        return $rate;
    }

    /**
     * Format a number according to currency rules
     * 
     * @param unknown_type $amount
     * @param unknown_type $currency
     * @return unknown_type
     */
    function format($amount, $currency = '', $options = '', $forceoptions = false) {

        if (isset($this) && is_a($this, 'HelperCurrency')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperCurrency', 'helpers.currency', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }
        
        // default to whatever is in config
        if (!is_array($options)) {
            $options = (array) $options;
        }

        $num_decimals = isset($options['num_decimals']) ? $options['num_decimals'] : '2';
        $thousands = isset($options['thousands']) ? $options['thousands'] : '.';
        $decimal = isset($options['decimal']) ? $options['decimal'] : ',';
        $symbol = isset($options['symbol']) ? $options['symbol'] : '€';
        $sym_position = isset($options['symbol_align']) ? $options['symbol_align'] : '2';

        // if currency is an object, use it's properties
        if ($forceoptions) {
            $num_decimals = isset($options['num_decimals']) ? $options['num_decimals'] : '2';
            $thousands = isset($options['thousands']) ? $options['thousands'] : '.';
            $decimal = isset($options['decimal']) ? $options['decimal'] : ',';
            $symbol = isset($options['symbol']) ? $options['symbol'] : '€';
            $sym_position = isset($options['symbol_align']) ? $options['symbol_align'] : '2';
        } elseif (is_object($currency)) {
            $table = $currency;
            $num_decimals = $table->currency_decimals;
            $thousands = $table->currency_thousands_separator;
            $decimal = $table->currency_decimals_separator;
            $symbol = $table->currency_symbol;
            $sym_position = $table->currency_symbol_position;
        } elseif (!empty($currency) && is_numeric($currency)) {
            // TODO if currency is an integer, load the object for its id
            $itemCurrency = $helper->getRowbyId($currency);
                    
            if (!empty($itemCurrency->currency_id)) {
                $num_decimals = $itemCurrency->currency_decimals;
                $thousands = $itemCurrency->currency_thousands_separator;
                $decimal = $itemCurrency->currency_decimals_separator;
                $symbol = $itemCurrency->currency_symbol;
                $sym_position = $itemCurrency->currency_symbol_position;
            }
        } elseif (!empty($currency)) {
            $itemCurrency = $helper->getRowbyCode($currency);
            
            if (!empty($itemCurrency->currency_id)) {
                $num_decimals = $itemCurrency->currency_decimals;
                $thousands = $itemCurrency->currency_thousands_separator;
                $decimal = $itemCurrency->currency_decimals_separator;
                $symbol = $itemCurrency->currency_symbol;
                $sym_position = $itemCurrency->currency_symbol_position;
            }
        } elseif (empty($currency)) {
            $currency = fwConfig::getInstance()->get('default_currencyid', '1');
            $itemCurrency = $helper->getRowbyId($currency);
            
            if (!empty($itemCurrency->currency_id)) {
                $num_decimals = $itemCurrency->currency_decimals;
                $thousands = $itemCurrency->currency_thousands_separator;
                $decimal = $itemCurrency->currency_decimals_separator;
                $symbol = $itemCurrency->currency_symbol;
                $sym_position = $itemCurrency->currency_symbol_position;
            }
        }

        if ($sym_position == '1') {
            $pre = $symbol;
            $post = '';
        } elseif ($sym_position == '2') {
            $pre = '';
            $post = $symbol;
        }
        $return = $pre . number_format($amount, $num_decimals, $decimal, $thousands) . $post;
        return $return;
    }

    function getClassFormat( $currency, $options = '', $forceoptions = false) {

        if (isset($this) && is_a($this, 'HelperCurrency')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperCurrency', 'helpers.currency', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }
        
        if (!is_array($options)) {
            $options = (array) $options;
        }

        $num_decimals = isset($options['num_decimals']) ? $options['num_decimals'] : '2';
        $thousands = isset($options['thousands']) ? $options['thousands'] : '.';
        $decimal = isset($options['decimal']) ? $options['decimal'] : ',';
        $symbol = isset($options['symbol']) ? $options['symbol'] : '€';
        $sym_position = isset($options['symbol_align']) ? $options['symbol_align'] : '2';

        // if currency is an object, use it's properties
        if ($forceoptions) {
            $num_decimals = isset($options['num_decimals']) ? $options['num_decimals'] : '2';
            $thousands = isset($options['thousands']) ? $options['thousands'] : '.';
            $decimal = isset($options['decimal']) ? $options['decimal'] : ',';
            $symbol = isset($options['symbol']) ? $options['symbol'] : '€';
            $sym_position = isset($options['symbol_align']) ? $options['symbol_align'] : '2';
        } elseif (is_object($currency)) {
            $table = $currency;
            $num_decimals = $table->currency_decimals;
            $thousands = $table->currency_thousands_separator;
            $decimal = $table->currency_decimals_separator;
            $symbol = $table->currency_symbol;
            $sym_position = $table->currency_symbol_position;
        } elseif (!empty($currency) && is_numeric($currency)) {
            // TODO if currency is an integer, load the object for its id
            $itemCurrency = $helper->getRowbyId($currency);
                    
            if (!empty($itemCurrency->currency_id)) {
                $num_decimals = $itemCurrency->currency_decimals;
                $thousands = $itemCurrency->currency_thousands_separator;
                $decimal = $itemCurrency->currency_decimals_separator;
                $symbol = $itemCurrency->currency_symbol;
                $sym_position = $itemCurrency->currency_symbol_position;
            }
        } elseif (!empty($currency)) {
            $itemCurrency = $helper->getRowbyCode($currency);
            
            if (!empty($itemCurrency->currency_id)) {
                $num_decimals = $itemCurrency->currency_decimals;
                $thousands = $itemCurrency->currency_thousands_separator;
                $decimal = $itemCurrency->currency_decimals_separator;
                $symbol = $itemCurrency->currency_symbol;
                $sym_position = $itemCurrency->currency_symbol_position;
            }
        } elseif (empty($currency)) {
            $currency = fwConfig::getInstance()->get('default_currencyid', '1');
            $itemCurrency = $helper->getRowbyId($currency);
            
            if (!empty($itemCurrency->currency_id)) {
                $num_decimals = $itemCurrency->currency_decimals;
                $thousands = $itemCurrency->currency_thousands_separator;
                $decimal = $itemCurrency->currency_decimals_separator;
                $symbol = $itemCurrency->currency_symbol;
                $sym_position = $itemCurrency->currency_symbol_position;
            }
        }
		
        $strReturn = "{";
        $strReturn .= "aNeg: '-', ";
        if ( intval($sym_position) == 1 ) {
            $strReturn .= "pSign: 'p', ";
        } else {
            $strReturn .= "pSign: 's', ";
        }
        $strReturn .= "mDec: " 	. $num_decimals . ", ";
        $strReturn .= "aSep: '" . $thousands . "', ";
        $strReturn .= "aDec: '" . $decimal . "', ";
        if ( intval($sym_position) == 1 ) {
            $strReturn .= "aSign: '" 	. $symbol . " ' ";
        } else {
            $strReturn .= "aSign: ' " 	. $symbol . "' ";
        }			
        $strReturn .= "}";

        return $strReturn;
    }    
}