VZ Address Fieldtype for Craft
==============================

A simple address field for Craft.


Template Variables
------------------

### Single-line output

Just output the address, on a single line.

    {{ entry.fieldName.text }}

### Plain text ouput

Standard address format, with line-breaks at the end of each line.

    {{ entry.fieldName.text(true) }}

Or simply:

    {{ entry.fieldName }}

### HTML ouput

Standard address format, with `<br>` tags at the end of each line.

    {{ entry.fieldName.html }}

You can also output the html in one of three structured data formats: Schema.org, Microformats, and RDFa. To do that, just add your preferred format as a parameter to the `html` tag.

    {{ entry.fieldName.html('schema') }}

### Address components

    {{ entry.fieldName.name }}
    {{ entry.fieldName.street }}
    {{ entry.fieldName.street2 }}
    {{ entry.fieldName.city }}
    {{ entry.fieldName.region }}
    {{ entry.fieldName.postalCode }}
    {{ entry.fieldName.country }} // Abbreviation, e.g. "US"
    {{ entry.fieldName.countryName }} // Full name, e.g. "United States"

### Link to the address on an external mapping service

    {{ entry.fieldName.mapUrl('google', { zoom: 5 }) }}

The first parameter is the service to use, one of `google`, `yahoo`, `bing`, or `mapquest`. The second parameter is an array of paramters to be passed through in the map URL. Please see the documentation for the particular mapping provider you are using for the available options.

### Output the URL of a static map image from Google Maps

    {% set mapParams = {
        width: '400',
        height: '200',
        type: 'satellite',
        key: '12345'
        styles: [
            {
                'featureType': 'road.local',
                'elementType': 'geometry',
                'stylers': [
                    { 'color': '#00ff00' }
                ]
            }
        ]
    } %}
    {{ entry.fieldName.staticMapUrl(mapParams) }}

The available parameters include:

* `width` and `height` - The size in pixels of the image that is generated. (default: 400 x 200)
* `scale` - Number of pixels returned. Set this to `2` for retina/hidpi support. (default: 1)
* `zoom` - Zoom level of the map. (default: 14)
* `format` - Specifies the image format to return. One of: png, png32, jpg, jpg-baseline, or gif. (default: png)
* `type` - One of: roadmap, satellite, hybrid, or terrain. (default: roadmap)
* `markerSize` - The relative size of the pushpin that marks the address location. One of: normal, mid, small, or tiny. (default: normal)
* `markerColor` - The color of the pushpin. Either a named color (black, brown, green, purple, yellow, blue, gray, orange, red, or white) or a 6-digit hex-code, like "#ff0000". Three-digit color codes are not supported. (default: red)
* `markerLabel` - Instead of the normal dot, you can specify a single letter or number to appear on the pushpin. (default: none)
* `key` - Your [Google API key](https://developers.google.com/maps/documentation/static-maps/get-api-key). Not required, but you may be subject to usage limits if you don't have an API key. You can also specify your API key globally in the plugin settings page. (default: none)
* `styles` - A [multidimensional array](https://developers.google.com/maps/documentation/javascript/styling) defining the styling you wish to be applied to the map 

### Output an image tag containing the static Google Map

    {% set mapParams = {
        width: '400',
        height: '200',
        type: 'satellite'
    } %}
    {{ entry.fieldName.staticMap(mapParams) }}

Accepts the same parameters as `staticMapUrl`.

### Output a dynamic Google Map

    {% set options = {
        width: 400,
        height: 200,
        clickableIcons: 'false',
        disableDefaultUI: 'true',
        disableDoubleClickZoom: 'false',
        draggable: 'true',
        draggableCursor: 'null',
        draggingCursor: 'null',
        fullscreenControl: 'true',
        gestureHandling: 'null',
        heading: '0',
        keyboardShortcuts: 'true',
        mapTypeControl: 'false',
        maxZoom: 'null',
        minZoom: 'null',
        noClear: 'false',
        rotateControl: 'false',
        scaleControl: 'false',
        scrollwheel: 'true',
        streetViewControl: 'false',
        tilt: '0',
        zoom: '16',
        zoomControl: 'false',
        styles: [
            {
                'featureType': 'road.local',
                'elementType': 'geometry',
                'stylers': [
                    { 'color': '#00ff00' }
                ]
            }
        ]
    } %}

    {% set icon = {
        url: "path/to/icon.png",
        width: 50,
        height: 50,
        origin: {
            x: 0,
            y: 0
        },
        anchor: {
            x: 0,
            y: 0
        }
    } %}

    {{ entry.fieldName.dynamicMap(options, icon) }}

* The options array mirrors the [MapOptions](https://developers.google.com/maps/documentation/javascript/3.exp/reference#MapOptions) configuration from Google Maps Javascript API. All available options are shown in the example above. Only the `width`, `height`, & `zoom` options are set by default.
* The icon array is used for configuring the look of the marker icon. All available options are shown in the example above.

### Bonus: Output a list of all countries

VZ Address makes available an array of all countries indexed by their country code as `craft.vzAddress.countries`. You can use this to generate your frontend entry form, or wherever you may need a list of all the countries in the world.

    <select name="country">
        {% for countryCode, countryName in craft.vzAddress.countries %}
            <option value="{{ countryCode }}">{{ countryName }}</option>
        {% endfor %}
    </select>

Installation
------------

Download and unzip the extension. Upload the `vzaddress` folder to your `/craft/plugins/` folder. Go to Settings -> Plugins in the Craft control panel and enable the VZ Address plugin. You can now create new Address fields.


Support
-------

Please post all bugs or feature requests in [GitHub Issues](https://github.com/elivz/VzAddress-Craft/issues). I maintain this fieldtype in my spare time, but I will try to respond to questions as quickly as possible.

Roadmap
-------

* Map preview in control panel
* Geocoding to fill in missing pieces of the address
