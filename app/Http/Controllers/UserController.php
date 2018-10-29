<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    //
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


    ///////////////////////////////////////////////////////////////////////////////
    //  NON AUTH RELATED
    //////////////////////////////////////////////////////////////////////////////
    public function getUserIndexes(Request $request){
        $type = $request->type;
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:bureau,board,member,all',
            //'limit' => 'required|min:1|max:1000'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }
        switch ($type) {
            case "bureau":
                $profiles = Profile::whereHas(
                    'roles', function($q){
                        $q->where('roles.id','=', 2);
                    }
                )->orderBy('lastName')->get()->pluck('id')->toArray();  
                break;
            case "board" :
                $profiles = Profile::whereHas(
                    'roles', function($q){
                        $q->where('roles.id','>=', 3);
                    }
                )->orderBy('lastName')->get()->pluck('id')->toArray();
                break;
            case "member" : 
                $profiles = Profile::whereHas(
                    'roles', function($q){
                        $q->where('roles.id','=', 1);
                    }
                )->orderBy('lastName')->get()->pluck('id')->toArray();            
                 /*if ($this->isLogged($request)) {

                } else {
                    $users = [];
                }*/
                break;
            default:
                $profiles = Profile::pluck('id')->toArray();
        }
        return response()->json($profiles,200);
    }   

    //
    public function getUserById(Request $request){
        //TODO: chose which data we provide depending on access...
        $id = $request->id;
        $profile = Profile::with('roles')->find($id);

      /*  if ($this->hasAccess($request,"member")) {
            $user = User::select('id','firstName','lastName','avatar','title','email', 'mobile')->find($id);
            return response()->json($user,200);
        }
        if ($this->hasAccess($request,"admin")) {
            $user = User::find($id);
            return response()->json($user,200);
        }*/
        //$user = User::select('id','firstName','lastName','avatar','title')->find($id);
        return response()->json($profile,200);          

    }   

    public function test(Request $request) {
        $data = [
            'name' => 'Sergi',
            'key' => Config::get('constants.API_URL') . '/api/auth/emailvalidate?id=5&key=roBcswk6qNlR7qoY7el1GI0cCT3oNcR5aapbKyJzojXOySDmV6'
        ];
        Mail::send('emails.validateemail',$data, function($message)
        {
            $message->from(Config::get('constants.EMAIL_FROM_ADDRESS'), Config::get('constants.EMAIL_FROM_NAME'));
            $message->replyTo(Config::get('constants.EMAIL_NOREPLY'));
            $message->to('sergi.redorta@hotmail.com');
            $message->subject("GMA500: Confirmation de votre adresse électronique");
        });   

    }


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
