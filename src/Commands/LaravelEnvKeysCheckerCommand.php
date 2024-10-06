<?php

namespace Msamgan\LaravelEnvKeysChecker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

use function Laravel\Prompts\table;

class LaravelEnvKeysCheckerCommand extends Command
{
    public $signature = 'env:keys-check';

    public $description = 'Check if all keys in .env file are present across all .env files. Like .env, .env.example, .env.testing, etc.';

    public function handle(): int
    {
        $envFiles = glob(base_path('.env*'));

        $ignoredFiles = config('env-keys-checker.ignore_files', []);

        if (empty($envFiles)) {
            $this->error('!! No .env files found.');

            return self::FAILURE;
        }

        $envFiles = collect($envFiles)->filter(function ($file) use ($ignoredFiles) {
            return ! in_array(basename($file), $ignoredFiles);
        })->toArray();

        $keys = $this->getAllKeys($envFiles);

        $missingKeys = collect();
        $keys->each(function ($keyData) use ($envFiles, $missingKeys) {
            $this->checkForKeyInFile($keyData, $envFiles, $missingKeys);
        });

        if ($missingKeys->isEmpty()) {
            $this->info('=> All keys are present in across all .env files.');

            return self::SUCCESS;
        }

        table(
            headers: ['Line', 'Key', 'Is missing in'],
            rows: $missingKeys,
        );

        return self::FAILURE;
    }

    private function getAllKeys($files): Collection
    {
        $ignoredKeys = config('env-keys-checker.ignore_keys', []);

        $files = is_array($files)
            ? collect($files)
            : collect([$files]);

        return $files
            ->map(function ($file) use ($ignoredKeys) {
                return collect(file($file))->map(function ($line, $index) {
                    [$key] = explode('=', $line);

                    return [
                        'key' => $key,
                        'line' => $index + 1,
                    ];
                })->filter(function ($item) {
                    return $item['key'] !== "\n" && ! str_starts_with($item['key'], '#');
                })->filter(function ($keyData) use ($ignoredKeys) {
                    return ! in_array($keyData['key'], $ignoredKeys);
                });
            })
            ->flatten(1)
            ->unique('key');
    }

    private function checkForKeyInFile($keyData, $envFiles, $missingKeys): void
    {
        collect($envFiles)->each(function ($envFile) use ($keyData, $missingKeys) {
            $envContent = file($envFile);
            $keyExists = false;

            foreach ($envContent as $line) {
                if (str_starts_with($line, $keyData['key'])) {
                    $keyExists = true;
                    break;
                }
            }

            if (! $keyExists) {
                $missingKeys->push([
                    'line' => $keyData['line'],
                    'key' => $keyData['key'],
                    'envFile' => basename($envFile),
                ]);
            }
        });
    }
}
