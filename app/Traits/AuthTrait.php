<?php

namespace App\Traits;

use App\Events\AccountRegistered;
use App\Events\PasswordRecovery;
use App\Mail\AccountVerification;
use App\Mail\PasswordReset;
use App\Models\PasswordSalt;
use App\Models\Verification;
use App\Traits\Enums\StatusCodesEnum;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait AuthTrait
{
    use StatusCodesEnum;
    public function sendVerificationCode($user, $type)
    {
        try {
            $code = mt_rand(1111, 9999);
            $token = str_replace("-", "", Str::uuid());
            $vtype = 0;
            $userid = $user->userid;
            $email = $user->email;
            $name = $user->username;
            $userData = ["name" => $name, "email" => $email];
            $mailData = ["code" => $code, "verifyUrl" => ""];
            switch ($type) {
                case "account":
                    $vtype = $this->accountVerificationStatusCode;
                    Mail::to($email)->send(new AccountVerification($userData, $mailData));
                    break;
                case "password":
                    $vtype = $this->passwordResetStatusCode;
                    Mail::to($email)->send(new PasswordReset($userData, $mailData));
                    break;
                default:
                    return [];
            }
            // AccountRegistered::dispatch($userData, $mailData);
            $code = Hash::make($code);
            $this->saveVerificationData($userid, $code, $vtype, $token);
            return ["success" => true, "data" => ["token" => $token]];
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


    public function saltPassword($userid, $password)
    {
        try {
            $salt = base64_encode(mt_rand());
            $newpass = $salt . $password;
            $pass = PasswordSalt::updateOrCreate([
                "userid" => $userid
            ], [
                "salt" => $salt
            ]);
            return $newpass;
        } catch (Exception $ex) {
            // echo ($ex);
            return null;
        }
    }

    public function getPasswordSalt($userid)
    {
        $slt = PasswordSalt::where("userid", $userid)->first();
        return $slt["salt"];
    }

    public function refreshUserCacheData($user)
    {
        $userid = $user->userid;
        Cache::put($userid . "_DT", $user, 3600);
        // Cache::forget($userid . "_VRF");
    }
}
