<?php

namespace App\Http\Controllers;

use App\Models\ChildCategory;
use Illuminate\Http\Request;

class ChildCategoryController extends Controller
{
    public function index()
    {

        $ChildCategory = ChildCategory::get();
        return parent::returnData($ChildCategory, 200);
    }



    public function store(Request $request)
    {

        $data = [
            'sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'route' => $request->route,
        ];

        if (ChildCategory::where('id', $request->id)->exists()) {

            #update
            $ChildCategory = ChildCategory::where('id', $request->id)->update($data);
        } else {

            $ChildCategory = ChildCategory::create($data);
        }
        return parent::returnData($ChildCategory, 200);
    }

    public function show($route)
    {



        $ChildCategory = ChildCategory::where('route', $route)->first();
        return parent::returnData($ChildCategory, 200);
    }


    public function destroy($route)
    {
        $ChildCategory = ChildCategory::where('route', $route)->delete();
        return parent::returnStatus($ChildCategory);
    }
}
