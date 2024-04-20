<?php

namespace Bubka\LaravelAuthenticationLog\Listeners;

use Bubka\LaravelAuthenticationLog\Notifications\NewDevice;
use Bubka\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LoginListener
{
    public Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(mixed $event) : void
    {
        $listener = config('authentication-log.events.login', Login::class);

        if (! $event instanceof $listener) {
            return;
        }

        if ($event->user) {
            if (! in_array(AuthenticationLoggable::class, class_uses_recursive(get_class($event->user)))) {
                return;
            }

            if (config('authentication-log.behind_cdn')) {
                $ip = $this->request->server(config('authentication-log.behind_cdn.http_header_field'));
            } else {
                $ip = $this->request->ip();
            }

            $user        = $event->user;
            $userAgent   = $this->request->userAgent();
            $known       = $user->authentications()->whereIpAddress($ip)->whereUserAgent($userAgent)->whereLoginSuccessful(true)->first();
            $newUser     = Carbon::parse($user->{$user->getCreatedAtColumn()})->diffInMinutes(Carbon::now()) < 1;
            
            /** @disregard Undefined function */
            $log = $user->authentications()->create([
                'ip_address'       => $ip,
                'user_agent'       => $userAgent,
                'login_at'         => now(),
                'login_successful' => true,
                'location'         => config('authentication-log.notifications.new-device.location') ? optional(geoip()->getLocation($ip))->toArray() : null,
            ]);

            if (! $known && ! $newUser && config('authentication-log.notifications.new-device.enabled')) {
                $newDevice = config('authentication-log.notifications.new-device.template') ?? NewDevice::class;
                $user->notify(new $newDevice($log));
            }
        }
    }
}
