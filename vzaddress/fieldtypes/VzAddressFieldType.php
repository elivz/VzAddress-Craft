<?php
namespace Craft;

/**
 * VZ Address field type
 */
class VzAddressFieldType extends BaseFieldType
{
    protected $countryCodes = array('AF', 'AX', 'AL', 'DZ', 'AS', 'AD', 'AO', 'AI', 'AQ', 'AG', 'AR', 'AM', 'AW', 'AU', 'AT', 'AZ', 'BS', 'BH', 'BD', 'BB', 'BY', 'BE', 'BZ', 'BJ', 'BM', 'BT', 'BO', 'BQ', 'BA', 'BW', 'BV', 'BR', 'IO', 'BN', 'BG', 'BF', 'BI', 'KH', 'CM', 'CA', 'CV', 'KY', 'CF', 'TD', 'CL', 'CN', 'CX', 'CC', 'CO', 'KM', 'CG', 'CD', 'CK', 'CR', 'CI', 'HR', 'CU', 'CW', 'CY', 'CZ', 'DK', 'DJ', 'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'EE', 'ET', 'FK', 'FO', 'FJ', 'FI', 'FR', 'GF', 'PF', 'TF', 'GA', 'GM', 'GE', 'DE', 'GH', 'GI', 'GR', 'GL', 'GD', 'GP', 'GU', 'GT', 'GG', 'GN', 'GW', 'GY', 'HT', 'HM', 'VA', 'HN', 'HK', 'HU', 'IS', 'IN', 'ID', 'IR', 'IQ', 'IE', 'IM', 'IL', 'IT', 'JM', 'JP', 'JE', 'JO', 'KZ', 'KE', 'KI', 'KP', 'KR', 'KW', 'KG', 'LA', 'LV', 'LB', 'LS', 'LR', 'LY', 'LI', 'LT', 'LU', 'MO', 'MK', 'MG', 'MW', 'MY', 'MV', 'ML', 'MT', 'MH', 'MQ', 'MR', 'MU', 'YT', 'MX', 'FM', 'MD', 'MC', 'MN', 'ME', 'MS', 'MA', 'MZ', 'MM', 'NA', 'NR', 'NP', 'NL', 'NC', 'NZ', 'NI', 'NE', 'NG', 'NU', 'NF', 'MP', 'NO', 'OM', 'PK', 'PW', 'PS', 'PA', 'PG', 'PY', 'PE', 'PH', 'PN', 'PL', 'PT', 'PR', 'QA', 'RE', 'RO', 'RU', 'RW', 'BL', 'SH', 'KN', 'LC', 'MF', 'PM', 'VC', 'WS', 'SM', 'ST', 'SA', 'SN', 'RS', 'SC', 'SL', 'SG', 'SX', 'SK', 'SI', 'SB', 'SO', 'ZA', 'GS', 'SS', 'ES', 'LK', 'SD', 'SR', 'SJ', 'SZ', 'SE', 'CH', 'SY', 'TW', 'TJ', 'TZ', 'TH', 'TL', 'TG', 'TK', 'TO', 'TT', 'TN', 'TR', 'TM', 'TC', 'TV', 'UG', 'UA', 'AE', 'GB', 'US', 'UM', 'UY', 'UZ', 'VU', 'VE', 'VN', 'VG', 'VI', 'WF', 'EH', 'YE', 'ZM', 'ZW');

    public function getName()
    {
        return Craft::t('Address');
    }

    public function defineContentAttribute()
    {
        return AttributeType::Mixed;
    }

    protected function defineSettings()
    {
        $defaultCountry = strtoupper(craft()->i18n->getLocaleData()->getTerritoryID(craft()->i18n->getPrimarySiteLocale()));

        return array(
            'showName' => array(AttributeType::Bool, 'default' => true),
            'defaultCountry' => array(AttributeType::String, 'default' => $defaultCountry),
        );
    }

    public function getSettingsHtml()
    {
        $countries = $this->_getCountryNames();

        return craft()->templates->render('vzaddress/fieldtype/settings', array(
            'settings' => $this->getSettings(),
            'countries' => $countries
        ));
    }

    public function getInputHtml($name, $value)
    {
        $settings = $this->getSettings();

        if (!$value) {
            $value = new VzAddress_AddressModel();
        }

        if (empty($value['country'])) {
            $value['country'] = $settings->defaultCountry;
        }

        $inputId = craft()->templates->formatInputId($name);
        $namespacedId = craft()->templates->namespaceInputId($inputId);

        // Include our Javascript
        craft()->templates->includeCssResource('vzaddress/css/input.css');
        craft()->templates->includeJsResource('vzaddress/js/input.js');
        craft()->templates->includeJs("$('#{$namespacedId}').vzAddress();");

        $countries = $this->_getCountryNames();

        return craft()->templates->render('vzaddress/fieldtype/input', array(
            'id' => $inputId,
            'name' => $name,
            'values' => $value,
            'countries' => $countries,
            'settings' => $settings,
        ));
    }

    public function prepValueFromPost($value)
    {
        if (empty($value)) {
            return new VzAddress_AddressModel();
        } else {
            return new VzAddress_AddressModel($value);
        }
    }

    private function _getCountryNames()
    {
        $localeData = craft()->i18n->getLocaleData();
        $countries = array();

        foreach ($this->countryCodes as $code) {
            $countries[$code] = $localeData->getTerritory($code);
        }

        return $countries;
    }
}