<?php

declare(strict_types=1);

it('Command Exist', function (): void {
    $this->artisan('env:keys-check --auto-add=none')
        ->assertExitCode(0);
});
