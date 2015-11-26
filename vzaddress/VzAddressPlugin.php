<?php
namespace Craft;

/**
 * VZ Address plugin
 */
class VzAddressPlugin extends BasePlugin
{
    function getName()
    {
        return 'VZ Address';
    }

    function getVersion()
    {
        return '1.0.2';
    }

    function getDeveloper()
    {
        return 'Eli Van Zoeren';
    }

    function getDeveloperUrl()
    {
        return 'http://elivz.com';
    }

    public function registerFeedMeMappingOptions()
    {
        return array(
            'VzAddress' => 'vzaddress/_plugins/feedMeOptions',
        );
    }

    public function prepForFeedMeFieldType($field, &$data, $handle)
    {
        // Ensure it's a VzAddress field
        if ($field->type == 'VzAddress') {

            // Initialize content array
            $content = array();

            // Separate field handle & subfield handle
            if (preg_match('/^(.*)\[(.*)]$/', $handle, $matches)) {
                $fieldHandle    = $matches[1];
                $subFieldHandle = $matches[2];

                // Ensure address array exists
                if (!array_key_exists($fieldHandle, $content)) {
                    $content[$fieldHandle] = array();
                }

                // Set value to subfield of correct address array
                $content[$fieldHandle][$subFieldHandle] = $data[$fieldHandle];
            }

            // Modify data
            $data = $content;
        }
    }
}
