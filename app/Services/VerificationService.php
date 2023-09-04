<?php
namespace App\Services;

use App\Enums\AccountStatusCodes;
use App\Enums\VerificationStatusCodes;
use App\Mail\AuthVerificationMail;
use App\Models\Verification;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class VerificationService{
    public static function sendVerificationCode($user, $type){
        try{
            $code = mt_rand(111111,999999);
            $data = [];
            switch($type){
                case VerificationStatusCodes::$accountVerification:
                    $data = VerificationStatusCodes::accountVerificationMailMsg('code', $code);
                    break;
                case VerificationStatusCodes::$passwordVerification:
                    $data = VerificationStatusCodes::passwordVerificationMailMsg('code', $code);
                    break;
                default:
                break;
            }

            $rtExp = RateLimiter::attempt('account_verify_'.$user->id, 2, function() use($user, $data, $code, $type){
                Mail::to($user->email)->queue(new AuthVerificationMail($user, $data));
                $hashCode = Hash::make($code);
                $token = self::saveVerificationData($user->id, $hashCode, $type);
                return ['token' => $token];
            }, 60);

            if(RateLimiter::tooManyAttempts('account_verify_' . $user->id, 2))
                return abort(429, "Too many attempt detected. Please hold.");

            return $rtExp;
        }catch(Exception $ex){
            return null;
        }
    }

    public static function validateToken($token, $type, $action = null){
        $verify = Verification::with("user")->where(["token" => $token, "verify_type" => $type])->first();
        if (!$verify)
            return abort(400, "Invalid request or expired token");

        $re = $verify;
        switch ($action) {
            case 'refresh':
                $verify->token = sha1(mt_rand(0000, 1111));
                $verify->status = $verify->status + 1;
                $verify->save();
                $verify->refresh();
                $re = $verify;
                break;
            case 'clear':
                $verify->delete();
                break;
            default:
                break;
        }
        return $re;
    }

    public static function validateCode($data, $type, $action = null)
    {
        $code = $data["code"] ?? null;
        $verify = self::validateToken($data['token'], $type);
        if(!Hash::check($code, $verify->code))
            return abort(400, "Incorrect code entered");

        $re = $verify;
        switch($action){
            case 'refresh':
                $verify->token = sha1(mt_rand(0000,1111));
                $verify->status = $verify->status + 1;
                $verify->save();
                $verify->refresh();
                $re = $verify;
                break;
            case 'clear':
                $verify->delete();
                break;
            default:
                break;
        }
        return $re;
    }

    private static function saveVerificationData($user, $code, $verify_type, $data = null, $status = 0){
        $token = sha1(mt_rand(0000,1111));
        Verification::updateOrCreate([
            "userid" => $user,
            "verify_type" => $verify_type
        ],[
            "token" => $token,
            "code" => $code,
            "data" => $data,
            "status" => $status
        ]);
        return $token;
    }
}
