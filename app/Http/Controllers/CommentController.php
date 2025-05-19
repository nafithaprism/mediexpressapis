<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {

        $comment = Comment::all();
        return parent::returnData($comment, 200);
    }



    public function store(Request $request)
    {

        $data = [
            'blog_id' => $request->blog_id,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'comment' => $request->comment,
        ];



        $comment = Comment::create($data);

        return parent::returnData($comment, 200);
    }

    public function show($id)
    {

        $comment = Comment::where('id', $id)->first();
        return parent::returnData($comment, 200);
    }


    public function destroy($id)
    {
        $comment = Comment::where('id', $id)->delete();
        return parent::returnStatus($comment);
    }


    public function changeStatus($id)
    {

        $comment = Comment::where('id', $id)->first();
        if ($comment['status'] == 1) {
            $update =  Comment::where('id', $id)->update(['status' => 0]);
        } elseif ($comment['status'] == 0) {

            $update =  Comment::where('id', $id)->update(['status' => 1]);
        } else {
            return response()->json('No Data Found', 401);
        }
        return parent::returnStatus($comment);
    }
}
