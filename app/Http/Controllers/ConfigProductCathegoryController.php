<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ConfigProductCathegory;

class ConfigProductCathegoryController extends Controller
{
    //
    public function index()
    {
        return ConfigProductCathegory::all();
    }

    public function show($id)
    {
        $result = ConfigProductCathegory::find($id);
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
        $result = ConfigProductCathegory::create($request->all());
        return response()->json($product, 201);
    }

    public function update(Request $request, ConfigProductCathegory $result)
    {
        $result->update($request->all());
        return response()->json($result, 200);
    }

    public function delete(ConfigProductCathegory $result)
    {
        $result->delete();
        return response()->json(null, 204);
    }    
}
