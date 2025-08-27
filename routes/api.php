<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscountController; 

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


Route::get('health/rds-tcp', function () {
    $host  = env('DB_HOST');
    $port  = (int) env('DB_PORT', 3306);
    $errno = null;
    $errstr = null;

    // Add some context that will appear on every following Log:: calls
    //hello
    Log::withContext([
        'route' => 'health/rds-tcp',
        'aws_trace' => request()->header('x-amzn-trace-id'),
        'client_ip' => request()->ip(),
        'db_host' => $host,
        'db_port' => $port,
    ]);

    $start = microtime(true);
    $okExt = extension_loaded('pdo_mysql');
    $sock  = @fsockopen($host, $port, $errno, $errstr, 2.0);
    $okTcp = $sock !== false;
    if ($okTcp) {
        fclose($sock);
    }
    $ms = (int) ((microtime(true) - $start) * 1000);

    Log::info('TCP check completed', [
        'pdo_mysql_loaded' => $okExt,
        'tcp_connect_3306' => $okTcp,
        'latency_ms' => $ms,
        'error' => $okTcp ? null : "{$errno} {$errstr}",
    ]);

    return response()->json([
        'ok'               => $okExt && $okTcp,
        'pdo_mysql_loaded' => $okExt,
        'tcp_connect_3306' => $okTcp,
        'host'             => $host,
        'port'             => $port,
        'latency_ms'       => $ms,
        'error'            => $okTcp ? null : "{$errno} {$errstr}",
    ], $okExt && $okTcp ? 200 : 500);
});

Route::get('health/db', function () {
    Log::withContext([
        'route' => 'health/db',
        'aws_trace' => request()->header('x-amzn-trace-id'),
        'client_ip' => request()->ip(),
    ]);

    $start = microtime(true);
    try {
        DB::connection()->getPdo();
        $ver = DB::select('select version() as v')[0]->v ?? null;
        $ms  = (int) ((microtime(true) - $start) * 1000);

        Log::info('DB connection OK', [
            'version' => $ver,
            'latency_ms' => $ms,
            // Safe to log: database host/name; avoid password/user if you prefer
            'db_host' => env('DB_HOST'),
            'db_name' => env('DB_DATABASE'),
        ]);

        return response()->json(['ok' => true, 'version' => $ver, 'latency_ms' => $ms], 200);
    } catch (\Throwable $e) {
        $ms = (int) ((microtime(true) - $start) * 1000);

        Log::error('DB connection FAILED', [
            'latency_ms' => $ms,
            'error' => $e->getMessage(),
            'code'  => $e->getCode(),
            // If needed during debugging, you can include a short trace (be careful with size):
            // 'trace' => substr($e->getTraceAsString(), 0, 2000),
            'db_host' => env('DB_HOST'),
            'db_name' => env('DB_DATABASE'),
        ]);

        return response()->json(['ok' => false, 'error' => $e->getMessage(), 'latency_ms' => $ms], 500);
    }
});



Route::get('/health/ping', function () {
    return response()->json([
        'ok' => true,
        'app' => config('app.name', 'laravel'),
        'env' => config('app.env'),
        'time' => now()->toIso8601String(),
    ]);
});

Route::get('clear-cache', 'CacheController@clearCache');
Route::post('uploads', 'UploadController@upload_media');
Route::post('upload-files', 'UploadController@files');
Route::get('uploads', 'UploadController@get_all_images');
Route::delete('uploads/{upload}', 'UploadController@delete_images');

#Pages
Route::get('pages', 'PageController@index');
Route::post('pages', 'PageController@store')->middleware('auth:sanctum');
Route::get('pages/{page}', 'PageController@show');
Route::delete('pages/{page}', 'PageController@destroy')->middleware('auth:sanctum');

#Blog
Route::get('blogs', 'BlogController@index');
Route::post('blogs', 'BlogController@store')->middleware('auth:sanctum');
Route::get('blogs/{blog}', 'BlogController@show');
Route::delete('blogs/{blog}', 'BlogController@destroy')->middleware('auth:sanctum');

#Blog Comments
Route::get('comments', 'CommentController@index');
Route::post('comments', 'CommentController@store');
Route::get('comments/{id}', 'CommentController@show');
Route::delete('comments/{id}', 'CommentController@destroy')->middleware('auth:sanctum');
Route::put('change-comment-status/{id}', 'CommentController@changeStatus')->middleware('auth:sanctum');

#Brands
Route::get('brands', 'BrandController@index');
Route::post('brands', 'BrandController@store')->middleware('auth:sanctum');
Route::get('brands/{brand}', 'BrandController@show');
Route::delete('brands/{brand}', 'BrandController@destroy')->middleware('auth:sanctum');

#Product 
Route::get('products', 'ProductController@index');
Route::post('products', 'ProductController@store')->middleware('auth:sanctum');
Route::get('products/{route}', 'ProductController@show');
Route::get('disable-product-list', 'ProductController@disableProducts');
Route::get('change-status/{id}', 'ProductController@changeStatus')->middleware('auth:sanctum');
Route::post('add-variation', 'ProductController@addVariation')->middleware('auth:sanctum');
Route::post('update-variation', 'ProductController@updateVariation')->middleware('auth:sanctum');

#Product Category

#Category
Route::get('categories', 'CategoryController@index');
Route::post('categories', 'CategoryController@store')->middleware('auth:sanctum');
Route::get('categories/{category}', 'CategoryController@show');
Route::delete('categories/{category}', 'CategoryController@destroy')->middleware('auth:sanctum');


#Category
Route::get('sub-categories', 'SubCategoryController@index');
Route::post('sub-categories', 'SubCategoryController@store')->middleware('auth:sanctum');
Route::get('sub-categories/{category}', 'SubCategoryController@show');
Route::delete('sub-categories/{category}', 'SubCategoryController@destroy')->middleware('auth:sanctum');

#Category
Route::get('child-categories', 'ChildCategoryController@index');
Route::post('child-categories', 'ChildCategoryController@store')->middleware('auth:sanctum');
Route::get('child-categories/{route}', 'ChildCategoryController@show');
Route::delete('child-categories/{route}', 'ChildCategoryController@destroy')->middleware('auth:sanctum');

#End Product


#Front 
Route::get('not-found', 'FrontController@notFound');
Route::get('home', 'FrontController@home');
Route::post('global-search', 'FrontController@globalSearch');
Route::get('about', 'FrontController@about');
Route::get('faq', 'FrontController@faq');
Route::get('privacy-policy', 'FrontController@privacyPolicy');
Route::get('terms-condition', 'FrontController@termsCondition');
Route::get('faq', 'FrontController@faq');
Route::get('sale-disc', 'FrontController@saleDisc');
Route::get('delivery', 'FrontController@delivery');
Route::get('client-review', 'FrontController@clientReview');
Route::post('most-purchased-products', 'FrontController@mostPurchasedProduct');
Route::get('top-brands', 'FrontController@topBrand');

#Blog
Route::get('blog-listing', 'FrontController@blog');
Route::get('blog-detail/{route}', 'FrontController@blogDetail');
Route::get('recent-blog', 'FrontController@recentBlog');
Route::get('blog-filter/{id}', 'FrontController@blogFilter');
Route::get('blog-category-count', 'FrontController@blogCategoryCount');
Route::post('blog-search', 'FrontController@blogSearch');
#End Blog


#Front Product
Route::post('product-list', 'FrontProductController@productList');
Route::post('product-filter', 'FrontProductController@productFilter');
Route::get('pop-up-product-detail/{id}/{country_id}', 'FrontProductController@popUpList');
Route::get('all-categories', 'FrontProductController@allCategory');
Route::get('all-brands', 'FrontProductController@allBrand');
Route::post('brand-filter', 'FrontProductController@brandFilter');
Route::post('price-filter', 'FrontProductController@priceFilter');
Route::post('reviews', 'ReviewController@store');
Route::post('product-detail', 'FrontProductController@productDetail');
Route::post('product-sort-by', 'FrontProductController@sortBy');
Route::get('deals/{country}', 'FrontProductController@deals');
Route::post('promotions', 'FrontController@promotion');
Route::post('shop', 'FrontProductController@shop');
Route::post('shop-category-filter', 'FrontProductController@shopFilter');


Route::post('contact-us-form', 'FormHandlerController@store');
Route::post('subscribers', 'SubscriberController@store');
Route::get('country/{route}', 'CountryController@country');




#End Front

# Forget Password
Route::post('forget-password', 'UserController@forgetPassword');
# End Forget Password

Route::post('discounts/apply', [DiscountController::class, 'apply'])
    ->name('discounts.apply');

// Admin CRUD (protect as needed)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('discounts', DiscountController::class);
});

// Admin / Staff (protect as you prefer: Sanctum/Passport)
Route::middleware('auth:sanctum')->group(function () {
    // Full CRUD (index, store, show, update, destroy)
    Route::apiResource('discounts', DiscountController::class);
});

Route::group(['prefix' => 'auth'], function ($router) {

    Route::get('/cards', 'DashboardController@cards');
    Route::post('/register', 'UserController@register');
    Route::post('/email-verification', 'UserController@emailVerify');

    Route::post('/login', 'UserController@login');
    Route::get('/me', 'UserController@me')->middleware('auth:sanctum');
    Route::post('/logout', 'UserController@logout')->middleware('auth:sanctum');
    Route::delete('/delete-user', 'UserController@deleteUser')->middleware('auth:sanctum');
    Route::post('/update-profile', 'UserController@updateProfile')->middleware('auth:sanctum');
    Route::post('/change-password', 'UserController@changePassword')->middleware('auth:sanctum');
    Route::get('/track-order/{id}', 'UserController@trackOrder')->middleware('auth:sanctum');
    Route::post('/cancel-order', 'UserOrderDetailController@cancelOrder')->middleware('auth:sanctum');

    #Todo
    Route::get('todos', 'TodoController@index')->middleware('auth:sanctum');
    Route::post('todos', 'TodoController@store')->middleware('auth:sanctum');
    Route::get('todos/{id}', 'TodoController@show')->middleware('auth:sanctum');
    Route::delete('todos/{id}', 'TodoController@destroy')->middleware('auth:sanctum');

    #Country
    Route::get('countries', 'CountryController@index')->middleware('auth:sanctum');
    Route::post('countries', 'CountryController@store')->middleware('auth:sanctum');
    Route::get('countries/{id}', 'CountryController@show')->middleware('auth:sanctum');
    Route::delete('countries/{id}', 'CountryController@destroy')->middleware('auth:sanctum');

    #Deals
    Route::get('deals', 'DealController@index')->middleware('auth:sanctum');
    Route::post('deals', 'DealController@store')->middleware('auth:sanctum');
    Route::get('deals/{id}', 'DealController@show')->middleware('auth:sanctum');
    Route::put('change-deal-status/{id}', 'DealController@changeStatus')->middleware('auth:sanctum');
    
    #Faqs
    Route::get('faqs', 'FaqController@index')->middleware('auth:sanctum');
    Route::post('faqs', 'FaqController@store')->middleware('auth:sanctum');
    Route::get('faqs/{id}', 'FaqController@show')->middleware('auth:sanctum');
    Route::put('faqs/{id}', 'FaqController@changeStatus')->middleware('auth:sanctum');

    #Products
    Route::get('products', 'ProductController@index')->middleware('auth:sanctum');
    Route::post('products', 'ProductController@store')->middleware('auth:sanctum');
    Route::post('add-variation', 'ProductController@addVariation')->middleware('auth:sanctum');
    Route::post('update-variation', 'ProductController@updateVariation')->middleware('auth:sanctum');
    Route::post('clone-variation', 'ProductController@cloneVariation')->middleware('auth:sanctum');
    Route::get('products/{product}', 'ProductController@show')->middleware('auth:sanctum');
    Route::put('change-status/{id}', 'ProductController@changeStatus')->middleware('auth:sanctum');
    Route::get('disable-product-list', 'ProductController@disableProductList')->middleware('auth:sanctum');
    Route::get('all-variation/{id}', 'ProductController@allVariation')->middleware('auth:sanctum');
    Route::get('single-variation/{id}', 'ProductController@singleVariation')->middleware('auth:sanctum');
    Route::delete('delete-variation/{id}', 'ProductController@deleteVariation')->middleware('auth:sanctum');
    Route::get('list/{id}', 'ProductController@list')->middleware('auth:sanctum');

    #Review
    Route::get('reviews', 'ReviewController@index')->middleware('auth:sanctum');
    Route::get('reviews/{id}', 'ReviewController@show')->middleware('auth:sanctum');
    Route::put('change-review-status/{id}', 'ReviewController@changeStatus')->middleware('auth:sanctum');

    #Address
    Route::get('addresses/{id}', 'AddressController@index')->middleware('auth:sanctum');
    Route::get('address-detail/{id}', 'AddressController@show')->middleware('auth:sanctum');

    Route::post('addresses', 'AddressController@store')->middleware('auth:sanctum');
    Route::delete('addresses/{address}', 'AddressController@destroy')->middleware('auth:sanctum');
    Route::put('set-default/{id}', 'AddressController@setDefault')->middleware('auth:sanctum');

    #User
    Route::get('user-listing', 'DashboardController@userListing')->middleware('auth:sanctum');
    Route::delete('delete-user/{id}', 'DashboardController@deleteUser')->middleware('auth:sanctum');

    Route::get('form-listing', 'DashboardController@formList')->middleware('auth:sanctum');
    Route::delete('delete-form-data/{id}', 'DashboardController@deleteForm')->middleware('auth:sanctum');

    Route::get('subscriber-listing', 'DashboardController@subscriberList')->middleware('auth:sanctum');
    Route::delete('delete-subscriber-data/{id}', 'DashboardController@deleteSubscriber')->middleware('auth:sanctum');

    #Most purchased Product
    Route::post('most-purchased', 'MostPurchasedController@store')->middleware('auth:sanctum');
    Route::get('most-purchased', 'MostPurchasedController@index')->middleware('auth:sanctum');
    Route::get('most-purchased/{id}', 'MostPurchasedController@show')->middleware('auth:sanctum');
    Route::delete('most-purchased/{id}', 'MostPurchasedController@destroy')->middleware('auth:sanctum');
    Route::get('most-purchased-products-drop-down/{id}', 'MostPurchasedController@mostPurchasedProductsDropDown')->middleware('auth:sanctum');

    #Coupons 
    Route::get('coupons', 'CouponController@index');
    Route::post('coupons', 'CouponController@store')->middleware('auth:sanctum');
    Route::get('coupons/{id}', 'CouponController@show');
    Route::delete('coupons/{id}', 'CouponController@destroy')->middleware('auth:sanctum');

    #Cms Orders
    Route::get('all-orders', 'DashboardController@allOrders');
    Route::get('cms-order-detail/{id}', 'DashboardController@orderDetail');
    Route::get('order-card', 'DashboardController@orderCard')->middleware('auth:sanctum');
    Route::post('order-filter', 'DashboardController@orderFilter')->middleware('auth:sanctum');
    Route::post('send-tracking-number', 'DashboardController@sendTracking')->middleware('auth:sanctum');

    #Order 
    Route::post('make-order', 'OrderController@order');
    Route::get('orders/{id}', 'OrderController@index')->middleware('auth:sanctum');
    Route::get('order-detail/{id}', 'OrderController@orderDetail')->middleware('auth:sanctum');
    Route::get('user-order/{id}', 'OrderController@userOrder')->middleware('auth:sanctum');

    #Address
    Route::post('add-address', 'UserController@addAddress')->middleware('auth:sanctum');
    Route::get('user-addresses/{id}', 'UserController@userAddress')->middleware('auth:sanctum');
    Route::delete('delete-addresses/{id}', 'UserController@deleteAddress')->middleware('auth:sanctum');
    Route::post('update-addresses', 'UserController@updateAddress')->middleware('auth:sanctum');
    Route::get('view-addresses/{id}', 'UserController@viewAddress')->middleware('auth:sanctum');


});


Route::get('/v1/health', function () {
    return response()->json([
        'status' => 'ok hello',
        'time' => now()->toIso8601String(),
    ]);
});


Route::fallback(function () {
    return response()->json(['message' => 'Invalid    Route'], 400);
});