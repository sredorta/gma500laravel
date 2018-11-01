<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Validator;
use App\Group;
use App\Profile;


class GroupController extends Controller
{
    public function getGroups(Request $request) {
        $groups = Group::all();
        return response()->json($groups->toArray(),200); 
    }

    //Adds a group from a profile
    public function add(Request $request) {
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
    public function remove(Request $request) {
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
}
