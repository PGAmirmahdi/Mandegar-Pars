<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnalyseIdToAnalyseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analyse_products', function (Blueprint $table) {
            $table->unsignedBigInteger('analyse_id')->after('id');
            $table->foreign('analyse_id')->references('id')->on('analyses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('analyse_products', function (Blueprint $table) {
            $table->dropForeign(['analyse_id']);
            $table->dropColumn('analyse_id');
        });
    }

}
