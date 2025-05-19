<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\ProductPriceVariation;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index()
    {

        $deal = Deal::get();
        return parent::returnData($deal, 200);
    }



    public function store(Request $request)
    {

        $data = $request->all();
        $dealData = [
            "country_id" => $data['country_id'],
            "deal_type" => $data['deal_type'],
            "product_id" => $data['product_id'],
            "country_id" => $data['country_id'],
        ];

        $deal = Deal::where('id', $request->id)->first();

        if ($deal) {

            $deal = Deal::where('id', $request->id)->update($dealData);
            foreach ($data['price_variation'] as $value) {
                $create['deal_price'] = $value['deal_price'];
                $price = ProductPriceVariation::where('product_id', $request->product_id)->where('id', $value['variation_id'])->where('country_id', $data['country_id'])->update($create);
            }
        } else {

            $deal = Deal::create($dealData);
            foreach ($data['price_variation'] as $value) {
                $create['deal_price'] = $value['deal_price'];
                $price = ProductPriceVariation::where('product_id', $request->product_id)->where('id', $value['variation_id'])->where('country_id', $data['country_id'])->update($create);
            }
        }
        return parent::returnData($deal, 200);
    }

    public function show($id)
    {

        $deal = Deal::where('id', $id)->first();
        return parent::returnData($deal, 200);
    }


    public function changeStatus($id)
    {

        try {

            $product = Deal::where('id', $id)->first();
            if ($product['status'] == "0") {
                $update = [
                    'status' => 1
                ];
                Deal::where('id', $id)->update($update);
                return response()->json('Deal Activated Successfully!', 200);
            } elseif ($product['status'] == "1") {
                $update = [
                    'status' => 0
                ];
                Deal::where('id', $id)->update($update);

                return response()->json('Deal Disabled Successfully!', 200);
            } else {
                return response()->json(['Error'], 500);
            }
        } catch (\Exception $e) {

            return response()->json(['Product is not added.', 'stack' => $e], 500);
        }
    }
}
