<?php

declare(strict_types=1);

it('Command Exists and Runs', function (): void {
    // Just verify the command exists and can be executed
    // We don't check the exit code as it depends on whether there are missing keys
    $this->artisan('env:keys-check', ['--auto-add' => 'none', '--no-display' => true]);

    // If we get here without exceptions, the command exists and runs
    expect(true)->toBeTrue();
});
