<?php

namespace App\Console;

use App\Console\Commands\ReportReminder;
use App\Console\Commands\SendWeeklyPriceList;
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
        $schedule->job(new SendWeeklyPriceList)->dailyAt('13:00');
        $schedule->job(new ReportReminder)->dailyAt('16:00');
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
