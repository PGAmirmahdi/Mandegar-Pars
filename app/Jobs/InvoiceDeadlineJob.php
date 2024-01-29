<?php

namespace App\Jobs;

use App\Models\Packet;
use App\Notifications\SendMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class InvoiceDeadlineJob implements ShouldQueue
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
        $packets = Packet::with('user')->where('notif_time', now()->format('Y/m/d 00:00:00'))->get();

        foreach ($packets as $packet){
            $message = "به تاریخ تسویه سفارش شماره $packet->invoice_id نزدیک می شوید";
            Notification::send($packet->user, new SendMessage($message, route('packets.index')));
        }
    }
}
