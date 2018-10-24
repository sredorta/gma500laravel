<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

//ROLE TABLE contains what is the role of the person
// PRESIDENT, BUREAU, ...
class Role extends Model
{
    protected $fillable = [
        'name','description'
    ];   

    //Define Role as a role to many profiles
    public function profiles() {
        return $this->belongsToMany('App\Profile');
    }  
}
