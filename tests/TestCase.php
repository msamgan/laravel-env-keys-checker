<?php

namespace Msamgan\LaravelEnvKeysChecker\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Msamgan\LaravelEnvKeysChecker\LaravelEnvKeysCheckerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Override;

class TestCase extends Orchestra
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName): string => 'Msamgan\\LaravelEnvKeysChecker\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    #[Override]
    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-env-keys-checker_table.php.stub';
        $migration->up();
        */
    }

    #[Override]
    protected function getPackageProviders($app)
    {
        return [
            LaravelEnvKeysCheckerServiceProvider::class,
        ];
    }
}
