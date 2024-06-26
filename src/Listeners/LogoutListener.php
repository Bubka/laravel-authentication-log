<?php

namespace Bubka\LaravelAuthenticationLog\Listeners;

use Bubka\LaravelAuthenticationLog\Models\AuthenticationLog;
use Bubka\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;

class LogoutListener
{
    public Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(mixed $event) : void
    {
        $listener = config('authentication-log.events.logout', Logout::class);

        if (! $event instanceof $listener) {
            return;
        }

        if ($event->user) {
            if (! in_array(AuthenticationLoggable::class, class_uses_recursive(get_class($event->user)))) {
                return;
            }

            $user = $event->user;

            if (config('authentication-log.behind_cdn')) {
                $ip = $this->request->server(config('authentication-log.behind_cdn.http_header_field'));
            } else {
                $ip = $this->request->ip();
            }

            $userAgent = $this->request->userAgent();
            $log       = $user->authentications()->whereIpAddress($ip)->whereUserAgent($userAgent)->orderByDesc('login_at')->first();
            $guard     = $event->guard;

            if (! $log) {
                $log = new AuthenticationLog([
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'guard'      => $guard,
                ]);
            }

            $log->logout_at = now();

            $user->authentications()->save($log);
        }
    }
}
