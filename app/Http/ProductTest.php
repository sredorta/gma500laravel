<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{    
    protected $fillable = array(
        'product_id',
        'cathegory',
        'type'
    );

    //Fields that can be mass assigned
    //protected $fillable = array('name', 'slogan', 'description');
    
    public function set($data) {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }
 /*   id //            $table->increments('id');
    $table->string('name', 50);
    $table->string('slogan', 100);
    $table->string('description',500);
    $table->string('type',50);
    $table->decimal('latitude', 11, 8);
    $table->decimal('longitude', 11, 8);   */

    //Define the one-to-many relation
    public function documents() {
        return $this->hasMany('App\Document');
    }
}
