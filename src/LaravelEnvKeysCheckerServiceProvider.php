<?php

namespace Msamgan\LaravelEnvKeysChecker;

use Msamgan\LaravelEnvKeysChecker\Commands\EnvInGitIgnoreCommand;
use Msamgan\LaravelEnvKeysChecker\Commands\EnvKeysSyncCommand;
use Msamgan\LaravelEnvKeysChecker\Commands\KeysCheckerCommand;
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
            ->hasCommand(KeysCheckerCommand::class)
            ->hasCommand(EnvInGitIgnoreCommand::class)
            ->hasCommand(EnvKeysSyncCommand::class);
    }
}
