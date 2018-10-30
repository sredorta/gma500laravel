<?php

namespace App\Http\Middleware;
use JWTAuth;
use Closure;
use App\User;

class Any
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if ($request->bearerToken() === null) {
                $request->attributes->add(['isLogged' => false, 'myProfile' => null, 'myAccess' => null]);
                return $next($request);
            }    
            JWTAuth::setToken($request->bearerToken()) ;
            //Get user id from the payload
            $payload = JWTAuth::parseToken()->getPayload();
            $user = User::find($payload->get('id'));

        } catch (Exception $e) {
            $request->attributes->add(['isLogged' => false, 'myProfile' => null, 'myAccess' => null]);
            return $next($request);
        }
        //We should here send parameter profile_id to the route so that we don't need to find again
        $request->attributes->add(['isLogged' => true, 'myProfile' => $user->profile_id, 'myAccess' => $user->access]);
        return $next($request);
    }
}
