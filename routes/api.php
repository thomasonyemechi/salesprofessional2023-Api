<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/app_info', function () {
    $data = [
        'name' => 'Sales Professional',
        'version' => 3.0,
        'date' => 2023
    ];
    return response($data);
});

Route::post('/create_business', [BusinessController::class, 'createBusiness']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {

    ///supplier
    Route::group(['prefix' => 'supplier'], function () {
        Route::post('/add', [SupplierController::class, 'addSupplier']);
        Route::post('/update', [SupplierController::class, 'updateSupplier']);
        Route::delete('/delete/{id}', [SupplierController::class, 'deleteSupplier']);
        Route::get('/', [SupplierController::class, 'getSuppliers']);
    });

    //affiliate profile
    Route::group(['prefix' => 'affiliate'], function () {
        Route::post('/add', [AffiliateController::class, 'registerAffiliate']);
        Route::post('/update', [AffiliateController::class, 'updateAffiliate']);
        Route::delete('/delete/{id}', [AffiliateController::class, 'deleteAffiliate']);
        Route::get('/', [AffiliateController::class, 'getAffiliates']);
    });

    //cutomer profile
    Route::group(['prefix' => 'customer'], function () {
        Route::post('/add', [CustomerController::class, 'addCustomer']);
        Route::post('/update', [CustomerController::class, 'updateCustomer']);
        Route::get('/', [CustomerController::class, 'getCustomers']);
    });

    //staffs
    Route::group(['prefix' => 'staff'], function () {
        Route::post('/add', [StaffController::class, 'addStaff']);
        Route::post('/update', [StaffController::class, 'updateStaff']);
        Route::get('/', [StaffController::class, 'getAllStaff']);
    });

    //product category
    Route::group(['prefix' => 'product_category'], function () {
        Route::post('/add', [ProductController::class, 'createProductCategory']);
        Route::post('/update', [ProductController::class, 'updateCategory']);
        Route::get('/', [ProductController::class, 'getProductCategory']);
    });

    //Items
    Route::group(['prefix' => 'item'], function () {
        Route::post('/add', [ItemsController::class, 'createItem']);
        Route::post('/update', [ItemsController::class, 'updateItem']);
        Route::get('/', [ItemsController::class, 'getItemsList']);
        Route::get('/pos', [ItemsController::class, 'getItemsForSale']);        
        Route::post('/search', [ItemsController::class, 'searchItem']);        
    });
});
