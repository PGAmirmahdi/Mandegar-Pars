<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('receiver');
            $table->text('address');
            $table->string('send_tracking_code')->unique()->comment('کد رهگیری ارسالی');
            $table->enum('sent_type', ['post','tipax','delivery']);
            $table->enum('packet_status', ['delivered','sending']);
            $table->enum('invoice_status', ['delivered','unknown']);
            $table->string('receive_tracking_code')->comment('کد رهگیری دریافتی')->nullable();
            $table->timestamp('sent_time')->comment('زمان ارسال');
            $table->timestamp('notif_time')->nullable();
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('packets');
    }
}
