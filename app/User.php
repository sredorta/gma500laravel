<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable 
{
    use Notifiable;

    //Define Role as a user to Many
    public function roles() {
        return $this->belongsToMany('App\Role');
    }

    public function hasAccess($access) {
        return !$this->roles()->where('name','=',$access)->get()->isEmpty();
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName','lastName', 'email', 'mobile','avatar','isMember','isBureau', 'isBoard', 'title','password','isEmailValidated','emailValidationKey'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password','emailValidationKey'
    ];

    //User delete from db
    public function delete()
    {
        // delete all related roles (needs to be done with all related tables)
        $this->roles()->delete();
        // delete the user
        return parent::delete();
    }





}
/*email: string;
mobile: string;
password: string;
firstName: string;
lastName: string;
avatar: any;
roles: string[] = [];   //Member, president...
isLoggedIn : boolean = false;
isValidated : boolean = false;
groups : string[] = ["none"]*/