<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Validator;
use App\Group;
use App\Profile;


class GroupController extends Controller
{
    //Get all groups
    public function getGroups(Request $request) {
        $groups = Group::all();
        return response()->json($groups->toArray(),200); 
    }

    //Adds a group from a profile
    public function attachProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|numeric',
            'group_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }          
        $profile = Profile::find($request->profile_id);
        $profile->groups()->attach($request->group_id);
        return response()->json(null,204); 
    }

    //Removes a group from a profile
    public function detachProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|numeric',
            'group_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }          
        $profile = Profile::find($request->profile_id);
        $profile->groups()->detach($request->group_id);
        return response()->json(null,204); 
    }       


    //create a new Role
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'string|min:3|max:50',
            'description' => 'required|min:10|max:500'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }          
        $role = Group::create([
            'name' => $request->name,
            'description' => $request->description
        ]);     
        return response()->json($role,200); 
    }    
     //delete a Role
     public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }          
        //When a role is removed all attached roles in the pivot are removed !
        Group::find($request->id)->delete();
        return response()->json(null,203); 
    }           


}
