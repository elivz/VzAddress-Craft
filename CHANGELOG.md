# VZ Address Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 2.0.0 - 2017-12-24
### Added
- Compatible with Craft 3.x only.
- Support for [OpenStreetMap (OSM)](http://www.openstreetmap.org).
- Support for [HERE WeGo maps](https://wego.here.com).
- Static Maps from all supported mapping services.

### Improved

### Removed
- Yahoo source for static maps.
- `styles` parameter for Google Maps (use `style` instead)

## 1.5.1 - 2017-10-31
### Fixed
- Failure to use Google API key from settings when generating a dymanic Google map.
- Use secure Google API url for geocoding addresses.

## 1.5.0 - 2017-06-14
### Added
- `craft.vzAddress.countries` variable containing an array of all the countries in the world.

## 1.4.2 - 2017-02-28
### Improved
- Accept either `style` or `styles` attributes for Google Maps styling.

## 1.4.1 - 2017-02-21
### Improved
- Handle missing Google API key more gracefully.

## 1.4.0 - 2017-02-14
### Added
- New `dynamicMap` tag outputs an embedded Google map to the page.
- Support for the [FeedMe](https://github.com/engram-design/FeedMe) plugin.

### Improved
- The `style` parameter on all map tags has been renamed to `styles` to match Google Maps API.

## 1.3.0 - 2017-01-17
### Added
- Support for Google map style configuration.

## 1.2.0 - 2017-01-09
### Added
- Google API Key setting, which will apply to all map tags.

### Improved
- All Google Maps URLs are now secure by default.

## 1.1.1 - 2016-11-16
### Fixed
- Prevent syntax error in PHP 5.3.

## 1.1.0 - 2016-10-23
### Added
- Support for importing address data using the [Import](https://github.com/boboldehampsink/import) plugin.

## 1.0.6 - 2016-10-19
### Fixed
- Leave API key in original case.

## 1.0.5 - 2016-09-16
### Fixed
- Duplicate map URL output.

## 1.0.4 - 2016-09-12
### Fixed
- Google static maps output."

### Added
- Static maps template tags now accept the `key` parameter for your Google API key.

## 1.0.3 - 2016-09-12
**Now requires Craft 2.5 or higher.**

### Added
- Plugin icon for Craft 2.5.
- Can now use Address fields as a column in element indexes.

### Improved
- Better custom field layout.
- Use the site's default locale as the default country for new fields.

### Fixed
- Remove duplicate name in microformatted outputs.

## 1.0.2 - 2016-03-30
### Fixed
- Reset the template path after including the formatting address, so includes later in the template aren't broken.

## 1.0.0 - 2014-12-08
- Initial release for Craft.