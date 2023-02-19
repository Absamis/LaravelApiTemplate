<?php

namespace App\Services;

use App\Events\PasswordChanged;
use App\Mail\PasswordChanged as MailPasswordChanged;
use App\Models\User;
use App\Models\Verification;
use App\Traits\AuthTrait;
use App\Traits\Enums\ResponseCodeEnum;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
    use AuthTrait, ResponseCodeEnum;
    public function registration($data = [])
    {
        try {
            $userid = $data["userid"] = str_replace("-", "", Str::uuid());
            $data["remember_token"] = md5(Str::uuid());
            $pass = $this->saltPassword($userid, $data["password"]);
            if (!$pass)
                return $this->processResponse($this->error["code"], "Error occured. Try again", ["err_code" => "SALT Error"]);
            $data["password"] = Hash::make($pass);
            $user = User::create($data);
            $response = $this->sendVerificationCode($user, "account");
            return $this->processResponse($this->success["code"], "Account registered. Verification code has been sent to you email", $response["data"]);
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured during registration.");
        }
    }

    public function forgotPassword($email)
    {
        try {
            $user = User::where("email", $email)->first();
            if (!$user)
                return $this->processResponse($this->error["code"], "Account not found");
            $response = $this->sendVerificationCode($user, "password");
            return $this->processResponse($this->success["code"], "Password verification code sent to your email.", $response["data"]);
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured. Try again");
        }
    }

    public function resetPassword($data)
    {
        try {
            $token = $data["token"];
            $password = $data["new_password"];
            $verify = Verification::with(["users"])->where(["token" => $token, "verify_type" => $this->passwordResetActionStatusCode])->first();
            if (!$verify)
                return $this->processResponse($this->error["code"], "Request cannot be processed at the moment");
            $user = $verify->users;
            $userid = $user->userid;

            $pass = $this->getPasswordSalt($user->userid);
            $pass = $pass . $password;
            if (Hash::check($pass, $user->password))
                return $this->processResponse($this->error["code"], "You can't use your previous password.");
            $user->password = Hash::make($pass);
            $user->login_status = 0;
            $verify->delete();
            $user->save();
            $userData = ["name" => $user->username, "email" => $user->email];
            $mailData = [];
            Mail::to($user->email)->send(new MailPasswordChanged($userData, $mailData));
            // PasswordChanged::dispatch($user);
            return $this->processResponse($this->success["code"], "Password changed successfully");
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured. Try again");
        }
    }

    public function login($data)
    {
        try {
            $email = $data["email"];
            $password = $data["password"];
            $user = User::where("email", $email)->first();
            if (!$user)
                return $this->processResponse($this->error["code"], "Invalid login details");
            $pass = $this->getPasswordSalt($user->userid);
            $pass = $pass . $password;
            if (!Hash::check($pass, $user->password))
                return $this->processResponse("99", "The email and password does not match");
            $status = $user->status;
            $user->login_status = 1;
            $user->last_login = now();
            $user->save();
            $userid = $user->userid;
            if ($status == 0) {
                if (!Cache::has($userid . "_VRF")) {
                    $response = $this->sendVerificationCode($user, "account");
                    Cache::put($userid . "_VRF", $response["data"], 120);
                }
                return $this->processResponse($this->unverifiedAccountError["code"], "Account not verified. Verification code sent to your mail", Cache::get($userid . "_VRF"));
                // if ($response["success"]) {
                //     return $this->processResponse($this->unverifiedAccountError, "Account not verified. Verification code sent to your mail", $response["data"]);
                // }
                // return $this->processResponse($this->error["code"], "Error loging you in to the application. Try again");
            }
            // $user->refresh();
            return $this->processResponse($this->success["code"], "Login Successful", $user);
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured. Try again");
        }
    }

    public function logout($userid)
    {
        try {
            $user = User::find($userid);
            $user->login_status = 0;
            $user->save();
            return $this->processResponse($this->success["code"], "Log out Successful");
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured. Try again");
        }
    }
}
