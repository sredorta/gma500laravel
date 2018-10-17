<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
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
        ]);
        $user = User::first();
        $token = JWTAuth::fromUser($user);
        //Save the token
        $user->api_token = $token;
        $user->save();

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
            if (!$token = JWTAuth::attempt($credentials)) {
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

    public function getAuthUser(Request $request){        
        $user = JWTAuth::toUser($request->bearerToken());        
        return response()->json($user,200);
    }

}
