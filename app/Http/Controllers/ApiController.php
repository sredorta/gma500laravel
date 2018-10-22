<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Validator;
use JWTAuth;
use App\User;
use App\Config\constants;
use Illuminate\Support\Facades\Mail;
use Config;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->user = new User;
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'required|unique:users'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'response' => 'error',
                'message' => 'user_already_registered'
            ],400);
        }       
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'firstName' => 'required',
            'lastName' => 'required',
            'mobile' => 'required|unique:users',
            'password'=> 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }
        $emailkey = Str::random(50);
        $user = User::create([
            'firstName' => $request->get('firstName'),           
            'lastName' => $request->get('lastName'),
            'mobile' => $request->get('mobile'),
            'email' => $request->get('email'),
            'password' =>   Hash::make($request->get('password'), ['rounds' => 12]),
            'emailValidationKey' => $emailkey,
            'avatar' => 'url(' . $request->get('avatar') . ')'
        ]);

        //Send email with validation key
        $data = [
            'name' =>  $user->firstName,
            'key' => Config::get('constants.API_URL') . '/api/auth/emailvalidate?id=' . 
                    $user->id  .
                    '&key=' .
                    $user->emailValidationKey
        ];

        Mail::send('emails.validateemail',$data, function($message) use ($user)
        {
            $message->from(Config::get('constants.EMAIL_FROM_ADDRESS'), Config::get('constants.EMAIL_FROM_NAME'));
            $message->replyTo(Config::get('constants.EMAIL_NOREPLY'));
            $message->to($user->email);
            $message->subject("GMA500: Confirmation de votre adresse électronique");
        });   



        //Add user notification

        //Return the token
        $object = (object) ['email' => $user->email, 'id' => $user->id];
        return response()->json($object, 200);
    }
    
    
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        $token = null;

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        //Define token lifeTime
        if ($request->keepconnected) {
            $tokenLife = 120;  //Should be 1week 
        } else {
            $tokenLife = 1;   //Should be 60
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

        try {
            if (!$token = JWTAuth::attempt($credentials,['exp' => Carbon::now()->addMinutes($tokenLife)->timestamp])) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'invalid_email_or_password',
                ],401);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'failed_to_create_token',
            ],401);
        }

        //Return the token
        $object = (object) ['token' => $token];
        return response()->json($object,200);
    }

    //It gets the token from the header and returns the user of the token or null
    public function getAuthUser(Request $request){    
        if ($request->bearerToken()=== null) {
            return response()->json(null,200);
        }
        JWTAuth::setToken($request->bearerToken()) ;
        $user = JWTAuth::toUser();
        return response()->json($user,200);    
    }

    public function logout(Request $request){
        if ($request->bearerToken()=== null) {
            return response()->json(null,200);
        }    
        JWTAuth::invalidate($request->bearerToken());
        return response()->json(null,200);
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
    public function emailValidate(REquest $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'key' => 'required'
        ]);        
        if ($validator->fails()) {
            return view('emailvalidation')->with('result',0);
        }        
        //Check that we have user with the requested id
        $user = User::where('id', '=', $request->get('id'))->where('emailValidationKey','=',$request->get('key'));
        if (!$user->count()) {
            return view('emailvalidation')->with('result',0);
        }
        //We are correct here so we update 
        $user = $user->first();
        //Regenerate a new key just in case we ask a new email
        /////$user->emailValidationKey = Str::random(50);
        $user->isEmailValidated = 1;
        $user->save();
        
        return view('emailvalidation')->with('result',1)->with('url',Config::get('constants.SITE_URL'));
    }


}
