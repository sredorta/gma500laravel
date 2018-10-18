<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

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
            $table->increments('id');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->text('avatar');
            $table->boolean('isMember')->default(false);
            $table->boolean('isBoard')->default(false);
            $table->boolean('isBureau')->default(false);
            $table->string('title')->nullable()->default(null);
            $table->string('password');
            $table->boolean('isEmailValidated')->default(false);
            $table->string('emailValidationKey')->default(Str::random(32));
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
