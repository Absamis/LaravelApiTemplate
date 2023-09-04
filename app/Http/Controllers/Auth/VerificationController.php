<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AccountStatusCodes;
use App\Enums\ApiStatusCodes;
use App\Enums\VerificationStatusCodes;
use App\Http\Controllers\Controller;
use App\Interfaces\IUserRepository;
use App\Services\VerificationService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    //
    private IUserRepository $userRepo;
    public function __construct(IUserRepository $repo)
    {
        $this->userRepo = $repo;
    }
    public function verifyAccount(Request $request, $type){
        $data = $request->validate([
            "token" => ["required"],
            "code" => ["required", "numeric"]
        ]);
        $verifyType = VerificationStatusCodes::getCode($type);
        $action = 'clear';
        if($verifyType == VerificationStatusCodes::$badRequest)
            return abort(404);
        if($verifyType == VerificationStatusCodes::$passwordVerification)
            $action = 'refresh';

        $res = VerificationService::validateCode($data, $verifyType, $action);
        if(!$res)
            return abort(422, "Something went wrong");
        if ($verifyType == VerificationStatusCodes::$passwordVerification) {
            return response()->json([
                "code" => ApiStatusCodes::$successCode,
                "message" => "verified",
                "data" => [
                    "token" => $res->token
                ]
            ]);
        }
        $response = $this->userRepo->changeUserStatus($res->user, AccountStatusCodes::$verifiedAccount);
        return response()->json($response);
    }

    public function resendVerificationCode(Request $request, $type){
        $data = $request->validate([
            "token" => ["required"],
        ]);
        $verifyType = VerificationStatusCodes::getCode($type);
        if ($verifyType == VerificationStatusCodes::$badRequest)
            return abort(404);
        $res = VerificationService::validateToken($data['token'], $verifyType);
        if (!$res)
            return abort(422, "Something went wrong");
        $response = VerificationService::sendVerificationCode($res->user, $res->verify_type);
        return response()->json([
            "code" => "00",
            "message" => "Verification code resent",
            "data" => $response
        ]);
    }
}
