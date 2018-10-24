<?php

namespace App;
use App\Profile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

// USER (To be seen as account)
//  One user belongs to ONE PROFILE (user)


class User extends Authenticatable 
{
    use Notifiable;

    //Define Role as a user to Many
    public function profile() {
        return $this->belongsTo('App\Profile');
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'profile_id','email','password','access'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    //User delete from db
    public function delete()
    {
        // delete all related roles (needs to be done with all related tables)
        //$this->roles()->delete();
        // delete the user (account)
        return parent::delete();
    }





}
