<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;  //  파사드 사용
use App\Models\User;                  //  User 모델 사용

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Post' => 'App\Policies\PostPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Only organiser can pass this gate
        Gate::define('isOrganiser', function (User $user) {
            return $user->role === 'organiser';
        });
    }
}
