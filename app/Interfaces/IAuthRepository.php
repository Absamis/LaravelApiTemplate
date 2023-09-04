<?php
namespace App\Interfaces;

interface IAuthRepository{
    function login($data);
    function register($data);
    function forgotPassword($data);
    function resetPassword($data);
    function logout();
}
