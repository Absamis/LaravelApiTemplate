<?php

namespace App\Traits;

use App\Events\AccountRegistered;
use App\Events\PasswordRecovery;
use App\Models\Verification;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait AuthTrait
{
    public function sendAccountVerificationCode($user)
    {
        try {
            $code = mt_rand(1111111, 9999999);
            $token = str_replace("-", "", Str::uuid());
            $vtype = 1;
            $userid = $user->userid;
            $tkn = $user->remember_token;
            $email = $user->email;
            $fullname = $user->firstname . " " . $user->lastname;
            $resendUrl = route("resend-verify-account", ["tkn" => $token]);
            $verifyUrl = route("verify-account", ["tkn" => $token]);
            $userData = ["name" => $fullname, "email" => $email];
            $mailData = ["code" => $code, "verifyUrl" => $verifyUrl];
            AccountRegistered::dispatch($userData, $mailData);
            $code = Hash::make($code);
            $this->saveVerificationData($userid, $code, $vtype, $token);
            return ["success" => true, "data" => ["resendUrl" => $resendUrl, "verifyUrl" => $verifyUrl, "userid" => $tkn]];
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Error occured."];
        }
    }

    public function sendPasswordRecoveryCode($user)
    {
        try {
            $code = mt_rand(1111111, 9999999);
            $token = str_replace("-", "", Str::uuid());
            $vtype = 2;
            $userid = $user->userid;
            $tkns = $user->remember_token;
            $email = $user->email;
            $fullname = $user->firstname . " " . $user->lastname;
            $tkn = Crypt::encryptString($token);
            $resendUrl = route("resend-forget-password", ["tkn" => $tkn]);
            $verifyUrl = route("verify-forget-password", ["tkn" => $tkn]);
            $userData = ["name" => $fullname, "email" => $email];
            $mailData = ["code" => $code, "verifyUrl" => $verifyUrl];
            PasswordRecovery::dispatch($userData, $mailData);
            $code = Hash::make($code);
            $this->saveVerificationData($userid, $code, $vtype, $token);
            return ["success" => true, "data" => ["resendUrl" => $resendUrl, "verifyUrl" => $verifyUrl, "userid" => $tkns]];
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Error occured."];
        }
    }

    private function saveVerificationData($userid, $code, $vtype, $token)
    {
        Verification::updateOrCreate(
            [
                "userid" => $userid,
                "verify_type" => $vtype
            ],
            [
                "code" => $code,
                "token" => $token
            ]
        );
    }
}
