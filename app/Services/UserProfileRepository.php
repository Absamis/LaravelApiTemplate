<?php

namespace App\Services;

use App\Models\User;
use Exception;

class UserProfileRepository extends BaseRepository
{

    public function getProfile($userid)
    {
        try {
            $user = User::find($userid);
            return $this->processResponse("00", "Successful", $user);
        } catch (Exception $ex) {
            return $this->processResponse("99", "Error occured. Try again");
        }
    }
    public function updateProfile($userid, $data)
    {
        try {
            $username = $data["username"];
            $exist = User::where("username", $username)->where("userid", "<>", $userid)->first();
            if ($exist) {
                return $this->processResponse("99", "This username is taken. Kindly change it");
            }
            $user = User::find($userid);
            $user->update($data);
            $user->refresh();
            return $this->processResponse("00", "Profle updated successfully", $user);
        } catch (Exception $ex) {
            return $this->processResponse("99", "Error occured. Try again");
        }
    }

    public function updateProfilePhoto($userid, $photourl)
    {
        try {
            $user = User::find($userid);
            $user->photo = $photourl;
            $user->save();
            $user->refresh();
            return $this->processResponse("00", "Profile photo updated", $user);
        } catch (Exception $ex) {
            return $this->processResponse("99", "Error occured. Try again");
        }
    }
}
