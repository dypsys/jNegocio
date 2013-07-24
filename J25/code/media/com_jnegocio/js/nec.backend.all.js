/*
 * Negocio.utils.table
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */
Class.Occlude = new Class({
    occlude: function(property, element) {
        element = document.id(element || this.element);
        var instance = element.retrieve(property || this.property);
        if (instance && !$defined(this.occluded))
            return this.occluded = instance;

        this.occluded = false;
        element.store(property || this.property, this);
        return this.occluded;
    }
});

var jNegocioHtmlTable = new Class({
    Implements: [Options, Events, Class.Occlude],
    options: {
        properties: {
            cellpadding: 0,
            cellspacing: 0,
            border: 0
        },
        rows: [],
        headers: [],
        footers: []
    },
    property: 'jNegocioHtmlTable',
    initialize: function() {
        var params = Array.link(arguments, {options: Object.type, table: Element.type});
        this.setOptions(params.options);
        this.element = params.table || new Element('table', this.options.properties);
        if (this.occlude())
            return this.occluded;
        this.build();
    },
    build: function() {
        this.element.store('jNegocioHtmlTable', this);

        this.body = document.id(this.element.tBodies[0]) || new Element('tbody').inject(this.element);
        $$(this.body.rows);

        if (this.options.headers.length)
            this.setHeaders(this.options.headers);
        else
            this.thead = document.id(this.element.tHead);
        if (this.thead)
            this.head = document.id(this.thead.rows[0]);

        if (this.options.footers.length)
            this.setFooters(this.options.footers);
        this.tfoot = document.id(this.element.tFoot);
        if (this.tfoot)
            this.foot = document.id(this.tfoot.rows[0]);

        this.options.rows.each(function(row) {
            this.push(row);
        }, this);

        ['adopt', 'inject', 'wraps', 'grab', 'replaces', 'dispose'].each(function(method) {
            this[method] = this.element[method].bind(this.element);
        }, this);
    },
    toElement: function() {
        return this.element;
    },
    empty: function() {
        this.body.empty();
        return this;
    },
    set: function(what, items) {
        var target = (what == 'headers') ? 'tHead' : 'tFoot';
        this[target.toLowerCase()] = (document.id(this.element[target]) || new Element(target.toLowerCase()).inject(this.element, 'top')).empty();
        var data = this.push(items, {}, this[target.toLowerCase()], what == 'headers' ? 'th' : 'td');
        if (what == 'headers')
            this.head = document.id(this.thead.rows[0]);
        else
            this.foot = document.id(this.thead.rows[0]);
        return data;
    },
    setHeaders: function(headers) {
        this.set('headers', headers);
        return this;
    },
    setFooters: function(footers) {
        this.set('footers', footers);
        return this;
    },
    push: function(row, rowProperties, target, tag) {
        if ($type(row) == "element" && row.get('tag') == 'tr') {
            row.inject(target || this.body);
            return {
                tr: row,
                tds: row.getChildren('td')
            };
        }
        var tds = row.map(function(data) {
            var td = new Element(tag || 'td', data ? data.properties : {}),
                    type = (data ? data.content : '') || data,
                    element = document.id(type);
            if ($type(type) != 'string' && element)
                td.adopt(element);
            else {
                if ($type(type) != 'string')
                    td.adopt(data);
                else
                    td.set('html', type);
            }

            return td;
        });

        return {
            tr: new Element('tr', rowProperties).inject(target || this.body).adopt(tds),
            tds: tds
        };
    }
});

/*
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */

var Negocio = Negocio || {};
Negocio = {};

window.addEvent('domready', function() { 
	Negocio.utils = new Negocio.utils.App();
});

/*
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */
var Negocio = Negocio || {};
Negocio.taxes = {};

Negocio.taxes.App = new Class({
    Implements: [Events, Options],
    taxes: [],
    options: {
        taxes: [],
        defaulttypetax: '',
        defaulttax: ''
    },
    initialize: function(options) {
        this.setOptions(options);
        this.addTaxes(this.options.taxes);
    },
    addTaxes: function(taxes) {
        $$(taxes).each(function(taxitem) {
            var row = new Negocio.Tax(taxitem);
            this.addTax(row);
        }, this);
    },
    addTax: function(taxclass) {
        this.taxes[taxclass.id] = taxclass;
    },
    getTax: function(tax_id) {
        return this.taxes[tax_id];
    },
    setdefaultTypeTax: function(typetax_id) {
        this.options.defaulttypetax = typetax_id;
        this.options.defaulttax = this.findtaxbytypetax(typetax_id);
    },
    setdefaultTax: function(tax_id) {
        this.options.defaulttax = tax_id;
    },
    getdefaultTax: function() {
        return this.options.defaulttax;
    },
    findtaxbytypetax: function(searchTypetax) {
        var itemreturn = 0;
        this.taxes.each(function(objeto, index) {
            if (objeto.typetaxid == searchTypetax) {
                itemreturn = index;
            }
        });
        return itemreturn;
    }
});

Negocio.Tax = new Class({
    Implements: [Options],
    id: 0,
    tax_rate: 0,
    typetaxid: 0,
    initialize: function(object, options) {
        this.setOptions(options);
        $each(object, function(value, key) {
            this[key] = value;
        }.bind(this));
    }
});

/*
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */
var Negocio = Negocio || {};
Negocio.usergroups = {};

Negocio.usergroups.App = new Class({
    Implements: [Events, Options],
    usergroups: [],
    options: {
        usergroups: [],
        defaultUsergroupId: ''
    },
    initialize: function(options) {
        this.setOptions(options);
        this.addUserGroups(this.options.usergroups);
    },
    addUserGroups: function(items) {
        $$(items).each(function(item) {
            var row = new Negocio.UserGroup(item);
            this.addUserGroup(row);
        }, this);
    },
    addUserGroup: function(item) {
        this.usergroups[item.id] = item;
    },
    getUserGroup: function(usergroup_id) {
        return this.usergroups[usergroup_id];
    },
    setDefaultUserGroupID: function(usergroup_id) {
        this.options.defaultUsergroupId = usergroup_id;
    },
    getDefaultUserGroupID: function() {
        return this.options.defaultUsergroupId;
    },
    getUserGroups: function() {
        return this.usergroups;
    }
});

Negocio.UserGroup = new Class({
    Implements: [Options],
    id: 0,
    name: 0,
    initialize: function(object, options) {
        this.setOptions(options);
        $each(object, function(value, key) {
            this[key] = value;
        }.bind(this));
    }
});/*
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */
var Negocio = Negocio || {};
Negocio.currencies = {};

Negocio.currencies.App = new Class({
    Implements: [Events, Options],
    currencies: [],
    options: {
        editor: null,
        currencies: [],
        defaultcurrency: ''
    },
    initialize: function(options) {
        this.setOptions(options);
        this.addCurrencies(this.options.currencies);
    },
    addCurrencies: function(currencies) {
        $$(currencies).each(function(currency) {
            var row = new Negocio.Currency(currency);
            this.currencies[row.id] = row;
        }, this);
    },
    addCurrency: function(currency) {
        this.currencies[currency.id] = currency;
    },
    getCurrency: function(currency_id) {
        return this.currencies[currency_id];
    },
    getjQueryFormating: function(currency_id) {
        var itemcurrency = this.currencies[currency_id];
        var str = "{aNeg: '-', pSign: 's', mDec: " + itemcurrency.decimals + ", aSep: '" + itemcurrency.thousands_separator + "', aDec: '" + itemcurrency.decimal_separator + "', aSign: '" + itemcurrency.symbol + "' }";
        return str;
    },
    getdefaultCurrency: function() {
        return this.options.defaultcurrency;
    },
    setdefaultCurrency: function(currency_id) {
        this.options.defaultcurency = currency_id;
    }
});

Negocio.Currency = new Class({
    Implements: [Options],
    initialize: function(object, options) {
        this.setOptions(options);
        $each(object, function(value, key) {
            this[key] = value;
        }.bind(this));
    }
});

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
        // var fx = row.effects({duration: 300, transition: Fx.Transitions.linear});
        // fx.start({'height': 0,'opacity': 0 });
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
        
//        select.grab(new Element('option', {value: 1,text: 'Default'}));
        
        // newRow[numcolumn] = new Element('span', {html: '&nbsp;'});
        newRow[numcolumn] = select;
        numcolumn = numcolumn + 1;
        
        var dlink = new Element('a', {href: '#', 'class': 'pp_delete_node', id: IDprdDelete, 'rel': numOfItems });

//            events : { 
//                click: function(){ alert('click'); }
//                }

            
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
});/*
 * @version		$Id$
 * @package		Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license		Comercial License
 */
Negocio.common = {};

Negocio.common.App = new Class({
	Implements: [Events, Options],
		
	options: {
			locale: 'en-GB'
		},
	
	initialize: function(options) {
		//options
		this.setOptions(options);
		
		// locale
        Locale.use(this.options.locale);
        
        // init functions
        this.common();
	},
	
	common: function() {
        if($('system-message')){
            this.hide($('system-message'));
        }
	},
	
	hide: function(el, delay) {
        if(!delay) delay = 4000;
        setTimeout(function(){
            el.tween('opacity', 0)
        }, delay);
        setTimeout(function(){
            el.morph({
                'height': 0,
                'margin': 0
            });
        }, delay+400);
        setTimeout(function(){
            el.destroy()
        }, delay+800);
    }
});

function jFWUpdatePage() { location.reload(true);}

/*
 * @version		$Id$
 * @package		Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license		Comercial License
 */

Negocio.formlist = {};

Negocio.formlist.App = new Class({
	Extends: Negocio.common.App,
	
	Implements: [Options, Events],
	
	options: {
			idForm: 'adminForm'
		},
	
	initialize: function(options) {
		this.parent(options);
			
		//options
		// this.setOptions(options);

        this.formularioList();
	},
	
	/* Lists */
	formularioList: function() {
		var self = this;
		if ($(this.options.idForm)) {
			$$('.nec_action_clearfilters').addEvent('click', function(e){
				self.clearfilters();
			});
			
			$$('.nec_action_applyfilters').addEvent('click', function(e){
				$(this.options.idForm).submit();
			});
		}
	},
	
	clearfilters: function() {
		$$('.necFilter').each(function(el){
			el.setProperty('value', '');
		});
		$(this.options.idForm).submit();
	}
});

/*
 * @version		$Id$
 * @package		Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license		Comercial License
 */

var Negocio = Negocio || {};
Negocio.utils = {};

Negocio.utils.App = new Class({
	Implements: [Events, Options],
	
	tvalue : '',
	
	options: {},
	
	initialize: function(options) {
		//options
		this.setOptions(options);		
	},
	
	loadElements_Form_toArray: function(form) {
		// loop through form elements and prepare an array of objects for passing to server
	    var str = new Array();
	    for(i=0; i<form.elements.length; i++)
	    {
	    	if (form.elements[i].name == "_token") {
	    		this.tvalue = form.elements[i].value;
	    	} else {
		        postvar = {
		            name : form.elements[i].name,
		            value : form.elements[i].value,
		            checked : form.elements[i].checked,
		            id : form.elements[i].id
		        };
		        str[i] = postvar;
	    	}
	    }
	    return str;
	}
});

