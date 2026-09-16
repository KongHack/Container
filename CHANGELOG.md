# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased](https://github.com/KongHack/Container)



## [1.2.0](https://github.com/KongHack/Container/releases/tag/1.2.0)

### Added
- Add `InvalidItemException` for invalid service registrations.
- Add PSR-11 implementation metadata through Composer's `psr/container-implementation` virtual package.
- Add a PHPUnit test suite covering named instances, eager and lazy services, static-method loaders, loader retries,
  non-resolving inspection, duplicate and reserved registrations, typed services, PSR-11 not-found behavior, and null
  rejection.
- Add Composer scripts for syntax checks, PHPStan, PHP_CodeSniffer, PHPUnit, and the combined quality suite.
- Add GitHub Actions quality checks on PHP 8.4 and 8.5, dependency auditing, release metadata validation, and automated
  GitHub releases for semantic-version tags.
- Add comprehensive usage documentation for installation, Composer bootstrapping, lazy services, typed services,
  named instances, compatibility behavior, and development.

### Changed
- Reject `null` values passed to `SharedContainer::set()` or `SharedContainer::overwrite()` instead of silently
  treating them as absent entries.
- Modernize the PHPStan and PHP_CodeSniffer configurations to analyze both source and test code.
- Update development tooling for PHPUnit 12, PHP_CodeSniffer 4, and CodeSnifferContrib 2.2.
- Document container array types, item-key return types, and exceptions for static analysis.
- Ignore PHPUnit's generated cache directory while continuing to leave `composer.lock` untracked for consumer
  compatibility.

### Fixed
- Fix case-insensitive reserved identifier checks so names such as `COMMON` report the correct dedicated setter without
  producing an undefined array-key warning.



## [1.1.5](https://github.com/KongHack/Container/releases/tag/1.1.5)
- @GameCharmer Update Dependencies



## [1.1.4](https://github.com/KongHack/Container/releases/tag/1.1.4)
- @GameCharmer Update Dependencies



## [1.1.3](https://github.com/KongHack/Container/releases/tag/1.1.3)
- @GameCharmer patch return type



## [1.1.2](https://github.com/KongHack/Container/releases/tag/1.1.2)
- @GameCharmer Add support for Exception Logger



## [1.1.1](https://github.com/KongHack/Container/releases/tag/1.1.1)
- @GameCharmer Add support for loading Object Manager
- @GameCharmer Cleanup gitignore, remove composer.lock from repo



## [1.1.0](https://github.com/KongHack/Container/releases/tag/1.1.0)
- @GameCharmer PHP 8.4



## [1.0.11](https://github.com/KongHack/Container/releases/tag/1.0.11)
- @GameCharmer Update composer dependencies



## [1.0.10](https://github.com/KongHack/Container/releases/tag/1.0.10)
- @GameCharmer Update composer dependencies



## [1.0.9](https://github.com/KongHack/Container/releases/tag/1.0.9)
- @GameCharmer Update composer dependencies



## [1.0.8](https://github.com/KongHack/Container/releases/tag/1.0.8)
- @GameCharmer Update Shared Container
  - Protect Constructor
  - Add `getItemKeys` method



## [1.0.7](https://github.com/KongHack/Container/releases/tag/1.0.7)
- @GameCharmer update composer.json requirements



## [1.0.6](https://github.com/KongHack/Container/releases/tag/1.0.6)
- @GameCharmer Add get/set UICore methods



## [1.0.5](https://github.com/KongHack/Container/releases/tag/1.0.5)
- @GameCharmer Update Variable Names



## [1.0.4](https://github.com/KongHack/Container/releases/tag/1.0.4)
- @GameCharmer Allow callables in sets



## [1.0.3](https://github.com/KongHack/Container/releases/tag/1.0.3)
- @GameCharmer Add get/set PageWrapper methods
- @GameCharmer Add get/set Router methods



## [1.0.2](https://github.com/KongHack/Container/releases/tag/1.0.2)
- @GameCharmer Add get/set Globals methods



## [1.0.1](https://github.com/KongHack/Container/releases/tag/1.0.1)
- @GameCharmer Add get/set Twig methods



## [1.0.0](https://github.com/KongHack/Container/releases/tag/1.0.0)
- @GameCharmer Initial Version

