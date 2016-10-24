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
        return '1.1.0';
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

    public function registerImportOptionPaths()
    {
        return array(
            'VzAddress' => 'vzaddress/integrations/import/options',
        );
    }

    public function modifyImportRow($element, $map, $data)
    {
        $rowData = array_combine($map, $data);
        $content = [];

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
}
