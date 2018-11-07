<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use App\Product;
use App\Profile;
use Validator;

class ProductController extends Controller
{
    //
    public function index(Request $request)
    {
        $result = Product::filterGet($request)->get();
        return response()->json($result, 200);
    }


    public function getById(Request $request)
    {
        //TODO:filterGET
        $id = $request->get('id');
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }  
        $product = Product::find($id);
        $product->assignedTo = $product->profile()->first();      
        return response()->json($product,200);   
    }
    //Delete product
    public function delete(Request $request)
    {
        $id = $request->get('product_id');
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(["response"=>"error", "message"=>"invalid_params"],400);
        }          
        Product::find($id)->delete();
        return response()->json(null, 204);
    }


    //Attach product to user
    public function attachProductToProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric',
            'profile_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json(["response"=>"error", "message"=>"invalid_params"],400);
        }       
        $profile = Profile::find($request->get('profile_id'));
        $product = Product::find($request->get('product_id'));
        $product->profile()->associate($profile);
        $product->save();
        return response()->json(null, 204);
    }    

    //Dettach product from user
    public function detachProductFromProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json(["response"=>"error", "message"=>"invalid_params"],400);
        }       
        $product = Product::find($request->get('product_id'));
        $product->profile_id = null;
        $product->save();
        return response()->json(null, 204);
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