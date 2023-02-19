<?php
header("Content-Type: application/json");

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerificationController;
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


Route::prefix("auth")->group(function () {
    Route::post("/register", [UserController::class, "register"])->name("register");
    Route::post("/account/verify", [VerificationController::class, "verifyAccount"])->name("verify-account");

    Route::post("/password", [UserController::class, "forgotPassword"])->name("forgot-password");
    Route::post("/password/verify", [VerificationController::class, "verifyPasswordReset"])->name("verify-forget-password");
    Route::post("/password/reset", [UserController::class, "resetPassword"])->name("password-reset");

    Route::post("/{vrf_type}/verify/resend", [VerificationController::class, "resendVerificationCode"])->name("resend-verify-code");

    Route::post("/login", [UserController::class, "login"])->name("login");
});
Route::middleware(["auth.user"])->prefix("users")->group(function () {
    //PROFILE ENDPOINTS
    Route::get("/{userid}", [ProfileController::class, "getUserProfile"]);
    Route::put("/{userid}", [ProfileController::class, "updateUserProfile"])->name("update-profile");
    Route::post("/{userid}/profile/photo", [ProfileController::class, "updateUserProfilePhoto"]);
    Route::put("/{userid}/profile/change-password", [ProfileController::class, "changePassword"]);
    Route::delete("/{userid}/logout", [UserController::class, "logout"])->name("logout");
});

//mail password 6v4=E9.#wyuB
