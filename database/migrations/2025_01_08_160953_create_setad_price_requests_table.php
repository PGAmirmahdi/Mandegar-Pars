<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetadPriceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setad_price_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('درخواست دهنده')->constrained()->onDelete('cascade');
            $table->foreignId('acceptor_id')->comment('تایید کننده')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->comment('مشتری')->constrained()->onDelete('cascade');
            $table->string('date')->comment('روز مهلت بررسی');
            $table->string('hour')->comment('ساعت مهلت بررسی');
            $table->string('price');
            $table->string('system_price');
            $table->string('status');
            $table->longText('products');
            $table->longText('description');
            $table->string('code', 12)->unique();
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
