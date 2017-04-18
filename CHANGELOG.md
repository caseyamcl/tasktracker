# Changelog

All Notable changes to `caseyamcl/tasktracker` will be documented in this file

## Version 2.2.1 (2017-04-18)

### Fixed

- Updated dependencies in `composer.json` to be compatible with more modern versions of
  Symfony components.
- Updated `.travis.yml` to run tests on PHP 7.0 and 7.1

## Version 2.2 (2016-01-13)

### Fixed

- Symfony Console Log line prefix defaults to 'SKIP', 'SUCC', or 'FAIL' instead of *
  to make logs more clear for monochrome terminals

### Added

- Added ability to create custom line prefixes for Symfony Console log
- Added `Tracker::run()` method to run the tracker with a callback to process items

## Version 2.1.1 (2015-12-03)

### Fixed

- Make Symfony Console Log display unknown sizes correctly.

## Version 2.1 (2015-04-09)

### Added

- Scrutinizer badge in README and automated Scrutinizer checks

### Fixed

- Fixed bug in `Report` class where `itemTime` was not getting set correctly

## Version 2.0 (2015-03-19)

### Changed

- Major rewrite; BC-breaking API changes
- Bumped minimum PHP version from 5.3 to 5.4
- Changed autoloader from PSR-0 to PSR-4
- Refactored event notification system from custom solution to use Symfony EventDispatcher
- Renamed `OutputHandler` classes to `Subscribers`
- Moved non-core functions into `Helper` Traits
- Removed tracker state-tracking from `Report` and `Tick` classes to the `Tracker` class.
  - `Report` and `Tick` classes are now new-able value objects.
- `Tracker::tick()`, `Tracker::finish`, and `Tracker::abort` now returns an instances of the latest `Report` class.
- Replaced Monolog output subscriber with more flexible PSR-3 output subscriber.
- Refactored Symfony Console output subscriber to use Symfony built-in progress bar instead of a custom solution

### Added

- Added ability to pass custom data to each tick event
- Added `SymfonyConsoleLog` subscriber for logging events line-by-line to the console.
- Created `TrackerFactory` service class
- Added `LICENSE` and `CONTRIBUTING.md` files
- Added `.gitattributes` to minimize download size
- Added `Tracker::build()` alternative constructor for creating a traker with a list of event subscribers

## Version 1.0 (2013-04-04)

- Initial Release
