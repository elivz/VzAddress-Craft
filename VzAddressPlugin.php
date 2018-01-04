<?php
namespace Craft;

/**
 * VZ Address plugin
 */
class VzAddressPlugin extends BasePlugin
{
    public function getName() {
        return 'VZ Address';
    }

    public function getVersion() {
        return '1.5.1';
    }

    public function getSchemaVersion() {
        return '1.0.0';
    }

    public function getDeveloper() {
        return 'Eli Van Zoeren';
    }

    public function getDeveloperUrl() {
        return 'http://elivz.com';
    }

    public function getDocumentationUrl() {
        return 'https://github.com/elivz/VzAddress-Craft/blob/master/README.md';
    }

    public function getReleaseFeedUrl() {
        return 'https://raw.githubusercontent.com/elivz/VzAddress-Craft/master/changelog.json';
    }

    protected function defineSettings() {
        return array(
            'googleApiKey' => array(AttributeType::String, 'default' => ''),
        );
    }

    public function getSettingsHtml() {
        return craft()->templates->render('vzaddress/settings', array(
            'settings' => $this->getSettings()
        ));
    }

    public function registerImportOptionPaths() {
        return array(
            'VzAddress' => 'vzaddress/integrations/import/options',
        );
    }

    public function modifyImportRow($element, $map, $data) {
        $rowData = array_combine($map, $data);
        $content = array();

        foreach ($rowData as $key => $value) {
            if (preg_match('/^(.*)\[(.*)]$/', $key, $matches)) {
                $handle   = $matches[1];
                $subHandle = $matches[2];
                $field = craft()->fields->getFieldByHandle($handle);

                if ($field->fieldType->classHandle === 'VzAddress') {
                    if (!array_key_exists($handle, $content)) {
                        $content[$handle] = array();
                    }

                    $content[$handle][$subHandle] = $value;
                }
            }
        }

        // Set new content
        $element->setContentFromPost($content);
    }

    public function registerFeedMeMappingOptions() {
        return array(
            'VzAddress' => 'vzaddress/integrations/feedme/options',
        );
    }

    public function prepForFeedMeFieldType($field, &$data, $handle) {
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
