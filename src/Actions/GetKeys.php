<?php

declare(strict_types=1);

namespace Msamgan\LaravelEnvKeysChecker\Actions;

use Illuminate\Support\Collection;

final class GetKeys
{
    public function handle(array|string $files, ?bool $withComments = false): Collection
    {
        $ignoredKeys = config('env-keys-checker.ignore_keys', []);

        $files = is_array($files)
            ? collect($files)
            : collect([$files]);

        return $files
            ->map(function ($file) use ($ignoredKeys, $withComments) {
                $lines = file($file) ?: [];
                $collection = collect($lines)->map(function ($line, $index) use ($lines): array {
                    [$key] = explode('=', $line);

                    return [
                        'key' => $key,
                        'line' => $index + 1,
                        'is_next_line_empty' => isset($lines[$index + 1]) && $lines[$index + 1] === "\n",
                    ];
                });

                if (! $withComments) {
                    $collection = $collection->filter(fn ($item): bool => $item['key'] !== "\n" && ! str_starts_with($item['key'], '#'));
                }

                return $collection->reject(fn ($keyData): bool => in_array($keyData['key'], $ignoredKeys));
            })
            ->flatten(1)
            ->unique('key');
    }
}
