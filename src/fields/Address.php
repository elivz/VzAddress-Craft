<?php
/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      http://elivz.com
 * @copyright Copyright (c) 2017 Eli Van Zoeren
 */

namespace elivz\vzaddress\fields;

use elivz\vzaddress\VzAddress;
use elivz\vzaddress\assetbundles\field\FieldAsset;
use elivz\vzaddress\models\Address as AddressModel;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Json;

use yii\db\Schema;

/**
 *  Field
 *
 * Whenever someone creates a new field in Craft, they must specify what
 * type of field it is. The system comes with a handful of field types baked in,
 * and we’ve made it extremely easy for plugins to add new ones.
 *
 * https://craftcms.com/docs/plugins/field-types
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
     * @var bool
     */
    public $showName = true;

    /**
     * @var string
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

    function init()
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
    public function rules()
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
     * Normalizes the field’s value for use.
     *
     * @param mixed                 $value   The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (is_string($value) && !empty($value)) {
            $value = Json::decode($value);
        }
        $model = new AddressModel($value);
        return $model;
    }

    /**
     * Returns the component’s settings HTML.
     *
     * @return string|null
     */
    public function getSettingsHtml()
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
     * @param mixed                 $value   The field’s value. This will either be the [[normalizeValue() normalized
     *                                       value]], raw POST data (i.e. if there was a validation error), or null
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return string The input HTML.
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(FieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Get countries list
        $countries = VzAddress::getInstance()->address->countries;

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = Json::encode(
            [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
            ]
        );
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
            ]
        );
    }
}
