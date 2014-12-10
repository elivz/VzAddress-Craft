VZ Address Fieldtype for Craft
==============================

A simple address field for Craft.


Template Variables
------------------

### Single-line output

Just output the address, on a single line.

    {{ entry.fieldName }} or {{ entry.fieldName.inline }}

### Plain HTML ouput

Standard address format, with `<br>` tags at the end of each line.

    {{ entry.fieldName.plainHtml }}

### Plain text ouput

Standard address format, with line-breaks at the end of each line.

    {{ entry.fieldName.plainText }}

### Address components

    {{ entry.fieldName.name }}
    {{ entry.fieldName.street }}
    {{ entry.fieldName.street2 }}
    {{ entry.fieldName.city }}
    {{ entry.fieldName.region }}
    {{ entry.fieldName.postalCode }}
    {{ entry.fieldName.country }} // Abbreviation, e.g. "US"
    {{ entry.fieldName.countryName }} // Full name, e.g. "United States"


Installation
------------

Download and unzip the extension. Upload the `vzaddress` folder to your `/craft/plugins/` folder. Go to Settings -> Plugins in the Craft control panel and enable the VZ Address plugin. You can now create new Address fields.


Support
-------

Please post all bugs or feature requests in [GitHub Issues](https://github.com/elivz/VzAddress-Craft/issues). I maintain this fieldtype in my spare time, but I will try to respond to questions as quickly as possible.

Roadmap
-------

