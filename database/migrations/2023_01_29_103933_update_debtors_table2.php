<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDebtorsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debtors', function (Blueprint $table) {
            $table->string('buy_date')->nullable()->comment('زمان خرید');
            $table->string('factor_number')->nullable()->comment('شماره فاکتور');
            $table->string('payment_due')->nullable()->comment('تاریخ موعد پرداخت');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
