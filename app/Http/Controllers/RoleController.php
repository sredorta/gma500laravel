<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Validator;
use App\Role;
use App\Profile;

class RoleController extends Controller
{
    //Get all roles available
    public function getRoles(Request $request) {
        $roles = Role::all();
        return response()->json($roles->toArray(),200); 
    }

    //Adds a role from a profile
    public function attachProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|numeric',
            'role_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }          
        $profile = Profile::find($request->profile_id);
        $profile->roles()->attach($request->role_id);
        return response()->json(null,204); 
    }

    //Removes a role from a profile
    public function detachProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|numeric',
            'role_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }          
        $profile = Profile::find($request->profile_id);
        $profile->roles()->detach($request->role_id);
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
        $role = Role::create([
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
        Role::find($request->id)->delete();
        return response()->json(null,203); 
    }       
}
