<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventories', function (Blueprint $table) {
            // حذف ستون‌های type، code و title
            $table->dropColumn(['type', 'code', 'title']);

            // اضافه کردن ستون product_id به صورت کلید خارجی
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventories', function (Blueprint $table) {
            // بازگشت به حالت قبل و حذف کلید خارجی
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');

            // اضافه کردن ستون‌های حذف‌شده مجدد
            $table->string('type');
            $table->string('code');
            $table->string('title');
        });
    }
}
