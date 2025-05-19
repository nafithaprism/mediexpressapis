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
        $create = [
            'name' => $request->name,
            'route' => $request->route,
            'standard_shipping_charges' => $request->standard_shipping_charges,
            'express_shipping_charges' => $request->express_shipping_charges,
            'currency' => $request->currency,
        ];

        if (Country::where('id', $request->id)->exists()) {

            $country = Country::where('id', $request->id)->update($create);
        } else {

            $country = Country::create($create);
        }
        if ($country) {
            return response()->json('Country Created Successfully');
        } else {
            return response()->json('Something went wrong');
        }
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

    public function country($route){

        $country = Country::where('route',$route)->select('id','name','route','standard_shipping_charges','express_shipping_charges')->first();
        if ($country) {
            return response()->json($country);
        } else {
            return response()->json('Something went wrong');
        }

    }
}
