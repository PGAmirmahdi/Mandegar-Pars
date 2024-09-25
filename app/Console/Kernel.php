<?php

namespace App\Console;

use App\Jobs\InvoiceDeadlineJob;
use App\Models\Packet;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Notification;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // cron set every day at 00:00 for this job
        $schedule->job(InvoiceDeadlineJob::class);

        // backup database
//        $schedule->command('backup:run --only-db');

        // test cron job
        // $schedule->call(function(){
        //     Notification::send(User::find(1), new SendMessage('test', 'test'));
        // });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
