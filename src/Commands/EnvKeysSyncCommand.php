<?php

namespace Msamgan\LaravelEnvKeysChecker\Commands;

use Illuminate\Console\Command;
use Msamgan\LaravelEnvKeysChecker\Concerns\HelperFunctions;

class EnvKeysSyncCommand extends Command
{
    use HelperFunctions;

    public $signature = 'env:sync-keys';

    public $description = 'Sync keys from master .env file to other .env files.';

    public function handle(): int
    {
        $allKeysCheck = $this->call('env:keys-check', ['--auto-add' => 'none']);

        if ($allKeysCheck === self::FAILURE) {
            $this->showFailureInfo('keys mismatch found. Syncing keys is not possible. Please fix the keys mismatch first.');
            $this->showFailureInfo('Run `php artisan env:keys-check --auto-add=auto` to add missing keys automatically.');

            return self::FAILURE;
        }

        $this->showSuccessInfo('All keys are in sync.');

        return self::SUCCESS;
    }
}
