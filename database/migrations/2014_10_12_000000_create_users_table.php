<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 70);
            $table->string('username', 100)->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->nullable()->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->tinyText('bio')->nullable();
            $table->string('location', 100)->nullable();
            $table->unsignedBigInteger('followers')->default(0);
            $table->unsignedBigInteger('followings')->default(0);
            $table->string('avatar')->nullable();
            $table->string('wall')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
