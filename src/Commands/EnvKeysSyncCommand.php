<?php

declare(strict_types=1);

namespace Msamgan\LaravelEnvKeysChecker\Commands;

use Illuminate\Console\Command;
use Msamgan\LaravelEnvKeysChecker\Actions\FilterFiles;
use Msamgan\LaravelEnvKeysChecker\Concerns\HelperFunctions;

final class EnvKeysSyncCommand extends Command
{
    use HelperFunctions;

    public $signature = 'env:sync-keys';

    public $description = 'Sync keys from master .env file to other .env files.';

    public function handle(FilterFiles $filterFiles): int
    {
        $allKeysCheck = $this->call('env:keys-check', [
            '--auto-add' => 'none',
            '--no-progress' => true,
            '--no-display' => true,
        ]);

        if ($allKeysCheck === self::FAILURE) {
            $this->showFailureInfo(message: 'keys mismatch found. Syncing keys is not possible. Please fix the keys mismatch first.');
            $this->showFailureInfo(message: 'Run `php artisan env:keys-check --auto-add=auto` to add missing keys automatically.');

            return self::FAILURE;
        }

        $envFiles = $this->getEnvs();
        $ignoredFiles = $this->getFilesToIgnore();

        if ($envFiles === [] || $envFiles === false) {
            $this->showFailureInfo(message: 'No .env files found.');

            return self::FAILURE;
        }

        $envFiles = $filterFiles->handle(envFiles: $envFiles, ignoredFiles: $ignoredFiles);

        if ($envFiles === []) {
            $this->showFailureInfo(message: 'No .env files found.');

            return self::FAILURE;
        }

        $envFiles = collect(value: $envFiles)->filter(callback: fn ($file): bool => basename(path: (string) $file) !== $this->getMasterEnv());

        $envFiles->each(callback: function ($envFile): void {
            $totalKeysFromMaster = count(value: file(filename: $this->getMasterEnv()));
            for ($line = 1; $line <= $totalKeysFromMaster; $line++) {
                $keyMaster = $this->getKeyFromFileOnLine(file: $this->getMasterEnv(), line: $line);
                $keyEnvFile = $this->getKeyFromFileOnLine(file: $envFile, line: $line);

                if ($keyMaster === $keyEnvFile) {
                    continue;
                }

                $keyMasterKey = explode(separator: '=', string: $keyMaster)[0];
                $keyEnvFileKey = explode(separator: '=', string: $keyEnvFile)[0];

                if ($keyMasterKey === $keyEnvFileKey) {
                    continue;
                }

                if ($this->checkIfComment(line: $keyMaster)) {
                    $this->pushKeyOnLine(file: $envFile, line: $line, key: $keyMaster);

                    continue;
                }

                if ($this->checkIfEmptyLine(line: $keyMaster)) {
                    $this->pushKeyOnLine(file: $envFile, line: $line, key: $keyMaster);

                    continue;
                }

                $this->moveKeyToLine(file: $envFile, key: $keyMasterKey, toLine: $line);
            }

            $this->removeAllLinesAfter(lineNumber: $totalKeysFromMaster, file: $envFile);
        });

        $this->showSuccessInfo(message: 'Keys synced successfully.');

        return self::SUCCESS;
    }

    private function getMasterEnv(): string
    {
        return (string) config(key: 'env-keys-checker.master_env', default: '.env');
    }

    private function getKeyFromFileOnLine(string $file, int $line): string
    {
        return file(filename: $file)[$line - 1] ?? '';
    }

    private function checkIfComment(string $line): bool
    {
        return str_starts_with($line, '#');
    }

    private function pushKeyOnLine(string $file, int $line, string $key): void
    {
        $lines = file(filename: $file);
        array_splice(array: $lines, offset: $line - 1, length: 0, replacement: $key);

        file_put_contents(filename: $file, data: implode(separator: '', array: $lines));
    }

    private function checkIfEmptyLine(string $line): bool
    {
        return $line === "\n";
    }

    private function moveKeyToLine(string $file, string $key, int $toLine): void
    {
        $lines = file(filename: $file);
        $keyLine = array_filter(array: $lines, callback: fn ($line): bool => str_starts_with($line, $key));

        if ($keyLine === []) {
            return;
        }

        $keyLine = array_keys(array: $keyLine)[0];
        $keyData = $lines[$keyLine];

        unset($lines[$keyLine]);

        array_splice(array: $lines, offset: $toLine - 1, length: 0, replacement: $keyData);

        file_put_contents(filename: $file, data: implode(separator: '', array: $lines));
    }

    private function removeAllLinesAfter(int $lineNumber, string $file): void
    {
        $lines = file(filename: $file);
        $lines = array_slice(array: $lines, offset: 0, length: $lineNumber);

        file_put_contents(filename: $file, data: implode(separator: '', array: $lines));
    }
}
