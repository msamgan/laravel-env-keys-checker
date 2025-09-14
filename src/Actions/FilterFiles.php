<?php

declare(strict_types=1);

namespace Msamgan\LaravelEnvKeysChecker\Actions;

use Msamgan\LaravelEnvKeysChecker\Concerns\HelperFunctions;

final class FilterFiles
{
    use HelperFunctions;

    public function handle(array $envFiles, array $ignoredFiles): array
    {
        return collect(value: $envFiles)
            ->reject(callback: fn ($file): bool => in_array(needle: $this->getRelativePath((string) $file), haystack: $ignoredFiles))
            ->reject(callback: fn ($file): bool => str_ends_with(haystack: basename((string) $file), needle: '.encrypted'))
            ->toArray();
    }
}
