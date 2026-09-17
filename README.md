# Container

![Packagist](https://img.shields.io/packagist/dm/gcworld/container.svg)
![Packagist](https://img.shields.io/packagist/dt/gcworld/container.svg)

![Packagist PHP](https://img.shields.io/packagist/php-v/gcworld/container.svg)
![Packagist](https://img.shields.io/packagist/v/gcworld/container.svg)
![GitHub](https://img.shields.io/github/tag/konghack/container.svg)

`gcworld/container` implements the [PSR-11 container read interface](https://www.php-fig.org/psr/psr-11/) for PHP 8.4
and later, with additional APIs for registering eager services, lazy factories, typed framework services, and
independently named container instances.

### Version
1.2.0

## Installation

```bash
composer require gcworld/container
```

## Basic usage

The default container is shared for the lifetime of the PHP process:

```php
use GCWorld\Container\Core\SharedContainer;

$container = SharedContainer::getInstance();

$container->set('configuration', $configuration);

if ($container->has('configuration')) {
    $configuration = $container->get('configuration');
}
```

Calling `set()` for an existing identifier throws `ItemAlreadyExistsException`. Use `overwrite()` when replacement of
a general service is intentional. Calling `get()` for an unknown identifier throws `ItemNotFoundException`, which
implements PSR-11's `NotFoundExceptionInterface`. Both `set()` and `overwrite()` reject `null` values with an
`InvalidItemException`.

## Lazy services

Any callable registered in the container is treated as a zero-argument factory. The factory is invoked on the first
`get()`, and its result replaces the factory in the container:

```php
$container->set('reporter', static fn (): Reporter => new Reporter());
$container->set('session', Session::class.'::getInstance');

$reporter = $container->get('reporter');
```

`has()` and `getItemKeys()` inspect the container without resolving factories. If a factory throws, it remains
registered and a later call to `get()` will retry it.

Because callable values are interpreted as factories, callable service objects are not supported as ordinary values.
A factory should also return a non-callable value so that the resolved service remains cached.

## Composer bootstrap

Applications can populate the container from a Composer autoload file. For example, add a project-owned bootstrap
file to the application's `composer.json`:

```json
{
  "autoload": {
    "psr-4": {
      "Example\\Application\\": "src/"
    },
    "files": [
      "bootstrap/container.php"
    ]
  }
}
```

The bootstrap can register eager services and lazy factories:

```php
<?php

use Example\Application\Configuration;
use Example\Application\Session;
use GCWorld\Container\Core\SharedContainer;

$container = SharedContainer::getInstance();

if (!$container->has('configuration')) {
    $container->set('configuration', new Configuration());
    $container->set('session', Session::class.'::getInstance');
}
```

Run `composer dump-autoload` after adding or moving an autoload file.

## Typed framework services

Frequently used framework services have dedicated setters and getters. These methods preserve native return types and
provide IDE completion without requiring casts.

| Identifier | Setter | Getter | Value type |
| --- | --- | --- | --- |
| `common` | `setCommon()` | `getCommon()` | `CommonInterface` or callable |
| `user` | `setUser()` | `getUser()` | `UserInterface` or callable |
| `twig` | `setTwig()` | `getTwig()` | `TwigInterface` or callable |
| `globals` | `setGlobals()` | `getGlobals()` | `GlobalsInterface` |
| `router` | `setRouter()` | `getRouter()` | `RoutingInterface` or callable |
| `page_wrapper` | `setPageWrapper()` | `getPageWrapper()` | `PageWrapper` or callable |
| `ui_core` | `setUICore()` | `getUICore()` | `UICoreInterface` or callable |
| `object_manager` | `setObjectManager()` | `getObjectManager()` | `ObjectManager` or callable |
| `exception_logger` | `setExceptionLogger()` | `getExceptionLogger()` | `ExceptionLoggerInterface` or callable |

These identifiers are reserved, including case variants, and cannot be registered through `set()` or `overwrite()`.
The dedicated setters reject duplicate registrations.

## Named instances

Use a name when an application needs an isolated container:

```php
$default = SharedContainer::getInstance();
$worker  = SharedContainer::getInstance('worker');
```

Repeated calls with the same name return the same instance. Different names have independent service collections.

## Compatibility notes

- Service identifiers are case-sensitive, except when enforcing the reserved typed-service identifiers.
- `null` is not a valid service value; `set()` and `overwrite()` throw `InvalidItemException` when given `null`.
- Container instances and their services remain in static memory for the lifetime of the PHP process.
- Lazy factories receive no arguments.

## Development

Install dependencies and run the complete quality suite:

```bash
composer install
composer check
```

The suite runs PHP syntax checks, PHPStan, PHP_CodeSniffer, and PHPUnit. This library intentionally does not commit
`composer.lock`, allowing consuming applications to resolve dependencies within their own compatibility constraints.
