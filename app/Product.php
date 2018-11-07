<?php

namespace App;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
class Product extends Model
{

    //One product can only belong to one user
    public function profile() {
        return $this->belongsTo(Profile::class, 'profile_id');
    }


    //Depending on request parameters that we have passed in the middleware
    // we return the correct fields
    public static function filterGet(Request $request) {
        if ($request->get('myAccess') == Config::get('constants.ACCESS_MEMBER'))  {
            return Product::select('id','image','cathegory','type','brand','description','usage','docLink','idGMA','serialNumber','profile_id');
        } else if ($request->get('myAccess') == Config::get('constants.ACCESS_ADMIN')) {
            return Product::select('*');
        } else {
            return Product::select('id','idGMA','image','cathegory','type','brand','description','usage','docLink');
        }        
    }

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'id',
            'image',
            'cathegory',
            'type',
            'brand',
            'description',
            'docLink',
            'idGMA',
            'serialNumber',
            'profile_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}
