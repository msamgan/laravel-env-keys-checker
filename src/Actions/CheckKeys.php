<?php

namespace Msamgan\LaravelEnvKeysChecker\Actions;

use Illuminate\Support\Collection;

class CheckKeys
{
    public function handle(array $keyData, array $envFiles, Collection $missingKeys): void
    {
        collect($envFiles)->each(function ($envFile) use ($keyData, $missingKeys) {
            $envContent = file($envFile);
            $keyExists = false;

            foreach ($envContent as $line) {
                if (str_starts_with($line, $keyData['key'])) {
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
