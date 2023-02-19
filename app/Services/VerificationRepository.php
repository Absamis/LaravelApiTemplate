<?php

namespace App\Services;

use App\Events\AccountCreated;
use App\Events\PasswordChanged;
use App\Mail\WelcomeNotification;
use App\Models\Profile\Wallet;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Verification;
use App\Traits\AuthTrait;
use App\Traits\Enums\ResponseCodeEnum;
use App\Traits\Enums\StatusCodesEnum;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class VerificationRepository extends BaseRepository
{
    use AuthTrait, ResponseCodeEnum, StatusCodesEnum;
    public function verifyAccount($data)
    {
        try {
            $token = $data["token"];
            $code = $data["code"];
            $verify = Verification::with(["users"])->where(["token" => $token, "verify_type" => $this->accountVerificationStatusCode])->first();
            if (!$verify)
                return $this->processResponse($this->error["code"], "Invalid verification request");
            if (!Hash::check($code, $verify->code))
                return $this->processResponse($this->error["code"], "Incorrect Code");
            $user = $verify->users;
            $userid = $user->userid;
            $user->status = $this->verifiedAccountCode;
            $user->remember_token = md5(Str::uuid());
            $user->save();
            $verify->delete();
            $user->refresh();
            $this->refreshUserCacheData($user);
            Wallet::create(["userid" => $userid]);
            Mail::to($user->email)->send(new WelcomeNotification($user));
            // AccountCreated::dispatch($user);
            return $this->processResponse($this->success["code"], "Account verified", $user);
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured. Try again");
        }
    }

    public function resendVerificationCode($token, $type)
    {
        try {
            $verify = Verification::with(["users"])->where("token", $token)->first();
            if (!$verify)
                return $this->processResponse($this->error["code"], "Invalid request. Try again.");
            $user = $verify->users;
            $vrf = $this->sendVerificationCode($user, $type);
            return $this->processResponse($this->success["code"], "Verification code resent successfully", $vrf["data"]);
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured. Try again $ex");
        }
    }

    public function verifyPasswordReset($data)
    {
        try {
            $token = $data["token"];
            $code = $data["code"];
            $verify = Verification::with(["users"])->where(["token" => $token, "verify_type" => $this->passwordResetStatusCode])->first();
            if (!$verify)
                return $this->processResponse($this->error["code"], "Invalid verification request");
            if (!Hash::check($code, $verify->code))
                return $this->processResponse($this->error["code"], "Incorrect Code");
            $user = $verify->users;
            $userid = $user->userid;
            $tkn = sha1(Str::uuid());
            $verify->verify_type = $this->passwordResetActionStatusCode;
            $verify->token = $tkn;
            $verify->save();
            return $this->processResponse($this->success["code"], "Verification successful", ["token" => $tkn]);
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured. Try again");
        }
    }
}
