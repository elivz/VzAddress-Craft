/*
 * Functionality for VZ Address fieldtype
 * by Eli Van Zoeren - http://elivz.com
 *
 * Depends on: jQuery
 */

 (function ($, window, document, undefined) {

    var pluginName = "vzAddress";
    var defaults = {
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
            styles: [
                {
                    "featureType": "landscape",
                    "stylers": [
                        { "color": "#fdfdfd" }
                    ]
                },
                {
                    "featureType": "water",
                    "stylers": [
                        { "color": "#d6dcf2" }
                    ]
                },
                {
                    "elementType": "labels.text.fill",
                    "stylers": [
                        { "color": "#29323d" }
                    ]
                },
                {
                    "featureType": "road",
                    "elementType": "geometry.fill",
                    "stylers": [
                        { "color": "#ebedef" }
                    ]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "geometry.stroke",
                    "stylers": [
                        { "color": "#899098" }
                    ]
                },
                {
                    "featureType": "road.arterial",
                    "elementType": "geometry.stroke",
                    "stylers": [
                        { "color": "#acb3bb" }
                    ]
                },
                {
                    "featureType": "road.local",
                    "elementType": "geometry.stroke",
                    "stylers": [
                        { "color": "#d1d5d9" }
                    ]
                },
                {
                    "featureType": "poi",
                    "stylers": [
                        { "visibility": "off" }
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
        latLng: false,

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
            vzAddress.$field.find('input, select').on('change', $.proxy(vzAddress.geocode, vzAddress));
        },

        initMap: function() {
            var vzAddress = this;
            var mapElement = vzAddress.$field.find('.vzaddress-map').get(0);

            if (vzAddress.showMap) {
                vzAddress.map = new google.maps.Map(mapElement, vzAddress.options.mapOptions);
            }

            vzAddress.geocoder = new google.maps.Geocoder();
            vzAddress.geocode();
        },

        geocode: function() {
            var vzAddress = this;

            if (!vzAddress.geocoder) return;

            var address = vzAddress.$field.find('input[type="text"], select').map(function() {
                return $(this).val() || null;
            }).get().join(', ');

            // Clear existing marker
            if (vzAddress.marker) {
                vzAddress.marker.setMap(null);
            }

            vzAddress.geocoder.geocode(
                { 'address': address },
                $.proxy(vzAddress.updateMap, vzAddress)
            );
        },

        updateMap: function(results, status) {
            var vzAddress = this;
            var componentMapping = {
                'locality': 'city',
                'administrative_area_level_1': 'region',
                'postal_code': 'postalCode'
            };

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
    };

    $.fn[pluginName] = function(options) {
        return this.each(function() {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new Plugin( this, options ));
            }
        });
    };

})(jQuery, window, document);