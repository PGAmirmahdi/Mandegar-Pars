<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePacketsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packets', function (Blueprint $table) {
            $table->unsignedInteger('delivery_code')->unique()->nullable()->after('packet_status');
            $table->enum('delivery_verify', ['confirmed','unconfirmed'])->after('delivery_code')->default('unconfirmed')->comment('تایید توسط تحویل دهنده');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packets', function (Blueprint $table) {
            $table->dropColumn('delivery_code');
            $table->dropColumn('delivery_verify');
        });
    }
}
