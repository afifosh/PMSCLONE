<?php

use App\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

return [
    /*
     * The database connection name to use
     *
     * Set to `null` in order to use the default database connection
     */
    'connection_name' => null,

    /*
     * FQCN of the model to use for media
     *
     * Should extend `Plank\Mediable\Media`
     */
    'model' => Media::class,

    /*
     * Name to be used for mediables joining table
     */
    'mediables_table' => 'mediables',

    /*
     * Filesystem disk to use if none is specified
     */
    'default_disk' => env('MEDIA_DISK', 'local'),

    /*
     * Filesystems that can be used for media storage
     *
     * Uploader will throw an exception if a disk not in this list is selected
     */
    'allowed_disks' => [
        'public',
        'local',
        's3',
    ],

    /*
     * The maximum file size in bytes for a single uploaded file
     *
     * Default: 25MB
     */
    'max_size' => env('MEDIA_MAX_SIZE', (int) max(1024 * 1024 * 25, UploadedFile::getMaxFilesize())),

    /*
     * What to do if a duplicate file is uploaded.
     *
     * Options include:
     *
     * * `'increment'`: the new file's name is given an incrementing suffix
     * * `'replace'` : the old file and media model is deleted
     * * `'error'`: an Exception is thrown
     */
    'on_duplicate' => Plank\Mediable\MediaUploader::ON_DUPLICATE_INCREMENT,

    /*
     * Reject files unless both their mime and extension are recognized and both match a single aggregate type
     */
    'strict_type_checking' => false,

    /*
     * Reject files whose mime type or extension is not recognized
     * if true, files will be given a type of `'other'`
     */
    'allow_unrecognized_types' => true,

    /*
     * Only allow files with specific MIME type(s) to be uploaded
     */
    'allowed_mime_types' => [],

    /*
     * Only allow files with specific file extension(s) to be uploaded
     */
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'pdf', 'aac', 'ogg', 'oga', 'mp3', 'wav', 'mp4', 'm4v','mov', 'ogv', 'webm', 'zip', 'rar', 'doc', 'docx', 'txt', 'text', 'xml', 'json', 'xls', 'xlsx', 'odt', 'csv', 'ppt', 'pptx', 'ppsx', 'ics', 'eml'],

    /*
     * Only allow files matching specific aggregate type(s) to be uploaded
     */
    'allowed_aggregate_types' => [],

    /*
     * List of aggregate types recognized by the application
     *
     * Each type should list the MIME types and extensions
     * that should be recognized for the type
     */
    'aggregate_types' => [
        Plank\Mediable\Media::TYPE_IMAGE => [
            'mime_types' => [
                'image/jpeg',
                'image/png',
                'image/gif',
            ],
            'extensions' => [
                'jpg',
                'jpeg',
                'png',
                'gif',
            ],
        ],
        Plank\Mediable\Media::TYPE_IMAGE_VECTOR => [
            'mime_types' => [
                'image/svg+xml',
            ],
            'extensions' => [
                'svg',
            ],
        ],
        Plank\Mediable\Media::TYPE_PDF => [
            'mime_types' => [
                'application/pdf',
            ],
            'extensions' => [
                'pdf',
            ],
        ],
        Plank\Mediable\Media::TYPE_AUDIO => [
            'mime_types' => [
                'audio/aac',
                'audio/ogg',
                'audio/mpeg',
                'audio/mp3',
                'audio/mpeg',
                'audio/wav',
            ],
            'extensions' => [
                'aac',
                'ogg',
                'oga',
                'mp3',
                'wav',
            ],
        ],
        Plank\Mediable\Media::TYPE_VIDEO => [
            'mime_types' => [
                'video/mp4',
                'video/mpeg',
                'video/ogg',
                'video/webm',
            ],
            'extensions' => [
                'mp4',
                'm4v',
                'mov',
                'ogv',
                'webm',
            ],
        ],
        Plank\Mediable\Media::TYPE_ARCHIVE => [
            'mime_types' => [
                'application/zip',
                'application/x-compressed-zip',
                'multipart/x-zip',
                'application/x-rar-compressed',
            ],
            'extensions' => [
                'zip',
                'rar',
            ],
        ],
        Plank\Mediable\Media::TYPE_DOCUMENT => [
            'mime_types' => [
                'text/plain',
                'application/plain',
                'text/xml',
                'text/json',
                'application/json',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
            'extensions' => [
                'doc',
                'docx',
                'txt',
                'text',
                'xml',
                'json',
            ],
        ],
        Plank\Mediable\Media::TYPE_SPREADSHEET => [
            'mime_types' => [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv',
                'application/vnd.oasis.opendocument.text',
            ],
            'extensions' => [
                'xls',
                'xlsx',
                'odt',
                'csv',
            ],
        ],
        Plank\Mediable\Media::TYPE_PRESENTATION => [
            'mime_types' => [
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            ],
            'extensions' => [
                'ppt',
                'pptx',
                'ppsx',
            ],
        ],
    ],

    /*
     * List of adapters to use for various source inputs
     *
     * Adapters can map either to a class or a pattern (regex)
     */
    'source_adapters' => [
        'class' => [
            Symfony\Component\HttpFoundation\File\UploadedFile::class => Plank\Mediable\SourceAdapters\UploadedFileAdapter::class,
            Symfony\Component\HttpFoundation\File\File::class => Plank\Mediable\SourceAdapters\FileAdapter::class,
            Psr\Http\Message\StreamInterface::class => Plank\Mediable\SourceAdapters\StreamAdapter::class,
        ],
        'pattern' => [
            '^https?://' => Plank\Mediable\SourceAdapters\RemoteUrlAdapter::class,
            '^/' => Plank\Mediable\SourceAdapters\LocalPathAdapter::class,
            '^[a-zA-Z]:\\\\' => Plank\Mediable\SourceAdapters\LocalPathAdapter::class,
        ],
    ],

    /*
     * List of URL Generators to use for handling various filesystem drivers
     *
     */
    'url_generators' => [
        'local' => Plank\Mediable\UrlGenerators\LocalUrlGenerator::class,
        's3' => Plank\Mediable\UrlGenerators\S3UrlGenerator::class,
    ],

    /**
     * Should mediable instances automatically reload their media relationships after modification are made to a tag.
     *
     * If true, will automatically reload media the next time `getMedia()`, `getMediaMatchAll()` or `getAllMediaByTag()` are called.
     */
    'rehydrate_media' => true,

    /**
     * Detach associated media when mediable model is soft deleted.
     */
    'detach_on_soft_delete' => false,
];
