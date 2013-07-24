/*
 * Negocio.product.prices
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2013 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */

var Negocio = Negocio || {};
Negocio.productprices = {};

Negocio.productprices.App = new Class({
    
    Implements: [Events, Options],
    
    options: {
        elementtable: '',
        work_pricewithtax: 0,
        image_path: '',
        priceFormat: {aNeg: '-', pSign: 's', mDec: 2, aSep: ',', aDec: '.', aSign: ' €'},
        percentFormat: {aNeg: '-', pSign: 's', mDec: 2, aSep: ',', aDec: '.', aSign: ' %', vMin: '0.00', vMax: '100'},
        quantityFormat: {aNeg: '-', pSign: '', mDec: 0, aSep: ',', aDec: '.', aSign: '', vMin: '0.00', vMax: '999999'},
        dateformat: '%Y-%m-%d'
    },
    
    tablePrices: null,
    
    initialize: function(options) {
        this.setOptions(options);
        this.tablePrices = new jNegocioHtmlTable(document.id(this.options.elementtable));
    },
            
    eventcalculteprice: function(e) {

        var event = e || window.event;
        if (event.stop) {
            event.stop();
        }
        event.preventDefault();
        
        var num = event.target.getParent().getParent().getAttribute('rel');
        var name_price          = 'price'+num;
	var name_priceincltax   = 'priceincltax'+num;
	var name_discount       = 'dto'+num;
	var name_total          = 'total'+num;
        
        var classtax = Negocio.taxes.getTax(Negocio.taxes.getdefaultTax());
        
        var nbruto = parseFloat( jQuery("#" + name_price).autoNumeric('get') );
        
        var priceincltax= nbruto + (nbruto * parseFloat(classtax.tax_rate) /100);
        var pdto        = parseFloat( jQuery("#" + name_discount).autoNumeric('get') );
	var ntotaldto   = (nbruto * pdto )/100;
	var nneto       = nbruto - ntotaldto;
	var total       = (nneto * ((100+ parseFloat(classtax.tax_rate))/100));
        
        jQuery("#" +name_priceincltax).autoNumeric('set', priceincltax);
        jQuery("#" +name_total).autoNumeric('set', total);
    },

    eventcalcultepricewithtax: function(e) {

        var event = e || window.event;
	if (event.stop) {
            event.stop();
	}
        event.preventDefault();
        
        var num = event.target.getParent().getParent().getAttribute('rel');
        var name_price          = 'price'+num;
	var name_priceincltax   = 'priceincltax'+num;
	var name_discount       = 'dto'+num;
	var name_total          = 'total'+num;
        
        var classtax = Negocio.taxes.getTax(Negocio.taxes.getdefaultTax());
        
        var nprecioiva  = parseFloat(  jQuery("#" + name_priceincltax).autoNumeric('get') );
        var nbruto      = nprecioiva / ((100+ parseFloat(classtax.tax_rate) )/100);
        
        var pdto        = parseFloat( jQuery("#" + name_discount).autoNumeric('get') );
        var ntotaldto   = (nbruto * pdto)/100;
        var nneto 	= nbruto - ntotaldto;
        var ntotal 	= nneto * ((100+parseFloat(classtax.tax_rate))/100);
        var ntotaliva 	= nbruto * ((100+parseFloat(classtax.tax_rate))/100);
        
        jQuery("#" +name_price).autoNumeric('set', nbruto);
        jQuery("#" +name_total).autoNumeric('set', ntotaliva);
    },
            
    calculatetotalpricewithtax: function(priceincltax, discount) {
        var classtax = Negocio.taxes.getTax(Negocio.taxes.getdefaultTax());
        var nbruto = priceincltax / ((100 + parseFloat(classtax.tax_rate)) / 100);
        var ntotaldto = (nbruto * discount) / 100;
        var nneto = nbruto - ntotaldto;
        return (nneto * ((100 + parseFloat(classtax.tax_rate)) / 100));
    },
            
    calculatetotalprice: function(price, discount) {
        var classtax = Negocio.taxes.getTax(Negocio.taxes.getdefaultTax());
        var nbruto = parseFloat(price);
        var ntotaldto = (nbruto * discount) / 100;
        var nneto = nbruto - ntotaldto;
        return (nneto * ((100 + parseFloat(classtax.tax_rate)) / 100));
    },
    
    erase: function(e) {
        var event = e || window.event;
	if (event.stop) {
            event.stop();
	}
        event.preventDefault();
        var row = e.target.getParent().getParent().getParent();
        var num = event.target.getParent().getAttribute('rel');
        var name_deleted = 'pp_deleted_'+num;
        if ($(name_deleted)) {
            $(name_deleted).value = 1;
        }
        row.setStyle('display', 'none');
    },
            
    addPrice: function() {
        var numOfItems = this.tablePrices.body.rows.length;

        var IDppid = 'ppid' + numOfItems;
        var IDppdelete = 'pp_deleted_' + numOfItems;
        var IDprice = 'price' + numOfItems;
        var IDpricetax = 'priceincltax' + numOfItems;
        var IDtotal = 'total' + numOfItems;
        var IDdto = 'dto' + numOfItems;
	var IDqntstart = 'qntstart' + numOfItems;
	var IDqntend = 'qntend'+ numOfItems;
        var IDusrGroup = 'group_'+ numOfItems;
        var IDprdDelete = 'prdprices_delete_'+ numOfItems;

        var numcolumn = 0;
        var newRow = [];
        
        var delement = new Element('div');
        var xelement = new Element('input', {id: IDppid, type: 'hidden', name: 'prdprices['+numOfItems +'][productprice_id]', 'value': '0'});
        var yelement = new Element('input', {id: IDppdelete, type: 'hidden', name: 'prdprices['+numOfItems +'][deleted]', 'value': '0'});
        delement.grab(xelement);
        delement.grab(yelement);
        
        newRow[numcolumn] = delement;
        numcolumn = numcolumn + 1;
        
        if (this.options.work_pricewithtax == 1) {
            newRow[numcolumn] = new Element('input', {id: IDprice, type: 'text', name: 'prdprices['+numOfItems +'][price]', 'class': 'classPrice', 'value': '0', 'readonly':'readonly'});
            numcolumn = numcolumn + 1;
            newRow[numcolumn] = new Element('input', {id: IDpricetax, type: 'text', name: 'prdprices['+numOfItems +'][priceincltax]', 'class': 'classPrice', 'value': '0'});
            numcolumn = numcolumn + 1;
        } else {
            newRow[numcolumn] = new Element('input', {id: IDprice, type: 'text', name: 'prdprices['+numOfItems +'][price]', 'class': 'classPrice', 'value': '0'});
            numcolumn = numcolumn + 1;
            newRow[numcolumn] = new Element('input', {id: IDpricetax, type: 'text', name: 'prdprices['+numOfItems +'][priceincltax]', 'class': 'classPrice', 'value': '0', 'readonly':'readonly'});
            numcolumn = numcolumn + 1;            
        }
        newRow[numcolumn] = new Element('input', {id: IDdto, type: 'text', name: 'prdprices['+numOfItems +'][discount]', 'class': 'classProcent', 'value': '0'});
        numcolumn = numcolumn + 1;
        newRow[numcolumn] = new Element('input', {id: IDtotal, type: 'text', name: 'prdprices['+numOfItems +'][total]', 'class': 'classPrice necReadonly', 'value': '0', 'readonly': 'readonly' });
        numcolumn = numcolumn + 1;
//        newRow[numcolumn] = new Element('span', {html: 'fecha Ini'});
//        numcolumn = numcolumn + 1;
//        newRow[numcolumn] = new Element('span', {html: 'fecha Fin'});
//        numcolumn = numcolumn + 1;
        newRow[numcolumn] = new Element('input', {id: IDqntstart, type: 'text', name: 'prdprices['+numOfItems +'][qntstart]', 'class': 'classQuantity', 'value': '0'});
        numcolumn = numcolumn + 1;
        newRow[numcolumn] = new Element('input', {id: IDqntend, type: 'text', name: 'prdprices['+numOfItems +'][qntend]', 'class': 'classQuantity', 'value': '999999'});
        numcolumn = numcolumn + 1;
        
        var select = new Element('select', {id: IDusrGroup, name: 'prdprices['+numOfItems +'][group_id]', 'class': 'inputbox select_usrgrp'} );
        Negocio.usergroups.usergroups.each(function(objeto, index) {
            select.grab(new Element('option', {value: objeto.id ,text: objeto.name }));
        });
        
        newRow[numcolumn] = select;
        numcolumn = numcolumn + 1;
        
        var dlink = new Element('a', {href: '#', 'class': 'pp_delete_node', id: IDprdDelete, 'rel': numOfItems });
            
        var dimage = new Element( 'img', {id: 'idelete'+ numOfItems, src: this.options.image_path+'16/remove.png' });
        dlink.adopt(dimage);
        
        newRow[numcolumn] = dlink;
        numcolumn = numcolumn + 1;

        var rowclass = 'row' + (numOfItems % 2);
        this.tablePrices.push(newRow, {'class': rowclass, 'rel': numOfItems});

        $(IDprice).addEvent('blur', this.eventcalculteprice);
        $(IDprice).addEvent('keyup', this.eventcalculteprice);
	$(IDpricetax).addEvent('blur',this.eventcalcultepricewithtax);
	$(IDpricetax).addEvent('keyup',this.eventcalcultepricewithtax);        

        if (this.options.work_pricewithtax == 1) {
            $(IDdto).addEvent('blur', this.eventcalcultepricewithtax);
            $(IDdto).addEvent('keyup', this.eventcalcultepricewithtax);            
        } else {
            $(IDdto).addEvent('blur', this.eventcalculteprice);
            $(IDdto).addEvent('keyup', this.eventcalculteprice);
        }
        $(IDprdDelete).addEvent('click', this.erase);

        jQuery("#" + IDprice).autoNumeric('init', this.options.priceFormat);
        jQuery("#" + IDpricetax).autoNumeric('init', this.options.priceFormat);
        jQuery("#" + IDtotal).autoNumeric('init', this.options.priceFormat);
        jQuery("#" + IDdto).autoNumeric('init', this.options.percentFormat);
        jQuery("#" + IDqntend).autoNumeric('init', this.options.quantityFormat);
        jQuery("#" + IDqntend).autoNumeric('init', this.options.quantityFormat);
        
        if (this.options.work_pricewithtax == 1) {
            $(IDpricetax).focus();
        } else {
            $(IDprice).focus();
        }
    }
});