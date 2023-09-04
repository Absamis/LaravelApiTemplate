<?php
namespace App\Repositories;

use App\Enums\ApiStatusCodes;

class BaseRepository{
    public function success($message, $data = []){
        return [
            "code" => ApiStatusCodes::$successCode,
            "message" => $message,
            "data" => $data
        ];
    }

    public function failed($message, $data = [])
    {
        return [
            "code" => ApiStatusCodes::$errorCode,
            "message" => $message,
            "data" => $data
        ];
    }
}
