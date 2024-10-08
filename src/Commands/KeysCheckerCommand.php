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
            $this->showFailureInfo(
                message: 'Invalid auto add option provided. Available options are: ' . implode(', ', $autoAddAvailableOptions)
            );

            return self::FAILURE;
        }

        if (empty($envFiles)) {
            $this->showFailureInfo(
                message: 'No .env files found.'
            );

            return self::FAILURE;
        }

        $envFiles = collect($envFiles)->filter(function ($file) use ($ignoredFiles) {
            return ! in_array(basename($file), $ignoredFiles);
        })->toArray();

        $keys = $getKeys->handle(files: $envFiles);

        $missingKeys = collect();

        progress(
            label: 'Checking keys...',
            steps: $keys,
            callback: fn ($key) => $checkKeys->handle(keyData: $key, envFiles: $envFiles, missingKeys: $missingKeys),
            hint: 'It won\'t take long.'
        );

        if ($missingKeys->isEmpty()) {
            $this->showSuccessInfo(
                message: 'All keys are present in all .env files.'
            );

            return self::SUCCESS;
        }

        $this->showMissingKeysTable($missingKeys);

        if ($autoAddStrategy === 'ask') {
            $confirmation = confirm('Do you want to add the missing keys to the .env files?');

            if ($confirmation) {
                $addKeys->handle(missingKeys: $missingKeys);

                $this->showSuccessInfo('All missing keys have been added to the .env files.');
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
            rows: $missingKeys->map(function ($missingKey) {
                return [
                    $missingKey['line'],
                    $missingKey['key'],
                    $missingKey['envFile'],
                ];
            })->toArray()
        );
    }
}
