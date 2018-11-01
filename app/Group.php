<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name','description'
    ];   

    //Define Role as a role to many profiles
    public function profiles() {
        return $this->belongsToMany('App\Profile');
    }      
}
