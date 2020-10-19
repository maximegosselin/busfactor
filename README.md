# BusFactor

[![Latest Version](https://img.shields.io/github/release/busfactor/busfactor.svg)](https://github.com/busfactor/busfactor/releases)
[![Composer](https://img.shields.io/badge/composer-busfactor/busfactor-lightgray)](https://packagist.org/packages/busfactor/busfactor)
![PHP](https://img.shields.io/packagist/php-v/busfactor/busfactor)
[![Build Status](https://img.shields.io/travis/busfactor/busfactor.svg)](https://travis-ci.org/busfactor/busfactor)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A comprehensive set of modern PHP components you can mix and match to implement the CQRS and Event Sourcing patterns in your application.

**Domain-centric** — Implement rich domain logic with aggregates and events.

**Event-driven** — Generate optimized query models and trigger actions in response to domain events.

**Command-oriented** — Decouple application logic from UI by dispatching commands to handlers.

**Test-friendly** — Write scenarios to test your application and domain behavior.

**Framework-agnostic** — Build on any framework or none at all.

**Control-focused** — Plug-in custom middlewares and storage adapters to fit your application's needs.

## Documentation

See [docs/README.md](docs/README.md)

## Install

Using [Composer](https://getcomposer.org/):

```bash
$ composer require busfactor/busfactor
```

## Requirements

PHP >=7.4 with `json` and `pdo` extensions enabled.

## Testing

```bash
$ vendor/bin/phpunit
```

## Credits

- [Maxime Gosselin](https://github.com/maximegosselin)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
