<?php

/**
 * @version		$Id$
 * @package		Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license		Comercial License
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jFWBase::load('jFWHelperBase', 'helpers._base');

$options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
jFWBase::load('HelperZones', 'helpers.zones', $options);
jFWBase::load('HelperCurrency', 'helpers.currency', $options);

class HelperProduct extends jFWHelperBase {

    static $aCategoriesxRef = array();
    static $_taxes = array();
    static $_products = array();

    function getProduct($ProductId) {
        $returnObject = false;
        if (empty($ProductId) || (is_numeric($ProductId) && ($ProductId == 0))) {
            return $returnObject;
        }

        if (isset($this) && is_a($this, 'HelperProduct')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperProduct', 'helpers.product', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }

        if (!isset($helper->_products[$ProductId])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
            $helper->_products[$ProductId] = JTable::getInstance('products', jFWBase::getTablePrefix());
            $helper->_products[$ProductId]->load($ProductId);
        }

        $returnObject = $this->_products[$ProductId];
        return $returnObject;
    }

    function setProduct($objProduct) {
        $objProduct->params = new JRegistry();
        return $objProduct;
    }

    function getCategories($ProductId) {
        if (isset($this) && is_a($this, 'HelperProduct')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperProduct', 'helpers.product', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }

        if (empty($helper->aCategoriesxRef[$ProductId])) {
            $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
            $model = jFWBase::getClass('jNegocioModelProductcategory', 'models.productcategory', $options);
            $model->emptyState();
            $model->setState('filter_productid', $ProductId);
            $helper->aCategoriesxRef[$ProductId] = $model->getData();
        }

        return $helper->aCategoriesxRef[$ProductId];
    }

    public function getTaxRate($ObjProduct, $GeoZoneID = null) {
        $returnRate = 0;
        if (empty($ObjProduct)) {
            return $returnRate;
        }

        if (empty($GeoZoneID) || is_null($GeoZoneID)) {
            $ZoneID = fwConfig::getInstance()->get('company_zone', 1);
            jFWBase::load('HelperZones', 'helpers.zones');
            $GeoZoneID = HelperZones::getGeoZonebyZoneID($ZoneID);
        }

        if (isset($this) && is_a($this, 'HelperProduct')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperProduct', 'helpers.product', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }

        if (!isset($helper->_taxes) || empty($helper->_taxes)) {
            $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
            $modeltaxrates = jFWBase::getClass('jNegocioModelTaxrates', 'models.taxrates', $options);

            // $modeltaxrates = JModel::getInstance( 'Taxrates', 'jNegocioModel' );
            $modeltaxrates->emptyState();
            $modeltaxrates->setState('limit', 0);
            $taxrates = $modeltaxrates->getData();
            foreach (@$taxrates as $taxrate) {
                $helper->_taxes[$taxrate->typetax_id][$taxrate->geozone_id] = $taxrate->tax_rate;
            }
        }

        if (isset($helper->_taxes[$ObjProduct->typetax_id][$GeoZoneID])) {
            $returnRate = $helper->_taxes[$ObjProduct->typetax_id][$GeoZoneID];
        }
        return $returnRate;
    }

    public function prepareProductforAddCar($productid, $product = null, $listprices = null) {
        if (isset($this) && is_a($this, 'HelperProduct')) {
            $helper = &$this;
        } else {
            $helper = &jFWBase::getClass('HelperProduct', 'helpers.product', array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio'));
        }

        if (is_null($product)) {
            $product = $helper->getProduct($productid);
        }

        $user = &JFactory::getUser();

        if ($user->get('guest')) {
            $product->negocio_user_group = fwConfig::getInstance()->get('default_usergroup', 1);
            $zoneid = fwConfig::getInstance()->get('company_zone', 1);
        } else {
            $userid = $user->get('id');
            $zoneid = fwConfig::getInstance()->get('company_zone', 1);
        }
        $geozoneid = HelperZones::getGeoZonebyZoneID($zoneid);
        $product->taxrate = $helper->gettaxrate($product, $geozoneid);

        unset($product->created_by);
        unset($product->created);
        unset($product->modified_by);
        unset($product->modified);
        unset($product->checked_out);
        unset($product->checked_out_time);

        $product->default_qty = $product->params->get('default_quantity', '1');

        if (is_null($listprices)) {
            $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
            $modelprices = jFWBase::getClass('jNegocioModelProductprices', 'models.productprices', $options);
            $modelprices->emptyState();
            $modelprices->setState('filter_productid', $product->product_id);
            $modelprices->setState('filter_groupid', $product->negocio_user_group);
            $modelprices->setState('filter_quantity', $product->default_qty);
            $modelprices->setState('limit', 0);
            $listprices = $modelprices->getData();
        }

        $prices = array();
        foreach ($listprices as $key => $price) {
            unset($price->created_by);
            unset($price->created);
            unset($price->modified_by);
            unset($price->modified);
            unset($price->checked_out);
            unset($price->checked_out_time);
            $prices[$key] = $price;
        }

        $product->listprices = $prices;

        if (count($product->listprices)) {
            $first_key = array_shift(array_keys($product->listprices));
            $itemprice = $product->listprices[$first_key];
            $product->pp_price = $itemprice->product_price;
            $product->pp_priceincltax = $itemprice->product_priceincltax;
            $product->pp_discount = $itemprice->product_discount;

            if (fwConfig::getInstance()->get('work_pricewithtax', 1) == 1) {
                $product->pp_bruto = $product->pp_priceincltax / ((100 + (float) $product->taxrate ) / 100);
                $product->pp_totaldto = ($product->pp_bruto * $product->pp_discount) / 100;
                $product->pp_neto = $product->pp_bruto - $product->pp_totaldto;
                $product->pp_total = $product->pp_neto * ((100 + (float) $product->taxrate ) / 100);
                $product->pp_ratetotal = $product->pp_total - $product->pp_neto;
            } else {
                $product->pp_bruto = (float) $product->pp_price;
                $product->pp_totaldto = ($product->pp_bruto * $product->pp_discount) / 100;
                $product->pp_neto = $product->pp_bruto - $product->pp_totaldto;
                $product->pp_total = $product->pp_neto * ((100 + (float) $product->taxrate) / 100);
                $product->pp_ratetotal = $product->pp_total - $product->pp_neto;
            }
        } else {
            $product->pp_price = 0;
            $product->pp_priceincltax = 0;
            $product->pp_discount = 0;
            $product->pp_bruto = 0;
            $product->pp_totaldto = 0;
            $product->pp_neto = 0;
            $product->pp_total = 0;
            $product->pp_ratetotal = 0;
            $product->pp_discount = 0;
        }

        // Formating Imports
        $product->f_pp_bruto = ($product->pp_bruto == 0) ? '' : HelperCurrency::format($product->pp_bruto);
        $product->f_pp_neto = ($product->pp_neto == 0) ? '' : HelperCurrency::format($product->pp_neto);
        
        if ($product->pp_totaldto > 0) {
            $product->f_pp_oldprice = HelperCurrency::format($product->pp_bruto);
            $product->f_pp_discount = number_format($product->pp_discount, 2, ',', '') . ' %';
            $product->f_pp_totaldto = HelperCurrency::format($product->pp_totaldto);
        } else {
            $product->f_pp_oldprice = '';
            $product->f_pp_discount = '';
            $product->f_pp_totaldto = '';
        }

        $product->f_pp_total = ($product->pp_total == 0) ? '' : HelperCurrency::format($product->pp_total);
        $product->f_taxrate = number_format($product->taxrate, 2, ',', '') . ' %';
        $product->f_pp_ratetotal = ($product->pp_ratetotal == 0) ? '' : HelperCurrency::format($product->pp_ratetotal);

        return $product;
    }

}