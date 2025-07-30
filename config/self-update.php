<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Self-Update Strategy
    |--------------------------------------------------------------------------
    |
    | Here you may specify which update strategy class you wish to use when
    | updating your application. This must be a class that implements the
    | "SelfUpdateStrategy" contract provided with this package.
    |
    */

    'strategy' => \LaravelZero\Framework\Components\Updater\Strategy\GitHubStrategy::class,

    /*
    |--------------------------------------------------------------------------
    | Version Installed
    |--------------------------------------------------------------------------
    |
    | This is the version that users will update from.
    | It should be the same as your application version.
    |
    */

    'version_installed' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Phar Name
    |--------------------------------------------------------------------------
    |
    | This is the name of the binary file for your application.
    |
    */

    'phar_name' => 'zeri',

    /*
    |--------------------------------------------------------------------------
    | Download URL
    |--------------------------------------------------------------------------
    |
    | This is the URL where your application can be downloaded from.
    | GitHub example: https://api.github.com/repos/user/repo
    |
    */

    'download_url' => 'https://api.github.com/repos/hadefication/zeri',

    /*
    |--------------------------------------------------------------------------
    | Access Token
    |--------------------------------------------------------------------------
    |
    | If you're using GitHub and your repository is private, you'll need
    | to specify an access token to download the releases.
    |
    */

    'access_token' => env('GITHUB_ACCESS_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Signature Check
    |--------------------------------------------------------------------------
    |
    | Disable signature verification for development.
    | In production, you should enable this and provide proper signing.
    |
    */

    'check_signature' => false,
];
