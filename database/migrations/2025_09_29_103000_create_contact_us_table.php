<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('contact_us', function (Blueprint $table) {
            $table->id(); // primary key
            $table->string('subject')->nullable();
            $table->string('description')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // صاحب طلب

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_us');
    }
}
