<?php

namespace App\Traits\Enums;

trait ResponseCodeEnum
{
    protected $error = [
        "code" => "99",
        "message" => "Error occured. Try again"
    ];
    protected $formError = [
        "code" => "33",
        "message" => "Form validation error"
    ];
    protected $success =
    [
        "code" => "00",
        "message" => "Operation successful"
    ];
    protected $unverifiedAccountError =
    [
        "code" => "90",
        "message" => "Account not verified"
    ];
}
