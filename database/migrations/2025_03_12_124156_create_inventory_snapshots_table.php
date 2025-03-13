<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventorySnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->comment('نام محصول')->constrained()->onDelete('cascade');
            $table->integer('stock_count')->comment('موجودی انبار');
            $table->text('snapshot_date');
            $table->foreignId('warehouse_id')->comment('نوع انبار')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_snapshots');
    }
}
