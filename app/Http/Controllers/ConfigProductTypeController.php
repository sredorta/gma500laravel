<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ConfigProductType;

class ConfigProductTypeController extends Controller
{
    //
    public function index()
    {
        return ConfigProductType::all();
    }

    public function show($id)
    {
        $result = ConfigProductType::find($id);
        if ($result) {
            return response()->json($result, 200);
        } else {
            //This needs to be commented and return null,204
            $object = (object) ['test' => 'no data'];
            return response()->json($object, 204);
        }
    }

    public function store(Request $request)
    {
        $result = ConfigProductType::create($request->all());
        return response()->json($result, 201);
    }

    public function update(Request $request, ConfigProductType $result)
    {
        $result->update($request->all());
        return response()->json($result, 200);
    }

    public function delete(ConfigProductCathgory $result)
    {
        $result->delete();
        return response()->json(null, 204);
    }    
}
