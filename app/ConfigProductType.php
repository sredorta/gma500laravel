<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConfigProductType extends Model
{
    //
    protected $fillable = array(
        'name'
    );        
    protected $hidden = [
        'created_at', 'updated_at',
    ];    
}
