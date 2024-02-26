<?php

namespace App\Providers;

use App\Models\BuyOrder;
use App\Models\Invoice;
use App\Models\Note;
use App\Models\Packet;
use App\Models\Permission;
use App\Models\Report;
use App\Models\SmsHistory;
use App\Models\Task;
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

        Gate::define('edit-packet', function ($user, Packet $packet){
            return $user->id == $packet->user_id || $user->isAdmin();
        });

        Gate::define('edit-invoice', function ($user, Invoice $invoice){
            return $user->id == $invoice->user_id || $user->isAdmin() || $user->isAccountant() || $user->isCEO() || $user->isSalesManager();
        });

        Gate::define('edit-factor', function ($user, Invoice $invoice){
            return $user->id == $invoice->user_id || $user->isAdmin() || $user->isAccountant() || $user->isCEO();
        });

        Gate::define('edit-task', function ($user, Task $task){
            return $user->id == $task->creator_id;
        });

        Gate::define('delete-task', function ($user, Task $task){
            return $user->id == $task->creator_id;
        });

        Gate::define('edit-note', function ($user, Note $note){
            return $user->id == $note->user_id;
        });

        Gate::define('show-sms-history', function ($user, SmsHistory $smsHistory){
            return $user->id == $smsHistory->user_id || $user->isAdmin() || $user->isCEO();
        });

        Gate::define('edit-report', function ($user, Report $report){
            return $user->id == $report->user_id || $user->isAdmin() || $user->isCEO();
        });

        Gate::define('edit-buy-order', function ($user, BuyOrder $buyOrder){
            return $user->id == $buyOrder->user_id || $user->isSalesManager();
        });
    }
}
