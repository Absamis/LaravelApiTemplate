<?php

namespace App\Services;

use App\Events\AccountCreated;
use App\Events\PasswordChanged;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Verification;
use App\Traits\AuthTrait;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class VerificationRepository extends BaseRepository
{
    use AuthTrait;
    public function verifyAccount($data)
    {
        try {
            $token = $data["tkn"];
            $userid = $data["userid"];
            $code = $data["code"];
            $user = User::with(["verifications"])->where("remember_token", $userid)->first();
            if (!$user)
                return $this->processResponse("99", "Unauthorized user");
            $userid = $user->userid;
            $verify = $user->verifications()->where(["userid" => $userid, "token" => $token, "verify_type" => 1])->first();
            if (!$verify)
                return $this->processResponse("99", "Invalid verification link");
            if (!Hash::check($code, $verify->code))
                return $this->processResponse("99", "Incorrect Code");
            $user->status = 1;
            $user->remember_token = md5(Str::uuid());
            $user->save();
            $verify->delete();
            $user->refresh();
            AccountCreated::dispatch($user);
            return $this->processResponse("00", "Account verified", $user);
        } catch (Exception $ex) {
            return $this->processResponse("99", "Error occured. Try again");
        }
    }

    public function resendAccountVerifyCode($token)
    {
        try {
            $verify = Verification::with(["users"])->where("token", $token)->first();
            if (!$verify)
                return $this->processResponse("99", "Unauthorized user");
            $user = $verify->users;
            // return $user;
            $vrf = $this->sendAccountVerificationCode($user);
            return $this->processResponse("00", "Verification code resent successfully", $vrf["data"]);
        } catch (Exception $ex) {
            return $this->processResponse("99", "Error occured. Try again");
        }
    }

    public function verifyPasswordRecovery($data)
    {
        try {
            $token = Crypt::decryptString($data["tkn"]);
            $userid = $data["userid"];
            $code = $data["code"];
            $user = User::with(["verifications"])->where("remember_token", $userid)->first();
            if (!$user)
                return $this->processResponse("99", "Unauthorized user");
            $userid = $user->userid;
            $verify = $user->verifications()->where(["userid" => $userid, "token" => $token, "verify_type" => 2])->first();
            if (!$verify)
                return $this->processResponse("99", "Invalid verification link");
            if (!Hash::check($code, $verify->code))
                return $this->processResponse("99", "Incorrect Code");
            $tkn = str_replace("-", "", Str::uuid());
            $tkn1 = Crypt::encryptString($tkn);
            $rmtkn = md5(Str::uuid());
            $user->remember_token = $rmtkn;
            $verify->verify_type = 22;
            $verify->token = $tkn;
            $verify->save();
            $user->save();
            $resetUrl = route("password-reset", ["hash" => $tkn1]);
            return $this->processResponse("00", "Account verified. Change your password", ["resetUrl" => $resetUrl, "userid" => $rmtkn]);
        } catch (Exception $ex) {
            return $this->processResponse("99", "Error occured. Try again");
        }
    }

    public function resendPasswordRecovery($tkn)
    {
        try {
            $token = Crypt::decryptString($tkn);
            $verify = Verification::with(["users"])->where("token", $token)->first();
            if (!$verify)
                return $this->processResponse("99", "Unauthorized user");
            $user = $verify->users;
            $vrf = $this->sendPasswordRecoveryCode($user);
            return $this->processResponse("00", "Verification code resent successfully", $vrf["data"]);
        } catch (Exception $ex) {
            return $this->processResponse("99", "Error occured. Try again");
        }
    }
}
