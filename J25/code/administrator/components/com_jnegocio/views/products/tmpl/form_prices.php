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

$default_usergroup = $this->config->get('default_usergroup', 1);
$default_currencyid = $this->config->get('default_currencyid', 1);
$default_zoneid = $this->config->get('company_zone', 1);
$work_pricewithtax = $this->config->get('work_pricewithtax', 1);
$default_typetax = @$this->row->typetax_id ? @$this->row->typetax_id : 0;
$default_taxrate = 0;

$currencyid = $this->config->get('default_currencyid', '1');
$priceClass = HelperCurrency::getClassFormat($currencyid);

$document = & JFactory::getDocument();

$script = array();
$script[] = "window.addEvent('domready', function() {";
$script[] = "Negocio.currencies = new Negocio.currencies.App({ defaultcurrency: " . $default_currencyid . " });";
foreach (@$this->currecies as $currency) {
    $script[] = "Negocio.currencies.addCurrency( new Negocio.Currency({ "
            . "id:" . $currency->currency_id . ", "
            . "symbol:'" . $currency->currency_symbol . "', "
            . "position:'" . $currency->currency_symbol_position . "', "
            . "decimals:'" . $currency->currency_decimals . "', "
            . "decimal_separator: '" . $currency->currency_decimals_separator . "', "
            . "thousands_separator: '" . $currency->currency_thousands_separator . "'"
            . "}) );";
}
$script[] = "$('currency_id').addEvent('change',function(event) {";
$script[] = "var index = document.id('currency_id').selectedIndex;";
$script[] = "var opt   = document.id('currency_id').options[index].value;";
$script[] = "Negocio.currencies.setdefaultCurrency(opt);";
$script[] = "});";

$script[] = "Negocio.usergroups = new Negocio.usergroups.App({ defaultUsergroupId: " . $default_usergroup . " });";
foreach ($this->usergroups as $usergroup) {
    $script[] = "Negocio.usergroups.addUserGroup(new Negocio.UserGroup({id: ".$usergroup->usergroup_id.", name: '". $usergroup->name."' }));";
}

$script[] = "Negocio.taxes = new Negocio.taxes.App({ defaulttypetax: " . $default_typetax . ", defaulttax: 0 });";
$script[] = "Negocio.taxes.addTax( new Negocio.Tax({ id: 0, tax_rate: 0, typetaxid: 0}) );";
$default_arraytaxrate = null;
foreach ($this->taxrates as $taxrate) {
    $script[] = "Negocio.taxes.addTax( new Negocio.Tax({ id: " . $taxrate->taxrate_id . ", tax_rate: " . $taxrate->tax_rate . ", typetaxid: " . $taxrate->typetax_id . "}) );";
    if ($taxrate->typetax_id == @$this->row->typetax_id) {
        $default_arraytaxrate = $taxrate;
    }
}
// echo "typetax_id:".@$this->row->typetax_id."<br/>";
// echo "taxrate:".@$default_arraytaxrate->tax_rate."<br/>";

if (!is_null($default_arraytaxrate)) {
    $script[] = "Negocio.taxes.setdefaultTax(" . $default_arraytaxrate->typetax_id . ");";
    $default_taxrate = $default_arraytaxrate->typetax_id;
}

$script[] = "$('taxrate_id').addEvent('change',function(event) {";
$script[] = "var index = document.id('taxrate_id').selectedIndex;";
$script[] = "var opt   = document.id('taxrate_id').options[index].value;";
$script[] = "Negocio.taxes.setdefaultTax(opt);";
$script[] = "});";

$script[] = "$('typetax_id').addEvent('change',function(event) {";
$script[] = "var index = document.id('typetax_id').selectedIndex;";
$script[] = "var opt   = document.id('typetax_id').options[index].value;";
$script[] = "Negocio.taxes.setdefaultTypeTax(opt);";
$script[] = "document.id('taxrate_id').selectedIndex = Negocio.taxes.getdefaultTax();";
$script[] = "});";

$script[] = "Negocio.prices = new Negocio.productprices.App({ elementtable: 'productsprices_container', work_pricewithtax: ". $work_pricewithtax ." ,priceFormat: " . $priceClass .", image_path: '". jFWBase::getURL('icons') ."' });";

$script[] = "jQuery('.classPrice').each(function() {jQuery(this).autoNumeric('init', " . $priceClass ."); });";
$script[] = "jQuery('.classProcent').each(function() {jQuery(this).autoNumeric('init', {aNeg: '-', pSign: 's', mDec: 2, aSep: ',', aDec: '.', aSign: ' %', vMin: '0.00', vMax: '100'}); });";
$script[] = "jQuery('.classQuantity').each(function() {jQuery(this).autoNumeric('init', {aNeg: '-', pSign: '', mDec: 0, aSep: ',', aDec: '.', aSign: '', vMin: '0.00', vMax: '999999.99'}); });";

$script[] = "$$('.classPriceoTax').addEvent('blur', function(event){Negocio.prices.eventcalculteprice(event);});";
$script[] = "$$('.classPriceoTax').addEvent('keyup', function(event){Negocio.prices.eventcalculteprice(event);});";

if ($work_pricewithtax==1) {
    $script[] = "$$('.classProcent').addEvent('blur', function(event){Negocio.prices.eventcalcultepricewithtax(event);});";
    $script[] = "$$('.classProcent').addEvent('keyup', function(event){Negocio.prices.eventcalcultepricewithtax(event);});";    
} else {
    $script[] = "$$('.classProcent').addEvent('blur', function(event){Negocio.prices.eventcalculteprice(event);});";
    $script[] = "$$('.classProcent').addEvent('keyup', function(event){Negocio.prices.eventcalculteprice(event);});";
}

$script[] = "$$('.classPricewTax').addEvent('blur', function(event){Negocio.prices.eventcalcultepricewithtax(event);});";
$script[] = "$$('.classPricewTax').addEvent('keyup', function(event){Negocio.prices.eventcalcultepricewithtax(event);});";

$script[] = "$$('.pp_delete_node').addEvent('click', function(event){Negocio.prices.erase(event);});";

// $script[] = "$$('.classPrice').each(function(el) {";
// $script[] = "el.addEvent('blur', function(event) { Negocio.prices.eventcalculteprice(event);});";
// $script[] = "el.addEvent('keyup', function(event) { Negocio.prices.eventcalculteprice(event);});";
// $script[] = "});";

$script[] = "});";

$document->addScriptDeclaration(implode("\n", $script));
?>
<fieldset class="adminform productprices">
    <ul class="adminformlist width-100">
        <li class="width-33 fltlft">
            <label id="typetax-lbl" class="width-100 hasTip" title="<?= @JText::_('COM_JNEGOCIO_TYPETAX_NAME_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_TYPETAX_NAME_DESC'); ?>" for="typetax_id">
                <?= @JText::_('COM_JNEGOCIO_TYPETAX_NAME_LABEL'); ?>
            </label>
            <?php echo HelperSelect::typetaxes(@$this->row->typetax_id, 'typetax_id', array('class' => 'inputbox width-90', 'size' => '1'), 'typetax_id', true, true); ?>
        </li>
        <li class="width-32 fltlft">
            <label id="taxrate-lbl" class="width-100 hasTip" title="<?= @JText::_('COM_JNEGOCIO_TAXRATE_NAME_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_TAXRATE_NAME_DESC'); ?>" for="taxrate_id">
                <?= @JText::_('COM_JNEGOCIO_TAXRATE_NAME_LABEL'); ?>
            </label>
            <?php echo HelperSelect::taxrates($default_taxrate, $default_zoneid, 'taxrate_id', array('class' => 'inputbox width-90 negocio_disabled', 'size' => '1', 'disabled' => 'disabled'), 'taxrate_id', true, true); ?>
        </li>
        <li class="width-33 fltlft">
            <label id="currency-lbl" class="width-100 hasTip" title="<?= @JText::_('COM_JNEGOCIO_CURRENCY_NAME_LABEL'); ?>::<?= @JText::_('COM_JNEGOCIO_CURRENCY_NAME_DESC'); ?>" for="currency_id">
                <?= @JText::_('COM_JNEGOCIO_CURRENCY_NAME_LABEL'); ?>
            </label>
            <?php echo HelperSelect::currencies(@$this->row->currency_id, 'currency_id', array('class' => 'inputbox width-90', 'size' => '1'), 'currency_id', true, true); ?>
        </li>        
    </ul>
</fieldset>
<div>
<table class="adminlist" id="productsprices_container">
    <header>
        <tr>
            <th>#</th>
            <th><?= @JText::_('COM_JNEGOCIO_PRODUCT_PRICE_NETO'); ?></th>
            <th><?= @JText::_('COM_JNEGOCIO_PRODUCT_PRICE_BRUTO'); ?></th>
            <th><?= @JText::_('COM_JNEGOCIO_PRODUCT_DISCOUNT'); ?></th>
            <th><?= @JText::_('COM_JNEGOCIO_PRODUCT_TOTAL'); ?></th>
            <th><?= @JText::_('COM_JNEGOCIO_PRODUCT_QUANTITY_START'); ?></th>
            <th><?= @JText::_('COM_JNEGOCIO_PRODUCT_QUANTITY_END'); ?></th>
            <th><?= @JText::_('COM_JNEGOCIO_USERGROUP_NAME_LABEL'); ?></th>
            <th></th>
        </tr>
    </header>
    <body>
        <?php
        $k = 1;
        $pcont = 0;
        for ($pcont = 0, $ptotal = count($this->prices_rows); $pcont < $ptotal; $pcont++) {
            $price = $this->prices_rows[$pcont];
            $aprice = $pcont+1;
            
            $pp_price           = $price->product_price;
            $pp_priceincltax    = $price->product_priceincltax;
            $pp_discount        = $price->product_discount;                    
            $taxrate            = HelperProduct::gettaxrate( @$this->row );
            
            if ($work_pricewithtax==1) {
                $pp_bruto       = $pp_priceincltax / ((100 + (float)$taxrate )/100); 
		$pp_totaldto 	= ($pp_bruto * $pp_discount)/100;
		$pp_neto        = $pp_bruto - $pp_totaldto;
		$pp_total	= $pp_neto * ((100 + (float)$taxrate )/100);
		$pp_ratetotal 	= $pp_total - $pp_neto;
                
                $ipvc_tabstop    = 'readonly="READONLY"';
                $ipvp_tabstop    = '';
            } else {
                $pp_bruto 	= (float)$pp_price;
		$pp_totaldto 	= ($pp_bruto * $pp_discount)/100;
		$pp_neto	= $pp_bruto - $pp_totaldto;
		$pp_total	= $pp_neto * ((100 + (float)$taxrate )/100);
		$pp_ratetotal 	= $pp_total - $pp_neto;
                
                $ipvc_tabstop    = '';
                $ipvp_tabstop    = 'readonly="READONLY"';
            }
            ?>
            <tr class="row<?= @$k; ?>" rel="<?= @$aprice;?>">
                <td>
                    <input type="hidden" id="ppid<?= @$aprice;?>" name="prdprices[<?= @$aprice;?>][productprice_id]" value="<?= @$price->productprice_id;?>" />
                    <input type="hidden" id="pp_deleted_<?= @$aprice;?>" name="prdprices[<?= @$aprice;?>][deleted]" value="0" />
                </td>
                <td>
                    <input type="text" <?= @$ipvc_tabstop;?> id="price<?= @$aprice;?>" name="prdprices[<?= @$aprice;?>][price]" class="classPrice classPriceoTax" value="<?= @$price->product_price;?>" />
                </td>
                <td>
                    <input type="text" <?= @$ipvp_tabstop;?> id="priceincltax<?= @$aprice;?>" name="prdprices[<?= @$aprice;?>][priceincltax]" class="classPrice classPricewTax" value="<?= @$price->product_priceincltax;?>" />
                </td>
                <td>
                    <input type="text" id="dto<?= @$aprice;?>" name="prdprices[<?= @$aprice;?>][discount]" class="classProcent" value="<?= @$price->product_discount; ?>" />
                </td>
                <td>
                    <input type="text" id="total<?= @$aprice;?>" name="prdprices[<?= @$aprice;?>][total]" class="classPrice necReadonly" readonly="READONLY" value="<?= @$pp_total;?>" />
                </td>
                <td>
                    <input type="text" id="qntstart<?= @$aprice;?>" name="prdprices[<?= @$aprice;?>][qntstart]" class="classQuantity" value="<?= @$price->price_quantity_start; ?>" />
                </td>
                <td>
                    <input type="text" id="qntend<?= @$aprice;?>" name="prdprices[<?= @$aprice;?>][qntend]" class="classQuantity" value="<?= @$price->price_quantity_end; ?>" />
                </td>
                <td>
                    <?php echo HelperSelect::usergroups(@$price->group_id, 'prdprices['. @$aprice .'][group_id]', array('class' => 'inputbox select_usrgrp', 'size' => '1') , 'group_' . $aprice, false, true); ?>
                </td>
                <td>
                    <a class="pp_delete_node" id="prdprices_delete_<?= @$aprice;?>" href="#" rel="<?= @$aprice;?>">
                        <img src="<?= @jFWBase::getURL('icons');?>16/remove.png" border="0" alt="<?= @JText::_('COM_JNEGOCIO_REMOVE'); ?>'" />
                    </a>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }?>
    </body>
</table>
<a class="nec_btn" onclick="Negocio.prices.addPrice();">Add</a>
</div>