<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
//            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->unsignedBigInteger('system_price')->comment('قیمت سامانه');
            $table->unsignedBigInteger('partner_price_tehran')->comment('قیمت همکار (تهران)');
            $table->unsignedBigInteger('partner_price_other')->comment('قیمت همکار (شهرستان)');
            $table->unsignedBigInteger('single_price')->comment('قیمت تک فروشی');
            $table->string('image');
            $table->unsignedInteger('total_count');
            $table->unsignedBigInteger('category_id');
            $table->json('properties')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
