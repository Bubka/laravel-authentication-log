<?php

namespace Bubka\LaravelAuthenticationLog\Database\Factories;

use Bubka\LaravelAuthenticationLog\Models\AuthenticationLog;
use Illuminate\Database\Eloquent\Factories\Factory;

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
