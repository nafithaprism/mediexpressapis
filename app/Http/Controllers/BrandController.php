<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {

        $brand = Brand::get();
        return parent::returnData($brand, 200);
    }



    public function store(Request $request)
    {

        $data = [
            'name' => $request->name,
            'route' => $request->route,
            'featured_img' => $request->featured_img,
            'top' => isset($request->top) ? $request->top : 0,

        ];

        if (Brand::where('id', $request->id)->exists()) {

            #create
            $brand = Brand::where('id', $request->id)->update($data);
            return parent::returnData($brand, 200);
        } else {

            if (Brand::where('route', $request->route)->exists()) {
                return response()->json(['error' => 'Route Already Exist']);
            } else {

                #create
                $brand = Brand::create($data);
                return parent::returnData($brand, 200);
            }
        }
    }

    public function show(Brand $brand)
    {
        $brand = Brand::where('route', $brand['route'])->first();
        return parent::returnData($brand, 200);
    }


    public function destroy(Brand $brand)
    {
        $brand = Brand::where('route', $brand['route'])->delete();
        return parent::returnStatus($brand);
    }
}
