<?php

it('Command Exist', function () {
    $this->artisan('env:keys-check')
        ->assertExitCode(0);
});
