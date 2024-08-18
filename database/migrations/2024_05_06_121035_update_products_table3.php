<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('market_price')->nullable()->after('single_price')->comment('قیمت بازار');
            $table->unsignedBigInteger('domestic_price')->nullable()->after('market_price')->comment('قیمت داخلی');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('market_price');
            $table->dropColumn('domestic_price');
        });
    }
}
