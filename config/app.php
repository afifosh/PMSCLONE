<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    'force_https' => (bool) env('FORCE_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    
    /*
    |--------------------------------------------------------------------------
    | Synchronization config
    |--------------------------------------------------------------------------
    |
    */
    'sync' => [
        /*
        |--------------------------------------------------------------------------
        | Synchronization interval definition
        |--------------------------------------------------------------------------
        |
        | For periodic synchronization like Google, the events by default
        | are synchronized every 3 minutes, the interval can be defined below.
        |
        | The accepted values are: Minute, TwoMinutes, ThreeMinutes, FourMinutes, FiveMinutes, TenMinutes, FifteenMinutes,
        | ThirtyMinutes, hourly, TwoHours, ThreeHours, FourHours, SixHours
        */
        'every' => env('SYNC_INTERVAL', 'ThreeMinutes'),
    ],
    /*
    |--------------------------------------------------------------------------
    | Application Microsoft Integration
    |--------------------------------------------------------------------------
    |
    | Microsoft integration related config for connecting via oAuth
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
    | Google integration related config for connecting via oAuth
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

     /*
    |--------------------------------------------------------------------------
    | Application Currency
    |--------------------------------------------------------------------------
    |
    | The application currency, is used on a specific features e.q. form groups
    |
    */
    'currency' => 'USD',

    /*
    |--------------------------------------------------------------------------
    | Mail client configuration
    |--------------------------------------------------------------------------
    |
    | Below, you can find some of the mail client configuration options
    |
    */

    'mail_client' => [
        'reply_prefix'   => env('MAIL_MESSAGE_REPLY_PREFIX', 'RE: '),
        'forward_prefix' => env('MAIL_MESSAGE_FORWARD_PREFIX', 'FW: '),
        'sync' => [
            /*
            |--------------------------------------------------------------------------
            | Sync mail client interval definition
            |--------------------------------------------------------------------------
            |
            | The mail client synchronizer, sync emails every 5 minutes, the interval can be defined below.
            |
            | The accepted values are: Minute, TwoMinutes, ThreeMinutes, FourMinutes, FiveMinutes, TenMinutes, FifteenMinutes,
            | ThirtyMinutes, hourly, TwoHours, ThreeHours, FourHours, SixHours
            */
            'every' => env('MAIL_CLIENT_SYNC_INTERVAL', 'ThreeMinutes'),
        ],
    ],
    'core' => [
        'minPhpVersion' => '8.1', // used in detached.php as well
    ],

    'requirements' => [
        'php' => [
            'bcmath',
            'ctype',
            'mbstring',
            'openssl',
            'pdo',
            'tokenizer',
            'cURL',
            'iconv',
            'gd',
            'fileinfo',
            'dom',
        ],

        'apache' => [
            'mod_rewrite',
        ],

        'functions' => [
            'symlink',
            'proc_open',
            'proc_close',
            'tmpfile',
            'ignore_user_abort',
            'fpassthru',
            'highlight_file',
        ],

        'recommended' => [
            'php' => [
                'imap',
                'zip',
            ],
        ],
    ],



    /*
    |--------------------------------------------------------------------------
    | Folders Permissions
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel folders permissions, if your application
    | requires more permissions just add them to the array list bellow.
    |
    */
    'permissions' => [
        'storage/app/'       => '755',
        'storage/framework/' => '755',
        'storage/logs/'      => '755',
        'bootstrap/cache/'   => '755',
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
    'colors' => explode(',', env(
        'COMMON_COLORS',
        '#374151,#DC2626,#F59E0B,#10B981,#2563EB,#4F46E5,#7C3AED,#EC4899,#F3F4F6'
    )),


    /*
    |--------------------------------------------------------------------------
    | User Repository
    |--------------------------------------------------------------------------
    |
    | Provide the user repository.
    |
    */
    'user_repository' => null,
    /*
    |--------------------------------------------------------------------------
    | Application security config
    |--------------------------------------------------------------------------
    | Here you can specify whether HTML purification should be performed on all
    | request data which contains HTML.
    |
    */
    'security' => [
        'purify' => env('HTML_PURIFY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Jenssegers\Agent\AgentServiceProvider::class,
        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Innoclapps\InnoclappsServiceProvider::class,

        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        OwenIt\Auditing\AuditingServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\MenuServiceProvider::class,
        App\Providers\FortifyServiceProvider::class,
        Spatie\Permission\PermissionServiceProvider::class,
        Yajra\DataTables\DataTablesServiceProvider::class,
        Laravolt\Avatar\ServiceProvider::class,
        App\Providers\InboxServiceProvider::class,
        App\Innoclapps\OAuth\OAuthServiceProvider::class,
        App\Providers\SettingServiceProvider::class,
        App\Providers\CustomBladeDirectiveServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // ...
        'Helper' => App\Helpers\Helpers::class,
        'DataTables' => Yajra\DataTables\Facades\DataTables::class,
        'Avatar'    => Laravolt\Avatar\Facade::class,
        'Agent' => Jenssegers\Agent\Facades\Agent::class,
    ])->toArray(),

];
