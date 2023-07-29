<?php

namespace Modules\Chat\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

class PinnedConversation extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'conversation_id',
        'conversation_type',
        'pinned_by',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'conversation_id' => 'string',
        'conversation_type' => 'string',
        'pinned_by' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function pinnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pinned_by');
    }

    public function pinable(): MorphTo
    {
        return $this->morphTo('conversation', 'conversation_type', 'conversation_id');
    }
}
