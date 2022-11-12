<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param String $authCheck
     * @return mixed
     */
    public function handle(Request $request, Closure $next,String $authCheck)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user){
            if($authCheck=="company" && $user->user_type != 'company'){

                abort(403);
            }
            if($authCheck=="staff" && $user->user_type != 'staff'){

                abort(403);
            }

            return $next($request);
        }
    }
}
