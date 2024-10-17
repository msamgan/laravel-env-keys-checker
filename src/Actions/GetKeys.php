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
                $collection = collect(file($file))->map(function ($line, $index) use ($file) {
                    [$key] = explode('=', $line);

                    return [
                        'key' => $key,
                        'line' => $index + 1,
                        'is_next_line_empty' => isset(file($file)[$index + 1]) && file($file)[$index + 1] === "\n",
                    ];
                });

                if (! $withComments) {
                    $collection = $collection->filter(function ($item) {
                        return $item['key'] !== "\n" && ! str_starts_with($item['key'], '#');
                    });
                }

                return $collection->filter(function ($keyData) use ($ignoredKeys) {
                    return ! in_array($keyData['key'], $ignoredKeys);
                });
            })
            ->flatten(1)
            ->unique('key');
    }
}
