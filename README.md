# check if all the keys are available across all the .env files.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/msamgan/laravel-env-keys-checker.svg?style=flat-square)](https://packagist.org/packages/msamgan/laravel-env-keys-checker)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/msamgan/laravel-env-keys-checker/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/msamgan/laravel-env-keys-checker/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/msamgan/laravel-env-keys-checker/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/msamgan/laravel-env-keys-checker/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/msamgan/laravel-env-keys-checker.svg?style=flat-square)](https://packagist.org/packages/msamgan/laravel-env-keys-checker)

This package is used to check if all the keys are available across all the .env files.
This package is useful when you have multiple .env files,
and you want to make sure that all the keys are available across all the .env files.

With a team of developers, it is possible that some developers might forget to add the keys they used in their .env file
to the .env.example file or the other way around.

## Installation

You can install the package via composer:

```bash
composer require msamgan/laravel-env-keys-checker
```

## Usage

```bash
php artisan env:keys-check
```

## In Test

You can also use this package in your test cases to make sure that all the keys are available across all the .env files.
Add the following code to your test case.

```php
it('tests that the .env key are same across all .env files.', function () {
    $this->artisan('env:keys-check')->assertExitCode(0);
});
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [msamgan](https://github.com/msamgan)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
