<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\UserController;
use App\Models\User;
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


Route::name('auth.')->prefix("auth")->group(function () {
    Route::post("register", [AuthController::class, "register"])->name("register");


    Route::post("{type}/verify", [VerificationController::class, "verifyAccount"])->name("verify-account");
    Route::post("{type}/resend", [VerificationController::class, "resendVerificationCode"])->name("resend-code");
    Route::post("forgot-password", [AuthController::class, "forgotPassword"])->name("forgot-password");
    Route::post("reset-password", [AuthController::class, "resetPassword"])->name("reset-password");
    Route::post("login", [AuthController::class, "login"])->name("login");
});


Route::name("app.")->middleware('auth:sanctum')->group(function(){
    Route::post("/users", function(){
        return User::all();
    });

    Route::get("user", [UserController::class, "getUser"])->name("user.get");
    Route::post("user/change-password", [UserController::class, "changeUserPassword"])->name("user.change-pass");
});
