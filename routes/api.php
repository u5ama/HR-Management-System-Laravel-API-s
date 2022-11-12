<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Staff\StaffController;
use App\Http\Controllers\Api\Staff\StaffEmergencyContactController;
use App\Http\Controllers\Api\Staff\StaffNotesController;
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
            // Staff
            Route::controller(StaffController::class)->group(function(){
                Route::get('staff', 'index')->name('staff.index');
                Route::post('staff', 'store')->name('staff.store');
                Route::post('showStaff', 'show')->name('staff.show');
                Route::post('staff/{id}', 'update')->name('staff.update');
                Route::delete('staff/{id}', 'destroy')->name('staff.destroy');
                Route::post('bulkAddStaff', 'bulkAddStaff')->name('bulkAddStaff');
            });
            // Staff Emergency Contact
            Route::controller(StaffEmergencyContactController::class)->group(function(){
                Route::get('staff_emergency', 'index')->name('staff_emergency.index');
                Route::post('staff_emergency', 'store')->name('staff_emergency.store');
                Route::post('showStaffEmergency', 'show')->name('staff_emergency.show');
                Route::post('staff_emergency/{id}', 'update')->name('staff_emergency.update');
                Route::delete('staff_emergency/{id}', 'destroy')->name('staff_emergency.destroy');
            });
            //Staff Notes
            Route::controller(StaffNotesController::class)->group(function(){
                Route::get('staff_notes', 'index')->name('staff_notes.index');
                Route::post('staff_notes', 'store')->name('staff_notes.store');
                Route::post('showStaffNotes', 'show')->name('staff_notes.show');
                Route::post('staff_notes/{id}', 'update')->name('staff_notes.update');
                Route::delete('staff_notes/{id}', 'destroy')->name('staff_notes.destroy');
            });
        });

        Route::group(['middleware' => 'is_admin:staff', 'prefix' => 'staff','as' => 'staff.'], function () {
        });
        Route::post('logout', [AuthController::class,'logout'])->name('logout');
    });
});
