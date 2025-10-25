<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // primary key
            $table->string('home_phone')->nullable();
            $table->string('mobile')->unique();
            $table->string('first_name')->nullable();
            $table->string('email')->nullable();
            $table->string('last_name')->nullable();
            $table->string('father_name')->nullable();
            $table->dateTime('birth_date')->nullable();
            $table->string('birth_loc')->nullable();
            $table->string('national_code', 10)->nullable();
            $table->string('reminder_interval')->default('1 day'); //'1 day', '1 week', '1 month'
            $table->boolean('show_notif')->default(true); //'1 day', '1 week', '1 month'
            $table->boolean('is_married')->default(false);
            $table->integer('children_count')->default(0);
            $table->integer('wife_count')->default(0);
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('fcmToken')->nullable();
            $table->string('app_version')->nullable()->default('101');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
