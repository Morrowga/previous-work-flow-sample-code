<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'avatars' => [
            'driver' => 'local',
            'root' => storage_path('app/avatars'),
            'visibility' => 'public',
        ],

        'media_user' => [
            'driver' => 'local',
            'root' => storage_path('app/public/users'),
            'url' => env('APP_URL').'/storage/users',
            'visibility' => 'public',
        ],
        'media_organization' => [
            'driver' => 'local',
            'root' => storage_path('app/public/organization'),
            'url' => env('APP_URL').'/storage/organization',
            'visibility' => 'public',
        ],

        'media_students' => [
            'driver' => 'local',
            'root' => storage_path('app/public/students'),
            'url' => env('APP_URL').'/storage/students',
            'visibility' => 'public',
        ],

        'media_teachers' => [
            'driver' => 'local',
            'root' => storage_path('app/public/teachers'),
            'url' => env('APP_URL').'/storage/teachers',
            'visibility' => 'public',
        ],

        'media_sitelogo' => [
            'driver' => 'local',
            'root' => storage_path('app/public/sitelogo'),
            'url' => env('APP_URL').'/storage/sitelogo',
            'visibility' => 'public',
        ],
        'media_sitefavico' => [
            'driver' => 'local',
            'root' => storage_path('app/public/sitefavico'),
            'url' => env('APP_URL').'/storage/sitefavico',
            'visibility' => 'public',
        ],
        'media_payment_receipt' => [
            'driver' => 'local',
            'root' => storage_path('app/public/payment_receipt'),
            'url' => env('APP_URL').'/storage/payment_receipt',
            'visibility' => 'public',
        ],
        'media_game_thumbnail' => [
            'driver' => 'local',
            'root' => storage_path('app/public/game_thumbnail'),
            'url' => env('APP_URL').'/storage/game_thumbnail',
            'visibility' => 'public',
        ],
        'media_game_file' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media_game_file'),
            'url' => env('APP_URL').'/storage/media_game_file',
            'visibility' => 'public',
        ],
        'media_reward' => [
            'driver' => 'local',
            'root' => storage_path('app/public/reward'),
            'url' => env('APP_URL').'/storage/reward',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
