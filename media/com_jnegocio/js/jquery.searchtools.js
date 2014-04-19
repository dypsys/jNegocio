/**
 * Created by carless on 19/04/14.
 */

;(function ($, window, document, undefined) {

    // Create the defaults once
    var pluginName = "negsearchtools";

    var defaults = {
        // Form options
        formSelector            : '.neg-stools-form',

        // Search
        searchFieldSelector     : '.neg-stools-field-search',
        clearBtnSelector        : '.neg-stools-btn-clear',

        // Global container
        mainContainerSelector   : '.neg-stools',

        // Filter fields
        searchBtnSelector       : '.neg-stools-btn-search',

        // Extra
        chosenSupport           : true,
        clearListOptions        : false
    };

    // The actual plugin constructor
    function Plugin(element, options) {
        this.element = element;
        this.options = $.extend({}, defaults, options);
        this._defaults = defaults;

        // Initialise selectors
        this.theForm        = $(this.options.formSelector);

        // Main container
        this.mainContainer = $(this.options.mainContainerSelector);

        // Search
        this.searchButton = $(this.options.formSelector + ' ' + this.options.searchBtnSelector);
        this.searchField  = $(this.options.formSelector + ' ' + this.options.searchFieldSelector);
        this.searchString = null;
        this.clearButton  = $(this.options.clearBtnSelector);

        // Selector values
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {
        init: function () {
            var self = this;

            // Get values
            this.searchString = this.searchField.val();

            self.clearButton.click(function(e) {
                self.clear();
            });
        },
        clear: function () {
            var self = this;
            self.searchField.val('');
            self.theForm.submit();
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };

})(jQuery, window, document);
