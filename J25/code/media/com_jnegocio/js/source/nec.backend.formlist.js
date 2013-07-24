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

