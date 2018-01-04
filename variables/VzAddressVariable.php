<?php
namespace Craft;

class VzAddressVariable
{
    /**
     * Return an array of country ids and names
     *
     * @return  array   Countries indexed by ID
     */
    public function countries()
    {
        return craft()->vzAddress->getCountries();
    }
}