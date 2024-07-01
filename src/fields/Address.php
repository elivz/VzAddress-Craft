<?php

/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      http://elivz.com
 * @copyright Copyright (c) 2019 Eli Van Zoeren
 */

namespace elivz\vzaddress\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Json;
use elivz\vzaddress\assetbundles\field\FieldAsset;
use elivz\vzaddress\models\Address as AddressModel;
use elivz\vzaddress\VzAddress;
use yii\db\Schema;

/**
 *  Field
 *
 * @author  Eli Van Zoeren
 * @package VzAddress
 * @since   2.0.0
 */
class Address extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var bool  Show name field
     */
    public $showName = true;

    /**
     * @var string  Default country value
     */
    public $defaultCountry;


    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('vzaddress', 'Address');
    }


    // Public Methods
    // =========================================================================

    function init(): void
    {
        parent::init();

        $this->defaultCountry = Craft::$app->getLocale()->getTerritoryID();
    }

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = parent::rules();
        // $rules = array_merge($rules, []);
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        $coords = VzAddress::getInstance()->address->geocodeAddress($value);
        $value->latitude = $coords['latitude'];
        $value->longitude = $coords['longitude'];

        $value = parent::serializeValue($value, $element);

        return $value;
    }

    /**
     * Normalizes the field’s value for use.
     *
     * @param mixed $value The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue(mixed $value, ElementInterface $element = null): mixed
    {
        if (is_string($value) && !empty($value)) {
            $value = Json::decode($value);
        } else if(empty($value)) {
            return new AddressModel();
        }

        // For backwards compatibility with VZ Address 1.x
        if (isset($value['__model__'])) {
            unset($value['__model__']);
        }

        // If the value is an array, we can use that to create a new Address model, otherwise try to convert the model to an array
        $value = is_array($value) ? $value : ($value->toArray() ?? []);
        return new AddressModel($value);
    }

    /**
     * Returns the component’s settings HTML.
     *
     * @return string|null
     */
    public function getSettingsHtml(): ?string
    {
        $countries = VzAddress::getInstance()->address->countries;

        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'vzaddress/_components/fields/_settings',
            [
                'field' => $this,
                'countries' => $countries,
            ]
        );
    }

    /**
     * Returns the field’s input HTML.
     *
     * @param mixed $value The field’s value. This will either be the [[normalizeValue() normalized
     *                     value]], raw POST data (i.e. if there was a validation error), or null
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return string The input HTML.
     */
    public function getInputHtml($value, ?ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(FieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Get countries list
        $countries = VzAddress::getInstance()->address->countries;

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = Json::encode([
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
        ]);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').VzAddress(" . $jsonVars . ");");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'vzaddress/_components/fields/_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'countries' => $countries,
                'defaultCountry' => $this->defaultCountry,
            ]
        );
    }
}
