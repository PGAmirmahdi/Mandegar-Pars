<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $permissions = Permission::pluck('name');

        foreach ($permissions as $permission) {
            Gate::define($permission, function ($user) use($permission){
                return (bool)$user->role->permissions()->where('name', $permission)->first();
            });
        }

        Gate::define('admin', function ($user) {
            return $user->role->name == 'admin';
        });

        Gate::define('edit-profile', function ($user, $user_id){
            return $user->id == $user_id;
        });
    }
}
