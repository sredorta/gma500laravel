<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Validator;
use App\Profile;

class ProfileController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////
    //  getProfileIndexesByRole
    //  
    //  Returns array of indexes of profiles that have the required role
    //////////////////////////////////////////////////////////////////////////////
    public function getProfileIndexesByRole(Request $request){
        $type = $request->type;
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:bureau,board,member,all'
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
                break;
            default:
                $profiles = Profile::pluck('id')->toArray();
        }
        return response()->json($profiles,200);
    }   


    public function getProfileById(Request $request){
        //TODO: chose which data we provide depending on access...
        $id = $request->id;
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }  
        $profile = Profile::filterGet($request)->with('roles')->find($id);      

        return response()->json($profile,200);          
    }   

    //Returns all members
    public function getAllMembers(Request $request){
        $profiles = Profile::filterGet($request)->whereHas(
            'roles', function($q){
                $q->where('roles.id','=', 1);
            }
        )->limit(10000)->orderBy('lastName')->get(['id'])->toArray();  
        return response()->json($profiles,200); 
    }


    //Finds profile by first or last name
    //TODO
    public function findProfileByName(Request $request){
        //TODO: chose which data we provide depending on access...
        $name = $request->name;
        $validator = Validator::make($request->all(), [
            'required' => 'string|min:3|max:50',
        ]);

        return response()->json($name,200); 
    }


    public function adminGetUsers(Request $request) {
        $profiles = Profile::with('roles')->with('users')->orderBy('lastName')->get();
        $profiles->each(function($profile) {
                $profile->accounts = $profile->users;
                unset($profile->users);
        });
        return response()->json($profiles->toArray(),200); 
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