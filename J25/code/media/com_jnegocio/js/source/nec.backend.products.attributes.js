/*
 * Negocio.product.prices
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2013 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */

var Negocio = Negocio || {};
Negocio.productattributes = {};

Negocio.productattributes.App = new Class({
    
    Implements: [Events, Options],
    
    options: {
        elementtable: '',
        image_path: ''
    },
    
    tableAttributes: null,
    
    initialize: function(options) {
        this.setOptions(options);
        this.tableAttributes = new jNegocioHtmlTable(document.id(this.options.elementtable));
    },
            
    add: function() {
        var numOfItems = this.tableAttributes.body.rows.length;
        
    }
});    