<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyAccountRequest;
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
        if (!$request->has("tkn"))
            return $this->actionResponse(["code" => "99", "message" => "Invalid verification link"]);
        $response = $this->verifyService->verifyAccount($request->all());
        return $this->actionResponse($response);
    }

    public function resendAccountVerification(Request $request)
    {
        if (!$request->has("tkn"))
            return $this->actionResponse(["code" => "99", "message" => "Invalid verification link"]);
        $response = $this->verifyService->resendAccountVerifyCode($request->input("tkn"));
        return $this->actionResponse($response);
    }

    public function verifyPasswordRecovery(Request $request)
    {
        if (!$request->has("tkn"))
            return $this->actionResponse(["code" => "99", "message" => "Invalid verification link"]);
        $response = $this->verifyService->verifyPasswordRecovery($request->all());
        // if ($response["code"] == "00")
        return redirect($response["data"]["url"]);
        // return $this->actionResponse($response);
    }

    public function resendPasswordRecovery(Request $request)
    {
        if (!$request->has("tkn"))
            return $this->actionResponse(["code" => "99", "message" => "Invalid verification link"]);
        $response = $this->verifyService->resendPasswordRecovery($request->input("tkn"));
        return $this->actionResponse($response);
    }
}
