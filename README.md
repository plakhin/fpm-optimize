<p align="center"><img src="/art/optimize-php-fpm.png" alt="Screenshot of the `php artisan optimize:php-fpm` command"></p>

## Optimal PHP-FPM config values based on system configuration and load

[![Tests](https://github.com/plakhin/fpm-optimize/actions/workflows/tests.yml/badge.svg)](https://github.com/plakhin/fpm-optimize/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/plakhin/fpm-optimize.svg)](https://packagist.org/packages/plakhin/fpm-optimize)
[![Total Downloads](https://img.shields.io/packagist/dt/plakhin/fpm-optimize.svg)](https://packagist.org/packages/plakhin/fpm-optimize)

> [!IMPORTANT]  
> Since v2.0 only Linux is supported.

This package determines the number of system CPU cores, available RAM, and average RAM usage per PHP-FPM pool worker process. It then calculates the opinionated optimal values for PHP-FPM pool configuration, such as:
- `pm.max_children`
- `pm.start_servers`
- `pm.min_spare_servers`
- `pm.max_spare_servers`

**Don't forget to keep an eye on your `php-fpm.log` to avoid failures!**

## Installation & Usage

> [!IMPORTANT]
> Ensure that your server is operating normally and serving incoming requests before executing the package command. This command takes into account the available RAM and the average RAM usage per PHP-FPM pool worker process to calculate values.

**The most simple way** to get the optimal PHP-FPM config values suggestions is to run the following command:
```sh
sh <(curl -s https://raw.githubusercontent.com/plakhin/fpm-optimize/main/suggest-fpm-config-values.sh)
```

### Laravel

Also, you can install the package with composer as a dependency to your Laravel 11+ project:

```sh
composer require plakhin/fpm-optimize
```

Once installed, you can run `php artisan optimize:php-fpm` command to see the suggested php-fpm config values.

Additionally, this package adds php-fpm config values suggestions into [`optimize` Artisan Command](https://laravel.com/docs/deployment#optimization) output.  
If you don't want this behavior, simply set `FPM_OPTIMIZE_INJECT_INTO_ARTISAN_OPTIMISE_COMMAND=false` in your `.env` file.

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

```sh
composer lint
```

### Refactoring with Rector

```sh
composer refactor
```

### Testing

Run all tests:
```sh
composer test
```

Check code style:
```sh
composer test:lint
```

Check possible code improvements:
```sh
composer test:refactor
```

Check types:
```sh
composer test:types
```

Run Unit tests:
```sh
composer test:unit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Stanislav Plakhin](https://github.com/plakhin)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
