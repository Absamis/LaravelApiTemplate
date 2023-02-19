<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\AuthTrait;
use App\Traits\Enums\ResponseCodeEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AuthenticateUser
{
    use AuthTrait, ResponseCodeEnum;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $userid = $request->userid;
        if ($userid) {
            $user = Cache::remember($userid . "_DT", 3600, function () use ($userid) {
                return User::find($userid);
            });
            if (!$user)
                return response()->json([
                    "code" => "99",
                    "message" => "Unauthorize user",
                    "data" => [],
                    "errors" => []
                ]);
            elseif ($user->login_status == 0)
                return response()->json([
                    "code" => "99",
                    "message" => "User not logged in. Logout or re-login to your account.",
                    "data" => [],
                    "errors" => []
                ]);
            elseif ($user->status == 0) {
                if (!Cache::has($userid . "_VRF")) {
                    $response = $this->sendVerificationCode($user, "account");
                    Cache::put($userid . "_VRF", $response["data"], 120);
                }
                return response()->json([
                    "code" => $this->unverifiedAccountError["code"],
                    "message" => "Verification code has been sent to you email.",
                    "data" => Cache::get($userid . "_VRF"),
                    "errors" => []
                ]);
            }
        }
        return $next($request);
    }
}
