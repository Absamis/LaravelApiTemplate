<?php

namespace App\Services;

use App\Events\PasswordChanged;
use App\Models\User;
use App\Traits\AuthTrait;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository
{
    /**
     *
     * VERIFICATION TYPE CODE
     *
     * 1 - Account Verification
     * 2 - Password Recovery
     * 22 - Password Reset
     */
    use AuthTrait;
    public function registration($data = [])
    {
        try {
            $data["password"] = Hash::make($data["password"]);
            $data["userid"] = str_replace("-", "", Str::uuid());
            $data["remember_token"] = md5(Str::uuid());
            $user = User::create($data);
            $response = $this->sendAccountVerificationCode($user);
            if ($response["success"]) {
                return $this->processResponse("00", "Account Registered", $response["data"]);
            } else {
                return $this->processResponse("99", "Error occured during registration." . $response["message"]);
            }
        } catch (Exception $ex) {
            return $this->processResponse("99", "Error occured during registration.");
        }
    }

    public function forgotPassword($email)
    {
        try {
            $user = User::where("email", $email)->first();
            if (!$user)
                return $this->processResponse("99", "Account not found");
            $response = $this->sendPasswordRecoveryCode($user);
            if ($response["success"]) {
                return $this->processResponse("00", "Password Recovery Mail Sent", $response["data"]);
            } else {
                return $this->processResponse("99", "Error occured.");
            }
        } catch (Exception $ex) {
            return $this->processResponse("99", "Error occured. Try again");
        }
    }

    public function resetPassword($data)
    {
        try {
            $token = Crypt::decryptString($data["hash"]);
            $userid = $data["userid"];
            $password = $data["newpassword"];
            $user = User::with(["verifications"])->where("remember_token", $userid)->first();
            if (!$user)
                return $this->processResponse("99", "Unauthorized user");
            $userid = $user->userid;
            $verify = $user->verifications()->where(["userid" => $userid, "token" => $token, "verify_type" => 22])->first();
            if (!$verify)
                return $this->processResponse("99", "Request cannot be processed at the moment");
            if (Hash::check($password, $user->password))
                return $this->processResponse("99", "You can't use your previous password.");
            $rmtkn = md5(Str::uuid());
            $user->remember_token = $rmtkn;
            $user->password = Hash::make($password);
            $user->login_status = 0;
            $verify->delete();
            $user->save();
            PasswordChanged::dispatch($user);
            return $this->processResponse("00", "Password changed successfully");
        } catch (Exception $ex) {
            return $this->processResponse("99", "Error occured. Try again");
        }
    }
}
