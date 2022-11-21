<?php

namespace App\Services;

class BaseRepository
{
    public function processResponse($code, $message, $data = null, $error = null)
    {
        return ["code" => $code, "message" => $message, "data" => $data, "errors" => $error];
    }
}
