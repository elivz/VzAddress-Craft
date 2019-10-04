<?php
/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      http://elivz.com
 * @copyright Copyright (c) 2017 Eli Van Zoeren
 */

namespace elivz\vzaddress\models;

use elivz\vzaddress\VzAddress;

use Craft;
use craft\base\Model;
use craft\helpers\Localization;
use craft\helpers\Template;
use craft\web\View;

/**
 * Address Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author  Eli Van Zoeren
 * @package VzAddress
 * @since   2.0.0
 *
 * @property string $countryName
 */
class Address extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $street = '';

    /**
     * @var string
     */
    public $street2 = '';

    /**
     * @var string
     */
    public $city = '';

    /**
     * @var string
     */
    public $region = '';

    /**
     * @var string
     */
    public $postalCode = '';

    /**
     * @var string
     */
    public $country = '';

    /**
     * @var float
     */
    public $latitude = null;

    /**
     * @var float
     */
    public $longitude = null;


    // Private Properties
    // =========================================================================

    /**
     * @var array
     */
    private $_settings;


    // Virtual Attributes
    // =========================================================================

    public function getCountryName()
    {
        $countries = VzAddress::getInstance()->address->countries;
        return $countries[$this->country];
    }

    public function getCoords()
    {
        if (empty($this->latitude) || empty($this->longitude)) {
            $coords = Address::geocodeAddress($this);
            $this->latitude = $coords['latitude'];
            $this->longitude = $coords['longitude'];
        }

        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }


    // Public Methods
    // =========================================================================

    /**
     * Constructor
     *
     * Gets the plugin's settings
     */
    function init()
    {
        $this->_settings = VzAddress::getInstance()->getSettings();
    }

    /**
     * Returns a plain string version of the address
     *
     * @return string Single-line address
     */
    public function __toString()
    {
        return $this->text(true);
    }

    /**
     * Returns an array of the address components
     *
     * @param  array $fields    the fields being requested
     * @param  array $expand    the additional fields being requested for exporting.
     * @param  bool  $recursive whether to recursively return array representation of embedded objects.
     * @return array the array representation of the object
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $address = parent::toArray($fields, $expand, $recursive);
        $address = array_filter($address);
        return $address;
    }

    /**
     * Returns a plain-text representation of the address
     *
     * @param  boolean $formatted Whether line-breaks should be included
     * @return string
     */
    public function text(bool $formatted = false): string
    {
        if ($formatted) {
            $oldMode = \Craft::$app->view->getTemplateMode();
            \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
            $html = \Craft::$app->view->renderTemplate(
                'vzaddress/_frontend/text', [
                'address' => $this,
                ]
            );
            \Craft::$app->view->setTemplateMode($oldMode);

            return $html;
        }

        return implode(', ', $this->toArray());
    }

    /**
     * Returns a formated HTML representation of the address
     *
     * @param  string $format Which format to use
     * @return $string
     */
    public function html(string $format = 'plain'): \Twig_Markup
    {
        if (in_array($format, ['schema', 'microformat', 'rdfa'], true)) {
            $oldMode = \Craft::$app->view->getTemplateMode();
            \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
            $html = \Craft::$app->view->renderTemplate(
                "vzaddress/_frontend/$format", [
                'address' => $this,
                ]
            );
            \Craft::$app->view->setTemplateMode($oldMode);
        } else {
            $html = str_replace("\n", '<br>', $this->text(true));
        }
        return Template::raw($html);
    }

    /**
     * Returns the URL for a static image from various mapping services
     *
     * @param  string $source Which mapping service to use
     * @param  array  $params Option to pass through to the source
     * @return $string
     */
    public function mapUrl(string $source = 'google', array $params = []): string
    {
        // Create the url-encoded address
        $query = urlencode(implode(', ', $this->toArray()));

        // Any additional URL parameters to include
        $params = count($params) ? '&' . http_build_query($params) : '';

        if ($source == 'osm') {
            $url = "http://www.openstreetmap.org/search?query={$query}?{$params}";
        } elseif ($source == 'here') {
            $url = "https://wego.here.com/search/{$query}?{$params}";
        } elseif ($source == 'bing') {
            $url = "https://www.bing.com/maps?q={$query}{$params}";
        } elseif ($source == 'mapquest') {
            $url = "https://www.mapquest.com/search/results?query={$query}{$params}";
        } else {
            if (empty($params['key']) && !empty($this->_settings->googleApiKey)) {
                $params['key'] = $this->_settings->googleApiKey;
            }
            $url = "https://www.google.com/maps?q={$query}{$params}";
        }

        return $url;
    }

    /**
     * Returns the URL for a static map image from one of several sources
     *
     * @param  array $params Parameters to control the map size & style
     * @return string
     */
    public function staticMapUrl(array $params = []): string
    {
        $source = !empty($params['source']) ? strtolower($params['source']) : 'google';
        unset($params['source']);

        // Set default dimensions
        $params['width'] = !empty($params['width']) ? (int)$params['width'] : 400;
        $params['height'] = !empty($params['height']) ? (int)$params['height'] : 200;
        $params['zoom'] = !empty($params['zoom']) ? (int)$params['zoom'] : 14;

        if ($source == 'here') {
            // Get the API key, either from the template params or the plugin config
            if (empty($params['appId']) && !empty($this->_settings->hereAppId)) {
                $params['appId'] = $this->_settings->hereAppId;
            }
            if (empty($params['appCode']) && !empty($this->_settings->hereAppCode)) {
                $params['appCode'] = $this->_settings->hereAppCode;
            }

            $appId = !empty($params['appId']) ? strtolower($params['appId']) : false;
            $appCode = !empty($params['appCode']) ? strtolower($params['appCode']) : false;

            if (!$appId || !$appCode) {
                throw new \yii\base\UserException('You must specify an App ID and App Code to use HERE WeGo maps.');
            }

            // Rename width & height parameters
            $params['w'] = $params['width'];
            $params['h'] = $params['height'];
            $params['z'] = $params['zoom'];
            unset($params['width'], $params['height'], $params['zoom']);

            // Get the coordinates
            $coords = $this->getCoords();
            $params['c'] = implode(',', $coords);

            // Add all the parameters to a query string
            $query = http_build_query($params);

            return "https://image.maps.cit.api.here.com/mia/1.6/mapview?{$query}";
        } elseif ($source == 'bing') {
            // Get the API key, either from the template params or the plugin config
            if (empty($params['key']) && !empty($this->_settings->bingApiKey)) {
                $params['key'] = $this->_settings->bingApiKey;
            }

            if (empty($params['key'])) {
                throw new \yii\base\UserException('You must specify a Bing Maps API Key.');
            }

            // Combine width & height parameters
            $params['size'] = $params['width'].','.$params['height'];
            unset($params['width'], $params['height']);

            // Place a marker on the address
            $params['query'] = urlencode(implode(', ', $this->toArray()));

            // Add all the parameters to a query string
            $query = http_build_query($params);

            return "https://dev.virtualearth.net/REST/v1/Imagery/Map/imagerySet/query?{$query}";
        } elseif ($source == 'mapquest') {
            // Get the API key, either from the template params or the plugin config
            if (empty($params['key']) && !empty($this->_settings->mapquestApiKey)) {
                $params['key'] = $this->_settings->mapquestApiKey;
            }

            if (empty($params['key'])) {
                throw new \yii\base\UserException('You must specify a Mapquest API Key.');
            }

            // Combine width & height parameters
            $params['size'] = $params['width'].','.$params['height'];
            unset($params['width'], $params['height']);

            // Place a marker on the address
            $params['locations'] = urlencode(implode(', ', $this->toArray()));

            // Add all the parameters to a query string
            $query = http_build_query($params);

            return "https://www.mapquestapi.com/staticmap/v5/map?{$query}";
        } else {
            // Get the API key, either from the template params or the plugin config
            if (empty($params['key']) && !empty($this->_settings->googleApiKey)) {
                $params['key'] = $this->_settings->googleApiKey;
            }

            if (!$params['key']) {
                throw new \yii\base\UserException('You must specify an API Key to use Google Maps.');
            }

            // Combine width & height parameters
            $params['size'] = $params['width'].'x'.$params['height'];
            unset($params['width'], $params['height']);

            // Marker options
            $params['markers'] = '';
            $params['markers'] .= !empty($params['markerSize']) ? 'size:'.strtolower($params['markerSize']).'|' : false;
            $params['markers'] .= !empty($params['markerLabel']) ? 'color:'.strtoupper($params['markerLabel']).'|' : false;
            $params['markers'] .= !empty($params['markerColor']) ? 'label:'.strtolower(str_replace('#', '0x', $params['markerColor'])).'|' : false;
            $params['markers'] .= urlencode(implode(', ', $this->toArray()));
            unset($params['markerSize'], $params['markerLabel'], $params['markerColor']);

            // Map style
            if (!empty($params['styles'])) {
                $params['style'] = $this->_googleMapsStyleString($params['style']);
            }

            // Add all the other parameters to a query string
            $params['sensor'] = 'false';
            $query = http_build_query($params);

            // Generate the URL
            return "https://maps.googleapis.com/maps/api/staticmap?{$query}";
        }
    }

    /**
     * Returns an HTML image tag for a static map image from one of several sources
     *
     * @param  array $params Parameters to control the map size & style
     * @return string
     */
    public function staticMap(array $params = []): \Twig_Markup
    {
        $width = !empty($params['width']) ? (int)$params['width'] : 400;
        $height = !empty($params['height']) ? (int)$params['height'] : 200;
        $mapUrl = $this->staticMapUrl($params);
        $alt = htmlspecialchars($this->text());

        $output = '<img src="'.$mapUrl.'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'">';
        return Template::raw($output);
    }


    /**
     * Generate a dynamic map using the Google Maps Javascript API
     *
     * @var array $params An array of MapOptions for the Google Map object
     * @var array $icon An array of configuration options for the Marker icon
     *
     * @see https://developers.google.com/maps/documentation/javascript/3.exp/reference#MapOptions
     *
     * @return \Twig_Markup The markup string wrapped in a Twig_Markup object
     */
    public function dynamicMap($params = [], $icon = []): \Twig_Markup
    {
        // Get the API key, either from the template params or the plugin config
        if (empty($params['key']) && !empty($this->_settings->googleApiKey)) {
            $params['key'] = $this->_settings->googleApiKey;
        }

        if (!$params['key']) {
            throw new \yii\base\UserException('You must specify an API Key to use Google Maps.');
        }

        $width  = isset($params['width']) ? strtolower($params['width']) : '400';
        $height = isset($params['height']) ? strtolower($params['height']) : '200';
        unset($params['width'], $params['height']);

        // these mirror MapOptions object - https://developers.google.com/maps/documentation/javascript/3.exp/reference#MapOptions
        $defaultParams = [
            'zoom'   => 14,
            'center' => $this->_getCoords(),
        ];
        $params = array_merge($defaultParams, $params);

        // Configuration for the generated map
        $config = [
            'id'     => uniqid('map-'),
            'width'  => $width,
            'height' => $height,
        ];

        // Get the rendered template as a string
        $oldMode = \Craft::$app->view->getTemplateMode();
        \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
        $html = \Craft::$app->view->renderTemplate(
            'vzaddress/_frontend/googlemap_dynamic', [
            'options' => $params,
            'config'  => $config,
            'icon'    => $icon,
            'key'     => $params['key'],
            ]
        );
        \Craft::$app->view->setTemplateMode($oldMode);

        return TemplateHelper::getRaw($html);
    }


    // Private Methods
    // =========================================================================

    /**
     * Geocode the address and return the latitude and longitude
     * 
     * @return array
     */
    private function _getCoords() {
        
    }

    /**
     * Parse the given style array and return a formatted string
     *
     * @see https://developers.google.com/maps/documentation/javascript/styling Documentation of styling Google Maps
     *
     * @var    array $style A multidimensional array structured according to Google's Styled Maps configuration
     * @return string A style string formatted for use with the Google Static Maps API
     */
    private function _googleMapsStyleString(array $style): string
    {
        $output = '';

        foreach ($style as $elem) {
            $declaration = [];

            if (array_key_exists('featureType', $elem)) {
                $declaration[] = "feature:{$elem['featureType']}";
            }

            if (array_key_exists('elementType', $elem)) {
                $declaration[] = "element:{$elem['elementType']}";
            }

            foreach ($elem['stylers'] as $styler) {
                foreach ($styler as $key => $value) {
                    if ($key == 'color') {
                        $value = str_replace('#', '0x', $value);
                    }
                    $declaration[] .= "{$key}:{$value}";
                }
            }

            $output .= '&style=' . implode($declaration, '|');
        }

        return $output;
    }
}
