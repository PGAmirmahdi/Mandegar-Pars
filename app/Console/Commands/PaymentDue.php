<?php

namespace App\Console\Commands;

use App\Models\Debtor;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class PaymentDue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:payment-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ارسال پیام به حسابداران جهت بررسی موعد پرداخت بدهکاران';

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
        $title = 'موعد پرداخت مشتری';
        $url = route('debtors.index');
        $debtors = Debtor::all();
        $admins = User::whereHas('role', function ($query) {
            $query->where('name', 'admin');
        })->get();
        foreach ($debtors as $debtor) {
            $paymentDue = verta($debtor->payment_due); // تاریخ هجری شمسی
            $paymentDueGregorian = $paymentDue->toCarbon()->format('Y-m-d'); // تبدیل به میلادی با استفاده از Carbon
            $today = verta(now())->format('Y-m-d');
            // محاسبه تفاوت تاریخ‌ها به روز
            $daysLeft = \Carbon\Carbon::parse($today)->diffInDays(\Carbon\Carbon::parse($paymentDueGregorian), false);
            // پیدا کردن کاربران با نقش ادمین

            if ($daysLeft <= 0) {
                $message = "موعد پرداخت مشتری '{$debtor->customer->name}' امروز است.";
                Notification::send($admins, new SendMessage($title, $message, $url));
            }
        }
    }
}
