<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Validator;
use App\Role;

class RoleController extends Controller
{
    public function getRoles(Request $request) {
        $roles = Role::all();
        return response()->json($roles->toArray(),200); 
    }
}
