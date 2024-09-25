<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExitDoorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exit_door', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_report_id');
            $table->enum('status', ['confirmed','not_confirmed']);
            $table->longText('description')->nullable();
            $table->timestamps();

            $table->foreign('inventory_report_id')->references('id')->on('inventory_reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exit_door');
    }
}
