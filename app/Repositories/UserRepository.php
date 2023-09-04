<?php
namespace App\Repositories;

use App\Interfaces\IUserRepository;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\error;

class UserRepository extends BaseRepository implements IUserRepository{
    public function getAllUsers(){

    }

    public function getUser($id = null){
        if($id){
            $user = User::where("id", $id)->orWhere("email", $id)->first();
        }else{
            $user = Auth::user();
        }
        return $this->success("Done", $user);
    }

    public function getUserData()
    {

    }

    public function changeUserPassword($data){
        $oldPass = $data['old_password'];
        $newPass = $data['new_password'];
        if(!Hash::check($oldPass, Auth::user()->password))
            return $this->failed("Current password is incorrect");
        $this->updateUserPassword(Auth::user(), $newPass);
        return $this->success("Password changed successfully");
    }

    public static function validateUserCredential($data)
    {
        $user = User::where("email", $data['email'])->first();
        if(!$user)
            return abort(400, "Invalid login details");
        if(!Hash::check($data['password'], $user->password))
            return abort(400, "Email and password does not match");
        return $user;
    }

    public function updateUserPassword($user, $password)
    {
        $password = Hash::make($password);
        $user->password = $password;
        $user->save();
        return $this->success("Password successfully updated");
    }

    /**
     * @param App\Models\User $user
     * @param int $status
     * @return array
     */
    public function changeUserStatus($user, $status, $remarks = null)
    {
        $user->status = $status;
        $user->remarks = $remarks ? $remarks : $user->remarks;
        $user->save();
        return $this->success("Operation successful", $user);
    }
}
