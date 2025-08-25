<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Database connection successful!";
    } catch (\Exception $e) {
        return "❌ Database connection failed: " . $e->getMessage();
    }
});


Route::get('reset-password/{token}', 'UserController@resetPassword');
Route::post('submit-reset-password', 'UserController@submitResetPassword')->name('submit-reset-password');
