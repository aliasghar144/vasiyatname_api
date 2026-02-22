<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('none_financial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->string('person')->nullable();       // حق الناس از شخص
            $table->string('person_phone')->nullable();       // شماره تماس شخص
            $table->enum('type', ['tohmat', 'ghyebat', 'abro', 'azar'])->default('tohmat');
            $table->text('description')->nullable();
            $table->boolean('payed')->default(false);
            $table->timestamps();
        });
    }

    /**
     */
    public function down(): void
    {
        Schema::dropIfExists('none_financial');
    }
};
