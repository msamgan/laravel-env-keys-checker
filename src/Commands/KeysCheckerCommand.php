<?php

namespace Msamgan\LaravelEnvKeysChecker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\table;

use Msamgan\LaravelEnvKeysChecker\Actions\AddKeys;
use Msamgan\LaravelEnvKeysChecker\Actions\CheckKeys;
use Msamgan\LaravelEnvKeysChecker\Actions\GetKeys;
use Msamgan\LaravelEnvKeysChecker\Concerns\HelperFunctions;

class KeysCheckerCommand extends Command
{
    use HelperFunctions;

    public $signature = 'env:keys-check {--auto-add=} {--no-progress} {--no-display}';

    public $description = 'Check if all keys in .env file are present across all .env files. Like .env, .env.example, .env.testing, etc.';

    public function handle(GetKeys $getKeys, CheckKeys $checkKeys, AddKeys $addKeys): int
    {
        $envFiles = glob(base_path(path: '.env*'));

        $ignoredFiles = config(key: 'env-keys-checker.ignore_files', default: []);
        $autoAddOption = $this->option(key: 'auto-add');
        $autoAddAvailableOptions = ['ask', 'auto', 'none'];

        $autoAddStrategy = $autoAddOption ?: config(key: 'env-keys-checker.auto_add', default: 'ask');

        if (! in_array(needle: $autoAddStrategy, haystack: $autoAddAvailableOptions)) {
            if (! $this->option(key: 'no-display')) {
                $this->showFailureInfo(message: 'Invalid auto add option provided. Available options are: ' . implode(', ', $autoAddAvailableOptions));
            }

            return self::FAILURE;
        }

        if (empty($envFiles)) {
            if (! $this->option(key: 'no-display')) {
                $this->showFailureInfo(message: 'No .env files found.');
            }

            return self::FAILURE;
        }

        $envFiles = collect(value: $envFiles)->filter(callback: function ($file) use ($ignoredFiles) {
            return ! in_array(needle: basename($file), haystack: $ignoredFiles);
        })->toArray();

        if (empty($envFiles)) {
            if (! $this->option(key: 'no-display')) {
                $this->showFailureInfo(message: 'No .env files found.');
            }

            return self::FAILURE;
        }

        $keys = $getKeys->handle(files: $envFiles);

        $missingKeys = collect();

        $processKeys = fn ($key) => $checkKeys->handle(keyData: $key, envFiles: $envFiles, missingKeys: $missingKeys);

        if ($this->option(key: 'no-progress')) {
            $keys->each(callback: $processKeys);
        } else {
            progress(
                label: 'Checking keys...',
                steps: $keys,
                callback: $processKeys,
                hint: 'It won\'t take long.'
            );
        }

        if ($missingKeys->isEmpty()) {
            if (! $this->option(key: 'no-display')) {
                $this->showSuccessInfo(message: 'All keys are present in all .env files.');
            }

            return self::SUCCESS;
        }

        if (! $this->option(key: 'no-display')) {
            $this->showMissingKeysTable(missingKeys: $missingKeys);
        }

        if ($autoAddStrategy === 'ask') {
            $confirmation = confirm(label: 'Do you want to add the missing keys to the .env files?');

            if ($confirmation) {
                $addKeys->handle(missingKeys: $missingKeys);

                if (! $this->option(key: 'no-display')) {
                    $this->showSuccessInfo(message: 'All missing keys have been added to the .env files.');
                }
            }

            return self::SUCCESS;
        }

        if ($autoAddStrategy === 'auto') {
            $addKeys->handle(missingKeys: $missingKeys);

            return self::SUCCESS;
        }

        return self::FAILURE;

    }

    private function showMissingKeysTable(Collection $missingKeys): void
    {
        table(
            headers: ['Line', 'Key', 'Is missing in'],
            rows: $missingKeys->map(callback: function ($missingKey) {
                return [
                    $missingKey['line'],
                    $missingKey['key'],
                    $missingKey['envFile'],
                ];
            })->toArray()
        );
    }
}
