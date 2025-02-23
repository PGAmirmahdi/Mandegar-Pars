<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandegarPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandegar_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('price')->comment('قیمت محصول')->nullable();
            $table->integer('order')->default(0)->comment('درگ و دراپ کردن');
            $table->timestamps();
            // کلید خارجی
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            // موتور InnoDB
        }, 'InnoDB');


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mandegar_prices');
    }
}
