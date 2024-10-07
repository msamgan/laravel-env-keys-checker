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

            $lineDiff = count($envContent) - $missingKey['line'];
            if ($lineDiff < 0) {
                $envContent = $this->appendEmptyLines(file: $filePath, numberOfLines: abs($lineDiff));
            }

            array_splice(
                $envContent, $missingKey['line'] - 1, 0, $missingKey['key'] . '=""' . PHP_EOL
            );

            if ($missingKey['is_next_line_empty']) {
                array_splice($envContent, $missingKey['line'], 0, PHP_EOL);
            }

            file_put_contents($filePath, $envContent);
        });
    }

    private function appendEmptyLines(string $file, int $numberOfLines = 1): array
    {
        $envContent = file($file);
        $lastLine = count($envContent);

        for ($i = 0; $i < $numberOfLines; $i++) {
            array_splice($envContent, $lastLine, 0, PHP_EOL);
        }

        return $envContent;
    }
}
