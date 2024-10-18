<?php

return [
    // List of all the .env files to ignore while checking the env keys
    'ignore_files' => explode(',', env('KEYS_CHECKER_IGNORE_FILES', '')),

    // List of all the env keys to ignore while checking the env keys
    'ignore_keys' => explode(',', env('KEYS_CHECKER_IGNORE_KEYS', '')),

    // strategy to add the missing keys to the .env file
    // ask: will ask the user to add the missing keys
    // auto: will add the missing keys automatically
    // none: will not add the missing keys
    'auto_add' => env('KEYS_CHECKER_AUTO_ADD', 'ask'),

    // List of all the .env.* files to be checked if they
    // are present in the .gitignore file
    'gitignore_files' => explode(',', env('KEYS_CHECKER_GITIGNORE_FILES', '.env')),

    // Master .env file to be used for syncing the keys
    'master_env' => env('MASTER_ENV', '.env'),
];
