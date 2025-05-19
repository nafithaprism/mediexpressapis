<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {

        $blog = Blog::get();
        return parent::returnData($blog, 200);
    }



    public function store(Request $request)
    {

        $data = [
            'title' => $request->title,
            'tags' => $request->tags,
            'route' => $request->route,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'featured_img' => $request->featured_img,
            'slider_img' => $request->slider_img,
        ];

        if (Blog::where('id', $request->id)->exists()) {

            #update
            $blog = Blog::where('id', $request->id)->update($data);
        } else {

            $blog = Blog::create($data);
        }
        return parent::returnData($blog, 200);
    }

    public function show(Blog $blog)
    {

        $blog = Blog::where('route', $blog['route'])->first();
        return parent::returnData($blog, 200);
    }


    public function destroy(Blog $blog)
    {
        $blog = Blog::where('route', $blog['route'])->delete();
        return parent::returnStatus($blog);
    }
}
