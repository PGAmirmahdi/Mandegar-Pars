<?php

namespace App\Console\Commands;

use App\Jobs\ReportReminderJob;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class ReportReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'report reminder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $roles_id = \App\Models\Role::whereHas('permissions', function ($q){
            $q->where('name','reports-create');
        })->pluck('id');

        $users = User::whereIn('role_id',$roles_id)->get();
        $title='ثبت گزارش روزانه';
        $message = 'لطفا گزارش امروز خود را ثبت کنید';
        $url = route('reports.index');

        Notification::send($users, new SendMessage($title,$message, $url));
    }
}
