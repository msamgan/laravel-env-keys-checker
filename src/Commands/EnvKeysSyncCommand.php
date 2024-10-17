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

                [$keyMasterKey, $valueMaster] = explode('=', $keyMaster);
                [$keyEnvFileKey, $valueEnvFile] = explode('=', $keyEnvFile);

                if ($keyMasterKey === $keyEnvFileKey) {
                    continue;
                }

                dump($keyMaster, $keyEnvFile);
            }
        });

        return self::SUCCESS;
    }

    private function getKeyFromFileOnLine(string $file, int $line): string
    {
        return file($file)[$line - 1];
    }
}
