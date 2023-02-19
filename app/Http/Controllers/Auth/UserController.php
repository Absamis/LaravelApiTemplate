<?php

namespace App\Http\Controllers\Auth;

// header("Content-Type: application/json");

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AccountEmailRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UserRegistrationRequest;
use App\Models\User;
use App\Services\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public $userRepo;
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function login(LoginRequest $request)
    {
        $response = $this->userRepo->login($request->validated());
        return $this->actionResponse($response);
    }
    public function register(UserRegistrationRequest $request)
    {
        $response = $this->userRepo->registration($request->validated());
        return $this->actionResponse($response);
    }
    public function forgotPassword(AccountEmailRequest $request)
    {
        $response = $this->userRepo->forgotPassword($request->input("email"));
        return $this->actionResponse($response);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $response = $this->userRepo->resetPassword($request->all());
        return $this->actionResponse($response);
    }

    public function logout($userid)
    {
        $response = $this->userRepo->logout($userid);
        return $this->actionResponse($response);
    }
}
