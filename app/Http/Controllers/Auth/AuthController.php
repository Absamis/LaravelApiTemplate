<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Interfaces\IAuthRepository;
use App\Services\VerificationService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    private IAuthRepository $authRepo;
    public function __construct(IAuthRepository $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function login(Request $request){
        $data = $request->validate([
            "email" => ["required", "email:rfc,dns"],
            "password" => ["required", "max:32"]
        ]);
        $response = $this->authRepo->login($data);
        return response()->json($response);
    }

    public function register(SignupRequest $request){
        $response = $this->authRepo->register($request->validated());
        return response()->json($response);
    }

    public function forgotPassword(Request $request){
        $data = $request->validate([
            "email" => ["required", "email:rfc,dns", "exists:users,email"],
        ], [
            "email.exists" => "Account not found"
        ]);
        $response = $this->authRepo->forgotPassword($data);
        return response()->json($response);
    }

    public function resetPassword(ResetPasswordRequest $request){
        $response = $this->authRepo->resetPassword($request->validated());
        return response()->json($response);
    }

    public function logout(){

    }
}
