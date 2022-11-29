<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AuthenticateUser
{
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
            $user = User::find($userid);
            if (!$user)
                return response()->json([
                    "code" => "99",
                    "message" => "Unauthorize user"
                ]);
            elseif ($user->login_status == 0)
                return response()->json([
                    "code" => "99",
                    "message" => "User not logged in"
                ]);
        }
        return $next($request);
    }
}
