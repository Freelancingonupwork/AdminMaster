<?php

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\User\AuthController;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::prefix('v1')->group(function() {
    Route::post('register-user', [AuthController::class, 'userRegister'])->name('register-user');
    Route::post('login-user', [AuthController::class, 'userLogin'])->name('login-user');
    Route::post('forget-password', [AuthController::class, 'forgotPassword'])->name('forget-password');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
    Route::group(['middleware' => ['auth:api']], function (): void {
        Route::post('update-profile',[AuthController::class, 'updateProfile'])->name('update-profile');

        // Product Related API
        Route::post('product-list', [ProductController::class, 'getProductList'])->name('product-list');
        Route::post('category-list', [ProductController::class, 'getCategoryList'])->name('category-list');
        Route::post('promocodes', [ProductController::class, 'getCouponList'])->name('promocodes');
    });
});
