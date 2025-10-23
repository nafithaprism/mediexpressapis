<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductChildCategory;
use App\Models\ChildCategory;
use App\Models\SubCategory;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Country;
use App\Models\Brand;
use App\Models\Deal;

class FrontProductController extends Controller
{
 

    public  function country($data) {
        $countryId = Country::where('name', $data['country'])->first();
        
        if($countryId){
           return  $countryId['id'] = '13';

        }else{
           return  $countryId['id'] = $countryId['id'];

        }
    }

    public function allCategory()
    {

        $Category = Category::select('id', 'name', 'route')->with('subCategory.childCategory')->get();
        return parent::returnData($Category, 200);
    }

    public function allBrand()
    {
        $brand = Brand::select('id', 'name', 'route')->get();
        return parent::returnData($brand, 200);
    }


    public function brandFilter(Request $request)
    {
        $countryId = Country::where('name', $request['country'])->first();
        $bRoute  = Brand::where('route', $request->brand)->first();

        if ($request->category) {
            $cId  = Category::where('route', $request->category)->first();
            $productCategory = ProductCategory::where('category_id', $cId['id'])->get();
            $product =  $this->brandFilterData($productCategory, $countryId, $bRoute);
            return $product;
        } elseif ($request->sub_category) {
            $cId  = SubCategory::where('route', $request->sub_category)->first();

            $productCategory = ProductSubCategory::where('sub_category_id', $cId['id'])->get();
            $product =  $this->brandFilterData($productCategory, $countryId, $bRoute);
            return $product;
        } elseif ($request->child_category) {

            $cId  = ChildCategory::where('route', $request->child_category)->first();
            $productCategory = ProductChildCategory::where('child_category_id', $cId['id'])->get();

            $product =  $this->brandFilterData($productCategory, $countryId, $bRoute);
            return $product;
        } else {
            return response()->json(['error' => 'No Product found', 'code' => 404]);
        }
    }

    public function brandFilterData($productCategory, $countryId, $bRoute)
    {
        $country = Country::where('name', $countryId)->first();

        if ($country) {
            $countryId = $country->id;
        } else {
            // Handle case when country is not found, maybe set default country ID
            $countryId = '13'; // Assuming default country ID
        }
        if ($productCategory == null) {

            return response()->json(['status' => 'No Product found', 404]);
        } else {
            $product = [];
            foreach ($productCategory as $key => $value) {

                $product['product'][$key] = Product::where('id', $value['product_id'])->where('brand_id', $bRoute['id'])->with('price', function ($country) use ($countryId) {
                    $country->where('country_id', '=', $countryId)
                    ->with('country:id,name,currency')
                    ->get();
                })->with('category')->select('id', 'route', 'brand_id',  'name', 'featured_img')->first();
            }
            $product = $this->reviewCount($product);
            return $product;
        }
    }

    public function priceFilter(Request $request)
    {
        $max =  $request->max;
        $min =   $request->min;
        $countryId = $this->country($request);

        if ($request->category) {

            $id  = Category::where('route', $request->category)->first();

            $productCategory = ProductCategory::where('category_id', $id['id'])->get();

            foreach ($productCategory as $key => $value) {

                $product['product'][$key] = Product::where('id', $value['product_id'])->with('category')->when(($request->min == 0 || $request->min > 0) &&  !empty($request->max), function ($q) use ($min, $max, $countryId) {
                    $q->with('price', function ($country) use ($min, $max, $countryId) {
                        $country->where('country_id', '=', $countryId)->whereBetween('actual_price', [$min, $max])
                        ->with('country:id,name,currency')
                        ->get();
                    });
                })->first();
            }
        } elseif ($request->sub_category) {

            $id  = SubCategory::where('route', $request->sub_category)->first();
            $productCategory = ProductSubCategory::where('sub_category_id', $id['id'])->get();
            foreach ($productCategory as $key => $value) {
                $product['product'][$key] = Product::where('id', $value['product_id'])->with('category')->when(($request->min == 0 || $request->min > 0) &&  !empty($request->max), function ($q) use ($min, $max, $countryId) {
                    $q->with('price', function ($country) use ($min, $max, $countryId) {
                        $country->where('country_id', '=', $countryId)->whereBetween('actual_price', [$min, $max])
                         ->with('country:id,name,currency')
                        ->get();
                    });
                })->first();
            }
        } elseif ($request->child_category) {

            $id  = ChildCategory::where('route', $request->child_category)->first();

            $productCategory = ProductChildCategory::where('child_category_id', $id['id'])->get();
            foreach ($productCategory as $key => $value) {
                $product['product'][$key] = Product::where('id', $value['product_id'])->with('category')->when(($request->min == 0 || $request->min > 0) &&  !empty($request->max), function ($q) use ($min, $max, $countryId) {
                    $q->with('price', function ($country) use ($min, $max, $countryId) {
                        $country->where('country_id', '=', $countryId)->whereBetween('actual_price', [$min, $max])
                         ->with('country:id,name,currency')
                        ->get();
                    });
                })->first();
            }
        } else {
            return response()->json(['error' => 'No Product found', 'code' => 404]);
        }

        $product = $this->reviewCount($product);
        return parent::returnData($product, 200);
    }


    public function reviewCount($product)
    {
        foreach ($product['product'] as $key => $productData) {
            $productId = $productData['id'];
            $reviews = Review::where('product_id', $productId)->where('status', 1)->get();

            $totalReviews = $reviews->count();
            $averageRating = $totalReviews > 0 ? $reviews->sum('rating') / $totalReviews : 0;

            $product['product'][$key]['review'] = $averageRating;
        }

        return $product;
    }


  public function productList(Request $request)
{
    $countryId = $this->country($request);

    // 1) Resolve category by route
    $categoryId = Category::where('route', $request->category)->value('id');
    if (!$categoryId) {
        return response()->json([], 200);
    }

    // 2) All product IDs in that category
    $productIds = ProductCategory::where('category_id', $categoryId)
        ->pluck('product_id')
        ->filter()
        ->unique();

    if ($productIds->isEmpty()) {
        return response()->json([], 200);
    }

    // 3) Fetch products in ONE query, only status=1
    $products = Product::whereIn('id', $productIds)
        ->where('status', 1) // ← only active products
        ->with([
            'price' => function ($query) use ($countryId) {
                $query->where('country_id', $countryId)
                      ->with('country:id,currency,name');
            },
            'category',
            'subCategory',
            'childCategory',
        ])
        ->select('id', 'route', 'brand_id', 'name', 'featured_img')
        ->get();

    if ($products->isEmpty()) {
        return response()->json([], 200);
    }

    // 4) If your reviewCount() expects ['product' => collection]
    $wrapped = ['product' => $products];
    $withReviews = $this->reviewCount($wrapped);

    return parent::returnData($withReviews, 200);
}




    public function productFilter(Request $request){
        $countryId = $this->country($request);
        $products = [];

        $categoryId = null;
        if ($request->sub_category) {
            $categoryId = SubCategory::where('route', $request->sub_category)->value('id');
            $productCategory = ProductSubCategory::where('sub_category_id', $categoryId)->get();
            foreach ($productCategory as $key => $value) {
                $products['product'][$key] = Product::with(['price' => function ($query) use ($countryId) {
                    $query->where('country_id', $countryId)->with('country:id,currency,name');
                }, 'category', 'subCategory', 'childCategory'])
                    ->select('id', 'route', 'brand_id', 'name', 'featured_img')
                    ->find($value['product_id']);
            }
        } elseif ($request->child_category) {
            $categoryId = ChildCategory::where('route', $request->child_category)->value('id');
            $productCategory = ProductChildCategory::where('child_category_id', $categoryId)->get();
           
            foreach ($productCategory as $key => $value) {
                $products['product'][$key] = Product::with(['price' => function ($query) use ($countryId) {
                    $query->where('country_id', $countryId)->with('country:id,currency,name');
                }, 'category', 'subCategory', 'childCategory'])
                    ->select('id', 'route', 'brand_id', 'name', 'featured_img')
                    ->find($value['product_id']);
            }
        }

        $product = $this->reviewCount($products);

        
        if( $product){
            
            return parent::returnData($product, 200);

        }else{

            return response()->json([], 200);

        }


        

        return $products;
    }


 

      











public function deals($country)
{
    // 1) Resolve country id; fallback to 13 if not found
    $countryId = optional(Country::where('name', $country)->first())->id ?? 13;

    // 2) Get active deals for the resolved country
    $deals = Deal::where('status', 1)
        ->where('country_id', $countryId)
        ->get();

    // 3) If none, fallback to default country 13
    if ($deals->isEmpty()) {
        $countryId = 13;
        $deals = Deal::where('status', 1)
            ->where('country_id', $countryId)
            ->get();

        if ($deals->isEmpty()) {
            // Nothing to return at all
            return parent::returnData(['product' => []], 200);
        }
    }

    // 4) Collect product ids from deals (unique)
    $productIds = $deals->pluck('product_id')->unique()->values();

    // 5) Fetch ONLY active products (status=1) and prices for the chosen country
    $products = Product::whereIn('id', $productIds)
        ->where('status', 1) // << only active products
        ->with([
            'price' => function ($q) use ($countryId) {
                $q->where('country_id', $countryId)
                  ->with('country:id,currency,name');
            },
            'category'
        ])
        ->select('id', 'name', 'route', 'featured_img')
        ->get();

    // 6) Shape payload for your existing reviewCount() helper
    $payload = ['product' => $products];

    // 7) Add average review score to each product
    $payload = $this->reviewCount($payload);

    return parent::returnData($payload, 200);
}














    // Product Detail Data
        public function popUpList($id, $countryIid)
        {
            $country = Country::where('name', $countryIid)->first();

            if ($country) {
                $countryId = $country->id;
            } else {
                // Handle case when country is not found, maybe set default country ID
                $countryId = '13'; // Assuming default country ID
            }

            $product = Product::where('id', $id)->with(['price' => function ($query) use ($countryId) {
                $query->where('country_id', $countryId)->with('country:id,currency,name');
            }, 'category'])
            ->select('id', 'route', 'sku', 'short_description', 'stock', 'slider_img', 'name')
            ->first();
            
            $product['shipping_charges'] = $countryId;
            $product['total_review'] = 0;
            $product['review_count'] = 0;

            if (Review::where('product_id', $product['id'])->exists()) {
                $noOfUser = Review::where('product_id', $product['id'])->where('status', 1)->count('user_id');
                $review = Review::where('product_id', $product['id'])->where('status', 1)->sum('rating');

                if ($noOfUser > 0) {
                    $product['total_review'] = $review / $noOfUser;
                }
                
                $product['review_count'] = $noOfUser;
            }

            if (!empty($product)) {
                return parent::returnData($product, 200);
            } else {
                return response()->json(['error' => 'No Product found', 'code' => 404]);
            }
        }


        public function productDetail(Request $request)
        {
            $countryId = $this->country($request);

            $product = Product::where('route', $request['route'])
        ->with(['price' => function ($query) use ($countryId) {
            $query->where('country_id', $countryId)
                ->with('country:id,currency,weight_based_shipping'); // CHANGED: removed old columns
        }, 'category', 'subCategory', 'childCategory'])
        ->first();

    if (!$product) {
        return response()->json(['error' => 'No Product found', 'code' => 404]);
    }

    // NEW: Get shipping charges from weight_based_shipping array
    $weightBasedShipping = $product['price'][0]['country']['weight_based_shipping'] ?? [];
    
    // Set default shipping charges (you can adjust logic based on your needs)
    $product['shipping_charges'] = !empty($weightBasedShipping) 
        ? $weightBasedShipping[0]['standard'] ?? 0 
        : 0;
    
    $product['express_charges'] = !empty($weightBasedShipping) 
        ? $weightBasedShipping[0]['express'] ?? 0 
        : 0;

    // Pass the full weight_based_shipping array if frontend needs it
    $product['weight_based_shipping'] = $weightBasedShipping;


            $reviews = Review::where('product_id', $product['id'])->where('status', 1)->get();
            $product['total_review'] = $reviews->avg('rating');
            $product['review'] = $reviews;
            $product['review_count'] = $reviews->count();

            $productCategory = ProductCategory::where('product_id', $product['id'])->first();
            $relatedProducts = ProductCategory::where('category_id', $productCategory['category_id'])
                ->where('product_id', '!=', $product['id'])
                ->with(['products' => function ($query) use ($countryId) {
                    $query->select('id', 'name', 'route', 'featured_img')
                        ->with(['price' => function ($query) use ($countryId) {
                            $query->where('country_id', $countryId)
                                ->select('id', 'product_id', 'country_id','actual_price', 'sale_price', 'deal_price')
                                ->with('country:id,name,currency');
                        }, 'category']);
                }])
                ->select('id', 'product_id')
                ->get();
                
            
            $product['related_product'] = $relatedProducts;

            return parent::returnData($product, 200);
        }
    // End Product Detail


    //Shop
        public function shop(Request $request)
        {
            $country = Country::where('name', $request->country)->first();

            if (!$country) {
                return response()->json([], 200);
            }

            $countryId = $country->id;

            $countryId = $this->country($request);
            $products = Product::with(['price' => function ($query) use ($countryId) {
                                        $query->where('country_id', $countryId)->with('country:id,currency,name');
                                    }, 'category'])
				     ->where('status', 1)
                                    ->select('id', 'route', 'brand_id', 'name', 'featured_img')
                                    ->get();
           $response = [];

            if ($products->isNotEmpty()) {
                foreach ($products as $key => $productData) {
                    $allCategoryIds = collect($productData->category)->pluck('id')->toArray();

                    $uniqueCategoryIds = array_unique($allCategoryIds);

                    $product = [
                        'id' => $productData->id,
                        'route' => $productData->route,
                        'brand_id' => $productData->brand_id,
                        'name' => $productData->name,
                        'featured_img' => $productData->featured_img,
                        'category_ids' => $uniqueCategoryIds,
                    ];

                    $response['products'][] = $product;
                }
            } else {
                $response['products'] = [];
            }

                    $allCategoryIds = collect($products->flatMap->category)->pluck('id')->unique()->toArray();

                    $categories = Category::whereIn('id', $allCategoryIds)->get(['id', 'name' ,'route']);


                    foreach ($products as $key => $productData) {
                        $productId = $productData['id'];
                        $reviews = Review::where('product_id', $productId)->where('status', 1)->get();
                        $totalReviews = $reviews->count();
                        $averageRating = $totalReviews > 0 ? $reviews->sum('rating') / $totalReviews : 0;
                      

                        $products[$key]['review'] = $averageRating;
                    }
                    $finalData['product'] = $products;
                    $finalData['categories'] = $categories;
                    return parent::returnData($finalData, 200);
        }


        public function shopFilter(Request $request){
            $country = Country::where('name', $request->country)->first();

            if (!$country) {
                return response()->json([], 200);
            }
            $countryId = $country->id;

            $countryId = $this->country($request);
            $categoryId = Category::where('route', $request->category)->pluck('id');
            $productCategory = ProductCategory::where('category_id', $categoryId)->get();
            foreach ($productCategory as $key => $value) {
                $products['product'][$key] = Product::with(['price' => function ($query) use ($countryId) {
                    $query->where('country_id', $countryId)->with('country:id,currency,name');
                }, 'category'])
                    ->select('id', 'route', 'brand_id', 'name', 'featured_img')
                    ->where('id', $value['product_id'])->first();
            }
            $product = $this->reviewCount($products);
            if( $product){
                
                return parent::returnData($product, 200);

            }else{

                return response()->json([], 200);

            }

        }


}
