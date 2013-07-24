/*
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

