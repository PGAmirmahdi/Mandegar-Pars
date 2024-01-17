<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuaranteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guarantees', function (Blueprint $table) {
            $table->id();
            $table->string('serial')->unique();
            $table->enum('period', ['12', '24']);
            $table->enum('status', ['active','inactive','voided','expired'])->default('inactive');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expired_at')->nullable();
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
        Schema::dropIfExists('guarantees');
    }
}
