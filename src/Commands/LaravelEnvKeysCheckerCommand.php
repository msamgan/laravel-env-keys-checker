<?php

namespace Msamgan\LaravelEnvKeysChecker\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\table;

use Msamgan\LaravelEnvKeysChecker\Actions\AddKeys;
use Msamgan\LaravelEnvKeysChecker\Actions\CheckKeys;
use Msamgan\LaravelEnvKeysChecker\Actions\GetKeys;

class LaravelEnvKeysCheckerCommand extends Command
{
    public $signature = 'env:keys-check {--auto-add=}';

    public $description = 'Check if all keys in .env file are present across all .env files. Like .env, .env.example, .env.testing, etc.';

    public function handle(GetKeys $getKeys, CheckKeys $checkKeys, AddKeys $addKeys): int
    {
        $envFiles = glob(base_path('.env*'));

        $ignoredFiles = config('env-keys-checker.ignore_files', []);
        $autoAddOption = $this->option('auto-add');
        $autoAddAvailableOptions = ['ask', 'auto', 'none'];

        $autoAddStrategy = $autoAddOption ?: config('env-keys-checker.auto_add', 'ask');

        if (! in_array($autoAddStrategy, $autoAddAvailableOptions)) {
            $this->error('!! Invalid auto add option provided. Available options are: ' . implode(', ', $autoAddAvailableOptions));

            return self::FAILURE;
        }

        if (empty($envFiles)) {
            $this->error('!! No .env files found.');

            return self::FAILURE;
        }

        $envFiles = collect($envFiles)->filter(function ($file) use ($ignoredFiles) {
            return ! in_array(basename($file), $ignoredFiles);
        })->toArray();

        $keys = $getKeys->handle(files: $envFiles);

        $missingKeys = collect();
        $keys->each(function ($keyData) use ($envFiles, $missingKeys, $checkKeys) {
            $checkKeys->handle(keyData: $keyData, envFiles: $envFiles, missingKeys: $missingKeys);
        });

        if ($missingKeys->isEmpty()) {
            $this->info('=> All keys are present in across all .env files.');

            return self::SUCCESS;
        }

        table(
            headers: ['Line', 'Key', 'Is missing in'],
            rows: $missingKeys->map(function ($missingKey) {
                return [
                    $missingKey['line'],
                    $missingKey['key'],
                    $missingKey['envFile'],
                ];
            })->toArray()
        );

        if ($autoAddStrategy === 'ask') {
            $confirmation = confirm('Do you want to add the missing keys to the .env files?');

            if ($confirmation) {
                $addKeys->handle(missingKeys: $missingKeys);
            }

            return self::SUCCESS;
        }

        if ($autoAddStrategy === 'auto') {
            $addKeys->handle(missingKeys: $missingKeys);

            return self::SUCCESS;
        }

        return self::FAILURE;
    }
}
