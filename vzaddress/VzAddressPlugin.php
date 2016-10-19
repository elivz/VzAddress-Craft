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
        return '1.0.6';
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
}
