<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $country = Country::get();
        return response()->json($country);
    }




    public function store(Request $request)
    {
      

$data = [
        'countryName'         => $request->input('countryName'),
        'route'               => $request->input('route'),
        'currency'            => $request->input('currency'),
        'weightBasedShipping' => $request->input('weightBasedShipping'),
    ];

    if ($request->filled('id') && \App\Models\Country::where('id', $request->id)->exists()) {
        $country = \App\Models\Country::find($request->id);
        $country->update($data);
        $message = 'Country updated successfully';
        $code = 200;
    } else {
        $country = \App\Models\Country::create($data);
        $message = 'Country created successfully';
        $code = 201;
    }

    return response()->json(['message' => $message, 'data' => $country], $code);




    }


    public function show($id)
    {
        $country = Country::where('id', $id)->first();

        if ($country) {
            return response()->json($country, 200);
        } else {
            return response()->json('Something went wrong');
        }
    }



    public function destroy($id)
    {
        $country = Country::where('id', $id)->delete();

        if ($country) {
            return response()->json('Data Deleted Successfully');
        } else {
            return response()->json('Something went wrong');
        }
    }

   

     public function country($route)
{
    $country = \App\Models\Country::where('route', $route)->first();
    if (!$country) {
        return response()->json(['message' => 'Not found'], 404);
    }
    return response()->json($country);
}

    
}
