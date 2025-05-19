<?php

namespace App\Services;

use App\Models\Address;
use DB;

class AddressService
{

    static function addAddress($data)
    {

        $user =  $data['user_id'];
        $exist = Address::where('user_id', $user)->first();

        if ($exist != null) {

            if (!empty($data['id'])) {

                #update
                $update = [
                    'user_id' => $data['user_id'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'state' => $data['state'],
                    'country' => $data['country'],
                    'city' => $data['city'],
                    'street_address' => $data['street_address'],
                    'address_type' => $data['address_type'],
                    'postal_code' => $data['postal_code'],
                ];
                $address = Address::where('id', $data['id'])->update($update);
            } else {

                #create
                $address = Address::create($data);
            }
        } else {

            $data['default'] = 1;
            $address = Address::create($data);
        }

        if ($address) {
            return response()->json('Data has been saved.', 200);
        }
    }

    static function setDefaultAddress($data, $id)
    {


        $setDefault = [
            'default' => 1
        ];

        $unsetPreviousDefaultValue = [
            'default' => 0
        ];
        $previousDafaultAddress = Address::where('user_id', $data['user_id'])->where('default', '=', 1)->first();

        if (($previousDafaultAddress == null)) {

            $updatePrevious = Address::where('id', $id)->update($setDefault);
        } else {
            $updatePrevious = $previousDafaultAddress->update($unsetPreviousDefaultValue);
            $updatePrevious = Address::where('id', $id)->update($setDefault);
        }

        $address = Address::where('id', $id)->update($setDefault);
        if ($address) {
            return  response()->json('Data has been Updated.', 200);
        }
    }
}
