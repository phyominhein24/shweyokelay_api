<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TableNumberController;
use App\Http\Controllers\ItemDataController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransferItemController;
use App\Http\Controllers\TransferMaterialController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\RoutesController;
use App\Http\Controllers\TablePackageController;
use App\Http\Controllers\VehiclesTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/forget-password', [PasswordResetController::class, 'forgetPassword'])->middleware('guest');
Route::get('/reset-password', [PasswordResetController::class, 'resetPasswordPage'])->middleware('guest');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->middleware('guest');

Route::group(['prefix' => 'auth'], function () {

    Route::post('/login', [AuthController::class, 'login']);

});

Route::middleware('jwt')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/profile', [AuthController::class, 'userProfile']);
    Route::post('/change-password/{id}', [AuthController::class, 'changePassword']);

    Route::group(['prefix' => 'role'], function () {
        Route::get('/', [RoleController::class, 'index'])->permission(PermissionEnum::ROLE_INDEX->value);
        Route::post('/', [RoleController::class, 'store'])->permission(PermissionEnum::ROLE_STORE->value);
        Route::get('/{id}', [RoleController::class, 'show'])->permission(PermissionEnum::ROLE_SHOW->value);
        Route::post('/{id}', [RoleController::class, 'update'])->permission(PermissionEnum::ROLE_UPDATE->value);
        Route::delete('/{id}', [RoleController::class, 'destroy'])->permission(PermissionEnum::ROLE_DESTROY->value);
    });

    Route::group(['prefix' => 'permission'], function () {
        Route::get('/', [PermissionController::class, 'index'])->permission(PermissionEnum::PERMISSION_INDEX->value);
        Route::get('/{id}', [PermissionController::class, 'show'])->permission(PermissionEnum::PERMISSION_SHOW->value);

    });
    
    Route::group(['prefix' => 'user'], function () {
        Route::post('/assign-role', [UserController::class, 'assignRole'])->permission(PermissionEnum::USER_STORE->value);
        Route::post('/remove-role', [UserController::class, 'removeRole'])->permission(PermissionEnum::USER_UPDATE->value);
        Route::get('/', [UserController::class, 'index'])->permission(PermissionEnum::USER_INDEX->value);
        Route::post('/', [UserController::class, 'store'])->permission(PermissionEnum::USER_STORE->value);
        Route::get('/{id}', [UserController::class, 'show'])->permission(PermissionEnum::USER_SHOW->value);
        Route::post('/{id}', [UserController::class, 'update'])->permission(PermissionEnum::USER_UPDATE->value);
        Route::delete('/{id}', [UserController::class, 'destroy'])->permission(PermissionEnum::USER_DESTROY->value);
    });

    Route::group(['prefix' => 'member'], function () {
        Route::get('/', [MemberController::class, 'index'])->permission(PermissionEnum::SHOP_INDEX->value);
        Route::post('/', [MemberController::class, 'store'])->permission(PermissionEnum::SHOP_STORE->value);
        Route::get('/{id}', [MemberController::class, 'show'])->permission(PermissionEnum::SHOP_SHOW->value);
        Route::post('/{id}', [MemberController::class, 'update'])->permission(PermissionEnum::SHOP_UPDATE->value);
        Route::delete('/{id}', [MemberController::class, 'destroy'])->permission(PermissionEnum::SHOP_DESTROY->value);        
    });

    Route::group(['prefix' => 'counter'], function () {
        Route::get('/', [CounterController::class, 'index'])->permission(PermissionEnum::SHOP_INDEX->value);
        Route::post('/', [CounterController::class, 'store'])->permission(PermissionEnum::SHOP_STORE->value);
        Route::get('/{id}', [CounterController::class, 'show'])->permission(PermissionEnum::SHOP_SHOW->value);
        Route::post('/{id}', [CounterController::class, 'update'])->permission(PermissionEnum::SHOP_UPDATE->value);
        Route::delete('/{id}', [CounterController::class, 'destroy'])->permission(PermissionEnum::SHOP_DESTROY->value);        
    });

    Route::group(['prefix' => 'vehiclesType'], function () {
        Route::get('/', [VehiclesTypeController::class, 'index'])->permission(PermissionEnum::SHOP_INDEX->value);
        Route::post('/', [VehiclesTypeController::class, 'store'])->permission(PermissionEnum::SHOP_STORE->value);
        Route::get('/{id}', [VehiclesTypeController::class, 'show'])->permission(PermissionEnum::SHOP_SHOW->value);
        Route::post('/{id}', [VehiclesTypeController::class, 'update'])->permission(PermissionEnum::SHOP_UPDATE->value);
        Route::delete('/{id}', [VehiclesTypeController::class, 'destroy'])->permission(PermissionEnum::SHOP_DESTROY->value);        
    });

    Route::group(['prefix' => 'routes'], function () {
        Route::get('/', [RoutesController::class, 'index'])->permission(PermissionEnum::SHOP_INDEX->value);
        Route::post('/', [RoutesController::class, 'store'])->permission(PermissionEnum::SHOP_STORE->value);
        Route::get('/{id}', [RoutesController::class, 'show'])->permission(PermissionEnum::SHOP_SHOW->value);
        Route::post('/{id}', [RoutesController::class, 'update'])->permission(PermissionEnum::SHOP_UPDATE->value);
        Route::delete('/{id}', [RoutesController::class, 'destroy'])->permission(PermissionEnum::SHOP_DESTROY->value);        
    });

    Route::group(['prefix' => 'paymentHistory'], function () {
        Route::get('/', [PaymentHistoryController::class, 'index'])->permission(PermissionEnum::SHOP_INDEX->value);
        Route::post('/', [PaymentHistoryController::class, 'store'])->permission(PermissionEnum::SHOP_STORE->value);
        Route::get('/{id}', [PaymentHistoryController::class, 'show'])->permission(PermissionEnum::SHOP_SHOW->value);
        Route::post('/{id}', [PaymentHistoryController::class, 'update'])->permission(PermissionEnum::SHOP_UPDATE->value);
        Route::delete('/{id}', [PaymentHistoryController::class, 'destroy'])->permission(PermissionEnum::SHOP_DESTROY->value);        
    });

    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [DashboardController::class, 'getDashboardData']);
    });

    // Route::group(['prefix' => 'dashboard'], function () {
    //     Route::get('/', [DashboardController::class, 'getDashboardData'])->permission(PermissionEnum::DASHBOARD_INDEX->value);
    // });

});

// Route::get('/image/{path}', [ItemController::class, 'getImage'])->where('path', '.*');
