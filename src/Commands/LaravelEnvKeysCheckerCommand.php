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
        $keys->each(function ($key) use ($envFiles, $missingKeys) {
            $this->checkForKeyInFile($key, $envFiles, $missingKeys);
        });

        if ($missingKeys->isEmpty()) {
            $this->info('=> All keys are present in across all .env files.');

            return self::SUCCESS;
        }

        table(
            headers: ['Key', 'Is missing in'],
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
            ->map(fn ($file) => file($file))
            ->map(fn ($lines) => collect($lines))
            ->map(fn ($lines) => $lines->map(fn ($line) => explode('=', $line)[0]))
            ->map(fn ($keys) => $keys->filter(fn ($key) => $key !== "\n"))
            ->map(fn ($keys) => $keys->filter(fn ($key) => ! str_starts_with($key, '#')));

        return $keyArray->flatten()->unique();
    }

    private function checkForKeyInFile($key, $envFiles, $missingKeys): void
    {
        collect($envFiles)->each(function ($envFile) use ($key, $missingKeys) {
            $envContent = file($envFile);
            $keyExists = collect($envContent)->contains(fn ($line) => str_starts_with($line, $key));
            if (! $keyExists) {
                $fileParts = explode(DIRECTORY_SEPARATOR, $envFile);
                $missingKeys->push([
                    'key' => $key,
                    'envFile' => end($fileParts),
                ]);
            }
        });
    }
}
