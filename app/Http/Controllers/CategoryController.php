<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {

        $category = Category::get();
        return parent::returnData($category, 200);
    }



    public function store(Request $request)
    {

        $data = [
            'name' => $request->name,
            'route' => $request->route,
        ];

        if (Category::where('id', $request->id)->exists()) {

            #update
            $category = Category::where('id', $request->id)->update($data);
        } else {

            $category = Category::create($data);
        }
        return parent::returnData($category, 200);
    }

    public function show(Category $category)
    {

        $category = Category::where('route', $category['route'])->first();
        return parent::returnData($category, 200);
    }


    public function destroy(Category $category)
    {
        $category = Category::where('route', $category['route'])->delete();
        return parent::returnStatus($category);
    }

}
