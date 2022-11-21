<?php
header("Content-Type: application/json");

use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get("test", function () {
    return $_SERVER['REMOTE_ADDR'];
});


Route::post("users/register", [UserController::class, "register"])->name("register");
Route::post("users/login", [UserController::class, "login"])->name("login");

Route::post("auth/account/verify", [VerificationController::class, "verifyAccount"])->name("verify-account");
Route::get("auth/account/verify/resend", [VerificationController::class, "resendAccountVerification"])->name("resend-verify-account");

Route::post("auth/password", [UserController::class, "forgotPassword"])->name("forgot-password");
Route::post("auth/password/verify", [VerificationController::class, "verifyPasswordRecovery"])->name("verify-forget-password");
Route::get("auth/password/verify/resend", [VerificationController::class, "resendPasswordRecovery"])->name("resend-forget-password");
Route::post("auth/password/reset", [UserController::class, "resetPassword"])->name("password-reset");
