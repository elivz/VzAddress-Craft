<?php

namespace elivz\vzaddress\fields;

use Cake\Utility\Hash;
use craft\feedme\base\Field;
use craft\feedme\base\FieldInterface;
use craft\feedme\helpers\DataHelper;

class VzAddressFeedMe extends Field implements FieldInterface
{
    // Properties
    // =========================================================================

// Static
    public static $name = 'VZ Address Field';
    public static $class = 'elivz\vzaddress\fields\Address';


    // Templates
    // =========================================================================

    public function getMappingTemplate()
    {
        return 'vzaddress/integrations/feedme';
    }


    // Public Methods
    // =========================================================================

    public function parseField()
    {
        $preppedData = [];
        $fields = Hash::get($this->fieldInfo, 'fields');
        if (!$fields) {
            return null;
        }
        foreach ($fields as $subFieldHandle => $subFieldInfo) {
            $preppedData[$subFieldHandle] = DataHelper::fetchValue($this->feedData, $subFieldInfo);
        }
        // Protect against sending an empty array
        if (!$preppedData) {
            return null;
        }
        return $preppedData;
    }

}
