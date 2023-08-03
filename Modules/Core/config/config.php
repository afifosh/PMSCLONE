<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

return [
    'name' => 'Core',

    'soft_deletes' => [
        'prune_after' => env('PRUNE_TRASHED_RECORDS_AFTER', 30), // in days
    ],

    /*
    |--------------------------------------------------------------------------
    | Mailable templates configuration
    |--------------------------------------------------------------------------
    |
    | layout => The mailable templates default layout path
    |
    */

    'mailables' => [
        'layout' => env('MAILABLE_TEMPLATE_LAYOUT', storage_path('mail-layouts/mailable-template.html')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can specificy the default directory where the media files
    | will be uploaded, keep in mind that the application will create
    | folder tree in this directory according to custom logic e.q.
    | /media/contacts/:id/image.jpg
    |
    */
    'media' => [
        'directory' => env('MEDIA_DIRECTORY', 'media'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Application favourite colors
    |--------------------------------------------------------------------------
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Application Microsoft Integration
    |--------------------------------------------------------------------------
    |
    | Microsoft integration related config for connecting via oAuth.
    |
    */
    'microsoft' => [

        /**
         * The Microsoft Azure Application (client) ID
         *
         * https://portal.azure.com
         */
        'client_id' => env('MICROSOFT_CLIENT_ID'),

        /**
         * Azure application secret key
         */
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),

        /**
         * Application tenant ID
         * Use 'common' to support personal and work/school accounts
         */
        'tenant_id' => env('MICROSOFT_TENANT_ID', 'common'),

        /*
        * Set the url to trigger the OAuth process this url should call return Microsoft::connect();
        */
        'redirect_uri' => ENV('MICROSOFT_REDIRECT_URI', '/microsoft/callback'),

        /**
         * Login base URL
         */
        'login_url_base' => env('MICROSOFT_LOGIN_URL_BASE', 'https://login.microsoftonline.com'),

        /**
         * OAuth2 path
         */
        'oauth2_path' => env('MICROSOFT_OAUTH2_PATH', '/oauth2/v2.0'),

        /**
         * Microsoft scopes to be used, Graph API will acept up to 20 scopes
         *
         * @see https://docs.microsoft.com/en-us/azure/active-directory/develop/v2-permissions-and-consent
         */
        'scopes' => [
            'offline_access',
            'openid',
            'User.Read',
            'Mail.ReadWrite',
            'Mail.Send',
            'MailboxSettings.ReadWrite',
            'Calendars.ReadWrite',
        ],

        /**
         * The default timezone is always set to UTC.
         */
        'prefer_timezone' => env('MS_GRAPH_PREFER_TIMEZONE', 'UTC'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Google Integration
    |--------------------------------------------------------------------------
    |
    | Google integration related config for connecting via oAuth.
    |
    */
    'google' => [
        /**
         * Google Project Client ID
         */
        'client_id' => env('GOOGLE_CLIENT_ID'),

        /**
         * Google Project Client Secret
         */
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),

        /**
         * Callback URL
         */
        'redirect_uri' => env('GOOGLE_REDIRECT_URI', '/google/callback'),

        /**
         * Access type
         */
        'access_type' => 'offline',

        /**
         * Scopes for OAuth
         */
        'scopes' => ['https://mail.google.com/', 'https://www.googleapis.com/auth/calendar'],
    ],
];
