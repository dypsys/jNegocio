/*
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */

Negocio.productsform = {};

Negocio.productsform.App = new Class({
    Extends: Negocio.common.App,
    Implements: [Options, Events],
    options: {
        idForm: 'product-form'
    },
    initialize: function(options) {
        this.parent(options);
        this.initFormulario();
    },
    initFormulario: function() {

    }
});