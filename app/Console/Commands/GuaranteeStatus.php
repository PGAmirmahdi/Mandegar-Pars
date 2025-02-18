<?php

namespace App\Console\Commands;

use App\Models\Guarantee;
use Illuminate\Console\Command;

class GuaranteeStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guarantee:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change status of guarantees that expired';

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
        // expire the guarantees where expired_at < now
        Guarantee::where('status', 'active')->where('expired_at', '<', now())->update(['status' => 'expired']);
    }
}
