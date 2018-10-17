<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConfigProductCathegory extends Model
{
    //
    protected $fillable = array(
        'name'
    );         
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
