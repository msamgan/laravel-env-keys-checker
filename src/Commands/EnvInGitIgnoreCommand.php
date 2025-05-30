<?php

declare(strict_types=1);

namespace Msamgan\LaravelEnvKeysChecker\Commands;

use Illuminate\Console\Command;
use Msamgan\LaravelEnvKeysChecker\Concerns\HelperFunctions;

final class EnvInGitIgnoreCommand extends Command
{
    use HelperFunctions;

    public $signature = 'env:in-git-ignore';

    public $description = 'Check if .env file is in .gitignore file.';

    public function handle(): int
    {
        $gitIgnoreFile = base_path(path: '.gitignore');

        if (! file_exists(filename: $gitIgnoreFile)) {
            $this->showFailureInfo(message: '.gitignore file not found.');

            return self::FAILURE;
        }

        $gitIgnoreContent = array_map(callback: 'trim', array: file($gitIgnoreFile));

        $filesToCheck = config(key: 'env-keys-checker.gitignore_files', default: ['.env']);

        $missingFiles = collect();
        collect(value: $filesToCheck)->each(callback: function ($file) use ($gitIgnoreContent, $missingFiles): void {
            if (! in_array(needle: $file, haystack: $gitIgnoreContent)) {
                $missingFiles->push(values: $file);
            }
        });

        if ($missingFiles->isEmpty()) {
            $this->showSuccessInfo(message: 'All files are present in .gitignore file.');

            return self::SUCCESS;
        }

        $this->showFailureInfo(message: $missingFiles->implode(value: ', ') . ' file(s) not found in .gitignore file.');

        return self::FAILURE;
    }
}
