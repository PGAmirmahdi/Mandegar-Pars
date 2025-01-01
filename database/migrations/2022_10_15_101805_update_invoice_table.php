<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->after('id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');

        });
    }


    public function down()
    {
//        Schema::table('invoices', function (Blueprint $table) {
//            $table->dropForeign(['order_id']);
//
//            $table->dropIndex(['order_id']);
//
//            $table->dropColumn('order_id');
//
//        });
    }
}
