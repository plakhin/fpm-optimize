<p align="center"><img src="/art/optimize-php-fpm.png" alt="Screenshot of the `php artisan optimize:php-fpm` command"></p>

## Optimal php-fpm config values based on system configuration and load

[![Tests Status](https://github.com/plakhin/fpm-optimize/actions/workflows/main.yml/badge.svg)](https://github.com/plakhin/fpm-optimize/actions)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/plakhin/fpm-optimize.svg)](https://packagist.org/packages/plakhin/fpm-optimize)
[![Total Downloads](https://img.shields.io/packagist/dt/plakhin/fpm-optimize.svg)](https://packagist.org/packages/plakhin/fpm-optimize)

> [!WARNING]  
> Despite the package also runs on Windows and macOS, it wasn't tested well enough on those operating systems, so pay close attention to the system config and load values it outputs.

This package determines the number of system CPU cores, available RAM, and average RAM usage per PHP-FPM pool worker process. It then calculates the opinionated optimal values for PHP-FPM pool configuration, such as:
- `pm.max_children`
- `pm.start_servers`
- `pm.min_spare_servers`
- `pm.max_spare_servers`

**Don't forget to keep an eye on your `php-fpm.log` to avoid failures!**

## Installation

You can install the package via composer:

```bash
composer require plakhin/fpm-optimize
```

## Usage

```bash
./vendor/bin/fpm-suggest
```

## Laravel

Once installed into Laravel 11+ app, this package adds php-fpm config values suggestions into [`optimize` Artisan Command](https://laravel.com/docs/deployment#optimization) output.  
If you don't want this behavior, simply set `FPM_OPTIMIZE_INJECT_INTO_ARTISAN_OPTIMISE_COMMAND=false` in your `.env` file.

Additionally, you can run `php artisan optimize:php-fpm` command to see the suggested php-fpm config values.

## Contributing
Contributions are welcome, and are accepted via pull requests.
Please review these guidelines before submitting any pull requests.

### Process

1. Fork the project
1. Create a new branch
1. Code, test, commit and push
1. Open a pull request detailing your changes.

### Guidelines

* Please ensure the coding style running `composer lint`.
* Please keep the codebase modernized using automated refactors with Rector `composer refactor`.
* Send a coherent commit history, making sure each individual commit in your pull request is meaningful.
* You may need to [rebase](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) to avoid merge conflicts.
* Please remember to follow [SemVer](http://semver.org/).

### Linting

```bash
composer lint
```

### Refactoring with Rector

```bash
composer refactor
```

### Testing

Run all tests:
```bash
composer test
```

Check code style:
```bash
composer test:lint
```

Check possible code improvements:
```bash
composer test:refactor
```

Check types:
```bash
composer test:types
```

Run Unit tests:
```bash
composer test:unit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Stanislav Plakhin](https://github.com/plakhin)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
