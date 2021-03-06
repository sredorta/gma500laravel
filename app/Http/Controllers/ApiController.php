<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Validator;
use JWTAuth;
use App\User;
use App\Profile;
use App\Role;
use App\Notification;
use App\Config\constants;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class ApiController extends Controller
{
    use ThrottlesLogins;
    public function __construct()
    {
        $this->user = new User;
    }

    //Function required by the throttler
    public function username() {
        return 'toto';
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    //
    //  register:
    //
    //  We create the new profile and a new 'standard' user and send email to validate email account
    //  we then add notification to user of welcome
    //  Finally we redirect to home
    //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function register(Request $request)
    {
        //Check if user already registerd in the Profiles
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:profiles',
            'mobile' => 'required|unique:profiles'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'response' => 'error',
                'message' => 'user_already_registered'
            ],400);
        }       
        //Check for all parameters
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'firstName' => 'required',
            'lastName' => 'required',
            'mobile' => 'required',
            'password'=> 'required'
        ]);
        if ($validator->fails()) {
            return response()
                ->json([
                    'response' => 'error',
                    'message' => 'validation_failed',
                    'errors' => $validator->errors()
                ], 400);            
        }
        //FIRST: we create a Profile
        $profile= Profile::create([
            'firstName' => $request->get('firstName'),           
            'lastName' => $request->get('lastName'),
            'mobile' => $request->get('mobile'),
            'email' => $request->get('email'),
            'avatar' => 'url(' . $request->get('avatar') . ')',
            'emailValidationKey' => Str::random(50)
        ]);
        //We don't assign any Role as user has only 'default' access when creating it

        //SECOND: we create the standard User (account)
        $user = new User;
        $user->email = $profile->email;
        $user->password = Hash::make($request->get('password'), ['rounds' => 12]);
        $user->access = Config::get('constants.ACCESS_DEFAULT');
        $profile->users()->save($user);
  
        //THIRD: Send email with validation key
        $data = [
            'name' =>  $profile->firstName,
            'key' => Config::get('constants.API_URL') . '/api/auth/emailvalidate?id=' . 
                    $profile->id  .
                    '&key=' .
                    $profile->emailValidationKey
        ];

        Mail::send('emails.validateemail',$data, function($message) use ($profile)
        {
            $message->from(Config::get('constants.EMAIL_FROM_ADDRESS'), Config::get('constants.EMAIL_FROM_NAME'));
            $message->replyTo(Config::get('constants.EMAIL_NOREPLY'));
            $message->to($profile->email);
            $message->subject("GMA500: Confirmation de votre adresse électronique");
        });   

        //Add user notification
        $notification = new Notification;
        $notification->text = "Bienvenu au site du GMA. Vous etes pré-inscrit";
        $notification->isRead = 0;
        $profile->notifications()->save($notification);

        //FINALLY:: Return ok code
        return response()->json([
            'response' => 'success',
            'message' => 'signup_success',
        ], 200);
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////
    //
    //  login:
    //      email
    //      password
    //      keepconnected
    //
    //  We use throttle to avoid to many attempts
    //  If login is accepted we return the token
    //  If isEmailValidated is false we return error and invalidate token
    //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        $token = null;

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|min:4',
            'access' => 'nullable|min:3'
        ]);
        //Define token lifeTime
        if ($request->keepconnected) {
            $tokenLife = Config::get('constants.TOKEN_LIFE_LONG'); 
        } else {
            $tokenLife = Config::get('constants.TOKEN_LIFE_SHORT'); 
        }
        //Check parameters
        if ($validator->fails()) {
            return response()
                ->json([
                    'response' => 'error',
                    'message' => 'validation_failed',
                    'errors' => $validator->errors()
                ], 400);
        }
        
        //Check for throttling count
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return response()->json(['response' =>'error','message' => 'too_many_logins'], 400);
        }        

        //If access is not provided and we have multiple accounts return the list of available accounts
        if ($request->access == null && User::where('email', $request->email)->count()>1) {
            $access = User::where('email', $request->email)->select('access')->get()->pluck('access');
            return response()->json([
                'response' => 'multiple_access',
                'message' => $access->toArray(),
            ],200);
        }

        try {
            //Get the user and store it in the payload
            if ($request->access !== null) {
            $user = User::where('email', $request->email)->where('access', $request->access)->first();
            } else {
                $user = User::where('email', $request->email)->first();
            }
            //Check that we got at least one user
            if ($user == null) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'invalid_email_or_password'                                 
                ],400);                
            }
            //Check for credentials
            if (($user->email !== $request->get('email'))|| !Hash::check($request->get('password'), $user->password)) {
                return response()->json([  
                    'response' => 'error',
                    'message' => 'invalid_email_or_password'                                
                ],400);
            }
            //We use user id because is unique for credentials
            $credentials = ['id' => $user->id, 'password'=> $request->get('password')];
            $customClaims = ['id'=> $user->id, 'exp' => Carbon::now()->addMinutes($tokenLife)->timestamp];
            if (!$token = JWTAuth::attempt($credentials,$customClaims)) {
                $this->incrementLoginAttempts($request); //Increments throttle count
                return response()->json([
                    'response' => 'error',
                    'message' => 'invalid_email_or_password'
                ],401);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'failed_to_create_token',
            ],401);
        }
        //Check if isEmailValidated in the Profile if not invalidate token and return error
        JWTAuth::setToken($token) ;
        $user = JWTAuth::toUser();
        $profile = Profile::find($user->profile_id);
        if ($profile->isEmailValidated == 0) {
            JWTAuth::invalidate($token);
            return response()->json([
                'response' => 'error',
                'message' => 'email_not_validated',
            ],401);            
        }

        //Return the token
        $object = (object) ['token' => $token];
        return response()->json($object,200);
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    //
    //  getAuthUser:
    //
    //  We parse from the header the token recieved
    //  If is valid then we return the user as json else empty
    //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function getAuthUser(Request $request){
        if ($request->bearerToken() === null) {
            return response()->json(null,200);
        }

        JWTAuth::setToken($request->bearerToken()) ;
        //Get user id from the payload
        $payload = JWTAuth::parseToken()->getPayload();
        $user = User::find($payload->get('id'));
        //Return all data
        $profile = Profile::with('roles')->with('groups')->with('notifications')->with('products')->find($user->profile_id);
        $notifsCount = Notification::where('profile_id', $user->profile_id)->where('isRead', 0)->count();
        $profile->access = $user->access;
        $profile->notifsUnreadCount = $notifsCount;
        return response()->json($profile,200);    
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    //
    //  logout:
    //
    //  Invalidates the token
    //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function logout(Request $request){
        if ($request->bearerToken()=== null) {
            return response()->json(null,200);
        }    
        JWTAuth::invalidate($request->bearerToken());
        return response()->json(null,200);
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    //  update:
    //      firstName or lastName or email or mobile, or avatar or (password_new and password_old)
    //
    //  We need to be registered and we update the requested field
    //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function update(Request $request) {
        //Update firstName if is required
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string'
        ]);        
        if (!$validator->fails()) {
            $profile = Profile::find($request->get('myProfile'));
            $profile->firstName = $request->firstName;
            $profile->save();
            return response()->json([
                'response' => 'success',
                'message' => 'update_success',
            ], 200);
        }
        //Update lastName if is required
        $validator = Validator::make($request->all(), [
            'lastName' => 'required|string'
        ]);        
        if (!$validator->fails()) {
            $profile = Profile::find($request->get('myProfile'));
            $profile->lastName = $request->lastName;
            $profile->save();
            return response()->json([
                'response' => 'success',
                'message' => 'update_success',
            ], 200);            
        }        
        //Update avatar if is required
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|string'
        ]);        
        if (!$validator->fails()) {
            $profile = Profile::find($request->get('myProfile'));
            $profile->avatar = $request->avatar;
            $profile->save();
            return response()->json([
                'response' => 'success',
                'message' => 'update_success',
            ], 200);
        } 

        //Update mobile if is required
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|unique:profiles'
        ]);        
        if (!$validator->fails()) {
            $profile = Profile::find($request->get('myProfile'));
            $profile->mobile = $request->mobile;
            $profile->save();
            return response()->json([
                'response' => 'success',
                'message' => 'update_success',
            ], 200);
        }  

        //Update password
        $validator = Validator::make($request->all(), [
            'password_old' => 'required|string|min:5',
            'password_new' => 'required|string|min:5'
        ]);        
        if (!$validator->fails()) {
            //Check that password old matches
            $user = Profile::find($request->get('myProfile'))->users()->where("access", $request->get('myAccess'))->first();
            if (!Hash::check($request->get('password_old'), $user->password)) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'invalid_password'
                ],400);
            }
            $user->password = Hash::make($request->get('password_new'), ['rounds' => 12]);
            $user->save();
            return response()->json([
                'response' => 'success',
                'message' => 'update_success',
            ], 200);
        }  

        //Update email if is required and then we need to set email validated to false and logout and send email
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:profiles'
        ]);        
        if (!$validator->fails()) {
            $profile = Profile::find($request->get('myProfile'));
            $profile->isEmailValidated = 0;
            $profile->emailValidationKey = Str::random(50);
            $profile->email = $request->email;
            $profile->save();
            //Update email on all users of the profile
            $profile->users()->each(function($user) use ($profile) {
                $user->email = $profile->email;
                $user->save();
            });
            //Send email with validation key
            $data = [
                'name' =>  $profile->firstName,
                'key' => Config::get('constants.API_URL') . '/api/auth/emailvalidate?id=' . 
                        $profile->id  .
                        '&key=' .
                        $profile->emailValidationKey
            ];

            Mail::send('emails.validateemail',$data, function($message) use ($profile)
            {
                $message->from(Config::get('constants.EMAIL_FROM_ADDRESS'), Config::get('constants.EMAIL_FROM_NAME'));
                $message->replyTo(Config::get('constants.EMAIL_NOREPLY'));
                $message->to($profile->email);
                $message->subject("GMA500: Confirmation de votre adresse électronique");
            });  
            //Invalidate the token
            JWTAuth::invalidate($request->bearerToken());
            return response()->json([
                'response' => 'success',
                'message' => 'update_success',
            ], 200);            
        }
        //If we got here, we have bad arguments
        return response()->json([
            'response' => 'error',
            'message' => 'update_params_error',
        ],400);

    }



    /////////////////////////////////////////////////////////////////////////////////////////
    //  resetPassword:
    //      email : email of the user
    //
    //  We get id of the user from email change password and send email with new password
    //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'access' => 'nullable|min:3'
        ]);        
        //Check parameters
        if ($validator->fails()) {
            return response()
                ->json([
                    'response' => 'error',
                    'message' => 'validation_failed'
                ], 400);
        }
        //If access is not provided and we have multiple accounts return the list of available accounts
        if ($request->access == null && User::where('email', $request->email)->count()>1) {
            $access = User::where('email', $request->email)->select('access')->get()->pluck('access');
            return response()->json([
                'response' => 'multiple_access',
                'message' => $access->toArray(),
            ],200);
        }
        //Get the user
        if ($request->access !== null) {
            $user = User::where('email', $request->email)->where('access', $request->access)->first();
        } else {
            $user = User::where('email', $request->email)->first();
        }
        //Check that we have user with the requested email/access
        if ($user == null) {
            return response()
                ->json([
                    'response' => 'error',
                    'message' => 'email_not_found'
                ], 400);          
        }
        //Regenerate a new password
        $newPass = $this->_generatePassword(10);
        $user->password = Hash::make($newPass, ['rounds' => 12]);
        $user->save();
        //Get the profile from the user
        $profile = Profile::find($user->profile_id);
        //Send email with new password
        $data = [
            'name' =>  $profile->firstName,
            'password' => $newPass,
            'access' => $user->access
        ];        
        Mail::send('emails.resetpassword',$data, function($message) use ($user)
        {
            $message->from(Config::get('constants.EMAIL_FROM_ADDRESS'), Config::get('constants.EMAIL_FROM_NAME'));
            $message->replyTo(Config::get('constants.EMAIL_NOREPLY'));
            $message->to($user->email);
            $message->subject("GMA500: Votre nouveau mot de passe");
        });        

        return response()->json([
            'response' => 'success',
            'message' => 'password_reset_success'
        ], 200); 
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    // emailValidate:
    //      id : Id of the user
    //      key: Email validation key
    //
    //  We get id and email and we check if it matches the one in the db, if it's the case
    //  we then set isEmailValidated to true
    //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function emailValidate(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'key' => 'required'
        ]);        
        if ($validator->fails()) {
            return view('emailvalidation')->with('result',0);
        }        
        //Check that we have user with the requested id
        $profile = Profile::where('id', '=', $request->get('id'))->where('emailValidationKey','=',$request->get('key'));
        if (!$profile->count()) {
            return view('emailvalidation')->with('result',0);
        }
        //We are correct here so we update 
        $profile = $profile->first();
        //Regenerate a new key just in case we ask a new email
        /////$profile->emailValidationKey = Str::random(50);
        $profile->isEmailValidated = 1;
        $profile->save();
        
        return view('emailvalidation')->with('result',1)->with('url',Config::get('constants.SITE_URL'));
    }

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

    /////////////////////////////////////////////////////////////////////////////////////////
    //
    //  deleteProfile:
    //
    //  Invalidates the token and deletes all data of a profile and the profile
    //  You need to be registered to be able to delete it
    //
    ////////////////////////////////////////////////////////////////////////////////////////
    //Delete profile and all associated data
    public function delete(Request $request) { 
        //Check that user doesn't have any product assigned
        if (Profile::find($request->get('myProfile'))->products()->count()>0) {
            return response()->json(["response" => "error", "message"=>"owning_products"],400);
        }
        //Invalidate the token
        JWTAuth::invalidate($request->bearerToken());
        Profile::find($request->get('myProfile'))->delete();
        //Invalidate the token
        return response()->json(null,200); 
    }    





    /////////////////////////////////////////////////////////////////////////////////////////
    //
    //  removeAccount:
    //
    //  Invalidates the token
    //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function removeAccount(Request $request){
        if ($request->bearerToken()=== null) {
            return response()
                ->json([
                    'response' => 'error',
                    'message' => 'token_expected'
                ], 400);
        }
        $token = $request->bearerToken(); 
        JWTAuth::setToken($token) ;
        JWTAuth::parseToken()->authenticate();
        $payload = JWTAuth::parseToken()->getPayload();
        $user = User::find($payload->get('id'));
        $profile = Profile::with('roles')->find($user->profile_id);
        //Check if profile has other users (accounts) if not remove associated data (roles,notifications,messages...)
        if ($profile->users()->get()->count() == 1) {
            $profile->deleteAssociatedData();
        }
        //Delete the user and invalidate the token
        $user->delete();
        JWTAuth::invalidate($request->bearerToken());

        //Return 
        return response()
            ->json(null, 200);           
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    //
    //  restoreAccount:
    //
    //  When a profile has no users(accounts) we still can restore the account
    //  All associated data like roles, notifications... has been removed but profile is there
    //
    //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function restoreAccount(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'mobile' => 'required'
        ]);        
        //Check parameters
        if ($validator->fails()) {
            return response()
                ->json([
                    'response' => 'error',
                    'message' => 'validation_failed'
                ], 400);
        }
        //Verify that there is a profile with such email and phone
        $profiles = Profile::where('email', $request->email)->where('mobile', $request->mobile);
        if ($profiles->count()==0) {
            return response()
                ->json([
                    'response' => 'error',
                    'message' => 'profile_not_found'
                ], 400);
        }
        //Check that the profile has no users
        if ($profiles->first()->users()->count() !== 0) {
            return response()
            ->json([
                'response' => 'error',
                'message' => 'account_exists'
            ], 200);

        }
        //If we got here we can now create the User with minimum access, no roles...
        $profile = $profiles->first();
        $newPass = $this->_generatePassword(10);
        $user = User::create([
            'profile_id' => $profile->id,
            'email' => $profile->email,
            'password' => Hash::make($newPass, ['rounds' => 12]),
            'access' => 'Pré-inscrit'
        ]);
        //Send email with new password
        $data = [
            'name' =>  $profile->firstName,
            'password' => $newPass
        ];   
        Mail::send('emails.restoreAccount',$data, function($message) use ($user)
        {
            $message->from(Config::get('constants.EMAIL_FROM_ADDRESS'), Config::get('constants.EMAIL_FROM_NAME'));
            $message->replyTo(Config::get('constants.EMAIL_NOREPLY'));
            $message->to($user->email);
            $message->subject("GMA500: Récuperation de compte");
        });        

        return response()->json(null, 200);            
    }
}
