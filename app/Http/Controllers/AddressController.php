<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    public function index($id)
    {



        $address = Address::where('user_id', $id)->get();
        if ($address->isEmpty()) {
            return response()->json([], 200);
        }
        return response()->json($address, 200);
    }

    public function store(Request $request)
    {

        $address = AddressService::addAddress($request->all());

        if ($address) {

            return  response()->json('Data has been saved.', 200);
        }
    }


    public function show($id)
    {

        $address = Address::where('id', $id)->first();
        return response()->json($address, 200);
    }


    public function destroy(Address $address)
    {

        $address = Address::where('id', $address['id'])->delete();
        if ($address) {
            return response()->json('Address has been deleted.', 200);
        }
    }


    public function setDefault(Request $request, $id)
    {



        $data = $request->all();
        $address = AddressService::setDefaultAddress($data, $id);

        if ($address) {

            return  response()->json('Address Updated Successfully.', 200);
        }
    }
}
