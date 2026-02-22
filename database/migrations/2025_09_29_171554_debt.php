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
            $table->string('subject');       // موضوع
            $table->string('person')->nullable();       // طلب از شخص
            $table->string('person_phone')->nullable();       // شماره تماس شخص
            $table->enum('debt_type', ['mardomi', 'banki'])->default('mardomi');
            $table->string('bank_name')->nullable();
            $table->string('amount')->nullable();
            $table->string('description')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('debts');
    }
};
