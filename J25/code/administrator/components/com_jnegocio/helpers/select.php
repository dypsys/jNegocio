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

jFWBase::load('jFWSelect', 'library.select');

class HelperSelect extends jFWSelect {

    public static function getProductLimitBox($selected, $style = 'list', $name = 'limit', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null) {
        // Initialise variables.
        $necConfig = fwConfig::getInstance();
	$limits = array();
        
        $products_line = $necConfig->get('productlist_products_x_row', 3 );
        
        // Make the option list.
	for ($i = $products_line; $i <= $products_line*5; $i += $products_line) {
            $limits[] = JHtml::_('select.option', "$i");
	}
	$limits[] = JHtml::_('select.option', $products_line*10, $products_line*10);
        $limits[] = JHtml::_('select.option', $products_line*20, $products_line*20);
	$limits[] = JHtml::_('select.option', '0', JText::_('JALL'));   
        
        if ($style == 'list') {
            return self::genericlist($limits, $name, $attribs, 'value', 'text', $selected, $idtag);
        }
        return '';
    }
    
    public static function getProductListOrder($selected, $style = 'list', $name = 'filter_order', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false) {
        $options = array();
        if ($allowAny) {
            $options[] = self::option('0', '- ' . JText::_('COM_JNEGOCIO_SELECT') . ' -');
        }
        $options[] = self::option('tbl.product_id', JText::_('COM_JNEGOCIO_OPTION_ID_NAME') );
        $options[] = self::option('tbl.name', JText::_('COM_JNEGOCIO_OPTION_PRODUCT_NAME') );
        $options[] = self::option('product_price', JText::_('COM_JNEGOCIO_OPTION_PRODUCT_PRICE') );
        
        if ($style == 'list') {
            return self::genericlist($options, $name, $attribs, 'value', 'text', $selected, $idtag);
        }
        return '';
    }
    
    /**
     * Generate list for select position
     * 
     * @param $selected
     * @param $name
     * @param $attribs
     * @param $idtag
     */
    public static function symbolalign($selected, $name = 'currency_symbol_align', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false) {
        $options = array();
        if ($allowAny) {
            $options[] = self::option('0', '- ' . JText::_('COM_JNEGOCIO_SELECT') . ' -');
        }
        $options[] = self::option('1', '- ' . JText::_('COM_JNEGOCIO_OPTION_POSITION_LEFT') . ' -');
        $options[] = self::option('2', '- ' . JText::_('COM_JNEGOCIO_OPTION_POSITION_RIGHT') . ' -');

        return self::genericlist($options, $name, $attribs, 'value', 'text', $selected, $idtag);
    }

    /**
     * Generate list for select Countries
     * 
     * @param $selected
     * @param $name
     * @param $attribs
     * @param $idtag
     * @param $allowAny
     * @return unknown_type
     */
    public static function countries($selected, $name = 'filter_countryid', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $enabled = null) {
        $list = array();
        if ($allowAny) {
            $list[] = self::option('', "- " . JText::_('COM_JNEGOCIO_SELECT_COUNTRY') . " -", 'country_id', 'name');
        }

        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $model = jFWBase::getClass('jNegocioModelCountries', 'models.countries', $options);
        $model->emptyState();
        if (!empty($enabled)) {
            $model->setState('filter_state', 'P');
        }
        $model->setState( 'limit', 0);
        $model->setState('filter_order', 'name');
        $model->setState('filter_order_Dir', 'ASC');
        $items = $model->getData();

        if (count($items)) {
            foreach (@$items as $item) {
                $list[] = self::option($item->country_id, $item->name, 'country_id', 'name');
            }
        }

        return self::genericlist($list, $name, $attribs, 'country_id', 'name', $selected, $idtag);
    }

    /**
     * Generate list for select Currencies
     * 
     * @param $selected
     * @param $name
     * @param $attribs
     * @param $idtag
     * @param $allowAny
     * @return unknown_type
     */
    public static function currencies($selected, $name = 'filter_currencyid', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $enabled = null) {
        $list = array();
        if ($allowAny) {
            $list[] = self::option('', "- " . JText::_('COM_JNEGOCIO_SELECT_CURRENCY') . " -", 'currency_id', 'currency_name');
        }

        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $model = jFWBase::getClass('jNegocioModelCurrencies', 'models.currencies', $options);
        $model->emptyState();
        if (!empty($enabled)) {
            $model->setState('filter_state', 'P');
        }
        $model->setState( 'limit', 0);
        $model->setState('filter_order', 'tbl.ordering');
        $model->setState('filter_order_Dir', 'ASC');
        $items = $model->getData();

        if (count($items)) {
            foreach (@$items as $item) {
                $list[] = self::option($item->currency_id, $item->currency_name, 'currency_id', 'currency_name');
            }
        }

        return self::genericlist($list, $name, $attribs, 'currency_id', 'currency_name', $selected, $idtag);
    }
    
    /**
     * Generate list for select Geo Zones
     * 
     * @param $selected
     * @param $name
     * @param $attribs
     * @param $idtag
     * @param $allowAny
     * @return unknown_type
     */
    public static function geozones($selected, $name = 'filter_geozoneid', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false) {
        $list = array();
        if ($allowAny) {
            $list[] = self::option('', "- " . JText::_('COM_JNEGOCIO_SELECT_GEOZONE') . " -", 'geozone_id', 'name');
        }

        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $model = jFWBase::getClass('jNegocioModelGeozones', 'models.geozones', $options);
        $model->emptyState();
//		if (!empty($enabled)) {
//            $model->setState( 'filter_state', 'P' );
//		}
        $model->setState( 'limit', 0);
        $model->setState('filter_order', 'name');
        $model->setState('filter_order_Dir', 'ASC');
        $items = $model->getData();

        if (count($items)) {
            foreach (@$items as $item) {
                $list[] = self::option($item->geozone_id, $item->name, 'geozone_id', 'name');
            }
        }

        return self::genericlist($list, $name, $attribs, 'geozone_id', 'name', $selected, $idtag);
    }

    /**
     * Generate list for select types taxes
     * 
     * @param $selected
     * @param $name
     * @param $attribs
     * @param $idtag
     * @param $allowAny
     * @return unknown_type
     */
    public static function typetaxes($selected, $name = 'filter_typetaxid', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $enabled = null) {
        $list = array();
        if ($allowAny) {
            $list[] = self::option('', "- " . JText::_('COM_JNEGOCIO_SELECT_TYPETAX') . " -", 'typetax_id', 'name');
        }

        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $model = jFWBase::getClass('jNegocioModelTypetaxes', 'models.typetaxes', $options);
        $model->emptyState();
        if (!empty($enabled)) {
            $model->setState('filter_state', 'P');
        }
        $model->setState( 'limit', 0);
        $model->setState('filter_order', 'name');
        $model->setState('filter_order_Dir', 'ASC');
        $items = $model->getData();

        if (count($items)) {
            foreach (@$items as $item) {
                $list[] = self::option($item->typetax_id, $item->name, 'typetax_id', 'name');
            }
        }

        return self::genericlist($list, $name, $attribs, 'typetax_id', 'name', $selected, $idtag);
    }

    /**
     * Generate list for select types taxes
     * 
     * @param $selected
     * @param $name
     * @param $attribs
     * @param $idtag
     * @param $allowAny
     * @return unknown_type
     */
    public static function taxrates($selected, $zoneid , $name = 'filter_taxrateid', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $enabled = null) {
        $list = array();
        if ($allowAny) {
            $list[] = self::option('', "- " . JText::_('COM_JNEGOCIO_SELECT_TAXRATE') . " -", 'taxrate_id', 'name');
        }
        
        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $model = jFWBase::getClass('jNegocioModelTaxrates', 'models.taxrates', $options);
        $model->emptyState();
        if (!empty($enabled)) {
            $model->setState('filter_state', 'P');
        }
        $model->setState( 'limit', 0);
        $model->setState('filter_zoneid', $zoneid);
        $model->setState('filter_order', 'tbl.geozone_id, tbl.typetax_id, tbl.tax_rate');
        $model->setState('filter_order_Dir', 'ASC');
        $items = $model->getData();

        if (count($items)) {
            foreach (@$items as $item) {
                $list[] = self::option($item->taxrate_id, number_format($item->tax_rate, 2, '.', ','). ' %', 'taxrate_id', 'name');
            }
        }

        return self::genericlist($list, $name, $attribs, 'taxrate_id', 'name', $selected, $idtag);
    }
        
    /**
     * Generate list for select category
     * 
     * @param $selected
     * @param $name
     * @param $attribs
     * @param $idtag
     * @param $allowAny
     * @return unknown_type
     */
    public static function category($selected, $name = 'filter_parentid', $attribs = array('class' => 'inputbox', 'size' => '1'), 
            $idtag = null, $allowAny = false, $allowNone = false, 
            $title = 'COM_JNEGOCIO_SELECT_CATEGORY', $title_none = 'COM_JNEGOCIO_OPTION_ROOT_NODE', $enabled = null,
            $allowAll = false, $allowAllText = 'COM_JNEGOCIO_SELECT_CATEGORY_ALL'
            ) {
        $lang = &HelperLanguages::getlang();
        $list = array();
        if ($allowAny) {
            $list[] = self::option('', "- " . JText::_($title) . " -", 'category_id', 'name');
        }
        if ($allowAll) {
            $list[] = self::option('-1', "- " . JText::_($allowAllText) . " -", 'category_id', 'name');
        }

        JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jnegocio' . DS . 'tables');
        if ($allowNone) {
            $rootid = JTable::getInstance('categories', jFWBase::getTablePrefix())->getRootId();
            $list[] = self::option($rootid, "- " . JText::_($title_none) . " -", 'category_id', 'name');
        }

        $table = JTable::getInstance('Categories', jFWBase::getTablePrefix());
        $items = $table->getTreeList();

        $fieldname = $lang->getField('name');
        foreach (@$items as $item) {
            $list[] = self::option($item->category_id, str_repeat('-&nbsp;', $item->level - 1) . $item->$fieldname, 'category_id', 'name');
        }
        return self::genericlist($list, $name, $attribs, 'category_id', 'name', $selected, $idtag);
    }

    /**
     * Generate list for select Manufacturers
     * 
     * @param $selected
     * @param $name
     * @param $attribs
     * @param $idtag
     * @param $allowAny
     * @return unknown_type
     */
    public static function manufacturers($selected, $name = 'filter_manufacturerid', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $enabled = null) {
        $list = array();
        if ($allowAny) {
            $list[] = self::option('', "- " . JText::_('COM_JNEGOCIO_SELECT_MANUFACTURER') . " -", 'manufacturer_id', 'name');
        }

        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $model = jFWBase::getClass('jNegocioModelManufacturers', 'models.manufacturers', $options);
        $model->emptyState();
        if (!empty($enabled)) {
            $model->setState('filter_state', 'P');
        }
        $model->setState( 'limit', 0);
        $model->setState('filter_order', 'name');
        $model->setState('filter_order_Dir', 'ASC');
        $items = $model->getData();

        if (count($items)) {
            foreach (@$items as $item) {
                $list[] = self::option($item->manufacturer_id, JText::_($item->name), 'manufacturer_id', 'name');
            }
        }

        return self::genericlist($list, $name, $attribs, 'manufacturer_id', 'name', $selected, $idtag);
    }
    
    /**
     * Generate list for select Attributes Values
     * 
     * @param $attributeid
     * @param $selected
     * @param $name
     * @param $attribs
     * @param $idtag
     * @param $allowAny
     * @return unknown_type
     */
    public static function attributesvalues($attributeid, $selected, $name = 'filter_attributevalueid', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $enabled = null) {
        $list = array();
        if ($allowAny) {
            $list[] = self::option('', "- " . JText::_('COM_JNEGOCIO_SELECT_ATTRIBUTEVALUE') . " -", 'value_id', 'name');
        }
        
        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $model = jFWBase::getClass('jNegocioModelAttributesValues', 'models.attributesvalues', $options);
        $model->emptyState();
        $model->setState( 'limit', 0);
        $model->setState( 'filter_attrid', $attributeid);
        $model->setState( 'filter_order', 'tbl.ordering' );
        
        $items = $model->getData();

        if (count($items)) {
            foreach (@$items as $item) {
                $list[] = self::option($item->value_id, JText::_($item->name), 'value_id', 'name');
            }
        }

        return self::genericlist($list, $name, $attribs, 'value_id', 'name', $selected, $idtag);
    }
    
    /**
     * Generate list for select price prefix
     * 
     * @param $selected
     * @param $name
     * @param $attribs
     * @param $idtag
     */
    public static function priceprefix($selected, $name = 'priceprefix', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false) {
        $options = array();
        if ($allowAny) {
            $options[] = self::option('-1', '- ' . JText::_('COM_JNEGOCIO_SELECT') . ' -');
        }
        // $options[] = self::option( '0', JText::_('COM_JNEGOCIO_OPTION_PRICEPREFIX_NONE') );
        $options[] = self::option( '1', JText::_('COM_JNEGOCIO_OPTION_PRICEPREFIX_PLUS') );
        $options[] = self::option( '2', JText::_('COM_JNEGOCIO_OPTION_PRICEPREFIX_MINUS') );
        $options[] = self::option( '3', JText::_('COM_JNEGOCIO_OPTION_PRICEPREFIX_EQUAL') );

        return self::genericlist($options, $name, $attribs, 'value', 'text', $selected, $idtag);
    }
    
    /**
     * Generate list for select UserGroups
     * 
     * @param $selected
     * @param $name
     * @param $attribs
     * @param $idtag
     * @param $allowAny
     * @return unknown_type
     */
    public static function usergroups($selected, $name = 'filter_usergroupid', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $enabled = null) {
        $list = array();
        if ($allowAny) {
            $list[] = self::option('', "- " . JText::_('COM_JNEGOCIO_SELECT_USERGROUP') . " -", 'usergroup_id', 'name');
        }

        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $model = jFWBase::getClass('jNegocioModelUsergroups', 'models.usergroups', $options);
        $model->emptyState();
        if (!empty($enabled)) {
            $model->setState('filter_state', 'P');
        }
        $model->setState('filter_order', 'name');
        $model->setState('filter_order_Dir', 'ASC');
        $items = $model->getData();

        if (count($items)) {
            foreach (@$items as $item) {
                $list[] = self::option($item->usergroup_id, JText::_($item->name), 'usergroup_id', 'name');
            }
        }

        return self::genericlist($list, $name, $attribs, 'usergroup_id', 'name', $selected, $idtag);
    }
    
    /**
     * Shows a true/false graphics
     *
     * @param	bool	Value
     * @param 	string	Image for true
     * @param 	string	Image for false
     * @param 	string 	Text for true
     * @param 	string	Text for false
     * @return 	string	Html img
     */
    public static function boolean($bool, $true_img = null, $false_img = null, $true_text = null, $false_text = null) {
        $true_img = $true_img ? $true_img : 'tick.png';
        $false_img = $false_img ? $false_img : 'publish_x.png';
        $true_text = $true_text ? $true_text : 'Yes';
        $false_text = $false_text ? $false_text : 'No';

        $imgsrc = ($bool ? $true_img : $false_img);
        $imgalt = JText::_($bool ? $true_text : $false_text);
        // $image = JHTML::_('image.administrator',  $imgsrc, '/media/plg_nemesys/images/icons/' , NULL, NULL, $imgalt );

        return '<img src="' . jFWBase::getURL('icons') . ($bool ? $true_img : $false_img) . '" border="0" alt="' . JText::_($bool ? $true_text : $false_text) . '" />';
        // return $image;
    }

}