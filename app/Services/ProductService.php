<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductChildCategory;
use App\Models\ProductPriceVariation;;

use DB;

class ProductService
{


    static function addProduct($data)
    {

        $product = Product::create([
            "name" => $data['name'],
            "product_type" => isset($data['product_type']) ? $data['product_type'] : "",
            "route" => $data['route'],
            "featured_img" => $data['featured_img'],
            "sku" => $data['sku'],
            "brand_id" => isset($data['brand_id']) ? $data['brand_id'] : 0,
            "short_description" => $data['short_description'],
            "description" => isset($data['description']) ? $data['description'] : 0,
            "additional_information" => isset($data['additional_information']) ? $data['additional_information'] : 0,
            "slider_img" => isset($data['slider_img']) ? $data['slider_img'] : 0,
            "tags" => isset($data['tags']) ? $data['tags'] : 0,
            "stock" => isset($data['stock']) ? $data['stock'] : 1,
        ]);

        foreach ($data['categories'] as $key => $value) {
            $create = [
                'product_id' => $product['id'],
                'category_id' => $value,
            ];
            $create[$key] = ProductCategory::create($create);
        }
        if (!empty($data['sub_categories'])) {
            foreach ($data['sub_categories'] as $key => $value) {
                $create = [
                    'product_id' => $product['id'],
                    'sub_category_id' => $value,
                ];
                $create[$key] = ProductSubCategory::create($create);
            }
        }
        if (!empty($data['child_categories'])) {
            foreach ($data['child_categories'] as $key => $value) {
                $create = [
                    'product_id' => $product['id'],
                    'child_category_id' => $value,
                ];
                $create[$key] = ProductChildCategory::create($create);
            }
        }

        return $product;
    }

    static function updateProduct($data)
    {

        $product = Product::where('id', $data['id'])->update([
            "name" => $data['name'],
            "product_type" => isset($data['product_type']) ? $data['product_type'] : "",
            "featured_img" => $data['featured_img'],
            "sku" => $data['sku'],
            "short_description" => $data['short_description'],
            "brand_id" => isset($data['brand_id']) ? $data['brand_id'] : 0,
            "description" => isset($data['description']) ? $data['description'] : 0,
            "additional_information" => isset($data['additional_information']) ? $data['additional_information'] : 0,
            "slider_img" => isset($data['slider_img']) ? $data['slider_img'] : 0,
            "tags" => isset($data['tags']) ? $data['tags'] : 0,
            "stock" => isset($data['stock']) ? $data['stock'] : 1,
        ]);
        ProductCategory::where('product_id', $data['id'])->delete();
        ProductSubCategory::where('product_id', $data['id'])->delete();
        ProductChildCategory::where('product_id', $data['id'])->delete();
        foreach ($data['categories'] as $key => $value) {
            $create = [
                'product_id' => $data['id'],
                'category_id' => $value,
            ];
            $create[$key] = ProductCategory::create($create);
        }
        if (!empty($data['sub_categories'])) {
            foreach ($data['sub_categories'] as $key => $value) {
                $create = [
                    'product_id' => $data['id'],
                    'sub_category_id' => $value,
                ];
                $create[$key] = ProductSubCategory::create($create);
            }
        }
        if (!empty($data['child_categories'])) {
            foreach ($data['child_categories'] as $key => $value) {
                $create = [
                    'product_id' => $data['id'],
                    'child_category_id' => $value,
                ];
                $create[$key] = ProductChildCategory::create($create);
            }
        }

        return $product;

        return $product;
    }

    static function addVariation($data)
    {
        foreach ($data['price_variation'] as $variation) {
            $variation['product_id'] =   $data['product_id'];
            $variation['country_id'] =   $data['country_id'];
            $variation['pack_of'] =  isset($variation['pack_of']) ? $variation['pack_of'] : '';
            $variation['actual_price'] =  isset($variation['actual_price']) ? $variation['actual_price'] : '';
            $variation['sale_price'] =  isset($variation['sale_price']) ? $variation['sale_price'] : null;
            $variation['weight'] =  isset($variation['weight']) ? $variation['weight'] : null;

            ProductPriceVariation::create($variation);
        }
        return $variation;
    }

    static function updateVariation($data)
    {

        $variation['product_id'] =   $data['product_id'];
        $variation['country_id'] =   $data['country_id'];
        $variation['pack_of'] =  isset($data['pack_of']) ? $data['pack_of'] : '';
        $variation['actual_price'] =  isset($data['actual_price']) ? $data['actual_price'] : '';
        $variation['sale_price'] =  isset($data['sale_price']) ? $data['sale_price'] : null;
        $variation['weight'] = isset($data['weight']) ? $data['weight'] : null;

        ProductPriceVariation::where('id', $data['id'])->update($variation);
        return $variation;
    }

    static function cloneVariation($data)
    {
        $priceVariation = ProductPriceVariation::where('product_id', $data['product_id'])->where('country_id', $data['from_country_id'])->get();
        $to_country_id = $data['to_country_id'];
        if (!empty($priceVariation)) {
            foreach ($priceVariation as $variation) {
                $data = [
                    'product_id' => $data['product_id'],
                    'country_id' => $to_country_id,
                    'pack_of' => isset($variation['pack_of']) ? $variation['pack_of'] : '',
                    'actual_price' => isset($variation['actual_price']) ? $variation['actual_price'] : '',
                    'sale_price' => isset($variation['sale_price']) ? $variation['sale_price'] : '',
                    'weight' => isset($variation['weight']) ? $variation['weight'] : '',
                ];
                ProductPriceVariation::create($data);
            }
        }
        return $data;
    }
}
