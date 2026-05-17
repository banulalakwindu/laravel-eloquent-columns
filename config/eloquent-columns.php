<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Register Blueprint macros
    |--------------------------------------------------------------------------
    |
    | When true, the service provider registers all schema macros on boot.
    |
    */

    'register_macros' => (bool) env('ELOQUENT_COLUMNS_REGISTER_MACROS', true),

    /*
    |--------------------------------------------------------------------------
    | User model for HasAuditColumns relationships
    |--------------------------------------------------------------------------
    |
    | Used by creator(), updater(), and deleter(). If null, the package falls
    | back to config('auth.providers.users.model').
    |
    */

    'user_model' => env('ELOQUENT_COLUMNS_USER_MODEL'),

];
