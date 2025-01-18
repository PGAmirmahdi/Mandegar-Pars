<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalePriceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_price_requests', function (Blueprint $table) {
            $table->id();

            // ارتباط با کاربران (درخواست‌دهنده)
            $table->unsignedBigInteger('user_id')->comment('درخواست دهنده');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // ارتباط با کاربران (تایید کننده)
            $table->unsignedBigInteger('acceptor_id')->nullable()->comment('تایید کننده');
            $table->foreign('acceptor_id')->references('id')->on('users')->onDelete('set null');

            // ارتباط با مشتریان
            $table->unsignedBigInteger('customer_id')->comment('مشتری');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            // تاریخ و ساعت مهلت بررسی
            $table->string('date')->comment('روز مهلت بررسی');
            $table->string('hour')->comment('ساعت مهلت بررسی');

            // قیمت‌ها
            $table->string('price')->nullable()->comment('قیمت ثبت شده');
            $table->string('payment_type')->nullable()->comment('نوع پرداختی');
            $table->string('system_price')->nullable()->comment('قیمت سیستمی');

            // وضعیت درخواست
            $table->enum('status', ['pending', 'accepted', 'rejected'])
                ->default('pending')
                ->comment('وضعیت درخواست');

            // محصولات و توضیحات
            $table->longText('products')->comment('محصولات درخواست شده');
            $table->longText('description')->nullable()->comment('توضیحات اضافی');

            $table->string('code', 14)->unique()->comment('کد یکتای درخواست');
            $table->string('type')->comment('نوع فروش');

            $table->string('need_no');
            $table->string('final_description')->nullable();
            $table->string('final_result')->nullable();
            // تاریخ‌های ثبت و بروزرسانی
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setad_price_requests');
    }
}
