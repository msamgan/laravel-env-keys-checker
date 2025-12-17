<?php

declare(strict_types=1);

namespace Msamgan\LaravelEnvKeysChecker;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Msamgan\LaravelEnvKeysChecker\Commands\EnvInGitIgnoreCommand;
use Msamgan\LaravelEnvKeysChecker\Commands\EnvKeysSyncCommand;
use Msamgan\LaravelEnvKeysChecker\Commands\KeysCheckerCommand;
use Override;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class LaravelEnvKeysCheckerServiceProvider extends PackageServiceProvider
{
    #[Override]
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
            ->hasCommand(EnvKeysSyncCommand::class)
            ->hasInstallCommand(function (InstallCommand $command): void {
                $repo = 'msamgan/laravel-env-keys-checker';
                $url = 'https://github.com/' . $repo;

                $command
                    ->startWith(function (InstallCommand $command) use ($url): void {
                        $command->info('Thanks for installing Laravel Env Keys Checker!');
                        $command->info('Repository: ' . $url);
                    })
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub($repo);
            });
    }
}
