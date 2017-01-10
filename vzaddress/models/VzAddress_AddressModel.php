<?php
namespace Craft;

class VzAddress_AddressModel extends BaseModel
{
    protected function defineAttributes() {
        return array(
            'name'       => AttributeType::String,
            'street'     => AttributeType::String,
            'street2'    => AttributeType::String,
            'city'       => AttributeType::String,
            'region'     => AttributeType::String,
            'postalCode' => AttributeType::String,
            'country'    => AttributeType::String,
        );
    }

    public function __toString() {
        return $this->text(true);
    }

    public function toArray() {
        $address = array_filter($this->getAttributes());
        $address['country'] = $this->countryName;
        return $address;
    }

    public function text($formatted = false) {
        if ($formatted) {
            $newTemplatePath = craft()->path->getPluginsPath() . 'vzaddress/templates/_frontend/';

            $originalTemplatesPath = method_exists(craft()->templates, 'getTemplatesPath') ?
                craft()->templates->getTemplatesPath() :
                craft()->path->getTemplatesPath();

            method_exists(craft()->templates, 'setTemplatesPath') ?
                craft()->templates->setTemplatesPath($newTemplatePath) :
                craft()->path->setTemplatesPath($newTemplatePath);

            $output = craft()->templates->render('text', array(
                'address' => $this
            ));

            method_exists(craft()->templates, 'setTemplatesPath') ?
                craft()->templates->setTemplatesPath($originalTemplatesPath) :
                craft()->path->setTemplatesPath($originalTemplatesPath);
        } else {
            $output = implode(', ', $this->toArray());
        }

        return $output;
    }

    public function html($format = "plain") {
        $newTemplatePath = craft()->path->getPluginsPath() . 'vzaddress/templates/_frontend/';

        $originalTemplatesPath = method_exists(craft()->templates, 'getTemplatesPath') ?
            craft()->templates->getTemplatesPath() :
            craft()->path->getTemplatesPath();

        method_exists(craft()->templates, 'setTemplatesPath') ?
            craft()->templates->setTemplatesPath($newTemplatePath) :
            craft()->path->setTemplatesPath($newTemplatePath);

        if (in_array($format, array('schema', 'microformat', 'rdfa'))) {
            $output = craft()->templates->render($format, array(
                'address' => $this
            ));
        } else {
            $output = str_replace("\n", '<br>', $this->text(true));
        }

        method_exists(craft()->templates, 'setTemplatesPath') ?
            craft()->templates->setTemplatesPath($originalTemplatesPath) :
            craft()->path->setTemplatesPath($originalTemplatesPath);

        return TemplateHelper::getRaw($output);
    }

    public function mapUrl($source = 'google', $params = array()) {
        $params = count($params) ? '&' . http_build_query($params) : '';
        $output = '';

        // Create the url-encoded address
        $query = urlencode(implode(', ', $this->toArray()));

        switch ($source) {
            case 'yahoo':
                $output = "https://maps.yahoo.com/#q={$query}{$params}";
                break;
            case 'bing':
                $output = "https://www.bing.com/maps/?v=2&where1={$query}{$params}";
                break;
            case 'mapquest':
                $output = "https://mapq.st/map?q={$query}{$params}";
                break;
            case 'google':
            default:
                $output = "https://maps.google.com/maps?q={$query}{$params}";
                break;
        }

        return $output;
    }

    public function staticMapUrl($params = array()) {
        $settings = craft()->plugins->getPlugin("vzAddress")->getSettings();

        $source = isset($params['source']) ? strtolower($params['source']) : 'google';
        $width  = isset($params['width']) ? strtolower($params['width']) : '400';
        $height = isset($params['height']) ? strtolower($params['height']) : '200';
        $scale  = isset($params['scale']) ? strtolower($params['scale']) : '1';
        $zoom   = isset($params['zoom']) ? strtolower($params['zoom']) : '14';
        $format = isset($params['format']) ? strtolower($params['format']) : 'png';
        $type   = isset($params['type']) ? strtolower($params['type']) : 'roadmap';
        $size   = isset($params['markerSize']) ? strtolower($params['markerSize']) : false;
        $label  = isset($params['markerLabel']) ? strtoupper($params['markerLabel']) : false;
        $color  = isset($params['markerColor']) ? strtolower($params['markerColor']) : false;

        if (isset($params['key'])) {
            $key = $params['key'];
        } elseif (!empty($settings['googleApiKey'])) {
            $key = $settings['googleApiKey'];
        }

        // Normalize the color parameter
        $color = str_replace('#', '0x', $color);

        // Create the url-encoded address
        $address = urlencode(implode(', ', $this->toArray()));

        $output = '';
        $marker = '';
        switch ($source) {
            case 'yahoo':
                // TODO
            case 'bing':
                // TODO
            case 'mapquest':
                // TODO
            case 'google':
            default:
                $marker .= $size ? 'size:'.$size.'|' : '';
                $marker .= $color ? 'color:'.$color.'|' : '';
                $marker .= $label ? 'label:'.$label.'|' : '';
                $output .= "https://maps.googleapis.com/maps/api/staticmap?zoom={$zoom}&size={$width}x{$height}&scale={$scale}&format={$format}&maptype={$type}&markers={$marker}{$address}&sensor=false";
                $output = $key ? $output.'&key='.$key : $output;
                break;
        }

        return $output;
    }

    public function staticMap($params = array()) {
        $width  = isset($params['width']) ? strtolower($params['width']) : '400';
        $height = isset($params['height']) ? strtolower($params['height']) : '200';
        $map_url = $this->staticMapUrl($params);
        $address = htmlspecialchars($this->text());

        $output = '<img src="'.$map_url.'" alt="'.$address.'" width="'.$width.'" height="'.$height.'">';
        return TemplateHelper::getRaw($output);
    }

    /**
     * Virtual Attributes
     */

    public function getCountryName() {
        $localeData = craft()->i18n->getLocaleData();
        return $localeData->getTerritory($this->country);
    }
}
