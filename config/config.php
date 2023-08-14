<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Environment condition
    |--------------------------------------------------------------------------
    |
    | Here you should specify whether or not to activate the dusk API config
    | functionality based on the environment. Ideally, it should not be
    | activated in production due to lead to a configuration leak.
    |
    | Instead, you can define the environment to not activate the dusk API
    | config, allowing it to be available in all other environments. Defining
    | the excluded environment overwrites the allowed environment.
    |
    */
    'env'          => env('DUSKAPICONF_ENV', 'local'),
    'excluded_env' => env('DUSKAPICONF_EXCLUDED_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Temporary configuration variables storage
    |--------------------------------------------------------------------------
    |
    | Define the storage disk and filename where you will store your config
    | values.
    |
    */
    'storage'      => [
        'disk' => 'local',
        'file' => 'duskapiconf_tmp.txt'
    ]
];