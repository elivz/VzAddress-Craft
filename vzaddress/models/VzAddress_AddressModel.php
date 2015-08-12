<?php
namespace Craft;

class VzAddress_AddressModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'name' => AttributeType::String,
            'street' => AttributeType::String,
            'street2' => AttributeType::String,
            'city' => AttributeType::String,
            'region' => AttributeType::String,
            'postalCode' => AttributeType::String,
            'country' => AttributeType::String,
        );
    }

    protected function setTemplatePath()
    {
        craft()->path->setTemplatesPath(craft()->path->getPluginsPath() . 'vzaddress/templates/_frontend/');
    }

    public function __toString()
    {
        return $this->text(true);
    }

    public function toArray()
    {
        $address = array_filter($this->attributes);
        $address['country'] = $this->countryName;
        return $address;
    }

    public function text($formatted = false)
    {
        if ($formatted) {
            $originalTemplatesPath = craft()->path->getTemplatesPath();
            craft()->path->setTemplatesPath(craft()->path->getPluginsPath() . 'vzaddress/templates/_frontend/');

            $output = craft()->templates->render('text', array(
                'address' => $this
            ));

            craft()->path->setTemplatesPath($originalTemplatesPath);
        } else {
            $output = implode(', ', $this->toArray());
        }

        return $output;
    }

    public function html($format = "plain")
    {
        $originalTemplatesPath = craft()->path->getTemplatesPath();
        craft()->path->setTemplatesPath(craft()->path->getPluginsPath() . 'vzaddress/templates/_frontend/');

        if (in_array($format, array('schema', 'microformat', 'rdfa'))) {
            $output = craft()->templates->render($format, array(
                'address' => $this
            ));
        } else {
            $output = str_replace("\n", '<br>', $this->text(true));
        }

        craft()->path->setTemplatesPath($originalTemplatesPath);

        return TemplateHelper::getRaw($output);
    }

    public function mapUrl($source='google', $params=array())
    {
        $params = count($params) ? '&' . http_build_query($params) : '';

        // Create the url-encoded address
        $query = urlencode(implode(', ', $this->toArray()));

        switch ($source)
        {
            case 'yahoo':
                $output = "http://maps.yahoo.com/#q={$query}{$params}";
                break;
            case 'bing':
                $output = "http://www.bing.com/maps/?v=2&where1={$query}{$params}";
                break;
            case 'mapquest':
                $output = "http://mapq.st/map?q={$query}{$params}";
                break;
            case 'google': default:
                $output = "http://maps.google.com/maps?q={$query}{$params}";
                break;
        }

        return $output;
    }

    public function staticMapUrl($params=array())
    {
        $source = isset($params['source']) ? strtolower($params['source']) : 'google';
        $width  = isset($params['width']) ? strtolower($params['width']) : '400';
        $height = isset($params['height']) ? strtolower($params['height']) : '200';
        $scale  = isset($params['scale']) ? strtolower($params['scale']) : '1';
        $zoom   = isset($params['zoom']) ? strtolower($params['zoom']) : '14';
        $format = isset($params['format']) ? strtolower($params['format']) : 'png';
        $type   = isset($params['type']) ? strtolower($params['type']) : 'roadmap';
        $size   = isset($params['markerSize']) ? strtolower($params['markerSize']) : FALSE;
        $label  = isset($params['markerLabel']) ? strtoupper($params['markerLabel']) : FALSE;
        $color  = isset($params['markerColor']) ? strtolower($params['markerColor']) : FALSE;

        // Normalize the color parameter
        $color = str_replace('#', '0x', $color);

        // Create the url-encoded address
        $address = urlencode(implode(', ', $this->toArray()));

        $output = isset($params['secure']) && $params['secure'] == 'yes' ? 'https' : 'http';
        $marker = '';
        switch ($source)
        {
            case 'yahoo':
                // TODO
            case 'bing':
                // TODO
            case 'mapquest':
                // TODO
            case 'google': default:
                $marker .= $size ? 'size:'.$size.'|' : '';
                $marker .= $color ? 'color:'.$color.'|' : '';
                $marker .= $label ? 'label:'.$label.'|' : '';
                $output .= "://maps.googleapis.com/maps/api/staticmap?zoom={$zoom}&size={$width}x{$height}&scale={$scale}&format={$format}&maptype={$type}&markers={$marker}{$address}&sensor=false";
                break;
        }

        return $output;
    }

    public function staticMap($params=array())
    {
        $width  = isset($params['width']) ? strtolower($params['width']) : '400';
        $height = isset($params['height']) ? strtolower($params['height']) : '200';
        $map_url = $this->staticMapUrl($params);
        $address = htmlspecialchars($this->inline());

        $output = '<img src="'.$map_url.'" alt="'.$address.'" width="'.$width.'" height="'.$height.'">';
        return TemplateHelper::getRaw($output);
    }

    /**
     * Virtual Attributes
     */

    public function getCountryName()
    {
        $localeData = craft()->i18n->getLocaleData();
        return $localeData->getTerritory($this->country);
    }
}
