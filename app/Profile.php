<?php

namespace App;
use App\User;
use Illuminate\Database\Eloquent\Model;

// PROFILE (To be seen as user)
//  This has to be seen as user table
//  One profile has many USERS (accounts)
//  One profile has many ROLES
//  One profile has many NOTIFS
//  One profile has many MESSAGES


class Profile extends Model
{
    
    //Return the roles of the profile
    public function roles() {
        return $this->belongsToMany('App\Role');
    }

    //Return the accounts of the profile
    public function users() {
        return $this->hasMany('App\User');
    }

    //Remove all profile associated data but not the profile itself
    public function deleteAssociatedData() {
        $this->roles()->detach();   //Detach all roles of Profile
        //Add here notifications, mssgs...
        return null;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName','lastName', 'email', 'mobile','avatar','isEmailValidated','emailValidationKey','restoreKey'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'restoreKey','emailValidationKey','isEmailValidated'
    ];

    //User delete from db
    public function delete()
    {
        // delete all related roles (needs to be done with all related tables)
        $this->roles()->delete();
        
        // We cannot delete the profile as we will be referencing it if user has posts,outings...
        return null;
    }


}
