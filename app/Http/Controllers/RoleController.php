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
    public function add(Request $request) {
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
    public function remove(Request $request) {
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
}
