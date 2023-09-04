<?php
namespace App\Repositories;

use App\Enums\AccountStatusCodes;
use App\Enums\VerificationStatusCodes;
use App\Interfaces\IAuthRepository;
use App\Interfaces\IUserRepository;
use App\Models\User;
use App\Services\VerificationService;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthRepository extends BaseRepository implements IAuthRepository{
    private IUserRepository $userRepo;
    public function __construct(IUserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function login($data)
    {
        $user = $this->userRepo::validateUserCredential($data);
        if($user->status != AccountStatusCodes::$verifiedAccount){
            $response = [];
            if($user->status == AccountStatusCodes::$unverifiedAccount){
                try {
                    $response = VerificationService::sendVerificationCode($user, VerificationStatusCodes::$accountVerification);
                } catch (Exception $ex) {
                }
            }
            return abort(403, 'Access to account forbidden', [
                "data" => $response + ["auth_type" => $user->status]
            ]);
        }
        $user->tokens()->delete();
        $user->makeVisible('rememberToken');
        $user->rememberToken = $user->createToken($user->id.time())->plainTextToken;
        return $this->success("Login Successful", $user);
    }

    public function register($data)
    {
        $data['password'] = Hash::make($data['password']);
        $data["status"] = AccountStatusCodes::$unverifiedAccount;
        $user = User::create($data);
        try{
            $response = VerificationService::sendVerificationCode($user, VerificationStatusCodes::$accountVerification);
        }catch(Exception $ex){}
        return $this->success("Registration successful", $response);
    }

    public function forgotPassword($data)
    {
        $srv = $this->userRepo->getUser($data['email']);
        $user = $srv["data"];
        try {
            $response = VerificationService::sendVerificationCode($user, VerificationStatusCodes::$passwordVerification);
        } catch (Exception $ex) {
        }
        return $this->success("Verification code sent", $response);
    }

    public function resetPassword($data)
    {
        $vrf = VerificationService::validateToken($data['token'], VerificationStatusCodes::$passwordVerification, 'clear');
        if($vrf->status != 1)
            return abort(400, "Invalid request");
        $response = $this->userRepo->updateUserPassword($vrf->user, $data['new_password']);
        return $response;
    }

    public function logout()
    {

    }
}
