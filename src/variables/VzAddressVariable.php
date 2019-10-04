<?php

/**
 * VZ Address plugin for Craft CMS 3.x
 *
 * A simple address field for Craft.
 *
 * @link      http://elivz.com
 * @copyright Copyright (c) 2019 Eli Van Zoeren
 */

namespace elivz\vzaddress\variables;

use elivz\vzaddress\VzAddress;

/**
 * VZ Address Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.vzAddress }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Eli Van Zoeren
 * @package   VzAddress
 * @since     2.0.0
 */
class VzAddressVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Return an array of country names indexed by their IDs
     *
     * @return  array   Countries indexed by ID
     */
    public function countries()
    {
        return VzAddress::getInstance()->getCountries();
    }
}
