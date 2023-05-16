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

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Fields\FieldsCollection;

class Import extends Model
{
    const STATUSES = [
        'mapping' => 1,
        'in-progress' => 2,
        'finished' => 3,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_path',
        'skip_file_path',
        'resource_name',
        'status',
        'imported',
        'skipped',
        'duplicates',
        'user_id',
        'completed_at',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'user_id' => 'int',
        'duplicates' => 'int',
        'skipped' => 'int',
        'imported' => 'int',
    ];

    /**
     * Boot the model
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleted(function ($model) {
            Storage::disk(static::disk())->deleteDirectory($model->storagePath());
        });
    }

    /**
     * Scope a query to only include imports of a given resource.
     */
    public function scopeByResource(Builder $query, string $resourceName): void
    {
        $query->where('resource_name', $resourceName);
    }

    /**
     * Scope a query to only include imports with status in progress.
     */
    public function scopeInProgress(Builder $query): void
    {
        $query->where('status', 'in-progress');
    }

    /**
     * Remove import file from storage
     */
    public function removeFile(string $path): bool
    {
        $disk = Storage::disk(static::disk());

        if ($disk->exists($path)) {
            return $disk->delete($path);
        }

        return false;
    }

    /**
     * Get the import files storage path
     *
     * Should be used once the model has been created and  the file is uploaded as it's
     * using the folder from the initial upload files, all other files will be stored there as well
     */
    public function storagePath(string $glue = ''): string
    {
        $path = $this->id ?
            pathinfo($this->file_path, PATHINFO_DIRNAME) :
            'imports'.DIRECTORY_SEPARATOR.Str::random(15);

        return $path.($glue ? (DIRECTORY_SEPARATOR.$glue) : '');
    }

    /**
     * An Import has user/creator
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Users\Models\User::class);
    }

    /**
     * Get the fields intended for this import
     */
    public function fields(): FieldsCollection
    {
        Innoclapps::setImportStatus('mapping');

        return Innoclapps::resourceByName($this->resource_name)
            ->importable()
            ->resolveFields();
    }

    /**
     * Get the file name attribute
     */
    public function fileName(): Attribute
    {
        return Attribute::get(fn () => basename($this->file_path));
    }

    /**
     * Get the skip file filename name attribute
     */
    public function skipFileFilename(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->skip_file_path) {
                return null;
            }

            return basename($this->skip_file_path);
        });
    }

    /**
     * Get the import storage disk
     */
    public static function disk(): string
    {
        return 'local';
    }

    /**
     * Get the import's status.
     */
    protected function status(): Attribute
    {
        return new Attribute(
            get: fn ($value) => array_search($value, static::STATUSES),
            set: fn ($value) => static::STATUSES[
                is_numeric($value) ? array_search($value, static::STATUSES) : $value
            ]
        );
    }
}
