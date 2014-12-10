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

    public function __toString()
    {
        return $this->inline();
    }

    public function toArray()
    {
        $address = array_filter($this->attributes);
        $address['country'] = $this->countryName;
        return $address;
    }

    public function inline()
    {
        return implode(', ', $this->toArray());
    }

    public function plainText()
    {
        $lines = array();
        if ($this->name) $lines[] = $this->name;
        if ($this->street) $lines[] = $this->street;
        if ($this->street2) $lines[] = $this->street2;
        $lines[] = ($this->city ? $this->city.', ' : '') . ($this->region ? $this->region.', ' : '') . ($this->postalCode ? $this->postalCode : '');
        if ($this->country) $lines[] = $this->countryName;

        return implode("\n", $lines);
    }

    public function plainHtml()
    {
        return str_replace("\n", '<br>', $this->plainText());
    }

    public function mapUrl($params=array())
    {
        $source = isset($params['source']) ? strtolower($params['source']) : 'google';
        unset($params['source']);
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

    /**
     * Virtual Attributes
     */

    public function getCountryName()
    {
        $localeData = craft()->i18n->getLocaleData();
        return $localeData->getTerritory($this->country);
    }
}