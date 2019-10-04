<?php

/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      http://elivz.com
 * @copyright Copyright (c) 2019 Eli Van Zoeren
 */

/**
 * VZ Address config.php
 *
 * This file exists only as a template for the VZ Address settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'vzaddress.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [

    // Your API key for Google Maps & Geocoding
    'googleApiKey' => '',

    // HERE maps app id
    'hereAppId' => '',
    'hereApiKey' => '',

    // API key for Bing maps
    'bingApiKey' => '',

    // API key for MapQuest maps
    'mapquestApiKey' => '',

];
