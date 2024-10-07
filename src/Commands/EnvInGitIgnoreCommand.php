<?php

namespace Msamgan\LaravelEnvKeysChecker\Commands;

use Illuminate\Console\Command;

class EnvInGitIgnoreCommand extends Command
{
    public $signature = 'env:in-git-ignore';

    public $description = 'Check if .env file is in .gitignore file.';

    public function handle(): int
    {
        $gitIgnoreFile = base_path('.gitignore');

        if (! file_exists($gitIgnoreFile)) {
            $this->error('!! .gitignore file not found.');

            return self::FAILURE;
        }

        $gitIgnoreContent = array_map('trim', file($gitIgnoreFile));

        $filesToCheck = config('env-keys-checker.gitignore_files', ['.env']);

        $missingFiles = collect();
        collect($filesToCheck)->each(function ($file) use ($gitIgnoreContent, $missingFiles) {
            if (! in_array($file, $gitIgnoreContent)) {
                $missingFiles->push($file);
            }
        });

        if ($missingFiles->isEmpty()) {
            $this->info('=> All files are present in .gitignore file.');

            return self::SUCCESS;
        }

        $this->error('!! ' . $missingFiles->implode(', ') . ' file(s) not found in .gitignore file.');

        return self::FAILURE;
    }
}
