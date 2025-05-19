<?php

namespace App\Http\Controllers;

use App\Models\UsedCoupon;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Validator;

class UsedCouponController extends Controller
{

    public function availableCoupon(Request $request)
    {

        $user  = $request['user_id'];
        $code = $request['coupon_id'];
        $couponCode = Coupon::where('name', $code)->first();
        // if ($couponCode['type']  == 'birthday' || $couponCode['type'] == 'anniversary' || $couponCode['type'] == 'first') {
        //     $usedCoupon = UsedCoupon::where('user_id', $user)->where('coupon_id', $couponCode['id'])->first();
        //     if ($usedCoupon == null) {
        //         return response()->json(['status' => 200, 'coupon_value' => $couponCode['value'], 'message' => 'Coupon Code Applied Successfully']);
        //     } else {
        //         return response()->json(['status' => 400, 'message' => 'You already Use This Code']);
        //     }
        // } else {
            if ($couponCode) {
                if ($couponCode->end_date < date('Y-m-d')) {
                    return response()->json(['status' => 400, 'message' => 'Coupon Code Expired']);
                } else {
                    $usedCoupon = UsedCoupon::where('user_id', $user)->where('coupon_id', $couponCode['id'])->first();
                    if ($usedCoupon == null) {
                        return response()->json(['status' => 200, 'coupon_value' => $couponCode['value'], 'message' => 'Coupon Code Applied Successfully']);
                    } else {
                        return response()->json(['status' => 400, 'message' => 'You already Use This Code']);
                    }
                }
            } else {
                return response()->json(['status' => 400, 'message' => 'Coupon Code Doesnot Exist']);
            }
        //}
    }
}
