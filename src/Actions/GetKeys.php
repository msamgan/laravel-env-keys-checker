<?php

namespace Msamgan\LaravelEnvKeysChecker\Actions;

use Illuminate\Support\Collection;

class GetKeys
{
    public function handle(array|string $files, ?bool $withComments = false): Collection
    {
        $ignoredKeys = config('env-keys-checker.ignore_keys', []);

        $files = is_array($files)
            ? collect($files)
            : collect([$files]);

        return $files
            ->map(function ($file) use ($ignoredKeys, $withComments) {
                $collection = collect(file($file))->map(function ($line, $index) use ($file): array {
                    [$key] = explode('=', $line);

                    return [
                        'key' => $key,
                        'line' => $index + 1,
                        'is_next_line_empty' => isset(file($file)[$index + 1]) && file($file)[$index + 1] === "\n",
                    ];
                });

                if (! $withComments) {
                    $collection = $collection->filter(fn ($item): bool => $item['key'] !== "\n" && ! str_starts_with((string) $item['key'], '#'));
                }

                return $collection->reject(fn ($keyData): bool => in_array($keyData['key'], $ignoredKeys));
            })
            ->flatten(1)
            ->unique('key');
    }
}
