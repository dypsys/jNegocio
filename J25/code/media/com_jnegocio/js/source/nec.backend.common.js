/*
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

