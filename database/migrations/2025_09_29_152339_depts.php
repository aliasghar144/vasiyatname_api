<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // صاحب طلب
            $table->string('from');       // طلب از
            $table->string('relation');   // نسبت
            $table->date('due_date')->nullable(); // تاریخ پرداخت
            $table->string('subject');    // موضوع
            $table->decimal('amount', 15, 2); // مبلغ
            $table->string('check_number')->nullable(); // شماره چک
            $table->enum('status', ['pending', 'received'])->default('pending'); // وضعیت
            $table->text('description')->nullable();   // توضیحات
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
