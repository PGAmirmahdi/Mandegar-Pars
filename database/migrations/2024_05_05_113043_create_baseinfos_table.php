<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaseinfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baseinfos', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable(); // نوع اطلاعات (مثلاً اطلاعات پایه، بانکی و غیره)
            $table->string('title'); // عنوان
            $table->string('file')->nullable(); // فایل
            $table->text('info'); // اطلاعات
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
        Schema::dropIfExists('baseinfos');
    }
}
