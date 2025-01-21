<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToDateToAnalysesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->string('to_date')->comment('تا تاریخ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('date_to_analyses', function (Blueprint $table) {
            //
        });
    }
}
