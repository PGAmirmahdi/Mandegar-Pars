<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNeedNoToSetadPriceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setad_price_requests', function (Blueprint $table) {
                $table->string('need_no');
                $table->string('final_description')->nullable();
                $table->string('final_result')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setad_price_requests', function (Blueprint $table) {
            //
        });
    }
}
