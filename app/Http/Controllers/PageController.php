<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;


class PageController extends Controller
{

    public function index()
    {

        $page = Page::all();
        return parent::returnData($page, 200);
    }



    public function store(Request $request)
    {

        $data = [
            'name' => $request->name,
            'content' => $request->content,
            'route' => $request->route,
            'identifier' => $request->identifier
        ];

        if (Page::where('id', $request->id)->exists()) {

            #update

            $page = Page::where('id', $request->id)->update($data);
        } else {

            $page = Page::create($data);
        }
        return parent::returnData($page, 200);
    }

    public function show(Page $page)
    {

        $page = Page::where('route', $page['route'])->first();
        return parent::returnData($page, 200);
    }


    public function destroy(Page $page)
    {
        $page = Page::where('route', $page['route'])->delete();
        return parent::returnStatus($page);
    }
}
