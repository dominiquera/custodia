<?php

namespace Custodia\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'Custodia\Model' => 'Custodia\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // the gate checks if the user is an admin or a superadmin
        Gate::define('accessAdminpanel', function($user) {
            return ($user->role->name == "Admin");
        });

        //
        // the gate checks if the user is a member
        Gate::define('accessProfile', function($user) {
            return $user->role->name == 'Member';
        });
    }
}
