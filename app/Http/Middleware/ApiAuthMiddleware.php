<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authenticated = false;

        $token = $request->header('Authorization');
        $user = User::where('token', $token)->first();

        if ($user && $token) {
            $authenticated = true;
        }

        if ($authenticated){
            Auth::login($user);
            return $next($request);
        }else{
            return response()->json([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
