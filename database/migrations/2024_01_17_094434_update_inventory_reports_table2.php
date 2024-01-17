<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInventoryReportsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('guarantee_id')->nullable()->after('factor_id');
            $table->foreign('guarantee_id')->references('id')->on('guarantees')->onDelete(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_reports', function (Blueprint $table) {
            //
        });
    }
}
