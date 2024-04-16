[![Latest Version on Packagist](https://img.shields.io/packagist/v/marcogermani87/filament-email.svg?style=flat-square)](https://packagist.org/packages/marcogermani87/filament-email)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/marcogermani87/filament-email/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/marcogermani87/filament-email/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/marcogermani87/filament-email/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/rickdbcn/filament-email/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/marcogermani87/filament-email.svg?style=flat-square)](https://packagist.org/packages/marcogermani87/filament-email)

Log all outgoing emails in your Laravel project within your Filament panel. You can also resend emails with 1-click in case your recipient hasn't received your email.

## Installation

You can install the package via composer:

```bash
composer require marcogermani87/filament-email
```

Publish and run the migrations with

```bash
php artisan vendor:publish --tag="filament-email-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-email-config"
```

Register the plugin through your panel service provider:
```php
->plugin(new \MG87\FilamentEmail\FilamentEmail::make())
```


## Testing

```bash
composer test
```

## Screenshots

![](https://raw.githubusercontent.com/marcogermani87/filament-email/main/screenshots/table.png)

![](https://raw.githubusercontent.com/marcogermani87/filament-email/main/screenshots/filters.png)

![](https://raw.githubusercontent.com/marcogermani87/filament-email/main/screenshots/re-send.png)

## Credits

- [Rick de Boer](https://github.com/RickDBCN)
- [Ramnzys](https://github.com/ramnzys/filament-email-log)
- [Marco Germani](https://github.com/marcogermani87)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
