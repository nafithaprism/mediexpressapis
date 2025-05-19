<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{

    public function index()
    {
        $faq = Faq::all();
        return parent::returnData($faq, 200);
    }


    public function store(Request $request)
    {
        $data = [
            'page' => isset($request->page) ? $request->page : null,
            'category_id' => isset($request->category_id) ? $request->category_id : null,
            'question' => $request->question,
            'answer' => $request->answer,
        ];
        if (Faq::where('id', $request->id)->exists()) {

            $create = Faq::where('id', $request->id)->update($data);
        } else {
            $create = Faq::create($data);
        }
        return parent::returnData($create, 200);
    }

    public function show($id)
    {
        $create = Faq::where('id', $id)->first();
        return parent::returnData($create, 200);
    }




    public function destroy($id)
    {
        $create = Faq::where('id', $id)->delete();
        return parent::returnStatus($create, 200);
    }
}
