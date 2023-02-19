<?php

namespace App\Services;

use App\Mail\PasswordChanged;
use App\Models\User;
use App\Traits\AuthTrait;
use App\Traits\Enums\ResponseCodeEnum;
use App\Traits\Enums\StatusCodesEnum;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserProfileRepository extends BaseRepository
{
    use AuthTrait, ResponseCodeEnum, StatusCodesEnum;
    public function getProfile($userid)
    {
        try {
            $user = User::with(["verifications" => function ($query) {
                $query->get()->makeVisible("token");
            }])->find($userid);
            return $this->processResponse($this->success["code"], "Successful", $user);
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured. Try again");
        }
    }
    public function updateProfile($userid, $data)
    {
        try {
            $username = $data["username"];
            $exist = User::where("username", $username)->where("userid", "<>", $userid)->first();
            if ($exist) {
                return $this->processResponse($this->error["code"], "This username is taken. Kindly change it");
            }
            $user = User::find($userid);
            $user->update($data);
            $user->refresh();
            return $this->processResponse($this->success["code"], "Profle updated successfully", $user);
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured. Try again");
        }
    }

    public function updateProfilePhoto($userid, $photo)
    {
        try {
            $user = User::find($userid);
            $pht = $user->getRawOriginal("photo");
            if ($pht) {
                if (Storage::disk("profile")->exists($pht))
                    Storage::disk("profile")->delete($pht);
            }
            $url = Storage::disk("profile")->put("photo", $photo);
            $user->photo = $url;
            $user->save();
            $user->refresh();
            return $this->processResponse($this->success["code"], "Profile photo updated", $user);
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured. Try again");
        }
    }

    public function changePassword($userid, $data)
    {
        try {
            $currentpassword = $data["current_password"];
            $password = $data["new_password"];
            $user = User::find($userid);
            $pass = $this->getPasswordSalt($user->userid);
            $pass = $pass . $currentpassword;
            if (!Hash::check($pass, $user->password))
                return $this->processResponse($this->error["code"], "Incorrect password. Try again");
            $pass = $this->saltPassword($user->userid, $password);
            $user->password = Hash::make($pass);
            $user->save();
            $userData = ["name" => $user->username, "email" => $user->email];
            $mailData = [];
            Mail::to($user->email)->send(new PasswordChanged($userData, $mailData));
            // $user->refresh();
            // PasswordChanged::dispatch($user);
            return $this->processResponse($this->success["code"], "Password changed successfully");
        } catch (Exception $ex) {
            return $this->processResponse($this->error["code"], "Error occured. Try again");
        }
    }
}
