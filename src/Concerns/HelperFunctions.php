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
}
