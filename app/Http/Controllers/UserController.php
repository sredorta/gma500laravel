<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;

class UserController extends Controller
{
    //
    public function index()
    {
        return User::all();
    }

    //Returns all users with Membre role
    public function getUsersByRole($role) {
        if ($role !== 'Board') {
            return User::whereHas('roles', function($q) use($role) {
                $q->where('role', '=', $role);
            })->get();
        } else {
            return User::whereHas('roles', function($q) {
                $q->where('role', '<>', 'Bureau');
            })->get();      
        }
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
/*
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
*/
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
