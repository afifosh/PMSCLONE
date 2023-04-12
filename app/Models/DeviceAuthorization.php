<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class Authorize
 * @package App
 */
class DeviceAuthorization extends Model

{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $table = 'device_authorizations';

    /**
     * @var boolean
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $dates = ['authorized_at', 'deleted_at'];

    /**
     * @var string
     */
    protected $fillable = [
        'uuid','authorized', 'fingerprint','ip_address','user_agent','location','token', 'attempt', 'authorized_at',
    ];

    /**
     * Morph to models User and Admin
     * 
     * @return MorphTo
     */
    public function authenticatable()
    {
        return $this->morphTo();
    }    

    /**
     * @param $date
     */
    public function setAuthorizedAtAttribute($date)
    {
        $this->attributes['authorized_at'] = Carbon::parse($date);
    }

    /**
     * @return mixed
     */
    public static function active()
    {
        return with(new self)
            ->where('ip_address', Request::ip())
            ->where('authorized', true)
            ->where('authorized_at', '<', Carbon::tomorrow())
            ->first();
    }

    /**
     * @return mixed
     */
    public function resetAttempt()
    {
        $this->update(['attempt' => 0]);

        return $this;
    }

    /**
     * @return mixed
     */
    public function noAttempt()
    {
        return $this->attempt < 1;
    }

    /**
     * @param $token
     */
    public static function validateToken($token = null)
    {
        $query = self::where([
            'token' => $token,
        ])->first();

        if (sizeof($query)) {
            $query->update([
                'authorized' => true,
                'authorized_at' => now(),
            ]);

            return self::active();
        }
    }

    /**
     * @return mixed
     */
    public static function make()
    {
        return self::firstOrCreate([
            'ip_address' => Request::ip(),
            'authorized' => false,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * @return mixed
     */
    public static function inactive()
    {
        $query = self::active();

        return $query ? null : true;
    }
}