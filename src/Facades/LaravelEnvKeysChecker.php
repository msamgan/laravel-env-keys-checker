<?php

namespace Msamgan\LaravelEnvKeysChecker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Msamgan\LaravelEnvKeysChecker\LaravelEnvKeysChecker
 */
class LaravelEnvKeysChecker extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Msamgan\LaravelEnvKeysChecker\LaravelEnvKeysChecker::class;
    }
}
