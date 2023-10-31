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
            $table->unsignedInteger('count');
            $table->string('color');
            $table->enum('unit', ['number'])->default('number');
            $table->unsignedInteger('price');
            $table->unsignedInteger('total_price');
            $table->unsignedInteger('discount_amount')->default(0)->comment('مبلغ تخفیف');
            $table->unsignedInteger('extra_amount')->default(0)->comment('مبلغ اضافات');
            $table->unsignedInteger('tax')->comment('جمع مالیات و عوارض');
            $table->unsignedInteger('invoice_net')->comment('خالص فاکتور');
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
