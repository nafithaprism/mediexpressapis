<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {

        $SubCategory = SubCategory::get();
        return parent::returnData($SubCategory, 200);
    }



    public function store(Request $request)
    {

        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'route' => $request->route,
        ];

        if (SubCategory::where('id', $request->id)->exists()) {

            #update
            $SubCategory = SubCategory::where('id', $request->id)->update($data);
        } else {

            $SubCategory = SubCategory::create($data);
        }
        return parent::returnData($SubCategory, 200);
    }

    public function show($route)
    {



        $SubCategory = SubCategory::where('route', $route)->first();
        return parent::returnData($SubCategory, 200);
    }


    public function destroy($route)
    {
        $SubCategory = SubCategory::where('route', $route)->delete();
        return parent::returnStatus($SubCategory);
    }
}
