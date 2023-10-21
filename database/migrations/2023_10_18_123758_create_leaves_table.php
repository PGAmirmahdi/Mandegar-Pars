<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('acceptor_id')->nullable();
            $table->string('title');
            $table->enum('type', ['hourly','daily']);
            $table->longText('desc')->nullable();
            $table->date('from_date');
            $table->date('to_date');
            $table->time('from')->nullable();
            $table->time('to')->nullable();
            $table->enum('status', ['pending','accept','reject'])->default('pending');
            $table->longText('answer')->nullable();
            $table->dateTime('answer_time')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('acceptor_id')->references('id')->on('users')->onDelete(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaves');
    }
}
