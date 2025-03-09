<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('عنوان');
            $table->string('document')->comment('مدارک');
            $table->enum('status', ['pending', 'sent', 'not-sent'])->comment('وضعیت');
            $table->unsignedBigInteger('user_id')->comment('درخواست دهنده');
            $table->unsignedBigInteger('sender_id')->nullable()->comment('ارسال کننده');
            $table->longText('description')->nullable()->comment('توضیحات درخواست دهنده');
            $table->longText('sender_description')->nullable()->comment('توضیحات ارسال کننده');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('send_documents');
    }
}
