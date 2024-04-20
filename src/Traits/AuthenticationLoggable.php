<?php

namespace Bubka\LaravelAuthenticationLog\Traits;

use Bubka\LaravelAuthenticationLog\Models\AuthenticationLog;

trait AuthenticationLoggable
{
    /**
     * Get all user's authentications from the auth log
     * 
     * @return Collection<int, AuthenticationLog>
     */
    public function authentications()
    {
        return $this->morphMany(AuthenticationLog::class, 'authenticatable')->latest('id');
    }

    public function latestAuthentication()
    {
        return $this->morphOne(AuthenticationLog::class, 'authenticatable')->latestOfMany('login_at');
    }

    public function notifyAuthenticationLogVia(): array
    {
        return ['mail'];
    }

    /**
     * Get the user's latest authentication datetime
     */
    public function lastLoginAt() : ?Carbon
    {
        return $this->authentications()->first()?->login_at;
    }

    /**
     * Get the user's latest successful login datetime
     */
    public function lastSuccessfulLoginAt() : ?Carbon
    {
        return $this->authentications()->whereLoginSuccessful(true)->first()?->login_at;
    }

    /**
     * Get the ip address of user's latest login
     */
    public function lastLoginIp() : ?string
    {
        return $this->authentications()->first()?->ip_address;
    }

    /**
     * Get the ip address of user's latest successful login
     */
    public function lastSuccessfulLoginIp() : ?string
    {
        return $this->authentications()->whereLoginSuccessful(true)->first()?->ip_address;
    }

    /**
     * Get the user's previous login datetime
     */
    public function previousLoginAt() : ?Carbon
    {
        return $this->authentications()->skip(1)->first()?->login_at;
    }

    /**
     * Get the ip address of user's previous login
     */
    public function previousLoginIp() : ?string
    {
        return $this->authentications()->skip(1)->first()?->ip_address;
    }
}
