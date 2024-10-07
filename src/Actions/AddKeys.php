<?php

namespace Msamgan\LaravelEnvKeysChecker\Actions;

use Illuminate\Support\Collection;

class AddKeys
{
    public function handle(Collection $missingKeys): void
    {
        $missingKeys->each(function ($missingKey) {
            $filePath = base_path($missingKey['envFile']);
            $envContent = file($filePath);

            array_splice(
                $envContent, $missingKey['line'] - 1, 0, $missingKey['key'] . '=""' . PHP_EOL
            );

            if ($missingKey['is_next_line_empty']) {
                array_splice($envContent, $missingKey['line'], 0, PHP_EOL);
            }

            file_put_contents($filePath, $envContent);
        });
    }
}
