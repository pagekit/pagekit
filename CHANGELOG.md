# Changelog

### WIP

- Added pagination in Blog extension

### 0.8.7 (September 8, 2014)

- Added OAuth api
- Fixed option cache issue
- Updated library dependencies

### 0.8.6 (August 28, 2014)

- IMPORTANT: Removed 'settings' from extension/theme config, use 'parameters' instead
- Fixed blog extension settings
- Fixed blog/page url handling
- Updated requirejs scripts ordering
- Removed GLOB_BRACE for Solaris compatiblity

### 0.8.5 (August 22, 2014)

- Added marketplace pagination
- Added beautified system emails
- Added admin theme font subset latin, latin-ext
- Fixed simple plugin regex
- Fixed gravatar on https
- Updated UIkit to 2.9
- Removed username in password reset

### 0.8.4 (July 25, 2014)

- Added Finnish, French, Spanish, Russian translation
- Added pagination in user manager
- Fixed comments ordering (blog)
- Fixed comments auto approval (blog)
- Fixed finder (Windows)
- Fixed demo data for SQLite versions < 3.7.11
- Fixed language registration for Themes and Extensions
- Fixed blank renderer in theme skeleton
- Fixed redirect after installation
- Fixed Apache configuration to serve SVG files with correct mime type
- Fixed verify mail action
- Updated mod_rewrite check
- Updated widgets now rendering themeselves
- Removed that the app root needs to be writable if the config already exists
- Refactored comments settings in blog