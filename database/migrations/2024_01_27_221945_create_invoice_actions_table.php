<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->enum('status', ['invoice', 'factor']);
            $table->string('invoice_file')->nullable();
            $table->string('factor_file')->nullable();
            $table->unsignedBigInteger('acceptor_id')->nullable();
            $table->boolean('confirm')->default(0)->comment('تایید توسط مسئول فروش');
            $table->boolean('sent_to_warehouse')->default(0);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
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
        Schema::dropIfExists('invoice_actions');
    }
}
