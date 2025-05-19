<?php

namespace App\Http\Controllers;

use App\Models\MostPurchased;
use App\Models\Product;
use App\Models\ProductPriceVariation;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MostPurchasedController extends Controller
{
    public function index()
    {
        $mostPurchased = MostPurchased::select('country_id')->distinct()->pluck('country_id');
        $data = [];
        foreach ($mostPurchased as $countryId) {
            $country = Country::find($countryId);
            $products = MostPurchased::where('country_id', $countryId)
                ->with(['product:id,name'])
                ->select('product_id')
                ->get()
                ->pluck('product')
                ->unique('id')
                ->values(); 
            $data[] = [
                'id' => $country->id,
                'name' => $country->name,
                'product' => $products
            ];
        }
        return parent::returnData( $data, 200);

    }





    public function store(Request $request)
    {
        $data  = $request->all();
        $countryId  = $request['country_id'];
        MostPurchased::where('country_id', $request['country_id'])->delete();
        foreach ($data['product_id'] as $key => $value) {
            $create['country_id'] = $countryId;
            $create['product_id'] = $value;
            $created[$key] = MostPurchased::create($create);
        }
        return parent::returnStatus($created, 200);
    }


    public function show($id)
    {
        $country = Country::find($id);
        if (!$country) {
            return parent::returnData([], 404, 'Country not found');
        }
        $mostPurchased = MostPurchased::where('country_id', $id)->pluck('product_id')->unique();
        $products = [];
        foreach ($mostPurchased as $productId) {
            $product = Product::where('id', $productId)->select('id', 'name')->first();
            if ($product) {
                $products[] = [
                    'id' => $product->id,
                    'name' => $product->name
                ];
            }
        }
        $data = [
            'id' => $country->id,
            'name' => $country->name,
            'product' => $products
        ];
        return parent::returnData($data, 200);
    }








    public function destroy($id)
    {
        $mostPurchased = MostPurchased::where('country_id', $id)->delete();
        return parent::returnStatus($mostPurchased, 200);
    }


    public function mostPurchasedProductsDropDown($id){
       
        
        $Id = ProductPriceVariation::where('country_id' , $id)->pluck('product_id');
        $products = Product::whereIn('id', $Id)->with('price')->select('id' , 'name')->get();
        return parent::returnData($products, 200);


    }
}
