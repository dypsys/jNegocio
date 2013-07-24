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

