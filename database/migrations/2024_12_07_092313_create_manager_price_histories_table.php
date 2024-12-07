<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManagerPriceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manager_price_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // ارجاع به محصول
            $table->unsignedBigInteger('user_id'); // کاربر ثبت‌کننده تغییر قیمت
            $table->decimal('old_price', 15, 2); // قیمت قبلی
            $table->decimal('new_price', 15, 2); // قیمت جدید
            $table->string('description')->nullable(); // توضیحات
            $table->timestamps(); // تاریخ تغییر قیمت

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manager_price_histories');
    }
}
