<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // صاحب طلب
            $table->string('from');       // طلب از
            $table->enum('claim_type', ['financial', 'none_financial'])->default('financial'); // وضعیت
            $table->integer('amount'); // مبلغ
            $table->text('description')->nullable();   // توضیحات
            $table->enum('status', ['pending', 'received'])->default('pending'); // وضعیت
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('claims');
    }
};
