<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InventorySnapshot extends Command
{
    protected $signature = 'inventory:snapshot';
    protected $description = 'Save the inventory snapshot at the end of each month';

    public function handle()
    {
        // دریافت موجودی‌های انبار
        $inventories = \App\Models\Inventory::all();

        // به‌دست آوردن تاریخ دیروز به صورت شمسی با استفاده از Verta
        // در اینجا فرمت 'Y/m/d' به عنوان فرمت شمسی به کار رفته است.
        $yesterdayJalali = \Verta::yesterday()->format('Y/m/d');

        foreach ($inventories as $inventory) {
            \App\Models\InventorySnapshot::create([
                'product_id'    => $inventory->product_id, // فیلد ارجاع به محصول
                'stock_count'   => $inventory->current_count,
                'snapshot_date' => $yesterdayJalali,
                'warehouse_id'  => $inventory->warehouse_id,
            ]);
        }

        $this->info('successfully Imported the inventory count ' . $yesterdayJalali);
    }
}
