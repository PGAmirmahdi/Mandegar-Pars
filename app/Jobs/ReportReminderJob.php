<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ReportReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $roles_id = \App\Models\Role::whereHas('permissions', function ($q){
            $q->where('name','reports-create');
        })->pluck('id');

        $users = User::whereIn('role_id',$roles_id)->get();

        $message = 'لطفا گزارش امروز خود را ثبت کنید';
        $url = route('reports.index');

        Notification::send($users, new SendMessage($message, $url));
    }
}
