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
            $table->string('title');
            $table->enum('type', ['mardomi', 'banki', 'mehriye']);
            $table->integer('amount');
            $table->integer('amount_paid')->default(0);
            $table->date('created_date');
            $table->date('due_date');
            $table->string('status');
            $table->string('full_name');
            $table->string('national_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('debts');
    }
};
