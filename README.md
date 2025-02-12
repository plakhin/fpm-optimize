[![Tests Status](https://github.com/plakhin/php-package-skeleton/actions/workflows/main.yml/badge.svg)](https://github.com/plakhin/php-package-skeleton/actions)

# PHP Package Skeleton
This repository provides a minimal PHP package skeleton with some batteries included.

#

## Installation

You can install the package via composer:

```bash
composer require plakhin/php-package-skeleton
```

## Usage

```php
use Plakhin\PPSkeleton\MyClass;

(new MyClass)->foo(); // string(3) "bar"
```

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

#

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Stanislav Plakhin](https://github.com/plakhin)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
