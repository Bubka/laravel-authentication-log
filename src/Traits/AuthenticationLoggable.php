<?php

namespace Bubka\LaravelAuthenticationLog\Traits;

use Bubka\LaravelAuthenticationLog\Models\AuthenticationLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

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

    /**
     * Get authentications for the provided timespan (in month)
     * 
     * @return Collection<int, AuthenticationLog>
     */
    public function authenticationsByPeriod(int $period = 1)
    {
        $from = Carbon::now()->subMonths($period);

        return $this->authentications->filter(function (AuthenticationLog $authentication) use ($from) {
            return $authentication->login_at >= $from || $authentication->logout_at >= $from;
        });
    }

    /**
     * Get the user's latest authentication
     */
    public function latestAuthentication() : ?AuthenticationLog
    {
        return $this->morphOne(AuthenticationLog::class, 'authenticatable')->latestOfMany('login_at');
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

    /**
     * The notification channels to be used for notifications
     */
    public function notifyAuthenticationLogVia() : array
    {
        return ['mail'];
    }
}
