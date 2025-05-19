<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        $todo = Todo::get();
        return response()->json($todo);
    }




    public function store(Request $request)
    {
        $create = [
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ];

        if (Todo::where('id', $request->id)->exists()) {

            $todo = Todo::where('id', $request->id)->update($create);
        } else {

            $todo = Todo::create($create);
        }
        if ($todo) {
            return response()->json('Todo Created Successfully');
        } else {
            return response()->json('Something went wrong');
        }
    }


    public function show($id)
    {
        $todo = Todo::where('id', $id)->first();

        if ($todo) {
            return response()->json($todo, 200);
        } else {
            return response()->json('Something went wrong');
        }
    }



    public function destroy($id)
    {
        $todo = Todo::where('id', $id)->delete();

        if ($todo) {
            return response()->json('Data Deleted Successfully');
        } else {
            return response()->json('Something went wrong');
        }
    }
}
