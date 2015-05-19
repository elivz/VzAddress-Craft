<?php
namespace Craft;

/**
 * VZ Address plugin
 */
class VzAddressPlugin extends BasePlugin
{
    public function getName()
    {
        return 'VZ Address';
    }

    public function getVersion()
    {
        return '1.1.0';
    }

    public function getDeveloper()
    {
        return 'Eli Van Zoeren';
    }

    public function getDeveloperUrl()
    {
        return 'http://elivz.com';
    }

    protected function defineSettings()
    {
        return array(
            'googleApiKey' => AttributeType::String,
        );
    }

    public function getSettingsHtml()
    {
        return craft()->templates->render('vzaddress/settings', array(
            'settings' => $this->getSettings()
        ));
    }
}