VZ Address Fieldtype for Craft
==========================

A VZ Address field displays a textbox where the user can enter a URL. It will ping the URL they entered and display an error message if it doesn't find a valid webpage there. It also prompts the user to update URLs that are redirected to a different location.

![Screenshot of VZ Address for Craft](../gh-pages/screenshot.png?raw=true)

Please note that VZ URL will not prevent the user from saving their weblog entry if if cannot validate the URL - it just warns them. This is intentional, perhaps they are linking to a page they have not yet created, or the site they are linking to is currently down but they know the URL is correct.

Template Variables
------------------

### Simple output

Just output the URL that was entered.

    <a href="{{ entry.fieldName }}">Link</a>

Installation
------------

Download and unzip the extension. Upload the `vzaddress` folder to your `/craft/plugins/` folder. Go to Settings -> Plugins in the Craft control panel and enable the VZ Address plugin. That's it!

Support
-------

Please post all bugs or feature requests in [GitHub Issues](https://github.com/elivz/VzAddress-Craft/issues). I maintain this fieldtype in my spare time, but I will try to respond to questions as quickly as possible.

Roadmap
-------
