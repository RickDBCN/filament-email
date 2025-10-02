<p class="filament-hidden">
<img src="https://banners.beyondco.de/filament-email.png?theme=light&packageManager=composer+require&packageName=rickdbcn%2Ffilament-email&pattern=architect&style=style_1&description=Log+emails+in+your+Filament+project&md=1&showWatermark=0&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg" class="filament-hidden">
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rickdbcn/filament-email.svg?style=flat-square)](https://packagist.org/packages/rickdbcn/filament-email)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/rickdbcn/filament-email/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/rickdbcn/filament-email/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/rickdbcn/filament-email/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/rickdbcn/filament-email/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rickdbcn/filament-email.svg?style=flat-square)](https://packagist.org/packages/rickdbcn/filament-email)
<a href="https://snyk.io/test/github/RickDBCN/filament-email"><img alt="Snyk Security" src="https://snyk.io/test/github/RickDBCN/filament-email/badge.svg"></a>
<a href="https://github.com/RickDBCN/filament-email/blob/main/LICENSE.md"><img alt="License" src="https://img.shields.io/github/license/RickDBCN/filament-email?color=blue&label=license"></a>

Log all outgoing emails in your Laravel project within your Filament panel. You can also resend emails with 1-click in case your recipient hasn't received your email.

## Version Compatibility

| Plugin | Filament | Laravel | PHP |
|--------|----------| ------------- | -------------|
| 1.x    | 3.x      | 10.x | 8.x |
| 1.x    | 3.x      | 11.x \| 12.x | 8.2 \| 8.3 \| 8.4 |
| 2.x    | 4.x      | 11.x \| 12.x | 8.3 \| 8.4 |

> [!CAUTION]
> After update to v1.3.1 or 1.4.0 you need to re-publish and run migrations
>
> ```bash
> php artisan vendor:publish --tag="filament-email-migrations"
> php artisan migrate
> ```

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
->plugin(\RickDBCN\FilamentEmail\FilamentEmail::make())
```

## Configuration

```php
use RickDBCN\FilamentEmail\Models\Email;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;

return [

    'resource' => [
        'class' => EmailResource::class,
        'model' => Email::class,
        'cluster' => null,
        'group' => null,
        'sort' => null,
        'icon' => null,
        'default_sort_column' => 'created_at',
        'default_sort_direction' => 'desc',
        'datetime_format' => 'Y-m-d H:i:s',
        'table_search_fields' => [
            'subject',
            'from',
            'to',
            'cc',
            'bcc',
        ],
    ],

    'keep_email_for_days' => 60,

    'label' => null,

    'prune_enabled' => true,

    'prune_crontab' => '0 0 * * *',

    'can_access' => [
        'role' => [],
    ],

    'pagination_page_options' => [
        10, 25, 50, 'all',
    ],

    'attachments_disk' => 'local',
    'store_attachments' => true,

    //Use this option for customize tenant model class
    //'tenant_model' => \App\Models\Team::class,

];
```

## Testing

```bash
composer test
```

## Screenshots

### E-mail list

<img src="https://raw.githubusercontent.com/RickDBCN/filament-email/main/screenshots/table.png" style="border-radius:2%"/>

### Advanced filters

<img src="https://raw.githubusercontent.com/RickDBCN/filament-email/main/screenshots/filters.png" style="border-radius:2%"/>

### Resend e-mail

<img src="https://raw.githubusercontent.com/RickDBCN/filament-email/main/screenshots/resend.png" style="border-radius:2%"/>

### Update addresses and resend e-mail

<img src="https://raw.githubusercontent.com/RickDBCN/filament-email/main/screenshots/update-and-resend.png" style="border-radius:2%"/>

### E-mail view with attachments

<img src="https://raw.githubusercontent.com/RickDBCN/filament-email/main/screenshots/view.png" style="border-radius:2%"/>

## Languages Supported

Filament Email Plugin is translated for:

- English <sup><sub>EN</sub></sup>
- Dutch <sup><sub>NL</sub></sup>
- Italian <sup><sub>IT</sub></sup>
- German <sup><sub>DE</sub></sup>
- Portuguese <sup><sub>PT</sub></sup>
- Turkish <sup><sub>TR</sub></sup>
- Hungarian <sup><sub>HU</sub></sup>
- Spanish <sup><sub>ES</sub></sup>

## Credits

- [Rick de Boer](https://github.com/RickDBCN)
- [Marco Germani](https://github.com/marcogermani87)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
