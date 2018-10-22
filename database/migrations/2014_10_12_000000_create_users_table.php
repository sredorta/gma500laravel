<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


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
            $table->string('email')->unique(); //Email max length is 191 chars and cannot be changed
            $table->string('mobile')->unique();
            $table->string('avatar',14000); //Depends on avatar size 
            $table->boolean('isMember')->default(false);
            $table->boolean('isBoard')->default(false);
            $table->boolean('isBureau')->default(false);
            $table->string('title')->nullable()->default(null);
            $table->string('password',255);
            $table->boolean('isEmailValidated')->default(false);
            $table->string('emailValidationKey',50)->default('default');
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
