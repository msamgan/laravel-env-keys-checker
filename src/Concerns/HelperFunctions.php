<?php

namespace Msamgan\LaravelEnvKeysChecker\Concerns;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

trait HelperFunctions
{
    private function showSuccessInfo(string $message): void
    {
        info(' => ' . $message);
    }

    private function showFailureInfo(string $message): void
    {
        error(' !! ' . $message);
    }
}
