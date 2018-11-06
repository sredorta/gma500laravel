<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Profile;

class Product extends Model
{
    //
    protected $fillable = array(
        'cathegory',
        'name'
    );

    //One product can only belong to one user
    public function profile() {
        return $this->belongsTo('App\Profile');
    }


    //Depending on request parameters that we have passed in the middleware
    // we return the correct fields
    public static function filterGet(Request $request) {
        if ($request->get('myAccess') == Config::get('constants.ACCESS_MEMBER'))  {
            return Product::select('id','image','cathegory','type','brand','description','docLink','idGMA','serialNumber');
        } else if ($request->get('myAccess') == Config::get('constants.ACCESS_ADMIN')) {
            return Product::select('*');
        } else {
            return Product::select('id','image','cathegory','type','brand','description','docLink');
        }        
    }
}
