<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {

        $review = Review::get();
        return parent::returnData($review, 200);
    }



    public function store(Request $request)
    {

        $data = [
            'user_id' => isset($request->user_id) ? $request->user_id : 0,
            'product_id' => $request->product_id,
            'name' => $request->name,
            'message' => $request->message,
            'rating' => $request->rating,
        ];



        $review = Review::create($data);
        return parent::returnData($review, 200);
    }

    public function show($id)
    {

        $review = Review::where('id', $id)->first();
        return parent::returnData($review, 200);
    }


    public function changeStatus($id)
    {
        $review = Review::where('id', $id)->first();
        if ($review['status'] == "0") {
            $update = [
                'status' => 1
            ];
            Review::where('id', $id)->update($update);
            return response()->json('Review Enable Successfully!', 200);
        } elseif ($review['status'] == "1") {
            $update = [
                'status' => 0
            ];
            Review::where('id', $id)->update($update);
            return response()->json('Review Disabled Successfully!', 200);
        } else {
            return response()->json(['Error'], 500);
        }
    }
}
