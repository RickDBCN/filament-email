<p class="filament-hidden">
<img src="https://banners.beyondco.de/filament-email.png?theme=light&packageManager=composer+require&packageName=rickdbcn%2Ffilament-email&pattern=architect&style=style_1&description=Log+emails+in+your+Filament+project&md=1&showWatermark=0&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg" class="filament-hidden">
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rickdbcn/filament-email.svg?style=flat-square)](https://packagist.org/packages/rickdbcn/filament-email)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/rickdbcn/filament-email/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/rickdbcn/filament-email/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/rickdbcn/filament-email/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/rickdbcn/filament-email/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rickdbcn/filament-email.svg?style=flat-square)](https://packagist.org/packages/rickdbcn/filament-email)

Log all outgoing emails in your Laravel project within your Filament panel. You can also resend emails with 1-click in case your recipient hasn't received your email.
## Installation

You can install the package via composer:

```bash
composer require rickdbcn/filament-email
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
// add this within return $panel:
->plugin(new \RickDBCN\FilamentEmail\FilamentEmail())
```


## Testing

```bash
composer test
```
## Screenshots
![](https://raw.githubusercontent.com/RickDBCN/filament-email/main/screenshots/tableview.png)

## Credits

- [Rick de Boer](https://github.com/RickDBCN)
- [Ramnzys](https://github.com/ramnzys/filament-email-log)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
