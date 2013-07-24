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

