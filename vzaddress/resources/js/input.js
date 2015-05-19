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

        wideMode: false,
        $field: false,

        map: false,
        marker: false,
        latLng: false,

        init: function() {
            var vzAddress = this;
            vzAddress.$field = $(this.element);

            $(window).on('resize', function() {
                wideMode = vzAddress.$field.width() > 450;
                if (wideMode !== vzAddress.wideMode) {
                    vzAddress.wideMode = wideMode;
                    vzAddress.$field.toggleClass('wide', vzAddress.wideMode);
                    vzAddress.$field.toggleClass('narrow', !vzAddress.wideMode);
                }
            });

            vzAddress.initMap();

            vzAddress.$field.find('input, select').on('change', $.proxy(this.geocode, this));
        },

        initMap: function() {
            this.map = new google.maps.Map(this.$field.find('.vzaddress-map').get(0), this.options.mapOptions);
            this.geocoder = new google.maps.Geocoder();
            this.geocode();
        },

        geocode: function() {
            if (!this.map) return;

            var vzAddress = this;
            var address = vzAddress.$field.find('input, select').map(function() {
                return $(this).val();
            }).get().join(' ');

            // Clear existing marker
            if (vzAddress.marker) {
                vzAddress.marker.setMap(null);
            }

            vzAddress.geocoder.geocode(
                { 'address': address },
                function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        vzAddress.latLng = results[0].geometry.location;
                        vzAddress.map.fitBounds(results[0].geometry.viewport);
                        vzAddress.marker = new google.maps.Marker({
                            position: vzAddress.latLng,
                            map: vzAddress.map
                        });
                    } else {
                        var latLng = new google.maps.LatLng(0, 0);
                        vzAddress.map.setCenter(vzAddress.latLng);
                        vzAddress.map.setZoom(0);
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