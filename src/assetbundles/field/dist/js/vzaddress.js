/**
 * VZ Address plugin for Craft CMS
 *
 * Address Field JS
 *
 * @author    Eli Van Zoeren
 * @copyright Copyright (c) 2019 Eli Van Zoeren
 * @link      http://elivz.com
 * @package   VzAddress
 * @since     2.0.0VzAddress
 */

(function($, window, document, undefined) {
    var pluginName = 'VzAddress',
        defaults = {};

    // Plugin constructor
    function Plugin(element, options) {
        this.element = element;

        this.options = $.extend({}, defaults, options);

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    function debounce(fn, delay) {
        var timer = null;
        return function() {
            var context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function() {
                fn.apply(context, args);
            }, delay);
        };
    }

    Plugin.prototype = {
        wideMode: false,

        init: function() {
            var vzUrl = this;
            var $field = $(this.element);

            var handleResize = debounce(function() {
                wideMode = $field.width() > 500;
                if (wideMode !== vzUrl.wideMode) {
                    vzUrl.wideMode = wideMode;
                    $field.toggleClass('wide', vzUrl.wideMode);
                }
            }, 250);

            $(window).on('resize', handleResize);
            handleResize();
        },
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function(options) {
        return this.each(function() {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery, window, document);
