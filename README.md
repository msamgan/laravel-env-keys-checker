# Check if all the keys are available across all the .env files.

![image](https://github.com/user-attachments/assets/ad617e05-5d45-4b2c-a6b9-5cd095719fa3)


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

You can publish the config file with:

```bash
php artisan vendor:publish --tag="env-keys-checker-config"
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
    $this->artisan('env:keys-check --auto-add=none')->assertExitCode(0);
});
```

## Configuration

You can configure the package by publishing the configuration file.

```php
# config/env-keys-checker.php
# List of all the .env files to ignore while checking the env keys
 
'ignore_files' => [],
```

```php
# config/env-keys-checker.php
# List of all the .env keys to ignore while checking the env keys

'ignore_keys' => [],
```

```php
# config/env-keys-checker.php  
# strategy to add the missing keys to the .env file
# ask: will ask the user to add the missing keys
# auto: will add the missing keys automatically
# none: will not add the missing keys

'auto_add' => 'ask',
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
