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
        // Changed 'countryName' to 'name'
        $data = [
            'name'                => $request->input('name'),  // CHANGED
            'route'               => $request->input('route'),
            'currency'            => $request->input('currency'),
            'weight_based_shipping' => $request->input('weightBasedShipping'),  // CHANGED to snake_case for database
        ];

        if ($request->filled('id') && Country::where('id', $request->id)->exists()) {
            $country = Country::find($request->id);
            $country->update($data);
            $message = 'Country updated successfully';
            $code = 200;
        } else {
            $country = Country::create($data);
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
            return response()->json(['message' => 'Country not found'], 404);
        }
    }

    public function destroy($id)
    {
        $country = Country::where('id', $id)->delete();

        if ($country) {
            return response()->json(['message' => 'Data Deleted Successfully'], 200);
        } else {
            return response()->json(['message' => 'Something went wrong'], 404);
        }
    }

    public function country($route)
    {
        $country = Country::where('route', $route)->first();
        
        if (!$country) {
            return response()->json(['message' => 'Not found'], 404);
        }
        
        return response()->json($country);
    }
}
