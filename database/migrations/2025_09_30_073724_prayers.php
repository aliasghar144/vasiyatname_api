<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prayers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // صاحب طلب
            $table->integer('fajr_prayer')->default(0);
            $table->integer('dhuhr_prayer')->default(0);
            $table->integer('asr_prayer')->default(0);
            $table->integer('maghrib_prayer')->default(0);
            $table->integer('isha_prayer')->default(0);
            $table->integer('fajr_prayer_rec')->default(0);
            $table->integer('dhuhr_prayer_rec')->default(0);
            $table->integer('asr_prayer_rec')->default(0);
            $table->integer('maghrib_prayer_rec')->default(0);
            $table->integer('isha_prayer_rec')->default(0);
            $table->integer('ayat_rec')->default(0);
            $table->integer('ayat')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prayers');
    }
};
