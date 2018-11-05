<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use JWTAuth;
use Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Mail\ValidateEmail;
use App\User;
use App\Role;
use App\Profile;


class UserController extends Controller
{
    //Generate a random password
    private function _generatePassword(
        $length,
        $keyspace = '01234567890123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
        ) {
            $str = '';
            while(preg_match_all( "/[0-9]/", $str )<2) {
            $str = '';  
            $max = mb_strlen($keyspace, '8bit') - 1;
            if ($max < 1) {
                throw new Exception('$keyspace must be at least two characters long');
            }
            for ($i = 0; $i < $length; ++$i) {
                $str .= $keyspace[random_int(0, $max)];
            }
            }
            //Add minimum criteria to pass front-End control
            $str = $str . 'Za8';
            return $str;
    }

    //Switches a Member to Pré-inscrit and viceversa
    public function toggleMember(Request $request) {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }
        $profile = Profile::find($request->profile_id);
        $user = $profile->users()->where("access", Config::get('constants.ACCESS_MEMBER'));
        if ($user->count() === 1) {
            $user = $user->first();
            $user->access = Config::get('constants.ACCESS_DEFAULT');
            $user->save();
            //Send email with Member -> default
            $data = [
                'title' => 'Compte Membre suprimé',
                'text' => 'Votre compte \'Membre\' vient d\'etre suprimé'
            ];        
            Mail::send('emails.generic',$data, function($message) use ($user)
            {
                $message->from(Config::get('constants.EMAIL_FROM_ADDRESS'), Config::get('constants.EMAIL_FROM_NAME'));
                $message->replyTo(Config::get('constants.EMAIL_NOREPLY'));
                $message->to($user->email);
                $message->subject("GMA500: Votre compte 'Membre'" );
            });            

        } else {
            $user = $profile->users()->where("access", Config::get('constants.ACCESS_DEFAULT'));
            if ($user->count() === 1) {
                $user = $user->first();
                $user->access = Config::get('constants.ACCESS_MEMBER');
                $user->save();
                //Send email with default -> Member
                $data = [
                    'title' => 'Compte Membre est accepté',
                    'text' => 'Votre compte \'Membre\' vient d\'etre accepté'
                ];        
                Mail::send('emails.generic',$data, function($message) use ($user)
                {
                    $message->from(Config::get('constants.EMAIL_FROM_ADDRESS'), Config::get('constants.EMAIL_FROM_NAME'));
                    $message->replyTo(Config::get('constants.EMAIL_NOREPLY'));
                    $message->to($user->email);
                    $message->subject("GMA500: Votre compte 'Membre'" );
                });   

            }        
        }
        return response()->json(null,204);
    
    }


    //Adds a user to a profile
    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|numeric',
            'access' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }

        $profile = Profile::find($request->profile_id);
        $pass = $this->_generatePassword(10); //Generate new password for new account

        $account = User::create([
            'profile_id' => $profile->id,
            'email' => $profile->email,
            'password' => Hash::make($pass, ['rounds' => 12]),
            'access' => $request->access
        ]);
        //Send email with new password
        $data = [
            'name' =>  $profile->firstName,
            'password' => $pass,
            'access' => $request->access
        ];        
        Mail::send('emails.newaccount',$data, function($message) use ($account)
        {
            $message->from(Config::get('constants.EMAIL_FROM_ADDRESS'), Config::get('constants.EMAIL_FROM_NAME'));
            $message->replyTo(Config::get('constants.EMAIL_NOREPLY'));
            $message->to($account->email);
            $message->subject("GMA500: Votre nouveau compte " . $account->access );
        });            
        return response()->json($account,200); 
    }

    //Removes a user to a profile
    public function remove(Request $request) {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|numeric',
            'access' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }
        //Remove the accounts
        User::where("profile_id", $request->profile_id)->where("access", $request->access)->delete();
        $profile = Profile::find($request->profile_id);
        //Send email with new password
        $access = $request->access;
        $data = [
            'name' =>  $profile->firstName,
            'access' => $request->access
        ];        
        Mail::send('emails.removedaccount',$data, function($message) use ($profile, $access)
        {
            $message->from(Config::get('constants.EMAIL_FROM_ADDRESS'), Config::get('constants.EMAIL_FROM_NAME'));
            $message->replyTo(Config::get('constants.EMAIL_NOREPLY'));
            $message->to($profile->email);
            $message->subject("GMA500: Compte " . $access . " suprimé" );
        });            
        return response()->json(null,204); 
    }
/*    //
    public function index()
    {
        return User::all();
    }


    //Checks if we are currently logged in and returns user if we are and null if not
    public function isLogged(Request $request) {
        if ($request->bearerToken()=== null) {
            return false;
        }
        //We need to remove the catch and redirect to loggin in production
        //try {
        JWTAuth::setToken($request->bearerToken()) ;
        $result = JWTAuth::toUser();
        //} catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        //    $result = null;
        //}
        return $result;
    }

    //Checks if the current user has the allowed access
    public function hasAccess(Request $request, $access) {
        $result = $this->isLogged($request);
        if ($result == null) return false;  //Token invalid
        return !$result->roles()->where('name','=',$access)->get()->isEmpty();
    }
*/


/*
    public function show($id)
    {
        $user = User::find($id);
        if ($product) {
            return response()->json($user, 200);
        } else {
            //This needs to be commented and return null,204
            $object = (object) ['test' => 'no data'];
            return response()->json($object, 204);
        }
    }

    public function store(Request $request)
    {
        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($request->all());
        return response()->json($product, 200);
    }

    public function delete(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
*/
}
/*
200: OK. The standard success code and default option.
201: Object created. Useful for the store actions.
204: No content. When an action was executed successfully, but there is no content to return.
206: Partial content. Useful when you have to return a paginated list of resources.
400: Bad request. The standard option for requests that fail to pass validation.
401: Unauthorized. The user needs to be authenticated.
403: Forbidden. The user is authenticated, but does not have the permissions to perform an action.
404: Not found. This will be returned automatically by Laravel when the resource is not found.
500: Internal server error. Ideally you're not going to be explicitly returning this, but if something unexpected breaks, this is what your user is going to receive.
503: Service unavailable. Pretty self explanatory, but also another code that is not going to be returned explicitly by the application
*/   
