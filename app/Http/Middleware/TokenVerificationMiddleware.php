<?php

namespace App\Http\Middleware;

use App\Helper\JWTToken;
use App\Models\User;
use Closure;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class TokenVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('token');
        if (!$token) {
            return redirect('/')->cookie('token', '', -1);
        }

        try{
            $result = JWTToken::VerifyToken($token);

            if($result == 'unauthorized'){
                return  redirect('/')->cookie('token', '', -1);
            }
            else{
                $request->headers->set('email', $result);
                $user = User::where('email', $result)->first();

                if (!$user) {
                    return redirect('/')->cookie('token', '', -1);
                }

                // Set user into Laravel's auth system
                Auth::login($user);


                return $next($request);
            }
        }catch (ExpiredException $e) {
            // Token is expired — logout
            return redirect('/')->cookie('token', '', -1);
        } catch (\Exception $e) {
            // Other JWT issues (e.g., malformed token)
            return redirect('/')->cookie('token', '', -1);
        }


    }
}
