<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Staff\StaffController;
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

Route::middleware(['cors'])->prefix('v1')->name('api.v1.')->group(function(){

    Route::post('login', [AuthController::class,'login'])->name('login');
    Route::post('register', [AuthController::class,'register'])->name('register');
    Route::post('sendPasswordResetLink', [AuthController::class,'forgotPassword'])->name('sendPasswordResetLink');
    Route::post('resetPassword', [AuthController::class,'resetPassword'])->name('resetPassword');

    Route::group(['middleware' => ['jwt.verify']], function(){
        Route::group(['middleware' => 'is_admin:company', 'prefix' => 'company','as' => 'company.'], function () {
            Route::controller(StaffController::class)->group(function(){
                Route::get('staff', 'index')->name('staff.index');
                Route::post('staff', 'store')->name('staff.store');
                Route::post('showStaff', 'show')->name('staff.show');
                Route::post('staff/{id}', 'update')->name('staff.update');
                Route::delete('staff/{id}', 'destroy')->name('staff.destroy');
                Route::post('bulkAddStaff', 'bulkAddStaff')->name('bulkAddStaff');
            });
        });

        Route::group(['middleware' => 'is_admin:staff', 'prefix' => 'staff','as' => 'staff.'], function () {
        });
        Route::post('logout', [AuthController::class,'logout'])->name('logout');
    });
});
