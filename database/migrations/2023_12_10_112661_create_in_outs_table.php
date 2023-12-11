<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInOutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_outs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_report_id');
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('count');
            $table->timestamps();

            $table->foreign('inventory_report_id')->references('id')->on('inventory_reports')->onDelete('cascade');
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_outs');
    }
}
