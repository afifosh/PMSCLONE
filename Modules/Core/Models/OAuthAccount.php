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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Database\Factories\OAuthAccountFactory;
use Modules\Core\Facades\Google;
use Modules\Core\OAuth\AccessTokenProvider;
use Modules\Core\OAuth\Events\OAuthAccountDeleting;

class OAuthAccount extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_accounts';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'requires_auth' => 'boolean',
        'access_token' => 'encrypted',
        'user_id' => 'int',
    ];

    /**
     * Boot the OAuthAccount Model
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($account) {
            OAuthAccountDeleting::dispatch($account);
        });

        static::deleted(function ($account) {
            if ($account->type === 'google') {
                try {
                    Google::revokeToken($account->access_token);
                } catch (\Exception) {
                }
            }
        });
    }

    /**
     * Set that this account requires authentication.
     */
    public function setRequiresAuthentication(bool $value = true)
    {
        $this->requires_auth = $value;
        $this->save();

        return $this;
    }

    /**
     * Get the user the OAuth account belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Users\Models\User::class);
    }

    /**
     * Create new token provider
     */
    public function tokenProvider(): AccessTokenProvider
    {
        return new AccessTokenProvider($this->access_token, $this->email);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): OAuthAccountFactory
    {
        return new OAuthAccountFactory;
    }
}
