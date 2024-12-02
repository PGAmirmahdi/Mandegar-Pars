<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transport_id');
            $table->unsignedBigInteger('transporter_id');
            $table->unsignedBigInteger('price');
            $table->string('payment_type');
            $table->timestamps();

            $table->foreign('transport_id')->references('id')->on('transports')->onDelete('cascade');
            $table->foreign('transporter_id')->references('id')->on('transporters')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_items');
    }
}
