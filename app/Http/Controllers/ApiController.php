<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use JWTAuth;
use App\User;
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
            'firstName' => 'required',
            'lastName' => 'required',
            'mobile' => 'required|unique:users',
            'password'=> 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }
        User::create([
            'firstName' => $request->get('firstName'),           
            'lastName' => $request->get('lastName'),
            'mobile' => $request->get('mobile'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'avatar' => 'url('+ $request->get('avatar')+')'
        ]);
        $user = User::first();
        $token = JWTAuth::fromUser($user);
        //Save the token
        //$user->api_token = $token;
        //$user->save();

        //Send email with validation key
        //Add user notification

        //Return the token
        $object = (object) ['token' => $token];
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
                    'code' => 1,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 400);
        }

        try {
            if (!$token = JWTAuth::attempt($credentials,['exp' => Carbon::now()->addMinutes($tokenLife)->timestamp])) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'invalid_email_or_password',
                ]);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'failed_to_create_token',
            ]);
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


}
