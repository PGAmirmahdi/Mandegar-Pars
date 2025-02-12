<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Ticket;
use Illuminate\Console\Command;

class AutoCloseTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:auto-close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'بستن تیکت‌هایی که پس از 12 ساعت بدون تبادل پیام مانده‌اند';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // دریافت تیکت‌های pending که از زمان ایجادشان بیش از 12 ساعت گذشته و تنها پیام اولیه دارند.
        $tickets = Ticket::where('status', 'pending')
            ->where('updated_at', '<=', now()->subHours(24))
            ->withCount('messages')
            ->get();

        foreach ($tickets as $ticket) {
            // اگر فقط یک پیام (پیام اولیه) ثبت شده باشد
            if ($ticket->messages_count == 1) {
                $ticket->update(['status' => 'closed']);

                Activity::create([
                    'user_id'     => 191, // یا در صورت نیاز می‌توانید یک شناسه مخصوص سیستم تعیین کنید.
                    'description' => "تیکت با عنوان '{$ticket->title}' به دلیل عدم تبادل پیام به صورت خودکار بسته شد.",
                    'action'      => 'بسته شدن تیکت (سیستم)',
                    'created_at'  => now(),
                ]);

                $this->info("تیکت با شناسه {$ticket->id} بسته شد.");
            }
        }

        $this->info('فرآیند بسته شدن تیکت‌ها به پایان رسید.');

        return 0;
    }
}
