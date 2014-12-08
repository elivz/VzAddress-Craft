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

    public function inline()
    {
        $values = array_filter($this->attributes);
        $values['country'] = $this->countryName;
        return implode(', ', $values);
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

    public function __toString()
    {
        return $this->inline();
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