<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ChangePasswordRequest;
use App\Interfaces\IUserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    private IUserRepository $userRepo;
    public function __construct(IUserRepository $repo)
    {
        $this->userRepo = $repo;
    }
    public function getUser(){
        $response = $this->userRepo->getUser();
        return response()->json($response);
    }

    public function changeUserPassword(ChangePasswordRequest $request){
        $response = $this->userRepo->changeUserPassword($request->validated());
        return response()->json($response);
    }
}
