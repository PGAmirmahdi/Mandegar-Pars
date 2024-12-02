<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelectToTransportsTable extends Migration
{
    public function up()
    {
        Schema::table('transport_items', function (Blueprint $table) {
            $table->string('select')->default('انتخاب نشده');  // اضافه کردن ستون وضعیت
        });
    }

    public function down()
    {
        Schema::table('transport_items', function (Blueprint $table) {
            $table->string('select')->default('انتخاب نشده');  // اضافه کردن ستون وضعیت
        });
    }
}
