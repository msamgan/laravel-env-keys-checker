<?php

namespace Msamgan\LaravelEnvKeysChecker\Commands;

use Illuminate\Console\Command;
use Msamgan\LaravelEnvKeysChecker\Actions\GetKeys;
use Msamgan\LaravelEnvKeysChecker\Concerns\HelperFunctions;

class EnvKeysSyncCommand extends Command
{
    use HelperFunctions;

    public $signature = 'env:sync-keys';

    public $description = 'Sync keys from master .env file to other .env files.';

    public function handle(GetKeys $getKeys): int
    {
        $allKeysCheck = $this->call('env:keys-check', [
            '--auto-add' => 'none',
            '--no-progress' => true,
            '--no-display' => true,
        ]);

        if ($allKeysCheck === self::FAILURE) {
            $this->showFailureInfo('keys mismatch found. Syncing keys is not possible. Please fix the keys mismatch first.');
            $this->showFailureInfo('Run `php artisan env:keys-check --auto-add=auto` to add missing keys automatically.');

            return self::FAILURE;
        }

        $envFiles = glob(base_path('.env*'));
        $ignoredFiles = config('env-keys-checker.ignore_files', []);

        if (empty($envFiles)) {
            $this->showFailureInfo(
                message: 'No .env files found.'
            );

            return self::FAILURE;
        }

        $envFiles = collect($envFiles)->filter(function ($file) use ($ignoredFiles) {
            return ! in_array(basename($file), $ignoredFiles);
        })->toArray();

        if (empty($envFiles)) {
            $this->showFailureInfo(
                message: 'No .env files found.'
            );

            return self::FAILURE;
        }

        $envFiles = collect($envFiles)->filter(function ($file) {
            return basename($file) !== config('env-keys-checker.master_env', '.env');
        });

        $envFiles->each(function ($envFile) {
            $totalKeysFromMaster = count(file(config('env-keys-checker.master_env', '.env')));
            for ($line = 1; $line <= $totalKeysFromMaster; $line++) {
                $keyMaster = $this->getKeyFromFileOnLine(config('env-keys-checker.master_env', '.env'), $line);
                $keyEnvFile = $this->getKeyFromFileOnLine($envFile, $line);

                if ($keyMaster === $keyEnvFile) {
                    continue;
                }

                $keyMasterKey = explode('=', $keyMaster)[0];
                $keyEnvFileKey = explode('=', $keyEnvFile)[0];

                if ($keyMasterKey === $keyEnvFileKey) {
                    continue;
                }

                if ($this->checkIfComment($keyMaster)) {
                    $this->pushKeyOnLine($envFile, $line, $keyMaster);

                    continue;
                }

                if ($this->checkIfEmptyLine($keyMaster)) {
                    $this->pushKeyOnLine($envFile, $line, $keyMaster);

                    continue;
                }

                $this->moveKeyToLine($envFile, $keyMasterKey, $line);
            }

            $this->removeAllLinesAfter($totalKeysFromMaster, $envFile);
        });

        $this->showSuccessInfo(
            message: 'Keys synced successfully.'
        );

        return self::SUCCESS;
    }

    private function getKeyFromFileOnLine(string $file, int $line): string
    {
        return file($file)[$line - 1];
    }

    private function checkIfComment(string $line): bool
    {
        return str_starts_with($line, '#');
    }

    private function pushKeyOnLine($file, $line, $key): void
    {
        $lines = file($file);
        array_splice($lines, $line - 1, 0, $key);

        file_put_contents($file, implode('', $lines));
    }

    private function checkIfEmptyLine(string $line): bool
    {
        return $line === "\n";
    }

    private function moveKeyToLine(string $file, string $key, int $toLine): void
    {
        $lines = file($file);
        $keyLine = array_filter($lines, function ($line) use ($key) {
            return str_starts_with($line, $key);
        });

        if (empty($keyLine)) {
            return;
        }

        $keyLine = array_keys($keyLine)[0];
        $keyData = $lines[$keyLine];

        unset($lines[$keyLine]);

        array_splice($lines, $toLine - 1, 0, $keyData);

        file_put_contents($file, implode('', $lines));
    }

    private function removeAllLinesAfter(int $lineNumber, string $file): void
    {
        $lines = file($file);
        $lines = array_slice($lines, 0, $lineNumber);

        file_put_contents($file, implode('', $lines));
    }
}
