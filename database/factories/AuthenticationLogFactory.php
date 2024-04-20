<?php

namespace Bubka\LaravelAuthenticationLog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Bubka\LaravelAuthenticationLog\Models\AuthenticationLog;

/**
 * @extends Factory<AuthenticationLog>
 */
class AuthenticationLogFactory extends Factory
{
    protected $model = AuthenticationLog::class;

    public function definition()
    {
        return [
        ];
    }
}
