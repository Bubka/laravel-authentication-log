<?php

namespace Bubka\LaravelAuthenticationLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $authenticatable_type
 * @property int $authenticatable_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $login_at
 * @property bool $login_successful
 * @property \Illuminate\Support\Carbon|null $logout_at
 * @property bool $cleared_by_user
 * @property array|null $location
 * @property string|null $auth_method
 */
class AuthenticationLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected $table = 'authentication_log';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ip_address',
        'user_agent',
        'login_at',
        'login_successful',
        'logout_at',
        'cleared_by_user',
        'location',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'cleared_by_user' => 'boolean',
        'location' => 'array',
        'login_successful' => 'boolean',
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    /**
     * Create a new Eloquent AuthenticationLog instance
     */
    public function __construct(array $attributes = [])
    {
        if (! isset($this->connection)) {
            $this->setConnection(config('authentication-log.db_connection'));
        }

        parent::__construct($attributes);
    }

    /**
     * Get the table associated with the model.
     */
    public function getTable()
    {
        return config('authentication-log.table_name', parent::getTable());
    }

    /**
     * MorphTo relation to get the associated authenticatable user
     * 
     * @return MorphTo<\Illuminate\Database\Eloquent\Model, AuthenticationLog>
     */
    public function authenticatable()
    {
        return $this->morphTo();
    }
}
