<?php

namespace Msamgan\LaravelEnvKeysChecker;

use Msamgan\LaravelEnvKeysChecker\Commands\LaravelEnvKeysCheckerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelEnvKeysCheckerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-env-keys-checker')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_env_keys_checker_table')
            ->hasCommand(LaravelEnvKeysCheckerCommand::class);
    }
}
