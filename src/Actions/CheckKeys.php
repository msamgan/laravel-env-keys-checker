<?php

declare(strict_types=1);

namespace Msamgan\LaravelEnvKeysChecker\Actions;

use Illuminate\Support\Collection;

final class CheckKeys
{
    public function handle(array $keyData, array $envFiles, Collection $missingKeys): void
    {
        collect($envFiles)->each(function ($envFile) use ($keyData, $missingKeys): void {
            $envContent = file($envFile);
            $keyExists = false;

            foreach ($envContent as $line) {
                if (str_starts_with($line, (string) $keyData['key'])) {
                    $keyExists = true;
                    break;
                }
            }

            if (! $keyExists) {
                $missingKeys->push([
                    'line' => $keyData['line'],
                    'key' => $keyData['key'],
                    'is_next_line_empty' => $keyData['is_next_line_empty'],
                    'envFile' => basename($envFile),
                ]);
            }
        });
    }
}
