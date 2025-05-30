<?php

it('Command Exist', function (): void {
    $this->artisan('env:keys-check --auto-add=none')
        ->assertExitCode(0);
});
