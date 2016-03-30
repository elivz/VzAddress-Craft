/*
 * Functionality for VZ Address fieldtype
 * by Eli Van Zoeren - http://elivz.com
 *
 * Depends on: jQuery
 */

 ;(function ( $, window, document, undefined ) {

    var pluginName = "vzAddress",
        defaults = {
            mapOptions: {
                center: {
                    lat: 0,
                    lng: 0
                },
                zoom: 0,
                scrollwheel: false,
                panControl: false,
                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.SMALL,
                    position: google.maps.ControlPosition.TOP_RIGHT
                },
                mapTypeControl: false,
                scaleControl: false,
                streetViewControl: false,
                overviewMapControl: false,
                styles:
                [
                  {
                    "featureType": "landscape",
                    "stylers": [
                      { "color": "#f7f7f8" }
                    ]
                  },{
                    "featureType": "water",
                    "stylers": [
                      { "color": "#d6dcf2" }
                    ]
                  },{
                    "featureType": "road.highway",
                    "elementType": "geometry.stroke",
                    "stylers": [
                      { "color": "#737f8c" },
                      { "lightness": 40 }
                    ]
                  },{
                    "featureType": "poi",
                    "stylers": [
                      { "visibility": "off" }
                    ]
                  },{
                    "featureType": "transit",
                    "stylers": [
                      { "visibility": "on" }
                    ]
                  },{
                    "featureType": "road.highway",
                    "elementType": "geometry.fill",
                    "stylers": [
                      { "color": "#ebedef" }
                    ]
                  },{
                    "elementType": "labels.text.fill",
                    "stylers": [
                      { "color": "#29323d" }
                    ]
                  }
                ]
            }
        };


    // Plugin constructor
    function Plugin( element, options ) {
        this.element = element;

        this.options = $.extend( {}, defaults, options) ;

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {

        showMap: false,
        $field: false,
        fieldId: false,
        map: false,
        marker: false,

        init: function() {
            var vzAddress = this;
            vzAddress.$field = $(this.element);
            vzAddress.fieldId = this.element.id;
            vzAddress.showMap = vzAddress.$field.hasClass('has-map');

            $(window).on('resize', function() {
                showMap = vzAddress.showMap && vzAddress.$field.width() > 450;
                vzAddress.$field.toggleClass('has-map', showMap);
            });

            vzAddress.initMap();

            vzAddress.$field.find('input, select').on('change', $.proxy(this.geocode, this));
        },

        initMap: function() {
            var vzAddress = this;

            if (vzAddress.showMap) {
                vzAddress.map = new google.maps.Map(vzAddress.$field.find('.vzaddress-map').get(0), vzAddress.options.mapOptions);
            }

            vzAddress.geocoder = new google.maps.Geocoder();
            vzAddress.geocode();
        },

        getAddress: function() {
            var vzAddress = this;

            return vzAddress.$field.find('input[type="text"], select').map(function() {
                return $(this).val() || null;
            }).get().join(', ');
        },

        geocode: function() {
            var vzAddress = this;

            if (!vzAddress.geocoder) return;

            var address = vzAddress.getAddress();

            vzAddress.geocoder.geocode(
                { 'address': address },
                function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results.length === 1) {
                            var location = results[0];

                            if (vzAddress.showMap) {
                                vzAddress.map.fitBounds(location.geometry.viewport);
                                vzAddress.marker = new google.maps.Marker({
                                    position: location.geometry.location,
                                    map: vzAddress.map
                                });
                            }

                            // Interpolate missing data
                            var componentMapping = {
                                'locality': 'city',
                                'administrative_area_level_1': 'region',
                                'postal_code': 'postalCode'
                            };
                            $.each(location.address_components, function(i, component) {
                                $.each(component.types, function(i, type) {
                                    if (componentMapping[type]) {
                                        var $field = vzAddress.$field.find('#'+vzAddress.fieldId+'-'+componentMapping[type]);
                                        if ($field.val() === '') {
                                            $field.val(component.short_name);
                                        }
                                    }
                                });
                            });

                            // Update the lat/long fields
                            vzAddress.$field.find('#'+vzAddress.fieldId+'-latitude').val(location.geometry.location.lat());
                            vzAddress.$field.find('#'+vzAddress.fieldId+'-longitude').val(location.geometry.location.lng());
                        }

                    } else {
                        if (vzAddress.showMap) {
                            var latLng = new google.maps.LatLng(0, 0);
                            vzAddress.map.setCenter(latLng);
                            vzAddress.map.setZoom(0);
                        }

                        // Clear latitude and longitude fields
                        vzAddress.$field.find('#'+vzAddress.fieldId+'-latitude').val('');
                        vzAddress.$field.find('#'+vzAddress.fieldId+'-longitude').val('');
                    }
                }
            );
        }


    };

    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new Plugin( this, options ));
            }
        });
    };

})( jQuery, window, document );