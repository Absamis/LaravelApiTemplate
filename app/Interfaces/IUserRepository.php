<?php
namespace App\Interfaces;

interface IUserRepository{
    function getAllUsers();
    function changeUserStatus($user, $status, $remarks = null);
    static function validateUserCredential($data);
    function getUser($id = null);
    function updateUserPassword($user, $password);
    function changeUserPassword($data);
}
