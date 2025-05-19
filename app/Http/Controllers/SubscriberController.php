<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscriber;


class SubscriberController extends Controller
{



    public function store(Request $request)
    {
        $data = $request->all();

        $subscriber = Subscriber::insert($data);
        return parent::returnStatus($subscriber, 200);
    }
}
