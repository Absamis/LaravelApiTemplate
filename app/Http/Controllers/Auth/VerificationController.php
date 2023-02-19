<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyAccountRequest;
use App\Services\VerificationRepository;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public $verifyService;
    //
    public function __construct(VerificationRepository $verifyRepo)
    {
        $this->verifyService = $verifyRepo;
    }
    public function verifyAccount(VerifyAccountRequest $request)
    {
        $response = $this->verifyService->verifyAccount($request->all());
        return $this->actionResponse($response);
    }

    public function resendVerificationCode(Request $request, $vrf_type)
    {
        $response = $this->verifyService->resendVerificationCode($request->input("token"), $vrf_type);
        return $this->actionResponse($response);
    }

    public function verifyPasswordReset(VerifyAccountRequest $request)
    {
        $response = $this->verifyService->verifyPasswordReset($request->all());
        return $this->actionResponse($response);
    }
}
