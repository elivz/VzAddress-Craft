<?php
namespace Craft;

/**
 * VZ Address field type
 */
class VzAddressFieldType extends BaseFieldType implements IPreviewableFieldType
{
    public function getName() {
        return Craft::t('Address');
    }

    public function defineContentAttribute() {
        return AttributeType::Mixed;
    }

    protected function defineSettings() {
        $defaultCountry = strtoupper(craft()->i18n->getLocaleData()->getTerritoryID(craft()->i18n->getPrimarySiteLocale()));

        return array(
            'showName' => array(AttributeType::Bool, 'default' => true),
            'defaultCountry' => array(AttributeType::String, 'default' => $defaultCountry),
        );
    }

    public function getSettingsHtml() {
        $countries = craft()->vzAddress->getCountries();

        return craft()->templates->render('vzaddress/fieldtype/settings', array(
            'settings' => $this->getSettings(),
            'countries' => $countries
        ));
    }

    public function getInputHtml($name, $value) {
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

        $countries = craft()->vzAddress->getCountries();

        return craft()->templates->render('vzaddress/fieldtype/input', array(
            'id' => $inputId,
            'name' => $name,
            'values' => $value,
            'countries' => $countries,
            'settings' => $settings,
        ));
    }

    public function prepValueFromPost($value) {
        if (empty($value)) {
            return new VzAddress_AddressModel();
        } else {
            return new VzAddress_AddressModel($value);
        }
    }

    public function getTableAttributeHtml($value) {
        if ($value) {
            return $value->html();
        }
    }
}
