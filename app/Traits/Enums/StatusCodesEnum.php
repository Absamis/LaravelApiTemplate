<?php

namespace App\Traits\Enums;

trait StatusCodesEnum
{
    protected $accountVerificationStatusCode = 1;
    protected $passwordResetStatusCode = 2;
    protected $passwordResetActionStatusCode = 22;



    protected $unverifiedAccountCode = 0;
    protected $verifiedAccountCode = 1;
}
