<?php

namespace Msamgan\LaravelEnvKeysChecker\Commands;

use Illuminate\Console\Command;

class LaravelEnvKeysCheckerCommand extends Command
{
    public $signature = 'laravel-env-keys-checker';

    public $description = 'My command samgan..';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
