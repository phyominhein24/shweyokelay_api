<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RoutesController;
use App\Http\Controllers\VehiclesTypeController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DailyRouteController;
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


Route::get('/payments', [PaymentController::class, 'index']);
Route::get('/counters', [CounterController::class, 'index']);
Route::get('/route', [RoutesController::class, 'index_for_web']);
Route::get('/vehiclesTypes', [VehiclesTypeController::class, 'index']);
Route::get('/profiles', [WebAuthController::class, 'userProfile']);
Route::get('/memberProfile/{id}', [DashboardController::class, 'memberProfile']);
Route::post('/getUserInfo', [UserController::class, 'getUserInfo']);
Route::post('/paymentHistory2', [PaymentHistoryController::class, 'store']);
Route::post('/paymentHistory3', [PaymentHistoryController::class, 'store2']);
Route::post('/paymentHistory4', [PaymentHistoryController::class, 'store3']);
Route::get('/myticket/{id}', [PaymentHistoryController::class, 'showKpayMemberTicket']);
Route::post('/auth/get-user-info', [PaymentHistoryController::class, 'authGetUserInfo']);
Route::get('members/{id}', [MemberController::class, 'show']);

// Route::post('/payment/create-order', [PaymentHistoryController::class, 'createOrder']);

Route::get('contact/', [ContactController::class, 'index']);
Route::post('contact/', [ContactController::class, 'store']);
Route::get('contact/{id}', [ContactController::class, 'show']);
Route::post('contact/{id}', [ContactController::class, 'update']);
Route::delete('contact/{id}', [ContactController::class, 'destroy']);        

Route::get('cancleTicket/{id}', [DashboardController::class, 'cancleTicket']); 



Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/loginnn', [WebAuthController::class, 'userProfile']);
    Route::post('/loginn', [WebAuthController::class, 'login']);
    Route::post('/register', [MemberController::class, 'store']);
});

Route::middleware('jwt')->group(function () {

    Route::get('/dashboard/top-agents', [DashboardController::class, 'topAgents']);
    Route::get('/dashboard/payment-stats', [DashboardController::class, 'paymentStats']);
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
        Route::get('/', [MemberController::class, 'index'])->permission(PermissionEnum::MEMBER_INDEX->value);
        Route::post('/', [MemberController::class, 'store'])->permission(PermissionEnum::MEMBER_STORE->value);
        Route::get('/{id}', [MemberController::class, 'show'])->permission(PermissionEnum::MEMBER_SHOW->value);
        Route::post('/{id}', [MemberController::class, 'update'])->permission(PermissionEnum::MEMBER_UPDATE->value);
        Route::delete('/{id}', [MemberController::class, 'destroy'])->permission(PermissionEnum::MEMBER_DESTROY->value);        
    });

    Route::group(['prefix' => 'counter'], function () {
        Route::get('/', [CounterController::class, 'index'])->permission(PermissionEnum::COUNTER_INDEX->value);
        Route::post('/', [CounterController::class, 'store'])->permission(PermissionEnum::COUNTER_STORE->value);
        Route::get('/{id}', [CounterController::class, 'show'])->permission(PermissionEnum::COUNTER_SHOW->value);
        Route::post('/{id}', [CounterController::class, 'update'])->permission(PermissionEnum::COUNTER_UPDATE->value);
        Route::delete('/{id}', [CounterController::class, 'destroy'])->permission(PermissionEnum::COUNTER_DESTROY->value);        
    });

    Route::group(['prefix' => 'vehiclesType'], function () {
        Route::get('/', [VehiclesTypeController::class, 'index'])->permission(PermissionEnum::VEHICLES_TYPE_INDEX->value);
        Route::post('/', [VehiclesTypeController::class, 'store'])->permission(PermissionEnum::VEHICLES_TYPE_STORE->value);
        Route::get('/{id}', [VehiclesTypeController::class, 'show'])->permission(PermissionEnum::VEHICLES_TYPE_SHOW->value);
        Route::post('/{id}', [VehiclesTypeController::class, 'update'])->permission(PermissionEnum::VEHICLES_TYPE_UPDATE->value);
        Route::delete('/{id}', [VehiclesTypeController::class, 'destroy'])->permission(PermissionEnum::VEHICLES_TYPE_DESTROY->value);        
    });

    // Route::group(['prefix' => 'payment'], function () {
    //     Route::get('/', [PaymentController::class, 'index']);
    //     Route::post('/', [PaymentController::class, 'store']);
    //     Route::get('/{id}', [PaymentController::class, 'show'])->permission(PermissionEnum::PAYMENT_SHOW->value);
    //     Route::post('/{id}', [PaymentController::class, 'update'])->permission(PermissionEnum::PAYMENT_UPDATE->value);
    //     Route::delete('/{id}', [PaymentController::class, 'destroy'])->permission(PermissionEnum::PAYMENT_DESTROY->value);        
    // });

    Route::group(['prefix' => 'payment'], function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/', [PaymentController::class, 'store']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::post('/{id}', [PaymentController::class, 'update']);
        Route::delete('/{id}', [PaymentController::class, 'destroy']);        
    });

    Route::group(['prefix' => 'paymentHistory'], function () {
        Route::get('/', [PaymentHistoryController::class, 'index'])->permission(PermissionEnum::PAYMENT_HISTORY_INDEX->value);
        Route::post('/', [PaymentHistoryController::class, 'store'])->permission(PermissionEnum::PAYMENT_HISTORY_STORE->value);
        Route::get('/confirm/{id}', [PaymentHistoryController::class, 'confirm'])->permission(PermissionEnum::PAYMENT_HISTORY_UPDATE->value);
        Route::get('/reject/{id}', [PaymentHistoryController::class, 'reject'])->permission(PermissionEnum::PAYMENT_HISTORY_UPDATE->value);
        Route::get('/{id}', [PaymentHistoryController::class, 'show'])->permission(PermissionEnum::PAYMENT_HISTORY_SHOW->value);
        Route::post('/{id}', [PaymentHistoryController::class, 'update'])->permission(PermissionEnum::PAYMENT_HISTORY_UPDATE->value);
        Route::delete('/{id}', [PaymentHistoryController::class, 'destroy'])->permission(PermissionEnum::PAYMENT_HISTORY_DESTROY->value);        
    });

    Route::group(['prefix' => 'routes'], function () {
        Route::get('/', [RoutesController::class, 'index'])->permission(PermissionEnum::ROUTES_INDEX->value);
        Route::post('/', [RoutesController::class, 'store'])->permission(PermissionEnum::ROUTES_STORE->value);
        Route::get('/{id}', [RoutesController::class, 'show'])->permission(PermissionEnum::ROUTES_SHOW->value);
        Route::post('/{id}', [RoutesController::class, 'update'])->permission(PermissionEnum::ROUTES_UPDATE->value);
        Route::delete('/{id}', [RoutesController::class, 'destroy'])->permission(PermissionEnum::ROUTES_DESTROY->value);        
    });

    // Route::group(['prefix' => 'contact'], function () {
    //     Route::get('/', [ContactController::class, 'index']);
    //     Route::post('/', [ContactController::class, 'store']);
    //     Route::get('/{id}', [ContactController::class, 'show']);
    //     Route::post('/{id}', [ContactController::class, 'update']);
    //     Route::delete('/{id}', [ContactController::class, 'destroy']);        
    // });

    Route::group(['prefix' => 'dailyRoute'], function () {
        Route::get('/', [DailyRouteController::class, 'index']);
        Route::post('/', [DailyRouteController::class, 'store'])->permission(PermissionEnum::DAILY_ROUTE_STORE->value);
        Route::get('/{id}', [DailyRouteController::class, 'show']);
        Route::post('/{id}', [DailyRouteController::class, 'update'])->permission(PermissionEnum::DAILY_ROUTE_UPDATE->value);
        Route::delete('/{id}', [DailyRouteController::class, 'destroy'])->permission(PermissionEnum::DAILY_ROUTE_DESTROY->value);        
    });

    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [DashboardController::class, 'getDashboardData']);
    });

    // Route::group(['prefix' => 'dashboard'], function () {
    //     Route::get('/', [DashboardController::class, 'getDashboardData'])->permission(PermissionEnum::DASHBOARD_INDEX->value);
    // });

});

// Route::get('/image/{path}', [ItemController::class, 'getImage'])->where('path', '.*');
