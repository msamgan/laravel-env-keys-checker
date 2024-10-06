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

        if (empty($envFiles)) {
            $this->error('!! No .env files found.');

            return self::FAILURE;
        }

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
        $files = is_array($files)
            ? collect($files)
            : collect([$files]);

        $keyArray = $files
            ->map(function ($file) {
                $lines = file($file);
                return collect($lines)->map(function ($line, $index) {
                    $key = explode('=', $line)[0];
                    return [
                        'key' => $key,
                        'line' => $index + 1
                    ];
                })->filter(function ($item) {
                    return $item['key'] !== "\n" && ! str_starts_with($item['key'], '#');
                });
            })
            ->flatten(1)
            ->unique('key');

        return $keyArray;
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
                $fileParts = explode(DIRECTORY_SEPARATOR, $envFile);
                $missingKeys->push([
                    'line' => $keyData['line'],
                    'key' => $keyData['key'],
                    'envFile' => end($fileParts),
                ]);
            }
        });
    }
}
