<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfilePhotoRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\UserProfileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    //
    public $userProfileRepo;
    public function __construct(UserProfileRepository $userProfile)
    {
        $this->userProfileRepo = $userProfile;
    }

    public function getUserProfile($userid)
    {
        $response = $this->userProfileRepo->getProfile($userid);
        return $this->actionResponse($response);
    }
    public function updateUserProfile($userid, UpdateProfileRequest $request)
    {
        $response = $this->userProfileRepo->updateProfile($userid, $request->validated());
        return $this->actionResponse($response);
    }

    public function updateUserProfilePhoto($userid, UpdateProfilePhotoRequest $request)
    {
        $response = $this->userProfileRepo->updateProfilePhoto($userid, $request->file("photo"));
        return $this->actionResponse($response);
    }

    public function  changePassword($userid, ChangePasswordRequest $request)
    {
        $response = $this->userProfileRepo->changePassword($userid, $request->validated());
        return $this->actionResponse($response);
    }
}
