<?php

return [
    'name' => 'Generator',

    /*
    |--------------------------------------------------------------------------
    | Override Paths
    |--------------------------------------------------------------------------
    |
    */

    'path' => [

        'migration'         => base_path('modules/{Module}/Database/Migrations/'),

        'model'             => base_path('modules/{Module}/Entities/'),

        'datatables'        => base_path('modules/{Module}/DataTables/'),

        'repository'        => base_path('modules/{Module}/Database/Repositories/'),

        'routes'            => base_path('modules/{Module}/Routes/web.php'),

        'api_routes'        => base_path('modules/{Module}/Routes/api.php'),

        'request'           => base_path('modules/{Module}/Http/Requests/'),

        'api_request'       => base_path('modules/{Module}/Http/Requests/API/'),

        'controller'        => base_path('modules/{Module}/Http/Controllers/'),

        'api_controller'    => base_path('modules/{Module}/Http/Controllers/API/'),

        'test_trait'        => base_path('modules/{Module}/Tests/traits/'),

        'repository_test'   => base_path('modules/{Module}/Tests/'),

        'api_test'          => base_path('modules/{Module}/Tests/'),

        'views'             => base_path('modules/{Module}/Resources/views/'),

        'schema_files'      => base_path('modules/{Module}/Resources/model_schemas/'),

        'modelJs'           => base_path('modules/{Module}/Resources/assets/js/models/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Override Namespaces
    |--------------------------------------------------------------------------
    |
    */

    'namespace' => [

        'model'             => 'Modules\{Module}\Entities',

        'datatables'        => 'Modules\{Module}\DataTables',

        'repository'        => 'Modules\{Module}\Database\Repositories',

        'controller'        => 'Modules\{Module}\Http\Controllers',

        'api_controller'    => 'Modules\{Module}\Http\Controllers\API',

        'request'           => 'Modules\{Module}\Http\Requests',

        'api_request'       => 'Modules\{Module}\Http\Requests\API',
    ],
];
