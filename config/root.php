<?php

use Cone\Root\Http\Middleware\Authenticate;
use Cone\Root\Http\Middleware\HandleRootRequests;

return [

    /*
    |--------------------------------------------------------------------------
    | Root Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Root will be accessible from. If this
    | setting is null, Root will reside under the same domain as the
    | application.
    |
    */

    'domain' => env('ROOT_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | Root Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Root will be accessible from. Feel free
    | to change this path to anything you like.
    |
    */

    'path' => env('ROOT_PATH', 'root'),

    /*
    |--------------------------------------------------------------------------
    | Root Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will get attached onto each Root route, giving you
    | the chance to add your own middleware to this list or change any
    | of the existing middleware.
    |
    */

    'middleware' => [
        'web',
        Authenticate::class,
        'verified:root.verification.show',
        'can:viewRoot',
        HandleRootRequests::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Settings
    |--------------------------------------------------------------------------
    |
    | You can specify the media settings here. Set the default disk to store
    | the media items. Also, you can specify the expiration of the chunks.
    |
    | Supported conversion drivers: "gd"
    |
    */

    'media' => [
        'disk' => 'public',
        'chunk_expiration' => 1440,
        'conversion' => [
            'default' => 'gd',
            'drivers' => [
                'gd' => [
                    'quality' => 70,
                ],
            ],
        ],
    ],

];
