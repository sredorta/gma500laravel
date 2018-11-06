<?php

namespace App;
use App\User;
use App\Notification;
use App\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

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

    //Return the groups of the profile
    public function groups() {
        return $this->belongsToMany('App\Group');
    }

    //Return the accounts of the profile
    public function users() {
        return $this->hasMany('App\User');
    }

    //Return the notifications of the profile
    public function notifications() {
        return $this->hasMany('App\Notification');
    }

    //One user can have several products
    public function products()
    {
        return $this->hasMany('App\Product');
    }
    
    //Remove all profile associated data but not the profile itself
    public function deleteAssociatedData() {
        $this->roles()->detach();   //Detach all roles of Profile
        $this->groups()->detach();
        $this->notifications()->delete();
        //Add here notifications, mssgs...

        
        return null;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName','lastName', 'email', 'mobile','avatar','isEmailValidated','emailValidationKey'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'emailValidationKey','isEmailValidated'
    ];

    //User delete from db
    public function delete()
    {
        // delete all related roles (needs to be done with all related tables)
        $this->roles()->detach();
        $this->groups()->detach();
        $this->users()->delete();
        $this->notifications()->delete();
        parent::delete();
        // We need to keep in each post,outing... the firstName and lastName so that even if the profile is deleted
        //  we still have the name of who posted
        //
    }


    //Depending on request parameters that we have passed in the middleware
    // we return the correct fields
    public static function filterGet(Request $request) {
        if ($request->get('myAccess') == Config::get('constants.ACCESS_MEMBER'))  {
            return Profile::select('id','firstName','lastName','avatar','mobile','email');
        } else if ($request->get('myAccess') == Config::get('constants.ACCESS_ADMIN')) {
            return Profile::select('*');
        } else {
            return Profile::select('id','firstName','lastName','avatar');
        }        
    }

}
