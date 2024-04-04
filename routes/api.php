<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ProfileController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post( "/register", [ AuthController::class, "register" ]);
Route::post( "/login", [ AuthController::class, "login" ]);
Route::post( "/addCity", [ CityController::class, "addCity" ]);

Route::group(["middleware"=>["auth:sanctum"]],function(){
    Route::put( "/modifyCity", [ ProfileController::class, "setUserProfileData" ]);
    Route::put( "/newPassword", [ ProfileController::class, "setNewPassword" ]);
    Route::get( "/getUser", [ ProfileController::class, "getUserProfileData" ]);
    Route::delete( "/deleteUser", [ ProfileController::class, "getUserProfileData" ]);
    Route::put( "/logout", [ AuthController::class, "deleteAccount" ]);

});
