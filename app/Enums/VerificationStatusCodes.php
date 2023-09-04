<?php
namespace App\Enums;
class VerificationStatusCodes{
    public static $accountVerification = 1;
    public static $passwordVerification = 2;
    public static $badRequest = 99;

    public static function accountVerificationMailMsg($type, $code){
        return [
            "message" => "Your account verification code is stated below",
            "code" => $code,
            "type" => $type,
            "subject" => "Account Verification"
        ];
    }

    public static function passwordVerificationMailMsg($type, $code)
    {
        return [
            "message" => "Your password recovery code is stated below",
            "code" => $code,
            "type" => $type,
            "subject" => "Password Recovery"
        ];
    }

    public static function getCode($text){
        switch($text){
            case 'account':
                return self::$accountVerification;
            case 'password':
                return self::$passwordVerification;
            default:
            return 99;
        }
    }
}
