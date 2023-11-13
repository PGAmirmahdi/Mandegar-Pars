<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['input','output']);
            $table->string('person')->comment('طرف حساب');
            $table->unsignedBigInteger('factor_id')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();

            $table->foreign('factor_id')->references('id')->on('factors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_reports');
    }
}
