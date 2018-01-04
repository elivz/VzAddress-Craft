/*
 * Functionality for VZ Address fieldtype
 * by Eli Van Zoeren - http://elivz.com
 *
 * Depends on: jQuery
 */

 ;(function ( $, window, document, undefined ) {
    var pluginName = "vzAddress";
    var defaults = {};

    // Plugin constructor
    function Plugin( element, options ) {
        this.element = element;
        this.options = $.extend( {}, defaults, options) ;
        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {
        wideMode: 0,

        init: function() {
            var vzUrl = this;
            var $field = $(this.element);
            var $geocodeBtn = $field.find('.geocodebtn');
            var $spinner = $field.find('.spinner');

            var handleResize = function() {
              wideMode = $field.width() > 500;
              if (wideMode !== vzUrl.wideMode) {
                  vzUrl.wideMode = wideMode;
                  $field.toggleClass('wide', vzUrl.wideMode);
                  $field.toggleClass('narrow', !vzUrl.wideMode);
              }
            }

            var bind = function() {
              $(window).on('resize', handleResize);
              $geocodeBtn.on('click', handleClick);
            }

            var handleClick = function(evt) {
              evt.preventDefault();
              var values = [];
              var desiredFields = [
                'street',
                'street2',
                'city',
                'region',
                'postalCode',
                'country'
              ];

              $.each(desiredFields, function(index, value) {
                var value = $field.find('[name$="[' + value + ']"]').val();
                if (value) {
                  values.push(value);
                }
              });

              geocodeAddress(values.join(' '));
            }

            var parseResults = function(response) {
              var firstResult = response.results[0];
              var latVal = firstResult.geometry.location.lat;
              var lngVal = firstResult.geometry.location.lng;

              fillLatLng(latVal, lngVal);
            }

            var fillLatLng = function(lat, lng) {
              var $latField = $field.find('[name$="[lat]"]');
              var $lngField = $field.find('[name$="[lng]"]');

              $latField.val(lat);
              $lngField.val(lng);

              $spinner.addClass('hidden');
            }

            var geocodeAddress = function(address) {
              var baseUrl = "https://maps.googleapis.com/maps/api/geocode/json";
              // googleApiKey is already in the template
              var queryString = $.param({
                address: address,
                key: googleApiKey
              });

              $.ajax({
                url: baseUrl + '?' + queryString,
                method: 'GET',
                beforeSend: function() {
                  $spinner.removeClass('hidden');
                },
                success: function(data) {
                  if (data.status != "OK") {
                    console.log('failed');
                  }
                  parseResults(data);
                },
                error: function() {
                  // TODO: Handle error
                  console.log("Shit");
                }
              });
            }

            handleResize();
            bind();
        },
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new Plugin( this, options ));
            }
        });
    };

})( jQuery, window, document );
