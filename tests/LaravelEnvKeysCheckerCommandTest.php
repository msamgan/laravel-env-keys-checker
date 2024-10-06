<?php

it('Command Exist', function () {
    $this->artisan('env:keys-check --auto-add=none')
        ->assertExitCode(0);
});
