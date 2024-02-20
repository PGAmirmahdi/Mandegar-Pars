<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('title');
            $table->unsignedBigInteger('count');
            $table->string('color');
            $table->enum('unit', ['number'])->default('number');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('total_price');
            $table->unsignedBigInteger('discount_amount')->default(0)->comment('مبلغ تخفیف');
            $table->unsignedBigInteger('extra_amount')->default(0)->comment('مبلغ اضافات');
            $table->unsignedBigInteger('tax')->comment('جمع مالیات و عوارض');
            $table->unsignedBigInteger('invoice_net')->comment('خالص فاکتور');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('other_products');
    }
}
