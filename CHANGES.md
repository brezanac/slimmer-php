# Changelog

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2020-08-05

### Added

- first fully functional release


## [1.0.1] - 2020-08-10

### Added

- a new Twig function called `response()` to allow on demand setting of response HTTP codes

## [1.1.0] - 2020-09-14

### Added

- the `TwigAssets` extension which now contains all the custom Twig functions
- a nice looking favicon.ico
- separate SCSS files for variables and animations
- a nice looking animation for the main page

### Removed

- generated static assets which are now removed from the repository

### Modified

- the app.js is now initialized automatically
- the manifest is now loaded only once per every request and stored statically
- the SCSS file layout is a bit restructured
- the Docker configuration now loads the mod_expires modules by default for Apache
