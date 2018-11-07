<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cathegory', 50);
            $table->string('type', 50);    
            $table->string('image',15000);
            $table->string('brand',50);
            $table->string('description',500);
            $table->string('usage',50);
            $table->string('serialNumber',50)->unique();
            $table->string('idGMA',50)->unique();
            $table->string('fabricatedOn',50);
            $table->string('boughtOn',50);
            $table->string('expiresOn',50);
            $table->string('docLink',200);
            $table->integer('profile_id')->unsigned()->nullable();
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('set null');
            //Missing controls
            //Missing comments
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
        Schema::dropIfExists('products');
    }
}
