<?php

declare(strict_types=1);

namespace Msamgan\LaravelEnvKeysChecker\Concerns;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

trait HelperFunctions
{
    private function showSuccessInfo(string $message): void
    {
        info(message: ' => ' . $message);
    }

    private function showFailureInfo(string $message): void
    {
        error(message: ' !! ' . $message);
    }

    private function getEnvs(): array
    {
        $envFiles = glob(pattern: base_path(path: '.env*'));

        $additionalLocations = $this->getAdditionalEnvLocations();

        return array_merge($envFiles, $additionalLocations);
    }

    private function getFilesToIgnore(): array
    {
        return (array) config(key: 'env-keys-checker.ignore_files', default: []);
    }

    private function getAdditionalEnvLocations(): array
    {
        $locations = (array) config(key: 'env-keys-checker.additional_env_locations', default: []);
        $envFiles = [];

        foreach ($locations as $location) {
            if (empty($location)) {
                continue;
            }

            $fullPath = base_path($location);

            if (is_dir($fullPath)) {
                $pattern = rtrim($fullPath, '/') . '/.env*';
                $files = glob($pattern);
                if ($files !== false) {
                    $envFiles = array_merge($envFiles, $files);
                }
            } elseif (is_file($fullPath)) {
                $envFiles[] = $fullPath;
            }
        }

        return array_unique($envFiles);
    }
}
