<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Models\Product;
use App\Models\ProductPriceVariation;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index()
    {
        $product = Product::with('price', 'brand', 'category', 'subCategory', 'childCategory')->get();
        return parent::returnData($product, 200);
    }

    public function store(Request $request)
    {

        try {

            if (Product::where('id', $request->id)->exists()) {

                #create
                $product = ProductService::updateProduct($request->all());
                return parent::returnStatus($product, 200);
            } else {

                if (Product::where('route', $request->route)->exists()) {
                    return response()->json(['error' => 'Route Already Exist']);
                } else {

                    #create
                    $product = ProductService::addProduct($request->all());
                    return parent::returnData($product, 200);
                }
            }
        } catch (\Error $exception) {
            return response()->json(['ex_message' => $exception->getMessage(), 'line' => $exception->getLine()], 400);
        }
    }

    public function show(Product $product)
    {

        $product = Product::where('route', $product['route'])->with('price', 'brand', 'category', 'subCategory', 'childCategory')->first();
        return parent::returnData($product, 200);
    }

    public function destroy(Product $product)
    {
        $product = Product::where('route', $product['route'])->delete();
        return parent::returnStatus($product);
    }

    public function changeStatus($id)
    {

        try {

            $product = Product::where('id', $id)->first();
            if ($product['status'] == "0") {
                $update = [
                    'status' => 1
                ];
                Product::where('id', $id)->update($update);
                return response()->json('Product Activated Successfully!', 200);
            } elseif ($product['status'] == "1") {
                $update = [
                    'status' => 0
                ];
                Product::where('id', $id)->update($update);
                return response()->json('Product Disabled Successfully!', 200);
            } else {
                return response()->json(['Error'], 500);
            }
        } catch (\Exception $e) {

            return response()->json(['Product is not added.', 'stack' => $e], 500);
        }
    }

    public function disableProductList()
    {
        $product = Product::where('status', 0)->get();
        return parent::returnData($product, 200);
    }

    public function addVariation(Request $request)
    {
        try {
            #create
            $product = ProductService::addVariation($request->all());
            return parent::returnData($product, 200);
        } catch (\Error $exception) {
            return response()->json(['ex_message' => $exception->getMessage(), 'line' => $exception->getLine()], 400);
        }
    }
    public function updateVariation(Request $request)
    {
        try {

            #create
            $product = ProductService::updateVariation($request->all());
            return parent::returnData($product, 200);
        } catch (\Error $exception) {
            return response()->json(['ex_message' => $exception->getMessage(), 'line' => $exception->getLine()], 400);
        }
    }

    public function cloneVariation(Request $request)
    {
        try {
            #create
            $product = ProductService::cloneVariation($request);
            return parent::returnData($product, 200);
        } catch (\Error $exception) {
            return response()->json(['ex_message' => $exception->getMessage(), 'line' => $exception->getLine()], 400);
        }
    }

    public function allVariation($id)
    {
        $product = ProductPriceVariation::where('product_id', $id)->with('country')->get();
        return parent::returnData($product, 200);
    }

    public function singleVariation($id)
    {
        $product = ProductPriceVariation::where('id', $id)->first();
        return parent::returnData($product, 200);
    }

    public function deleteVariation($id)
    {
        $product = ProductPriceVariation::where('id', $id)->delete();
        return parent::returnStatus($product, 200);
    }


    public function list($id){

        $id = ProductPriceVariation::where('country_id', $id)->pluck('product_id');
        $products = Product::whereIn('id',$id)->select('id','name','route' ,'featured_img' ,'description')->get();
        return parent::returnData($products, 200);

    }
}
