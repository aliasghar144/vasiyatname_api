<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // صاحب طلب
            $table->string('from');
            $table->enum('debt_type', ['mardomi', 'banki'])->default('mardomi');
            $table->string('bank_name')->nullable();
            $table->integer('amount');
            $table->dateTime('due_date');
            $table->enum('status', ['pending', 'received'])->default('pending'); // وضعیت
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('debts');
    }
};
