# Changelog

## 1.0.11 (January 20, 2017)

### Security
- Fixed replay attack with password reset links when debug toolbar is enabled, discovered by SecureLayer7 

## 1.0.10 (December 22, 2016)

### Fixed
- Fixed Vue Warnings in Debug Bar
- Fixed URL replacement to undefined when using pagination (IE, Opera)

### Security
- Stored XSS in email templates, discovered by Raphael de la Vienne

## 1.0.9 (November 9, 2016)

### Fixed
- Fixed self update command
- Fixed an localisation issue which could lead to JS crashes (e.g. finder)

## 1.0.8 (August 18, 2016)

### Fixed
- Fixed internalization route in maintenance mode
- Fixed admin logout if an route alias exists
- Fixed location widget settings

### Changed
- Make re-login modal available in front end by default

## 1.0.7 (August 11, 2016)

### Changed
- Permission to access admin area now includes the right to use the site in maintenance mode

### Fixed
- Fixed JS error during user role sorting
- Fixed unintentional duplication of dashboard widgets in rare cases
- Fixed re-login in maintenance mode for certain API routes
- Fixed login interceptor to not intercept CORS requests

## 1.0.6 (August 8, 2016)

### Added
- Twig debug mode
- Float filter for request arguments

### Fixed
- Fixed wrong user role assignment in very rare cases (SQLite)

### Security
- XSS vulnerabilities at 404 page, discovered by Onur Yilmaz (https://www.netsparker.com)
- XSS vulnerabilities at login page, discovered by Raphael de la Vienne and Luuk Spreeuwenberg
- SQL injection vulnerability, which can be misused by users with admin privileges, discovered by Raphael de la Vienne and Luuk Spreeuwenberg

## 1.0.5 (July 1, 2016)

### Fixed
- Fixed asset upload

## 1.0.4 (June 29, 2016)

### Added
- Added node's access check

### Fixed
- Fixed access check for user and site settings
- Fixed admin dashboard for Safari private window
- Fixed a situation where a node could be assigned as its own parent
- Fixed backend password recovery
- Fixed user approval if verification is activated as well
- Fixed user verification state

## 1.0.3 (May 11, 2016)

### Added
- Parse MySQL Port from hostname in installer
- SSL support for location widget

### Changed
- Improved widget visibility settings
- Redirect to extensions/themes overview after install and activation from marketplace
- Changed signature of setup command

### Fixed
- Fixed touch support in backend
- Fixed superfluous request caching
- Fixed widget settings validation
- Fixed relative date for languages without plural
- Fixed non expiring local storage
- Fixed style and script helper for use in Twig templates

## 1.0.2 (April 22, 2016)

### Fixed
- Fixed notice when og:image in site meta settings not defined

## 1.0.1 (April 21, 2016)

### Added
- Added OpenGraph image option for site nodes
- Added file extension check for storage uploads
- Added maintenance logo option
- Added cache break for language file

### Changed
- Smoothed packages updates
- Optimized .htaccess

### Fixed
- Fixed save shortcut in Firefox
- Fixed reordering in site tree
- Fixed missing territory data
- Fixed redirect after login
- Fixed missing initial active state at pagination
- Fixed duplicated request occasionally caused by pagination

## 1.0.0 (April 13, 2016)

### Fixed
- Temporarily fixed menu params bug

## 0.11.3 (April 7, 2016)

### Fixed
- Fixed blank widget settings page
- Fixed missing marketplace icons
- Fixed RFC 3986 encoding of static URLs
- Fixed render params
- Fixed CLI command enables extensions
- Fixed db prefix check in installer
- Fixed different prefixes with SQLLite
- Fixed SQLite collations

### Removed
- Removed Guzzle dependency

### Changed
- CLI setup command requires admin password to be specified

## 0.11.2 (April 1, 2016)

### Fixed
- Fixed missing extension icons

## 0.11.1 (April 1, 2016)

### Changed
- Dashboard: Use drag handle

### Fixed
- Fixed wrapping sidebar if content in main column is to large
- Fixed adding of new images in editor

## 0.11.0 (March 30, 2016)

### Added
- Added OpenGraph and Twitter Cards
- Added CLI command to setup Pagekit installation
- Added redirect after login to user settings
- Added view.init event
- Added global params object to view
- Added file picker
- Added support for script tag attributes 'defer' and 'async'

### Changed
- Transfer widget and menu positions on theme change
- Image-, video-, link-picker: Preserve existing attributes
- Video-picker: Switched from shortcodes to html representation
- Video-picker: Improved URL matching
- Link preview: Support for html
- Editor preview: Remove script and style tags
- Installer: SQLite is now default
- Installer: Show SQLite only if available
- SelfUpdater: Check new requirements before update
- Removed system messages from template.php

### Fixed
- Fixed info page for high directory depths
- Fixed overflow container in modals
- Fixed password reset link
- Fixed canonical links

## 0.10.4 (March 1, 2016)

### Added
- Added filter cache for lists and searches
- Remember last finder position and view setting
- Added pagination cache and pagination links
- Added changelog to update view
- Added extension dependency update command for developer
- Added prefer-source option to package install and update command
- Added filter and ordering highlighting

### Changed
- Bundled Pagekit installer
- Updated to Symfony 3.0
- Hide Trash menu from Site node picker
- Deny cross site redirects after login and logout
- Session Cookie uses HttpOnly flag now
- Nicer login, registration and profile pages
- Nicer update notification on dashboard
- Improved ORM Metadata cache breaker
- Reenabled Packagist for zip uploads

### Fixed
- Fixed username validation in installer and backend (#513)
- Fixed widget settings
- Fixed embedded Youtube videos (#533)
- Fixed Gravatar retina resolution
- Fixed Gravatar mutual exclusion
- Fixed Finder thumbnails for file names containing HTML special chars
- Fixed distinguish Pagekit instances at same domain
- Fixed selecting items at site tree and widget settings
- Fixed image picker in editor now keeps class attributes
- Fixed ExceptionHandler response
- Fixed an issue which could lead to an open_basedir restriction exception
- Fixed registration verification mail
- Fixed user authenticated role assignment
- Fixed package upload zip verification
- Fixed single quote issue by using RFC4627-compliant JSON within embedded script tags (#551)

## 0.10.3 (February 19, 2016)

### Changed
- Increased package installation speed by disabling usage of Packagist repository (Pagekit API now provides a subset of required Packagist dependencies)
- Prepared self updater for bundled versions of Pagekit

## 0.10.2 (January 11, 2016)

### Added
- Show login modal for unauthorized ajax requests
- Added events to DebugBar
- Added current route info to DebugBar
- Added request switcher to DebugBar

### Changed
- Updated requirements
- Enforce reinstall of packages (#479)
- Allow comment posting for 'authenticated' users by default (#518)

### Fixed
- Fixed auto updater
- Fixed "Add" new roles (#512)
- Fixed package upload
- Cleanup package dependencies (#488)
- Fixed installation without PDO_MYSQL (#516)

## 0.10.1 (December 17, 2015)

### Added
- Added version cache break for JS and CSS
- Added 'storage:' file path

### Changed
- Updated Vue-Resource
- Improved handling of Gravatar images

### Fixed
- Fixed admin panel for IE
- Fixed system messages
- Fixed editor preview handles Vuejs markup
- Fixed redirects on login and logout

## 0.10.0 (December 15, 2015)

### Added
- Added options in video picker

### Changed
- Switched to Vuejs 1.0
- Optimized site tree
- Optimized user settings

### Fixed
- On widget copy, theme settings are copied too
- Fixed password edit on user view

## 0.9.5 (October 30, 2015)

### Added
- Added widget copy API function
- Added preliminary update notifications to dashboard

### Changed
- Random string generator uses low strength now (#478)

### Fixed
- Installer error messages use correct locale now
- Canonical routes are absolute now

## 0.9.4 (October 14, 2015)

### Added
- Added Twig support
- Resource paths for themes are added by default

### Changed
- Made type in module definition for extensions/themes obsolete

### Fixed
- Fixed date conversion to ISO8601
- Fixed feed charset and feed title escaping
- Openweathermap.org requires Api key now

## 0.9.3 (October 8, 2015)

### Fixed
- Fixed freezing browser in marketplace

## 0.9.2 (October 8, 2015)

### Added
- Added Https for Pagekit API (#415)
- Added site title to browser title
- Added Mysql character set compatibility (#434, #465)
- Added sections tabs in user edit view (#390)

### Changed
- Site tree adds its leaf node routes first (#420)
- User authentication uses separate table
- Changed config file generation

### Removed
- Removed usage of environment variables (#428)
- Removed site description
- Removed Pagekit version from generator tag

### Fixed
- Fixed user widget ordering
- Fixed nodes reordering
- Fixed Finder component for non Unix OS's (#448)
- Fixed HttpExceptions returning with Code 500
- Fixed internal URLs not being resolved in feeds (#466)
- Fixed theme updates (#472)
- Fixed extensions and themes view in IE
- Fixed permissions issue on site edit (#471)
- Fixed redirect to login, if failed, due to insufficient user rights

## 0.9.1 (September 11, 2015)

### Added
- Added additional system requirements (#410)
- Added link to gitter chat

### Changed
- By default "display errors" are set to "off"

### Fixed
- Fixed auto login
- Fixed login widget (#423)

## 0.9.0 (September 10, 2015)

### Added
- Added site tree
- Added new default theme
- Added new admin panel
- Added data-reactive components with Vue.js
- Added package management using Composer

### Changed
- Major codebase update

## 0.8.8 (November 17, 2014)

### Added
- Added pagination in Blog extension
- Added languages from Transifex

### Changed
- Updated UIkit to 2.11.1

### Fixed
- Fixed comment status bug
- Fixed reordering menu bug
- Fixed Marketplace grid
- Fixed thumbnail grid in Storage
- Fixed several issues for shared hosters

## 0.8.7 (September 8, 2014)

### Added
- Added OAuth API

### Changed
- Updated library dependencies

### Fixed
- Fixed option cache issue

## 0.8.6 (August 28, 2014)

### Changed
- Changed requirejs scripts ordering

### Removed
- Removed 'settings' from extension/theme config, use 'parameters' instead
- Removed GLOB_BRACE for Solaris compatibility

### Fixed
- Fixed Blog/Page url handling
- Fixed Blog extension settings

## 0.8.5 (August 22, 2014)

### Added
- Added Marketplace pagination
- Beautified system emails
- Added admin theme font subset latin, latin-ext

### Changed
- Updated UIkit to 2.9

### Removed
- Removed username in password reset

### Fixed
- Simple plugin regex
- Fixed Gravatar on https

## 0.8.4 (July 25, 2014)

### Added
- Added Finnish, French, Spanish, Russian translations
- Added pagination in user manager

### Changed
- Changed mod_rewrite check
- Widgets render themselves now
- Changed comments settings in blog
- The app root no longer needs to be writable if the config already exists

### Fixed
- Fixed Comments ordering (blog)
- Fixed Comments auto approval (blog)
- Fixed Finder (Windows)
- Fixed demo data for SQLite versions < 3.7.11
- Fixed language registration for themes and extensions
- Fixed blank renderer in theme skeleton
- Fixed redirect after installation
- Fixed Apache configuration to serve SVG files with correct mime type
- Fixed verify mail action
