<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Validator;

class CouponController extends Controller
{

    public function index()
    {
        $coupon = Coupon::get();
        return parent::returnData($coupon);
    }




    public function store(Request $request)
    {

        $input  = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:coupons'
        ]);
        if (!$validator->fails()) {

            $create = [
                'name' => $request->name,
                'type' => $request->type,
                'value' => $request->value,
                'value_type' => $request->value_type,
                'start_date' => isset($request->start_date) ? $request->start_date : '',
                'end_date' => isset($request->end_date) ? $request->end_date : ''
            ];

            if (Coupon::where('id', $request->id)->exists()) {

                $coupon = Coupon::where('id', $request->id)->update($create);
            } else {

                $coupon = Coupon::create($create);
            }

            if ($coupon) {

                return parent::returnData($coupon);
            } else {

                return parent::returnStatus($coupon);
            }
        } else {

            return response()->json(['errors' => $validator->errors(), 'status' => 404]);
        }
    }


    public function show($id)
    {
        $coupon = Coupon::where('id', $id)->first();

        if ($coupon) {
            return parent::returnData($coupon);
        } else {
            return parent::returnStatus($coupon);
        }
    }



    public function destroy($id)
    {
        $coupon = Coupon::where('id', $id)->delete();

        if ($coupon) {
            return response()->json('Data Deleted Successfully');
        } else {
            return response()->json('Something went wrong');
        }
    }
}
