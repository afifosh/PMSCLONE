<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\Model;
use Modules\MailClient\Database\Factories\PredefinedMailTemplateFactory;
use App\Models\Admin as User;

class PredefinedMailTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'subject', 'body', 'is_shared', 'user_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_shared' => 'boolean',
        'user_id' => 'int',
    ];

    /**
     * Get the template owner
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include templates visible for the user.
     */
    public function scopeVisibleToUser(Builder $query, ?int $userId = null): void
    {
        $query->where(function ($query) use ($userId) {
            $query->where('user_id', $userId ?: Auth::id())->orWhere('is_shared', true);
        });
    }

    /**
     * Scope a query to only include shared templates.
     */
    public function scopeShared(Builder $query): void
    {
        $query->where('is_shared', true);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): PredefinedMailTemplateFactory
    {
        return PredefinedMailTemplateFactory::new();
    }
}
