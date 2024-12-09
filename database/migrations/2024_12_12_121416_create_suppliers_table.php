<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('user_id')->comment('ثبت کننده')->nullable()->constrained()->onDelete('cascade');
            $table->enum('supplier_type', ['local','foreign']);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('code', 8)->unique();
            $table->string('economical_number')->comment('شماره اقتصادی')->nullable();
            $table->string('national_number')->comment('شماره ملی');
            $table->string('province');
            $table->string('city');
            $table->longText('description')->nullable();
            $table->string('phone1');
            $table->string('phone2')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('postal_code');
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
        Schema::dropIfExists('suppliers');
    }
}
