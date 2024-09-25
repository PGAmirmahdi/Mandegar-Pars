<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIpAddressToUserVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_visits', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable(); // اضافه کردن فیلد ip_address
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_visits', function (Blueprint $table) {
            $table->dropColumn('ip_address'); // حذف فیلد ip_address در صورت برگشت مایگریشن
        });
    }
}
