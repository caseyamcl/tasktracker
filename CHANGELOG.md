# Changelog

All Notable changes to `caseyamcl/tasktracker` will be documented in this file

## Version 2.0 (2015-02-XX)

### Changed
- Major rewrite; BC-breaking API changes
- Bumped minimum PHP version from 5.3 to 5.4
- Changed autoloader from PSR-0 to PSR-4
- Refactored event notification system from custom solution to use Symfony EventDispatcher
- Renamed `OutputHandler` clases to `Subscribers`
- Moved non-core functions into `Helper` Traits
- Removed state-tracking from `Report` and `Tick` classes to the `Tracker` class.
  `Report` and `Tick` classes are now new-able value objects.
- `Tracker::tick()`, `Tracker::finish`, and `Tracker::abort` now returns an instances of the latest
  report class.
- Replaced Monolog output subscriber with more flexible PSR-3 output subscriber.
- Refactored Symfony Console output subscriber to use Symfony built-in progress bar instead of custom solution
- Added `SymfonyConsoleLog` subscriber for logging events line-by-line to the console.
- Created `TrackerFactory` service class
- Added `LICENSE` and `CONTRIBUTING.md` files
- Added `.gitattributes` to minimize download size
- Added `Tracker::build()` alternative constructor for creating a traker with a list of event subscribers

## Version 1.0 (2013-04-04)
  - Initial Release
